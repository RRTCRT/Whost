# Deploying to Hostinger

Staging: `https://www.mediumseagreen-gerbil-453930.hostingersite.com/`

**You do not need SSH.** Everything below happens in wp-admin, in a browser.
Skip SSH entirely unless you later want it for something else.

---

## The short version

1. Download the two zips *(next section)*
2. **Appearance ➜ Themes ➜ Add New ➜ Upload Theme** ➜ `hide-and-soul.zip` ➜ Install ➜ Activate
3. **Plugins ➜ Add New ➜ Upload Plugin** ➜ `hide-and-soul-redirects.zip` ➜ Install ➜ Activate
4. **Settings ➜ Permalinks** ➜ *Post name* ➜ Save

Under two minutes. Repeat steps 2 and 3 whenever there's an update.

---

## Getting the zips

### Easiest — download from GitHub

Every push builds them automatically.

1. Go to the repo's **Actions** tab
2. Click the newest **"Build installable zips"** run
3. Scroll to **Artifacts** and download **hide-and-soul-wordpress**
4. Unzip it — inside are `hide-and-soul.zip` and `hide-and-soul-redirects.zip`

> GitHub wraps artifacts in an outer zip. Upload the **inner** zips to
> WordPress, not the wrapper.

### Or build them yourself

Needs Python, which macOS and Linux already have and Windows offers in the
Microsoft Store:

```bash
python3 tools/build_zips.py
```

Writes both files to `dist/`. It also checks the archive is shaped the way
WordPress expects before it finishes.

---

## "But a one-time upload won't work — I'll need to change things"

It isn't one-time. **WordPress lets you upload the same theme again.**

Since WordPress 5.5, uploading a theme or plugin that's already installed shows
a comparison of the current and uploaded versions, with a **"Replace current
with uploaded"** button. Click it and the theme updates in place. Your settings,
menus, pages and Customizer options are all untouched — they live in the
database, not the theme.

So the update loop is:

| | |
|---|---|
| I push a change | GitHub builds a fresh zip automatically |
| You download it | Actions tab ➜ newest run ➜ Artifacts |
| You upload it | Appearance ➜ Themes ➜ Add New ➜ Upload ➜ **Replace current with uploaded** |

About a minute, no terminal.

### For small CSS tweaks, skip the zip

**Appearance ➜ Customize ➜ Additional CSS** takes CSS straight into the
database. Good for a colour or a spacing nudge you want to try immediately.
Anything you want to keep should come back to the repo so it isn't lost on the
next theme upload.

---

## Option B — hPanel File Manager

Worth knowing, though you won't need it for normal updates.

1. hPanel ➜ **Files ➜ File Manager**
2. Go to `public_html/wp-content/themes/`
3. Upload `hide-and-soul.zip`, right-click ➜ **Extract**

The File Manager is the only way to reach `wp-content/mu-plugins/`, which is
where the redirects file ideally lives — must-use plugins load automatically and
**cannot be deactivated**, which is exactly what you want for redirects.

The plugin zip exists so you don't need that. The only difference is that a
normal plugin *can* be switched off, and if it is, every old Wix URL starts
returning 404. The plugin header says so at the top of the file.

If you ever do get File Manager access working comfortably, move
`hide-and-soul-redirects.php` into `wp-content/mu-plugins/` (create the folder
if it isn't there) and deactivate the plugin version. Same behaviour, one less
thing to break.

---

## Post-install checklist

- [ ] **Settings ➜ Permalinks ➜ Post name**, then Save. Do this again after
      installing WooCommerce — it flushes the `/product-page/` rules.
- [ ] **Appearance ➜ Customize ➜ Hide and Soul ➜ Business Info** — check phone,
      address and hours. These feed the footer *and* the data Google reads.
- [ ] **Customize ➜ Homepage Hero** — set a background photo.
- [ ] **Settings ➜ Reading** — static homepage.
- [ ] **Appearance ➜ Menus** — Primary, Footer and Legal menus.
- [ ] Create each page and set its **Page Attributes ➜ Template** — the README
      lists which template goes with which page.

---

## Hostinger gotchas

**LiteSpeed cache.** Hostinger ships LiteSpeed. After any theme upload, purge:
hPanel ➜ **Performance ➜ Cache Manager ➜ Purge All**. If a change appears not to
have worked, this is why nine times out of ten. Hard-refresh your browser too
(Ctrl+Shift+R, or Cmd+Shift+R on a Mac).

**PHP version.** hPanel ➜ Advanced ➜ PHP Configuration ➜ **8.2** or newer.

**Upload size limit.** The theme zip is about 60 KB, far below any limit, so
uploads should never fail on size. If one does, the file is corrupt — rebuild it.

**Email.** Hostinger's PHP mail is unreliable. Install **WP Mail SMTP** before
turning on any form, or enquiries will vanish silently.

---

## Rolling back

The last few zips stay in GitHub Actions for 90 days, and tagged releases keep
theirs permanently — so rolling back is just uploading an older zip and choosing
"Replace current with uploaded".

Take a Hostinger backup before the DNS cutover regardless: hPanel ➜ **Files ➜
Backups**.

---

## If you do want SSH later

Nothing above needs it, so treat it as optional. Two things that catch people
out: Hostinger's SSH port is **65002**, not 22, and SSH access is only available
on Business plans and above — on lower plans the menu appears but never
connects, which is a common way to lose an afternoon.
