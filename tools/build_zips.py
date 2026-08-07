#!/usr/bin/env python3
"""Build installable zips for Hostinger — no SSH, no File Manager needed.

Produces two files in dist/:

  hide-and-soul.zip            Appearance > Themes > Add New > Upload Theme
  hide-and-soul-redirects.zip  Plugins > Add New > Upload Plugin

Both can be uploaded again later to update: WordPress offers a
"Replace current with uploaded" button when the item already exists.

    python3 tools/build_zips.py

Stdlib only, so it runs anywhere Python does — Windows, Mac or Linux.
"""

from __future__ import annotations

import re
import shutil
import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DIST = ROOT / "dist"

THEME_SRC = ROOT / "theme" / "hide-and-soul"
REDIRECTS_SRC = ROOT / "mu-plugins" / "hide-and-soul-redirects.php"

# Never ship these.
EXCLUDE_NAMES = {".DS_Store", "Thumbs.db", ".gitkeep"}
EXCLUDE_SUFFIXES = {".map", ".orig", ".rej", ".bak"}
EXCLUDE_DIRS = {"__MACOSX", ".git", "node_modules", ".idea", ".vscode"}


def theme_version() -> str:
    """Read Version: from the theme's style.css header."""
    header = (THEME_SRC / "style.css").read_text(encoding="utf-8")[:2000]
    match = re.search(r"^\s*Version:\s*(.+)$", header, re.MULTILINE)
    return match.group(1).strip() if match else "0.0.0"


def should_skip(path: Path) -> bool:
    if path.name in EXCLUDE_NAMES or path.suffix in EXCLUDE_SUFFIXES:
        return True
    return any(part in EXCLUDE_DIRS for part in path.parts)


def build_theme() -> Path:
    """Zip the theme with hide-and-soul/ at the archive root.

    WordPress rejects an archive whose style.css is not inside a single top
    folder — "the theme is missing the stylesheet" almost always means the
    files were zipped instead of the folder.
    """
    target = DIST / "hide-and-soul.zip"
    count = 0

    with zipfile.ZipFile(target, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
        for path in sorted(THEME_SRC.rglob("*")):
            if not path.is_file() or should_skip(path):
                continue
            zf.write(path, Path("hide-and-soul") / path.relative_to(THEME_SRC))
            count += 1

    print(f"  theme     {target.name:<30} {count:>3} files  {target.stat().st_size / 1024:6.0f} KB")
    return target


def build_redirects() -> Path:
    """Wrap the redirects file as an ordinary installable plugin.

    It normally lives in mu-plugins, which cannot be reached from wp-admin.
    Packaged this way it installs like any other plugin — the only cost is
    that it appears in the Plugins list and can be deactivated, so it must
    not be. The header says so.
    """
    target = DIST / "hide-and-soul-redirects.zip"
    source = REDIRECTS_SRC.read_text(encoding="utf-8")

    warning = (
        " * IMPORTANT: do not deactivate this plugin. It carries the 301s from\n"
        " * the old Wix URLs; without it those addresses return 404 and the\n"
        " * site loses whatever search ranking they hold.\n"
        " *\n"
        " * Installed as a normal plugin so it can be uploaded through wp-admin.\n"
        " * If you ever get file access, move it to wp-content/mu-plugins/ where\n"
        " * it cannot be switched off by accident.\n"
        " *\n"
    )
    source = source.replace(" * @package HideAndSoul", warning + " * @package HideAndSoul", 1)

    with zipfile.ZipFile(target, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
        zf.writestr("hide-and-soul-redirects/hide-and-soul-redirects.php", source)

    print(f"  plugin    {target.name:<30} {1:>3} file   {target.stat().st_size / 1024:6.0f} KB")
    return target


def verify(theme_zip: Path) -> bool:
    """Check the archive is shaped the way WordPress expects."""
    with zipfile.ZipFile(theme_zip) as zf:
        names = zf.namelist()
        bad = zf.testzip()

    ok = True

    if bad:
        print(f"  ✗ corrupt entry: {bad}")
        ok = False

    if "hide-and-soul/style.css" not in names:
        print("  ✗ style.css is not at hide-and-soul/style.css — WordPress will reject this")
        ok = False

    roots = {n.split("/")[0] for n in names}
    if roots != {"hide-and-soul"}:
        print(f"  ✗ archive has more than one top-level folder: {sorted(roots)}")
        ok = False

    for required in ("hide-and-soul/functions.php", "hide-and-soul/index.php"):
        if required not in names:
            print(f"  ✗ missing {required}")
            ok = False

    templates = [n for n in names if "/page-templates/" in n]
    print(f"  ✓ structure valid · {len(templates)} page templates included")
    return ok


def main() -> int:
    if not THEME_SRC.is_dir():
        print(f"error: {THEME_SRC} not found — run this from the repo", file=sys.stderr)
        return 1

    if DIST.exists():
        shutil.rmtree(DIST)
    DIST.mkdir()

    print(f"Building v{theme_version()}\n")
    theme_zip = build_theme()
    build_redirects()
    print()

    if not verify(theme_zip):
        return 1

    print(
        "\nUpload both through wp-admin:\n"
        "  1. Appearance > Themes > Add New > Upload Theme   -> hide-and-soul.zip\n"
        "  2. Plugins > Add New > Upload Plugin              -> hide-and-soul-redirects.zip\n"
        "\nUpdating later: upload the same zip again and choose\n"
        '"Replace current with uploaded". No SSH, no File Manager.'
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
