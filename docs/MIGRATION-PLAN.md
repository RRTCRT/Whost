# Hide and Soul — Wix ➜ Hostinger WordPress migration plan

Target: replace `https://www.hideandsoul.com/` (Wix) with a hand-coded WordPress
theme on Hostinger, without losing search rankings, product URLs, or the Google
Business Profile match.

Staging site: `https://www.mediumseagreen-gerbil-453930.hostingersite.com/`

---

## The one decision that shapes everything else

**Does the new site take payments?** — and the Wix shop page already answered it:

> "Each item is unique with it's own character and story. Due to this factor, it
> is hard for our small business to maintain an accurate inventory on our
> website. **Please contact us for our current styles, colors and sizes today!**"

So the site does not really sell online today; it shows the range and asks
people to call. The theme therefore **ships in catalog mode by default** — that
is the faithful migration. A live checkout on an inventory the shop says it
cannot keep accurate would sell garments that may not exist.

| | Full WooCommerce | Catalog *(default)* |
|---|---|---|
| Cart + checkout | Yes | No |
| Payments, tax, shipping setup | Required | None |
| Product pages, photos, prices | Yes | Yes |
| Order path | Online | "Call the shop" |
| Setup time | ~2 days | Already done |

One checkbox switches it: **Appearance ➜ Customize ➜ Hide and Soul ➜ Shop
Behaviour ➜ Catalog mode**. Launch as a catalog, turn checkout on whenever the
inventory question is solved. Nothing needs rebuilding.

### Reproducing the /shop landing page

The Wix `/shop` page is not a product grid — it's six garment families with a
blurb each. WooCommerce does this natively: **Customize ➜ WooCommerce ➜ Product
Catalog ➜ Shop page display ➜ Show categories**. Paste the blurbs from
`build/product-categories.md` into each category's description.

---

## Phase 1 — Get WordPress ready (staging)

1. **PHP 8.2+**, WordPress 6.4+. Hostinger sets this in hPanel ➜ Advanced ➜ PHP Configuration.
2. **Permalinks**: Settings ➜ Permalinks ➜ **Post name**. Non-negotiable — every
   URL below assumes it.
3. **Plugins** — keep the list short; each one is a speed and security cost:
   - **WooCommerce** (only if you're selling)
   - **Yoast SEO** or **Rank Math** — for titles, meta descriptions, sitemap
   - **WP Mail SMTP** — Hostinger's default PHP mail lands in spam; you need
     contact-form and order emails to actually arrive
   - A contact form: **Fluent Forms** or **WPForms Lite**
   - *Optional:* **Redirection** if you'd rather manage redirects in the admin
     than in the mu-plugin here
4. **Delete** the Hostinger starter theme/plugins you aren't using (Hello Dolly,
   any bundled builder, the sample "Hostinger AI" content).

## Phase 2 — Install the theme

See [`HOSTINGER-DEPLOY.md`](HOSTINGER-DEPLOY.md) for the exact upload steps.

Then, in wp-admin:

1. Appearance ➜ Themes ➜ activate **Hide and Soul**
2. Appearance ➜ Customize ➜ **Business Info** — check the phone, address and
   hours. These feed the footer, the header bar, *and* the LocalBusiness
   structured data Google reads. They are wrong nowhere or wrong everywhere.
3. Appearance ➜ Customize ➜ **Homepage Hero** — set the background photo and
   headline.
