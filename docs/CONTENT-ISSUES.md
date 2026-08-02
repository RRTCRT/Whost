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

## 3. Prices: the store was stale, confirmed three ways — ✅ FIXED

The same garment is priced on the custom-orders page, on its shop page, and on
the WooCommerce product. Where the two pages agree against the store, the store
is the outlier.

| Garment | Custom orders | Shop page | Store *(was)* | Corrected to |
|---|--:|--:|--:|--:|
| Maverick Shirt | $899 | $899 *(men's)* | ~~$999~~ | **$899** |
| Men's Jacket | $1,299 | $1,299 *(men's)* | ~~$1,195~~ | **$1,299** |
| Ladies Jacket | $1,199 | $1,199 *(women's)* | ~~$1,099~~ | **$1,199** |
| Boone Shirt | $999 | from $899 | $999 | unchanged ✅ |
| Men's vests | $525 / $575 / $649 | same | same | unchanged ✅ |
| Chaps / half chaps | $895 / $399 | same | same | unchanged ✅ |
| Ladies vests | $399 | $399 | $399 | unchanged ✅ |

All three resolved the same way — the shop product was the stale one — and all
three are now corrected in `data/wix-products-*.json`, so the WooCommerce import
carries the right price. The store was undercharging by $100–104 on each.

> **This changed the migration data, not the live Wix store.** The Wix shop
> still shows the old prices until it's retired or edited there.

### Still open: Adjustable Half Chaps quotes three prices

Listed at **$399**, while its own description says *"Our basic pair starts at
$295"* and *"an upgraded CUSTOM pair that starts at $550"*. Left alone because
it isn't clear which is current — the $295 may predate a price rise, or describe
a plainer version. Fix the description text in wp-admin once decided.

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

## 6. Hours — three different answers, now ✅ RESOLVED

| Source | Tue | Wed–Sat | Sun | Mon |
|---|---|---|---|---|
| **Owner — correct** | 9–5 | 9–5 | 10–4 | by prior arrangement |
| Homepage | *not listed* | ~~10–5~~ | 10–4 | closed |
| Locations page | ~~9–6~~ | ~~9–6~~ | 10–4 | *not listed* |

The owner has confirmed the first row. The other two are stale — the site has
had several editors over the years and neither page was kept current. **Do not
reinstate hours from the old pages during content migration.**

The theme already uses the correct set, and Monday renders as "By appointment"
while staying out of the structured data.

## 7. Wix Stores is DISABLED

Attempting to write a price to the live store returns:

```
401 — TPA 1380b703-ce81-ff05-f115-39571d94dfcd is in invalid state DISABLED
```

That app ID is Wix Stores. The catalogue data is still readable — which is how
all 33 products were pulled — but the app itself is switched off, so nothing can
be written to it and the storefront may not be functioning for customers.

This also explains why Wix Stores never appeared in the site's installed-app
list. It has no bearing on the migration: the product data is already extracted
and committed.

## 8. Smaller things on the Locations page

- Email is typed as `roadkillleather@gmail. com` — **note the space** — in the
  Cave Creek block. A visitor copying it gets a bad address.
- Cave Creek gives no street address at all, just "At Frontier Town". The
  theme now carries the real one: 6245 E Cave Creek Rd, Cave Creek, AZ 85331.
- Cave Creek lists only the mobile. The 605 number is now a Google Voice line
  that rings Jennifer's mobile, so it reaches her in Arizona too — both numbers
  are shown for both shops.

## 9. Who to trust when sources disagree

Several people have edited the Wix site over the years and it drifted. The
owner has taken it over for this migration. Ranking, highest first:

1. **What the owner says** — overrides everything below.
2. **The Wix Stores catalogue** *(read via API)* — for product options, images
   and stock, which no page duplicates.
3. **Shop and custom-order pages** — for prices, where two pages agreeing beat
   a stale product record.
4. **The homepage and locations page** — the most-edited, least-maintained; both
   carried wrong hours.

Target for going live: **September**.

---

## Where these are handled in code

| Issue | File |
|---|---|
| Prices | `theme/hide-and-soul/inc/business-info.php` → `hs_base_prices()` |
| Hours | same file → `hs_hours()` |
| Product prices | `data/wix-products-*.json`, then rebuild the CSV |
| Page copy | rewrite in wp-admin after import — do not carry §1 across |
