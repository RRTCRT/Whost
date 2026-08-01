# Hide and Soul — WordPress rebuild

Rebuilding [hideandsoul.com](https://www.hideandsoul.com/) off Wix as a
hand-coded WordPress theme on Hostinger.

```
theme/hide-and-soul/     the theme — PHP templates, one stylesheet, one JS file
mu-plugins/              legacy Wix URL redirects (must-use, can't be deactivated)
tools/                   migration scripts (Python 3, stdlib only)
docs/                    the plan, the deploy runbook, the URL map
content/                 generated: page copy extracted from your saved Wix HTML
build/                   generated: WordPress import file + media URL list
```

## Start here

1. **[docs/MIGRATION-PLAN.md](docs/MIGRATION-PLAN.md)** — the whole migration, phase by phase
2. **[docs/HOSTINGER-DEPLOY.md](docs/HOSTINGER-DEPLOY.md)** — getting the code onto the server
3. **[docs/URL-MAP.csv](docs/URL-MAP.csv)** — every old Wix URL and where it now goes

## Moving your saved Wix pages in

You've already saved the Wix pages with "Save page as". Put those `.html` files
in a folder — say `wix-html/` — and run:

```bash
python3 tools/wix_extract.py parse wix-html
```

You get:

| Output | What it's for |
|---|---|
| `content/*.md` | Every page's copy, readable — proofread here first |
| `build/pages.json` | The same data, structured (titles, meta descriptions, images) |
| `build/media-urls.txt` | Every image, rewritten to **full resolution** |

Then generate a WordPress import file and pull the real photos:

```bash
python3 tools/wix_extract.py wxr --site https://www.mediumseagreen-gerbil-453930.hostingersite.com --draft
python3 tools/wix_extract.py media --download media
```

Import `build/import.xml` at **Tools ➜ Import ➜ WordPress**.

> The `--draft` flag is deliberate. Wix pages carry a lot of layout text that
> reads like nonsense once it's out of its slideshow. Import as drafts, read
> them, then publish.

**Why the full-resolution rewrite matters:** Wix serves cropped, resized copies
in the page source — a 600px-wide thumbnail of a 3000px photo. The extractor
strips the `/v1/fill/w_600,h_400,.../` transform off each URL so you download
the original upload instead of the crop.

## Moving the shop

Export from Wix (**Dashboard ➜ Store Products ➜ Export**), then:

```bash
python3 tools/wix_products_to_woo.py wix-products.csv -o woo-products.csv --draft
```

Import at **WooCommerce ➜ Products ➜ Import**. Woo maps the columns automatically.

## The one setting worth knowing about

**Appearance ➜ Customize ➜ Hide and Soul ➜ Shop Behaviour ➜ Catalog mode.**

Checked, the store shows products, photos and prices but has no cart or
checkout — every product page gets a "call the shop" panel instead. Unchecked,
it's a full WooCommerce store.

You can launch in catalog mode and turn checkout on later without rebuilding
anything.

## Local development

There's no build step. Edit the PHP and CSS directly.

```bash
# syntax-check every template before deploying
find theme -name '*.php' -exec php -l {} \;
```

## What still needs your input

Marked `TODO` in the code, and listed in full at the bottom of
[docs/MIGRATION-PLAN.md](docs/MIGRATION-PLAN.md):

- Custer and Cave Creek addresses (`theme/hide-and-soul/inc/business-info.php`)
- Shop hours — currently taken from directory listings, not your site
- Whether `/bh-guide` and `/crp` should keep those slugs
- All placeholder body copy in `front-page.php`
