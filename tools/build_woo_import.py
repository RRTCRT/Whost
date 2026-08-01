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
#   Men's Vests       -> superseded by the "Mens Vests" family below; keeping
#                        both would create two categories differing only by an
#                        apostrophe, holding the same three products
DROP_CATEGORIES = {"Category1", "All Products", "Men's Vests", ""}

# Wix option names mapped to cleaner Woo attribute names.
# The shop page advertises six garment families, but the Wix store only tagged
# products with gender collections (Mens / Womens / Accessories) — nothing tied
# the two together, so "Unisex Chaps" was a heading with no products behind it.
# This maps each product to the family the site actually sells it under. Gender
# collections are kept as secondary categories so both ways of browsing work.
PRODUCT_FAMILIES = {
    "Jackets & Shirts": [
        "women-s-deerskin-jacket",
        "deerskin-biker-jacket",
        "mavrick-shirt",
        "boone-shirt",
    ],
    "Mens Vests": [
        "mens-deerskin-vest-1",
        "mens-elk-vest",
        "mens-bison-vest",
    ],
    "Ladies Vests": [
        "ladies-dress-vest",
        "ladies-v-neck-club-vest",
        "ladies-piped-club-vest",
        "ladies-club-vest",
        "ladies-rustic-vest",
    ],
    "Unisex Chaps": [
        "deerskin-chaps",
        "bison-chaps-1",
        "elk-chaps",
    ],
    "Half Chaps": [
        "adjustable-half-chaps-1",
    ],
    # Non-leather goods: shirts, stickers, knives and the like. The shop page
    # doesn't list this heading yet, but the owner keeps them as a separate
    # group, so it's here ready for stock that isn't cut in the workshop.
    "Other Items": [
        "40th-anniversary-shirt",
    ],
    "Accessories": [
        "unisex-harness-cowhide-belt",
        "cowhide-belt",
        "western-purse",
        "fort-worth-cowhide-purse",
        "minot-bag",
        "cheyenne-cowhide-clip-on-bag",
        "medium-rustic-purses",
        "medium-rustic-purse",
        "rustic-purses",
        "hip-bags",
        "moccasins-slipper",
        "braided-strap",
        "bone-vest-extenders",
        "zippered-deerskin-coin-card-pouch",
        "skidmore-s-waterproofer",
        "skidmore-s-waterproofer-1",
    ],
}

# Family blurbs, taken verbatim from the shop page, for the Woo category
# descriptions. These render above the products on each category archive.
FAMILY_DESCRIPTIONS = {
    "Jackets & Shirts": "Jackets for men and women, and unisex shirts. No outfit is complete without a jacket that breaks the wind and sun while staying lightweight and breathable.",
    "Mens Vests": "Made from deer, elk, or bison hides, we've created a men's vest that has been our top seller with different color combinations and XS-5XL patterns. With 4 pockets, it's orderable in tall, solid side or side lace.",
    "Ladies Vests": "Our ladies' Rustic vest has been a top seller since the early 2000s! With rustic, natural edges, and a wide color variety, it is embellished with natural stones or glass beads. Orderable 2XS-5XL to flatter anyone's figure!",
    "Unisex Chaps": "Made from deer, elk, or bison hides, we've created beautiful unisex chaps with different colors and 2XS-3XL patterns. These chaps are also very easily customized with fringe, pockets or conchos.",
    "Half Chaps": "These unisex half-chaps are typically a mix of elk and bison hides in Western or Rustic styles. Perfect for motorcycles or horses. Orderable 2XS-3XL to protect everyone!",
    "Accessories": "Belts, holsters, bags, purses, wallets, unique jewelry, Damascus knives & sheaths and more!",
    "Other Items": "Shirts, stickers, knives and everything else in the shop that isn't cut from hide.",
}

# Reverse lookup, built once.
FAMILY_BY_SLUG = {
    slug: family for family, slugs in PRODUCT_FAMILIES.items() for slug in slugs
}

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


def build(rows: list[dict], draft: bool) -> tuple[list[dict], list[str], list[str]]:
    out: list[dict] = []
    media: list[str] = []
    unfamilied: list[str] = []

    for product in rows:
        cats, featured = split_categories(product.get("cats", ""))

        # Lead with the garment family, then the gender collections.
        family = FAMILY_BY_SLUG.get(product["slug"])
        if family:
            cats = [family] + [c for c in cats if c != family]
        else:
            unfamilied.append(product["name"])

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

    return out, media, unfamilied


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("-o", "--output", type=Path, default=BUILD / "woo-products.csv")
    parser.add_argument("--draft", action="store_true", help="import products unpublished")
    args = parser.parse_args()

    products = load_products()
    rows, media, unfamilied = build(products, args.draft)

    args.output.parent.mkdir(parents=True, exist_ok=True)

    with args.output.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(handle, fieldnames=WOO_COLUMNS, extrasaction="ignore")
        writer.writeheader()
        for record in rows:
            writer.writerow({c: record.get(c, "") for c in WOO_COLUMNS})

    unique_media = sorted(set(media))
    BUILD.mkdir(exist_ok=True)
    (BUILD / "media-urls.txt").write_text("\n".join(unique_media) + "\n", encoding="utf-8")

    # Category descriptions to paste into Products > Categories in wp-admin.
    lines = [
        "# Product category descriptions",
        "",
        "Paste each into **Products > Categories > Edit > Description** in wp-admin.",
        "Taken verbatim from the Wix shop page.",
        "",
    ]
    for family, blurb in FAMILY_DESCRIPTIONS.items():
        count = sum(1 for r in rows if r["Categories"].split(", ")[0] == family)
        lines += [f"## {family}  _({count} products)_", "", blurb, ""]

    (BUILD / "product-categories.md").write_text("\n".join(lines), encoding="utf-8")

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

    if unfamilied:
        print(
            f"\nNot assigned to a garment family ({len(unfamilied)}): {', '.join(unfamilied)}"
            "\n  These keep their gender categories only. Add them to PRODUCT_FAMILIES"
            "\n  in this script if they belong under one of the six shop headings."
        )

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