4. Appearance ➜ Menus — build **Primary** and **Footer** menus.
5. Settings ➜ Reading ➜ "Your homepage displays: A static page" ➜ Homepage =
   a page called *Home*. (The theme's `front-page.php` takes over automatically.)

## Phase 3 — Move the content

You've already saved the Wix pages as HTML. Feed them to the extractor:

```bash
python3 tools/wix_extract.py parse ./wix-html
python3 tools/wix_extract.py wxr --site https://www.mediumseagreen-gerbil-453930.hostingersite.com --draft
```

That writes:

- `content/*.md` — every page's copy, readable, for you to proofread
- `build/import.xml` — a WordPress import file (Tools ➜ Import ➜ WordPress)
- `build/media-urls.txt` — every image, rewritten to **full resolution**

Then pull the real photos down (Wix serves cropped thumbnails in the page
source; the tool strips the crop so you get the originals):

```bash
python3 tools/wix_extract.py media --download ./media
```

Upload `./media/*` via Media ➜ Add New, or drop it into `wp-content/uploads/`
and run a media-library rescan plugin.

> Import as `--draft` first. Read each page, fix the copy, then publish. Wix
> pages carry a lot of layout text that reads like nonsense out of context.

## Phase 4 — The shop

**Already done.** The full catalogue was pulled live from the Wix Stores API and
is committed to `data/wix-products-*.json`: **33 products, 212 images**, with the
real descriptions, prices, options and category assignments.

```bash
python3 tools/build_woo_import.py --draft
```

Writes `build/woo-products.csv` — import at **WooCommerce ➜ Products ➜ Import**.

- 27 variable products (size / colour / style), 6 simple
- 13 flagged as Featured
- Categories: Accessories, Mens, Men's Vests, Womens

After importing, open each variable product ➜ **Variations ➜ Generate variations**,
then set any per-variation prices (several products charge more for XL, or for a
"Tall" +2″ cut).

Everything is marked **Backorders: notify** rather than hidden when out of stock —
these are made-to-order goods, and an out-of-stock vest is still orderable.

> `tools/wix_products_to_woo.py` remains for the CSV-export route, but you don't
> need it: the API pull is more complete than the dashboard export.

**Product URLs**: the theme sets WooCommerce's permalink base to
`/product-page` on first admin load, which is exactly what Wix used. Every
indexed product URL — `/product-page/deerskin-chaps`, `/product-page/western-purse`
— keeps working with no redirect. This is the single highest-value SEO move in
the whole migration; don't change the base afterwards.

## Price and hours conflicts to settle before launch

These are contradictions **already live on the Wix site** — the migration just
made them visible by putting the numbers side by side. Each one needs a
decision, not a code change.

### Prices: custom-orders page vs. the shop

| Garment | Custom-orders page | Shop product | |
|---|--:|--:|---|
| Maverick Shirt | $899 | $999 | conflict |
| Ladies Jacket | $1,199 *(Basic Ladies Jacket)* | $1,099 *(Women's Deerskin Jacket)* | conflict |
| Men's Jacket | $1,299 *(Basic Men's Jacket)* | $1,195 *(Deerskin Jacket)* | conflict |
| Boone Shirt | $999 | $999 | ✅ |
| Men's vests (deer/elk/bison) | $525 / $575 / $649 | same | ✅ |
| Chaps, 1/2 chaps, ladies vests | $895 / $399 / $399 | same | ✅ |

The two jacket rows might be genuinely different garments — a "Basic Ladies
Jacket" need not be the "Women's Deerskin Jacket". If so, say so on the page.
If they're the same thing, pick a price.

### Adjustable Half Chaps contradicts itself

The product is **listed at $399**, but its own description says *"Our basic pair
starts at $295"* and *"an upgraded CUSTOM pair that starts at $550"*. Three
numbers for one product, on one page.

### Hours

The owner gives **Tue–Sat 9–5, Sun 10–4, Mon by arrangement**. The Wix homepage
advertises **Wed–Sat 10–5, Sun 10–4, closed Mon**. The theme uses the owner's
version. Whichever is stale is costing walk-in customers.

Update `hs_base_prices()` and `hs_hours()` in
`theme/hide-and-soul/inc/business-info.php` once these are settled.

## Phase 5 — URLs and redirects

Most pages keep their Wix slug, so no redirect is needed. The leftovers are
handled by `mu-plugins/hide-and-soul-redirects.php` — see
[`URL-MAP.csv`](URL-MAP.csv) for the full table.

Before launch, confirm each old URL resolves:

```bash
while IFS=, read -r old new _; do
  printf '%-28s ' "$old"
  curl -s -o /dev/null -w '%{http_code} -> %{redirect_url}\n' "https://www.hideandsoul.com$old"
done < <(tail -n +2 docs/URL-MAP.csv)
```

(Run that *after* the DNS cutover, against the live domain.)

## Phase 6 — SEO parity

- [ ] Every page has a title and meta description in Yoast/Rank Math. The
      extractor saved Wix's originals in `content/*.md` and `build/pages.json`.
- [ ] Submit the new sitemap (`/wp-sitemap.xml` or Yoast's) in Google Search Console.
- [ ] Use GSC's **Change of Address**? No — the domain isn't changing, only the
      platform. Instead, watch Coverage for 404 spikes in the first two weeks.
- [ ] Confirm the LocalBusiness JSON-LD renders: view source, search for
      `ClothingStore`. Validate at <https://search.google.com/test/rich-results>.
- [ ] Google Business Profile: the website URL doesn't change, but re-verify
      the phone and hours match the site exactly.

## Phase 7 — Cutover

1. Take a full backup of the Hostinger site (hPanel ➜ Backups).
2. Point DNS: in Wix, remove the domain connection; at your registrar, set
   Hostinger's nameservers or A record. **Lower the TTL to 300s a day before.**
3. In WordPress: Settings ➜ General ➜ change both URLs from the
   `hostingersite.com` staging domain to `https://www.hideandsoul.com`.
4. Run a search-replace for the old staging domain in the database — otherwise
   images stay pointed at the staging URL:
   ```bash
   wp search-replace 'mediumseagreen-gerbil-453930.hostingersite.com' 'www.hideandsoul.com' --all-tables --precise
   ```
   Hostinger gives you WP-CLI over SSH on Business plans and up; otherwise use
   the *Better Search Replace* plugin and **dry-run it first**.
5. Force HTTPS (hPanel ➜ SSL) and confirm the redirect chain is
   `http://hideandsoul.com` ➜ `https://www.hideandsoul.com` in **one** hop.
6. Keep the Wix subscription alive for ~30 days. It's cheap insurance.

## Phase 8 — After launch

- [ ] Test a real order end-to-end (if checkout is on), including the email receipt.
- [ ] PageSpeed Insights on the homepage and a product page — Wix scored poorly
      here, and beating it is an easy, visible win.
- [ ] Set up Hostinger's automatic daily backups.
- [ ] Add the site to Google Search Console *and* Bing Webmaster Tools.

---

## What came from where

**Confirmed via the Wix API** (authoritative — pulled from your live account):

- Business email `roadkillleather@gmail.com`, phone `(605) 578-9746`
- Address `21576 US HWY 385, Deadwood, SD 57732`, timezone America/Denver, USD
- Two shops, confirmed by the owner: **Deadwood, SD** (April–November) and
  **Frontier Town, N. Cave Creek Rd, Cave Creek, AZ** (December–March). Custer,
  SD is closed. The header and Locations page switch automatically by month —
  see `hs_current_season()`.
- All 33 products with descriptions, prices, options, categories and images
- Site: Premium plan, custom domain, Editor (not Studio), Velo enabled

**Still unverified** — the container's network policy blocks both
`hideandsoul.com` and the Hostinger staging URL, so no page was rendered or read
directly:

- **Cave Creek street number.** The winter shop is set up at Frontier Town on
  N. Cave Creek Rd, Cave Creek, AZ (December–March), confirmed by the owner. The
  map embed searches for "Frontier Town" rather than pinning an exact address —
  add the street number in `hs_locations()` if you want a precise pin.
  *(The Custer, SD shop is closed and has been removed.)*
- **Opening hours** come from third-party directory listings (Yelp, chamber of
  commerce), not from your site. Check them — they feed the structured data
  Google shows in search results.
- **`/bh-guide` and `/crp`** — mapped to `/black-hills-guide/` and `/referrals/`
  by guessing at intent. Change `docs/URL-MAP.csv` if that's wrong.
- **Page copy** — Wix Editor page content isn't exposed through the API (it
  lives in the editor document, not a content API). Use your saved HTML with
  `tools/wix_extract.py` for that, per Phase 3.
- Placeholder copy in `front-page.php` is marked `TODO` and written to be
  replaced.

**One content note:** product descriptions were cleaned of Wix's editor markup
(empty `<p>` tags, `&nbsp;`, inline `<span>` styling). Text, links and lists are
otherwise verbatim — including the Etsy cross-links, which were kept.
