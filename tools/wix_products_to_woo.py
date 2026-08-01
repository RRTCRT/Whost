#!/usr/bin/env python3
"""Convert a Wix Stores product export into a WooCommerce import CSV.

Wix: Dashboard ➜ Store Products ➜ Export (gives you a CSV of every product,
with one "Product" row per item followed by one "Variant" row per combination).

WooCommerce: Products ➜ Import ➜ upload the file this writes. Woo's importer
maps the column names below automatically — no manual field mapping needed.

Usage
-----
    python3 tools/wix_products_to_woo.py wix-products.csv -o woo-products.csv
    python3 tools/wix_products_to_woo.py wix-products.csv -o woo.csv --category "Men's"

Stdlib only.
"""

from __future__ import annotations

import argparse
import csv
import html
import re
import sys
from collections import OrderedDict
from pathlib import Path

# Wix wraps descriptions in markup; Woo is happy with HTML but not with the
# editor cruft Wix leaves behind.
TAG_RE = re.compile(r"<[^>]+>")
WS_RE = re.compile(r"\s+")

# Wix media URLs carry a render transform. Strip it for the original upload.
WIX_TRANSFORM = re.compile(r"/v1/(?:fill|crop|fit)/[^\s,]*", re.IGNORECASE)

WOO_COLUMNS = [
    "Type",
    "SKU",
    "Name",
    "Published",
    "Visibility in catalog",
    "Short description",
    "Description",
    "In stock?",
    "Stock",
    "Weight (lbs)",
    "Regular price",
    "Sale price",
    "Categories",
    "Tags",
    "Images",
    "Parent",
    "Attribute 1 name",
    "Attribute 1 value(s)",
    "Attribute 1 visible",
    "Attribute 1 global",
    "Attribute 2 name",
    "Attribute 2 value(s)",
    "Attribute 2 visible",
    "Attribute 2 global",
    "Attribute 3 name",
    "Attribute 3 value(s)",
    "Attribute 3 visible",
    "Attribute 3 global",
]


def clean_text(value: str, strip_tags: bool = False) -> str:
    text = html.unescape(value or "")
    if strip_tags:
        text = TAG_RE.sub(" ", text)
    return WS_RE.sub(" ", text).strip()


def clean_images(value: str) -> str:
    """Wix separates image URLs with ';'; Woo wants ', '."""
    urls = []
    for part in re.split(r"[;\n]", value or ""):
        url = part.strip()
        if not url:
            continue
        if url.startswith("//"):
            url = "https:" + url
        urls.append(WIX_TRANSFORM.sub("", url))
    return ", ".join(urls)


def clean_price(value: str) -> str:
    text = (value or "").strip().replace("$", "").replace(",", "")
    try:
        number = float(text)
    except ValueError:
        return ""
    return f"{number:.2f}".rstrip("0").rstrip(".") if number else ""


def sale_price(regular: str, mode: str, value: str) -> str:
    """Resolve Wix's discount into an absolute Woo sale price.

    Wix stores the discount as a mode plus a bare number: PERCENT 10 means
    "10% off", AMOUNT 10 means "$10 off". Copying the number straight into
    Woo's "Sale price" column would price a $349 vest at $10.
    """
    try:
        base = float(regular)
        amount = float((value or "").strip() or 0)
    except ValueError:
        return ""

    if not base or not amount:
        return ""

    mode = (mode or "").strip().upper()

    if mode.startswith("PERCENT"):
        result = base * (1 - amount / 100)
    elif mode.startswith("AMOUNT"):
        result = base - amount
    else:
        # No mode given — treat the value as an absolute price only if it is
        # plausibly one, i.e. below the regular price.
        result = amount if amount < base else 0

    if result <= 0 or result >= base:
        return ""

    return f"{result:.2f}"


def get(row: dict, *names: str) -> str:
    """Read the first column that exists, case-insensitively."""
    lowered = {k.strip().lower(): v for k, v in row.items() if k}
    for name in names:
        value = lowered.get(name.lower())
        if value:
            return value.strip()
    return ""


