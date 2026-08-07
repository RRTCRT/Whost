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
2. **[docs/CONTENT-ISSUES.md](docs/CONTENT-ISSUES.md)** — decisions only you can make, **read this one**
3. **[docs/HOSTINGER-DEPLOY.md](docs/HOSTINGER-DEPLOY.md)** — getting the code onto the server
4. **[docs/URL-MAP.csv](docs/URL-MAP.csv)** — every old Wix URL and where it now goes

## Installing it — no SSH needed

Grab the two zips from the repo's **Actions** tab (newest run ➜ Artifacts), or
build them yourself with `python3 tools/build_zips.py`. Then:

| Zip | Where it goes |
|---|---|
| `hide-and-soul.zip` | Appearance ➜ Themes ➜ Add New ➜ **Upload Theme** |
| `hide-and-soul-redirects.zip` | Plugins ➜ Add New ➜ **Upload Plugin** |

**Updating later is the same two clicks.** Upload the newer zip and WordPress
offers **"Replace current with uploaded"** — your pages, menus and settings live
in the database and are not touched. Full detail in
[HOSTINGER-DEPLOY.md](docs/HOSTINGER-DEPLOY.md).

## State of play

All 15 Wix pages are extracted and every one has a template.

| Template | Replaces |
|---|---|
| `front-page.php` | Home |
| `template-about.php` | About Hide & Soul |
| `template-custom-orders.php` | Custom Orders |
| `template-locations.php` | Locations |
| `template-repairs.php` | Repairs & Patches |
| `template-care.php` | Cleaning & Care |
| `template-testimonials.php` | Testimonials |
| `template-videos.php` | Videos *(was the Wix Video app)* |
| `template-bh-guide.php` | Black Hills Guide |
| `template-contact.php` | Contact |
| `template-referrals.php` | Customer Referral Program |
| WooCommerce | Shop, Mens, Womens, Accessories |

Assign these under **Page Attributes ➜ Template** when you create each page.

## ⏳ Two things to do before Wix is cancelled

Both are unrecoverable afterwards:

1. **Copy the testimonials.** At least eight exist on `/testamonials`; three are
   saved. They live on Wix's servers, not in the page — see
   [CONTENT-ISSUES §11](docs/CONTENT-ISSUES.md).
2. **Re-download the repairs before/after photos.** The captions survived, the
   images didn't — Wix galleries are JavaScript.

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

## Moving the shop — already pulled

The full catalogue came straight off the Wix Stores API and is committed in
`data/wix-products-*.json`: **33 products, 212 images**, real descriptions,
prices, options and categories. Nothing to export by hand.

```bash
python3 tools/build_woo_import.py --draft
```

Writes `build/woo-products.csv` → import at **WooCommerce ➜ Products ➜ Import**.

| | |
|---|---|
| Products | 27 variable, 6 simple |
| Featured | 13 |
| Categories | Accessories, Mens, Men's Vests, Womens |
| Images | 212, all at original resolution |

After importing, open each variable product ➜ **Variations ➜ Generate
variations** and set any per-variation prices (some sizes cost more).

## The one setting worth knowing about

**Appearance ➜ Customize ➜ Hide and Soul ➜ Shop Behaviour ➜ Catalog mode** —
**on by default.**

Products, photos and prices all show, but there's no cart or checkout: every
product page gets a "call the shop" panel instead. That matches what the Wix
shop page told customers — *"it is hard for our small business to maintain an
accurate inventory on our website. Please contact us for our current styles,
colors and sizes."*

Untick it for a full WooCommerce store with checkout. Nothing needs rebuilding.

## Local development

There's no build step. Edit the PHP and CSS directly.

```bash
# syntax-check every template before deploying
find theme -name '*.php' -exec php -l {} \;
```

## What still needs your input

Full breakdown of verified vs. unverified at the bottom of
[docs/MIGRATION-PLAN.md](docs/MIGRATION-PLAN.md). The short list:

- **Cave Creek street number** — the winter shop is set to Frontier Town on
  N. Cave Creek Rd, and the map searches for it by name. Add the street number
  in `inc/business-info.php` if you want an exact pin.
- **Shop hours** — taken from directory listings, not your site. These feed the
  structured data Google shows, so they're worth a minute.
- Whether `/bh-guide` and `/crp` should keep those slugs
- Placeholder body copy in `front-page.php`
