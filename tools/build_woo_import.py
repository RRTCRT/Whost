#!/usr/bin/env python3
"""Build a WooCommerce import CSV from the Wix catalogue pulled via the Wix API.

The `data/wix-products-*.json` files were fetched live from the Wix Stores API
(stores-reader/v1), so this needs no CSV export from the Wix dashboard and no
manual retyping — descriptions, options, prices and images are the real ones.

    python3 tools/build_woo_import.py
    python3 tools/build_woo_import.py --draft -o build/woo-products.csv

Also writes build/media-urls.txt with every product image at full resolution,
ready for `wix_extract.py media --download`.

Stdlib only.
"""

from __future__ import annotations

import argparse
import csv
import json
import re
from pathlib import Path

DATA = Path("data")
BUILD = Path("build")

# Wix media IDs resolve to their original upload at this base — no /v1/fill/...
# transform means no downscaled crop.
MEDIA_BASE = "https://static.wixstatic.com/media/"

# Wix collections that are not real categories.
#   Featured Products -> Woo's "Is featured?" flag
#   Category1         -> leftover from a Wix template, carries no meaning
#   All Products      -> Wix's implicit catch-all
FEATURED = "Featured Products"
DROP_CATEGORIES = {"Category1", "All Products", ""}

# Wix option names mapped to cleaner Woo attribute names.
ATTRIBUTE_NAMES = {
    "Stocked Sizes": "Size",
    "Color Choices": "Color",
    "Color Selection": "Color",
    "Second Color": "Second Color",
    "Style Options": "Style",
    "Pipping Options": "Piping",
    "Product option": "Finish",
    "Options": "Option",
    "Material": "Material",
}

WOO_COLUMNS = [
    "Type",
    "SKU",
    "Name",
    "Published",
    "Is featured?",
    "Visibility in catalog",
    "Short description",
    "Description",
    "In stock?",
    "Backorders allowed?",
    "Regular price",
    "Categories",
    "Images",
    "Position",
]

for _i in range(1, 5):
    WOO_COLUMNS += [
        f"Attribute {_i} name",
        f"Attribute {_i} value(s)",
        f"Attribute {_i} visible",
        f"Attribute {_i} global",
    ]


def load_products() -> list[dict]:
    files = sorted(DATA.glob("wix-products-*.json"))

    if not files:
        raise SystemExit(f"error: no wix-products-*.json in {DATA}/")

    products: list[dict] = []
    for path in files:
        products.extend(json.loads(path.read_text(encoding="utf-8")))

    # A slug can appear twice if a batch overlapped; keep the first.
    seen: set[str] = set()
    unique = []
    for p in products:
        if p["slug"] in seen:
            continue
        seen.add(p["slug"])
        unique.append(p)

    return unique


def short_description(html: str) -> str:
    """First sentence or two of the description, stripped of markup."""
    text = re.sub(r"<[^>]+>", " ", html or "")
    text = text.replace("&amp;", "&").replace("&nbsp;", " ")
    text = re.sub(r"\s+", " ", text).strip()

    if len(text) <= 200:
        return text

    cut = text[:200]
    stop = max(cut.rfind(". "), cut.rfind("! "))
    return (cut[: stop + 1] if stop > 80 else cut.rsplit(" ", 1)[0] + "…").strip()


def split_categories(raw: str) -> tuple[list[str], bool]:
    """Wix collections were joined with ' > '. They are siblings, not a tree.

    WooCommerce reads '>' as parent > child hierarchy and ',' as separate
    categories — so passing these through unchanged would invent a nested
    'Accessories > Mens > Womens' category that never existed.
    """
    names = [c.strip() for c in (raw or "").split(">")]
    featured = FEATURED in names
    cats = [c for c in names if c and c not in DROP_CATEGORIES and c != FEATURED]
    return cats, featured


def parse_options(opts: list[str]) -> list[tuple[str, str]]:
    """'Stocked Sizes=XS|S|M' -> ('Size', 'XS, S, M')."""
    parsed: list[tuple[str, str]] = []

    for raw in opts or []:
        if "=" not in raw:
            continue

        name, _, values = raw.partition("=")
        name = ATTRIBUTE_NAMES.get(name.strip(), name.strip())

        seen: list[str] = []
        for value in values.split("|"):
            value = value.strip()
            if value and value not in seen:
                seen.append(value)

        if seen:
            parsed.append((name, ", ".join(seen)))

    return parsed[:4]


def build(rows: list[dict], draft: bool) -> tuple[list[dict], list[str]]:
    out: list[dict] = []
    media: list[str] = []

    for product in rows:
        cats, featured = split_categories(product.get("cats", ""))
        attributes = parse_options(product.get("opts", []))
        images = [MEDIA_BASE + i for i in product.get("imgs", [])]
        media.extend(images)

        record = {
            "Type": "variable" if attributes else "simple",
            "SKU": product["slug"],
            "Name": product["name"],
            "Published": "0" if draft else "1",
            "Is featured?": "1" if featured else "0",
            "Visibility in catalog": "visible",
            "Short description": short_description(product.get("desc", "")),
            "Description": product.get("desc", ""),
            "In stock?": "1" if product.get("inStock") else "0",
            # Everything here is made to order, so an out-of-stock item is
            # still orderable — it just isn't sitting on the shelf today.
            "Backorders allowed?": "notify",
            "Regular price": ("%g" % product["price"]) if product.get("price") else "",
            "Categories": ", ".join(cats),
            "Images": ", ".join(images),
            "Position": "0",
        }

        for index, (name, values) in enumerate(attributes, start=1):
            record[f"Attribute {index} name"] = name
            record[f"Attribute {index} value(s)"] = values
            record[f"Attribute {index} visible"] = "1"
            record[f"Attribute {index} global"] = "1"

        out.append(record)

    return out, media


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("-o", "--output", type=Path, default=BUILD / "woo-products.csv")
    parser.add_argument("--draft", action="store_true", help="import products unpublished")
    args = parser.parse_args()

    products = load_products()
    rows, media = build(products, args.draft)

    args.output.parent.mkdir(parents=True, exist_ok=True)

    with args.output.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(handle, fieldnames=WOO_COLUMNS, extrasaction="ignore")
        writer.writeheader()
        for record in rows:
            writer.writerow({c: record.get(c, "") for c in WOO_COLUMNS})

    unique_media = sorted(set(media))
    BUILD.mkdir(exist_ok=True)
    (BUILD / "media-urls.txt").write_text("\n".join(unique_media) + "\n", encoding="utf-8")

    variable = sum(1 for r in rows if r["Type"] == "variable")
    featured = sum(1 for r in rows if r["Is featured?"] == "1")
    no_price = [r["Name"] for r in rows if not r["Regular price"]]

    categories = sorted({c for r in rows for c in r["Categories"].split(", ") if c})

    print(f"{len(rows)} products -> {args.output}")
    print(f"  {variable} variable, {len(rows) - variable} simple, {featured} featured")
    print(f"  categories: {', '.join(categories)}")
    print(f"  {len(unique_media)} unique images -> {BUILD / 'media-urls.txt'}")

    if no_price:
        print(f"\nWarning: no price on {len(no_price)}: {', '.join(no_price)}")

    print(
        "\nNext:\n"
        "  1. WooCommerce > Products > Import, upload the CSV\n"
        "  2. Open each variable product > Variations > Generate variations,\n"
        "     then set any per-variation prices (e.g. +$10 for XL)\n"
        f"  3. python3 tools/wix_extract.py media --download media"
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