def collect_options(row: dict) -> list[tuple[str, str]]:
    """Pull Wix's productOptionName{n} / productOptionDescription{n} pairs."""
    options: list[tuple[str, str]] = []

    for i in range(1, 7):
        name = get(row, f"productOptionName{i}")
        desc = get(row, f"productOptionDescription{i}")

        if not name or not desc:
            continue

        # Wix writes "Small;Medium;Large" or "Small:0;Medium:0" depending on
        # whether the choice carries a price surcharge.
        values = []
        for chunk in desc.split(";"):
            chunk = chunk.strip()
            if not chunk:
                continue
            values.append(chunk.split(":")[0].strip())

        if values:
            options.append((name, " | ".join(OrderedDict.fromkeys(values))))

    return options[:3]  # Woo's importer handles 3 attributes cleanly


def convert(source: Path, target: Path, default_category: str, drafts: bool) -> int:
    with source.open(newline="", encoding="utf-8-sig") as handle:
        rows = list(csv.DictReader(handle))

    if not rows:
        print("error: source CSV is empty", file=sys.stderr)
        return 1

    out_rows: list[dict] = []
    skipped_variants = 0

    for row in rows:
        field_type = get(row, "fieldType", "field type").lower()

        # Variant rows describe combinations of the parent's options. Importing
        # them as Woo variations needs the parent to exist first, which the
        # single-pass importer can't guarantee — so the parent keeps the options
        # as attributes and variants are reported, not emitted.
        if field_type and field_type != "product":
            skipped_variants += 1
            continue

        name = clean_text(get(row, "name", "product name"))
        if not name:
            continue

        description = clean_text(get(row, "description", "productDescription"))
        options = collect_options(row)
        regular = clean_price(get(row, "price"))

        record = {
            "Type": "variable" if options else "simple",
            "SKU": get(row, "sku"),
            "Name": name,
            "Published": "0" if drafts else "1",
            "Visibility in catalog": "visible",
            "Short description": clean_text(description, strip_tags=True)[:180],
            "Description": description,
            "In stock?": "1",
            "Stock": get(row, "inventory", "quantity"),
            "Weight (lbs)": get(row, "weight"),
            "Regular price": regular,
            "Sale price": sale_price(
                regular,
                get(row, "discountMode", "discount mode"),
                get(row, "discountValue", "discount value", "salePrice"),
            ),
            "Categories": get(row, "collection", "collections") or default_category,
            "Tags": get(row, "ribbon"),
            "Images": clean_images(get(row, "productImageUrl", "images", "imageUrl")),
            "Parent": "",
        }

        for index, (attr_name, attr_values) in enumerate(options, start=1):
            record[f"Attribute {index} name"] = attr_name
            record[f"Attribute {index} value(s)"] = attr_values
            record[f"Attribute {index} visible"] = "1"
            record[f"Attribute {index} global"] = "1"

        out_rows.append(record)

    with target.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(handle, fieldnames=WOO_COLUMNS, extrasaction="ignore")
        writer.writeheader()
        for record in out_rows:
            writer.writerow({column: record.get(column, "") for column in WOO_COLUMNS})

    print(f"{len(out_rows)} products -> {target}")

    if skipped_variants:
        print(
            f"\n{skipped_variants} variant rows were folded into their parent's attributes.\n"
            "Woo will create the variations for you: open each variable product,\n"
            "go to Variations ➜ Generate variations, then set per-variation prices."
        )

    missing_price = sum(1 for r in out_rows if not r["Regular price"])
    if missing_price:
        print(f"\nWarning: {missing_price} products have no price. Check them before publishing.")

    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("source", type=Path, help="Wix products CSV export")
    parser.add_argument("-o", "--output", type=Path, default=Path("woo-products.csv"))
    parser.add_argument("--category", default="Uncategorized", help="fallback category")
    parser.add_argument("--draft", action="store_true", help="import products unpublished")
    args = parser.parse_args()

    if not args.source.exists():
        print(f"error: {args.source} not found", file=sys.stderr)
        return 1

    return convert(args.source, args.output, args.category, args.draft)


if __name__ == "__main__":
    raise SystemExit(main())
