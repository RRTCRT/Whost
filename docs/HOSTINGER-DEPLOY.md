# Deploying to Hostinger

Staging: `https://www.mediumseagreen-gerbil-453930.hostingersite.com/`

Three ways to get the theme onto the server. Pick one — **A** is the fastest if
you're doing this once, **C** is the one to use if you'll be editing often.

---

## A. Zip upload through wp-admin (easiest)

```bash
cd theme
zip -r hide-and-soul.zip hide-and-soul \
  -x '*.DS_Store' -x '__MACOSX/*'
```

Then: **wp-admin ➜ Appearance ➜ Themes ➜ Add New ➜ Upload Theme ➜ Install ➜ Activate.**

> The zip must contain the folder `hide-and-soul/` at its root, with
> `style.css` directly inside it. If WordPress says "the theme is missing the
> stylesheet", you zipped the files instead of the folder.

Repeat for the redirects plugin — but it goes in a different place, see below.

## B. hPanel File Manager

1. hPanel ➜ **Files ➜ File Manager**
2. Navigate to `public_html/wp-content/themes/`
3. Upload `hide-and-soul.zip`, right-click ➜ **Extract**
4. Navigate to `public_html/wp-content/` and create a folder called
   **`mu-plugins`** if it doesn't exist
5. Upload `mu-plugins/hide-and-soul-redirects.php` into it

`mu-plugins` = *must-use plugins*. Files there load automatically and can't be
deactivated — which is exactly what you want for redirects, because a
deactivated redirect plugin is a site full of 404s.

## C. SSH + git (best for ongoing work)

Available on Hostinger Business plans and above. hPanel ➜ **Advanced ➜ SSH Access**.

```bash
ssh -p 65002 uXXXXXXXX@YOUR.SERVER.IP

cd ~/domains/hideandsoul.com/public_html/wp-content

# First time
git clone https://github.com/RRTCRT/Whost.git ~/whost-src
ln -s ~/whost-src/theme/hide-and-soul themes/hide-and-soul
mkdir -p mu-plugins
ln -s ~/whost-src/mu-plugins/hide-and-soul-redirects.php mu-plugins/

# Every update after that
cd ~/whost-src && git pull
```

Symlinks mean `git pull` deploys. If your Hostinger plan disallows symlinks,
clone directly into `themes/` instead:

```bash
cd ~/domains/hideandsoul.com/public_html/wp-content/themes
git clone --depth 1 https://github.com/RRTCRT/Whost.git tmp-whost
mv tmp-whost/theme/hide-and-soul ./hide-and-soul
rm -rf tmp-whost
```

---

## Post-install checklist

- [ ] **Appearance ➜ Customize ➜ Hide and Soul ➜ Business Info** — verify phone,
      address, hours. These drive the footer *and* the structured data Google reads.
- [ ] **Appearance ➜ Customize ➜ Homepage Hero** — set a background image.
- [ ] **Settings ➜ Permalinks** — set to **Post name**, then save once more after
      installing WooCommerce (this flushes the `/product-page/` rewrite rules).
- [ ] **Settings ➜ Reading** — static homepage.
- [ ] **Appearance ➜ Menus** — create Primary and Footer menus.
- [ ] Upload a logo at **Customize ➜ Site Identity** (or the theme renders the
      site name as a wordmark, which is a fine fallback).

## Hostinger-specific gotchas

**LiteSpeed cache.** Hostinger ships LiteSpeed. After any theme change, purge:
hPanel ➜ **Performance ➜ Cache Manager ➜ Purge All**. If CSS edits appear not
to work, this is why, nine times out of ten.

**PHP version.** hPanel ➜ Advanced ➜ PHP Configuration ➜ **8.2** or newer. The
theme declares `Requires PHP: 8.0` and uses typed properties.

**Object cache.** If you enable Hostinger's Redis object cache, flush it after
changing Customizer settings — theme mods are cached.

**Email.** Hostinger's `mail()` is unreliable for transactional mail. Install
**WP Mail SMTP** and point it at a real mailbox before you turn on checkout,
or order confirmations will silently vanish.

**File permissions.** Directories `755`, files `644`. If the File Manager
extraction leaves things at `777`, WordPress may refuse to load the theme.

---

## Rolling back

Hostinger keeps automatic backups: hPanel ➜ **Files ➜ Backups ➜ Restore**.
Take a manual one before the DNS cutover regardless — the automatic schedule
may not have run recently enough to matter.
