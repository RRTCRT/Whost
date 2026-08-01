# Content issues found on the live Wix site

Everything here is a problem that **already exists on hideandsoul.com**. The
migration didn't create any of it — extracting the pages side by side just made
it visible. None of it is a code change; each needs a decision or a rewrite.

---

## 1. The accessories page shows men's vest and jacket copy

This is the big one. `/shop-accessories` contains descriptions belonging to the
men's page, under accessory headings:

| Heading on the accessories page | Description actually shown | Price shown |
|---|---|---|
| Medium Purse | chaps ("All of our chaps are unisex…") | $895 |
| Sheridan Purse | deerskin jacket ("unlined, 4-pocket, zipper front…") | $1,299 |
| Small Purse | deerskin jacket | $1,299 |
| Large Purse | 1/2 chaps | $399 |
| Purse Strap | deerskin jacket | $1,299 |
| Harness Leather *(belt)* | men's deerskin vest | $525 |
| English Bridle *(belt)* | elk vest | $575 |
| Holsters | bison vest | $649 |
| Gusset Clip-On Bag | Maverick/Boone pullover shirts | $899 |

**This is verified, not a guess.** The phrase "Our basic deerskin jacket"
appears **four times** on the accessories page, and none of these blocks carry
`display:none` — they render. It reads like the page was duplicated from the
men's page and the text never replaced.

The customer-facing effect: someone reading about a **small purse** is told it's
an unlined 4-pocket jacket costing **$1,299**.

> One caveat on precision: Wix positions blocks visually, so the *pairing* of a
> given heading to a given paragraph above may not be exactly what a visitor
> sees. That the wrong copy is present and visible is certain; which heading
> each sits under is worth eyeballing on the live page.

## 2. Truncated and mistyped text

On the accessories page:

- **Knife Sheaths** — the description is a broken sentence: *"Rick makes many of
  the"* and then nothing.
- **Knife Sheaths** — *"**Star ing** at $40 and up"* (missing the "t").

Also present on both the men's and accessories pages: a stray **`487059`**
sitting in the body copy with no context.

## 3. Prices: the store is stale, confirmed three ways

The same garment is priced on the custom-orders page, on its shop page, and on
the WooCommerce product. Where the two pages agree against the store, the store
is the outlier.

| Garment | Custom orders | Shop page | Store | Verdict |
|---|--:|--:|--:|---|
| Maverick Shirt | $899 | $899 *(men's)* | **$999** | store stale → **$899** |
| Men's Jacket | $1,299 | $1,299 *(men's)* | **$1,195** | store stale → **$1,299** |
| Ladies Jacket | $1,199 | $1,199 *(women's)* | **$1,099** | store stale → **$1,199** |
| Boone Shirt | $999 | from $899 | $999 | ✅ $999 |
| Men's vests | $525 / $575 / $649 | same | same | ✅ |
| Chaps / half chaps | $895 / $399 | same | same | ✅ |
| Ladies vests | $399 | $399 | $399 | ✅ |

All three conflicts now resolve the same way: **the shop product is out of
date.** Fix them in WooCommerce after import, or in `data/wix-products-*.json`
before it.

### Possibly not a conflict

| | Page | Store |
|---|--:|--:|
| Moccasins | "Starting at $99" | $249 |

The accessories page describes three kinds — grounding, baby and slipper. The
$249 store product is "Moccasins - Slipper". Most likely different items rather
than a wrong price, but the page should say so.

## 4. Advertised but not sellable

These have prices on the site and **no product in the store**, so nobody can
order them:

**Garments**
- Bikini — $99 *(women's page)*
- Snap Front Shirt — $1,199
- Basic Shirt — $899
- Western Fringed Jacket — $1,199

**Bags**
- Large Clip-On Bag — $80
- Gusset Clip-On Bag / Western Gusset — $70
- Claudia Purse — $275
- Medium, Sheridan, Small and Large Purse, Purse Strap — prices unclear, see §1

**Hard leather & other**
- Gun Belt, Holsters — no price given
- Knife Sheaths — $40 and up
- Knives — $99 and up *(Damascus, hand-selected by Rick)*
- Earrings — $15 and up
- Necklaces — $15 and up
- Moccasin Slippers — $99 and up
- Hides — $40 and up *(craft-grade; also full hides and buffalo robes)*

That's roughly **20 items** advertised without a product behind them, against 33
that exist. Worth deciding which should become real products before launch and
which are "call us" items — the theme's catalog mode handles the latter fine.

## 5. Colour options that don't match

The men's page lists **Mahogany** for the Deerskin Vest. The store's colour
options for that product are Black, Chocolate, Tobacco, Saddle and Smoke —
no Mahogany.

## 6. Hours

The owner gives **Tue–Sat 9–5, Sun 10–4, Mon by arrangement**. The Wix homepage
advertises **Wed–Sat 10–5, Sun 10–4, closed Mon**. The theme uses the owner's
version.

---

## Where these are handled in code

| Issue | File |
|---|---|
| Prices | `theme/hide-and-soul/inc/business-info.php` → `hs_base_prices()` |
| Hours | same file → `hs_hours()` |
| Product prices | `data/wix-products-*.json`, then rebuild the CSV |
| Page copy | rewrite in wp-admin after import — do not carry §1 across |
