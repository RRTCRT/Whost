#!/usr/bin/env python3
"""Turn saved Wix pages into WordPress-ready content.

You saved hideandsoul.com with the browser's "Save page as". This reads those
files and produces three things:

  1. content/<slug>.md   — the readable copy of each page, for review/editing
  2. build/pages.json    — the same data, structured
  3. build/import.xml    — a WordPress WXR file you import in one shot via
                           Tools > Import > WordPress

It also collects every Wix media URL it finds and rewrites each one to its
full-resolution original, so you can download real photos instead of the
thumbnail crops the page happened to render.

Stdlib only — no pip install needed.

Usage
-----
    python3 tools/wix_extract.py parse  ./wix-html
    python3 tools/wix_extract.py media  ./wix-html --download ./media
    python3 tools/wix_extract.py wxr    --site https://www.hideandsoul.com

Run `parse` first; `wxr` reads build/pages.json.
"""

from __future__ import annotations

import argparse
import html
import json
import os
import re
import sys
import time
import unicodedata
from dataclasses import dataclass, field, asdict
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urljoin, urlparse
from xml.sax.saxutils import escape as xml_escape

# --------------------------------------------------------------------------
# Configuration
# --------------------------------------------------------------------------

BUILD = Path("build")
CONTENT = Path("content")

# Tags whose contents are never page copy.
#
# <head> is deliberately absent: title, meta description and canonical all live
# there and we need them. The tags below cover everything inside it that isn't.
SKIP_TAGS = {"script", "style", "noscript", "svg", "template", "iframe"}

# Tags we keep as structured blocks.
BLOCK_TAGS = {"h1", "h2", "h3", "h4", "h5", "h6", "p", "li", "blockquote", "figcaption"}

# Chrome that repeats on every Wix page and adds nothing to the content.
BOILERPLATE = {
    "top of page",
    "bottom of page",
    "skip to main content",
    "log in",
    "cart",
    "0",
    "use tab to navigate through the menu items.",
    "this site was designed with the .com website builder. create your website today.",
    "start now",
}

# Wix serves media from these hosts.
WIX_MEDIA_HOSTS = ("static.wixstatic.com", "images-wixmp")

# A Wix media URL looks like:
#   https://static.wixstatic.com/media/<id>~mv2.jpg/v1/fill/w_600,h_400,.../<id>~mv2.jpg
# Everything from "/v1/" onwards is a render-time transform. Dropping it gives
# the original upload at full resolution.
WIX_TRANSFORM = re.compile(r"/v1/(?:fill|crop|fit)/[^)\s\"']*", re.IGNORECASE)
WIX_URL_IN_TEXT = re.compile(
    r"https://static\.wixstatic\.com/media/[A-Za-z0-9_~.\-/,%]+\.(?:jpg|jpeg|png|webp|gif|avif)",
    re.IGNORECASE,
)


# --------------------------------------------------------------------------
# Data model
# --------------------------------------------------------------------------


@dataclass
class Block:
    tag: str
    text: str


@dataclass
class Page:
    source_file: str
    slug: str = ""
    title: str = ""
    meta_description: str = ""
    canonical: str = ""
    blocks: list[Block] = field(default_factory=list)
    images: list[str] = field(default_factory=list)
    links: list[str] = field(default_factory=list)

    def heading(self) -> str:
        for block in self.blocks:
            if block.tag == "h1":
                return block.text
        return self.title

    def body_html(self) -> str:
        """Render blocks as clean WordPress block-editor markup."""
        out: list[str] = []
        bullets: list[str] = []

        def flush_list() -> None:
            if not bullets:
                return
            items = "".join(f"<li>{html.escape(b)}</li>" for b in bullets)
            out.append(f"<!-- wp:list -->\n<ul>{items}</ul>\n<!-- /wp:list -->")
            bullets.clear()

        skipped_h1 = False

        for block in self.blocks:
            if block.tag == "li":
                bullets.append(block.text)
                continue

            flush_list()
            text = html.escape(block.text)

            if block.tag == "h1":
                # The theme prints the page title in its banner already.
                if not skipped_h1:
                    skipped_h1 = True
                    continue
                out.append(
                    f'<!-- wp:heading {{"level":2}} -->\n<h2>{text}</h2>\n<!-- /wp:heading -->'
                )
            elif block.tag in {"h2", "h3", "h4", "h5", "h6"}:
                level = block.tag[1]
                out.append(
                    f'<!-- wp:heading {{"level":{level}}} -->\n'
                    f"<h{level}>{text}</h{level}>\n<!-- /wp:heading -->"
                )
            elif block.tag == "blockquote":
                out.append(
                    "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">"
                    f"<p>{text}</p></blockquote>\n<!-- /wp:quote -->"
                )
            else:
                out.append(f"<!-- wp:paragraph -->\n<p>{text}</p>\n<!-- /wp:paragraph -->")

        flush_list()
        return "\n\n".join(out)

    def markdown(self) -> str:
        lines = [f"# {self.heading()}", ""]

        if self.meta_description:
            lines += [f"> {self.meta_description}", ""]

        lines += [
            f"<!-- source: {self.source_file} -->",
            f"<!-- slug: {self.slug} -->",
            "",
        ]

        for block in self.blocks:
            if block.tag == "h1":
                continue
            if block.tag.startswith("h"):
                level = int(block.tag[1])
                lines += ["#" * min(level, 6) + f" {block.text}", ""]
            elif block.tag == "li":
                lines.append(f"- {block.text}")
            elif block.tag == "blockquote":
                if lines and lines[-1] != "":
                    lines.append("")
                lines += [f"> {block.text}", ""]
            else:
                lines += [block.text, ""]

        if self.images:
            lines += ["", "## Images referenced", ""]
            lines += [f"- {u}" for u in self.images]

        return "\n".join(lines).strip() + "\n"


# --------------------------------------------------------------------------
# HTML parsing
# --------------------------------------------------------------------------


class WixPageParser(HTMLParser):
    """Pull headings, copy, images and links out of a saved Wix page."""

    def __init__(self) -> None:
        super().__init__(convert_charrefs=True)
        self.title = ""
        self.meta_description = ""
        self.canonical = ""
        self.blocks: list[Block] = []
        self.images: list[str] = []
        self.links: list[str] = []

        self._skip_depth = 0
        self._in_title = False
        self._block_stack: list[str] = []
        self._buffer: list[str] = []

    # -- helpers ---------------------------------------------------------

    def _attr(self, attrs: list[tuple[str, str | None]], name: str) -> str:
        for key, value in attrs:
            if key.lower() == name:
                return value or ""
        return ""

    def _flush(self) -> None:
        if not self._block_stack:
            self._buffer.clear()
            return

        text = collapse(" ".join(self._buffer))
        self._buffer.clear()

        if not text or text.lower() in BOILERPLATE:
            return

        tag = self._block_stack[-1]

        # Wix nests identical text in wrappers; drop consecutive duplicates.
        if self.blocks and self.blocks[-1].text == text:
            return

        self.blocks.append(Block(tag=tag, text=text))

    # -- HTMLParser API --------------------------------------------------

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        tag = tag.lower()

        if self._skip_depth:
            if tag in SKIP_TAGS:
                self._skip_depth += 1
            return

        if tag in SKIP_TAGS:
            self._skip_depth = 1
            return

        if tag == "title":
            self._in_title = True
            return

        if tag == "meta":
            name = self._attr(attrs, "name").lower()
            prop = self._attr(attrs, "property").lower()
            if name == "description" and not self.meta_description:
                self.meta_description = collapse(self._attr(attrs, "content"))
            elif prop == "og:description" and not self.meta_description:
                self.meta_description = collapse(self._attr(attrs, "content"))
            return

        if tag == "link" and self._attr(attrs, "rel").lower() == "canonical":
            self.canonical = self._attr(attrs, "href")
            return

        if tag == "img":
            src = self._attr(attrs, "src") or self._attr(attrs, "data-src")
            if src:
                self.images.append(src)
            srcset = self._attr(attrs, "srcset")
            if srcset:
                for candidate in srcset.split(","):
                    url = candidate.strip().split(" ")[0]
                    if url:
                        self.images.append(url)
            return

        if tag == "a":
            href = self._attr(attrs, "href")
            if href:
                self.links.append(href)

        # Background images hide in inline styles.
        style = self._attr(attrs, "style")
        if style and "url(" in style:
            for match in re.findall(r"url\(([^)]+)\)", style):
                self.images.append(match.strip("'\" "))

        if tag == "br":
            self._buffer.append(" ")
            return

        if tag in BLOCK_TAGS:
            self._flush()
            self._block_stack.append(tag)

    def handle_endtag(self, tag: str) -> None:
        tag = tag.lower()

        if self._skip_depth:
            if tag in SKIP_TAGS:
                self._skip_depth -= 1
            return

        if tag == "title":
            self._in_title = False
            return

        if tag in BLOCK_TAGS and self._block_stack:
            self._flush()
            self._block_stack.pop()

    def handle_data(self, data: str) -> None:
        if self._skip_depth:
            return

        if self._in_title:
            self.title += data
            return

        if self._block_stack:
            self._buffer.append(data)


def collapse(text: str) -> str:
    """Normalise whitespace and unicode oddities."""
    text = unicodedata.normalize("NFKC", text or "")
    text = text.replace("​", "").replace("\xa0", " ")
    return re.sub(r"\s+", " ", text).strip()


def slugify(value: str) -> str:
    value = unicodedata.normalize("NFKD", value)
    value = value.encode("ascii", "ignore").decode()
    value = re.sub(r"[^\w\s-]", "", value).strip().lower()
    return re.sub(r"[-\s]+", "-", value) or "page"


def clean_title(raw: str) -> str:
    """Strip the site suffix Wix appends to every <title>."""
    title = collapse(raw)
    for sep in (" | ", " — ", " - "):
        if sep in title:
            head = title.split(sep)[0].strip()
            if head:
                title = head
                break
    return title


def normalise_media(url: str, base: str = "") -> str:
    """Rewrite a Wix media URL to the full-resolution original."""
    if url.startswith("//"):
        url = "https:" + url
    elif base and not url.startswith(("http://", "https://", "data:")):
        url = urljoin(base, url)

    if url.startswith("data:"):
        return ""

    host = urlparse(url).netloc
    if not any(h in host for h in WIX_MEDIA_HOSTS):
        return url

    return WIX_TRANSFORM.sub("", url)


# --------------------------------------------------------------------------
# Commands
# --------------------------------------------------------------------------


def cmd_parse(args: argparse.Namespace) -> int:
    root = Path(args.directory)

    if not root.exists():
        print(f"error: {root} does not exist", file=sys.stderr)
        return 1

    files = sorted(
        p
        for p in root.rglob("*")
        if p.suffix.lower() in {".html", ".htm"} and "_files" not in p.parts
    )

    if not files:
        print(f"error: no .html files under {root}", file=sys.stderr)
        return 1

    BUILD.mkdir(exist_ok=True)
    CONTENT.mkdir(exist_ok=True)

    pages: list[Page] = []

    for path in files:
        raw = path.read_text(encoding="utf-8", errors="replace")

        parser = WixPageParser()
        try:
            parser.feed(raw)
        except Exception as exc:  # a saved page can be malformed; keep going
            print(f"  ! {path.name}: {exc}", file=sys.stderr)

        title = clean_title(parser.title) or path.stem.replace("-", " ").title()

        # Prefer the canonical path for the slug so URLs match the old site.
        slug = ""
        if parser.canonical:
            slug = urlparse(parser.canonical).path.strip("/").split("/")[-1]
        if not slug:
            slug = slugify(path.stem)
        if slug in {"", "index", "home"}:
            slug = "home"

        images: list[str] = []
        seen: set[str] = set()

        for candidate in parser.images + WIX_URL_IN_TEXT.findall(raw):
            url = normalise_media(candidate, parser.canonical)
            if url and url not in seen:
                seen.add(url)
                images.append(url)

        page = Page(
            source_file=str(path),
            slug=slug,
            title=title,
            meta_description=parser.meta_description,
            canonical=parser.canonical,
            blocks=parser.blocks,
            images=images,
            links=sorted({l for l in parser.links if l.startswith("http")}),
        )

        pages.append(page)
        (CONTENT / f"{slug}.md").write_text(page.markdown(), encoding="utf-8")

        print(f"  {path.name:<44} -> {slug:<22} {len(page.blocks):>3} blocks, {len(images):>3} images")

    payload = [asdict(p) for p in pages]
    (BUILD / "pages.json").write_text(
        json.dumps(payload, indent=2, ensure_ascii=False), encoding="utf-8"
    )

    all_media = sorted({u for p in pages for u in p.images})
    (BUILD / "media-urls.txt").write_text("\n".join(all_media) + "\n", encoding="utf-8")

    print()
    print(f"{len(pages)} pages -> content/*.md and build/pages.json")
    print(f"{len(all_media)} unique media URLs -> build/media-urls.txt")
    return 0


def cmd_media(args: argparse.Namespace) -> int:
    """List, and optionally download, every full-resolution Wix image."""
    listing = BUILD / "media-urls.txt"

    if not listing.exists():
        print("error: run `parse` first", file=sys.stderr)
        return 1

    urls = [u for u in listing.read_text(encoding="utf-8").splitlines() if u.strip()]

    if not args.download:
        print("\n".join(urls))
        print(f"\n{len(urls)} URLs. Pass --download <dir> to fetch them.", file=sys.stderr)
        return 0

    # Imported lazily so the parse command never needs network modules.
    import urllib.error
    import urllib.request

    out = Path(args.download)
    out.mkdir(parents=True, exist_ok=True)

    ok = 0
    for i, url in enumerate(urls, 1):
        name = os.path.basename(urlparse(url).path) or f"image-{i}"
        target = out / name

        if target.exists() and target.stat().st_size > 0:
            ok += 1
            continue

        try:
            request = urllib.request.Request(
                url, headers={"User-Agent": "Mozilla/5.0 (compatible; site-migration)"}
            )
            with urllib.request.urlopen(request, timeout=30) as response:
                target.write_bytes(response.read())
            ok += 1
            print(f"  [{i}/{len(urls)}] {name}")
        except (urllib.error.URLError, OSError) as exc:
            print(f"  [{i}/{len(urls)}] FAILED {name}: {exc}", file=sys.stderr)

        time.sleep(0.2)  # be polite to the CDN

    print(f"\n{ok}/{len(urls)} downloaded to {out}")
    return 0


def cmd_wxr(args: argparse.Namespace) -> int:
    """Emit a WordPress import file containing every parsed page."""
    source = BUILD / "pages.json"

    if not source.exists():
        print("error: run `parse` first", file=sys.stderr)
        return 1

    pages = json.loads(source.read_text(encoding="utf-8"))
    site = args.site.rstrip("/")
    now = time.strftime("%Y-%m-%d %H:%M:%S")

    items: list[str] = []

    for i, raw in enumerate(pages, start=100):
        page = Page(
            source_file=raw["source_file"],
            slug=raw["slug"],
            title=raw["title"],
            meta_description=raw["meta_description"],
            canonical=raw["canonical"],
            blocks=[Block(**b) for b in raw["blocks"]],
            images=raw["images"],
            links=raw["links"],
        )

        # The homepage content lives in front-page.php, not in a page record.
        status = "draft" if args.draft else "publish"

        items.append(
            f"""	<item>
		<title>{xml_escape(page.title)}</title>
		<link>{xml_escape(site)}/{xml_escape(page.slug)}/</link>
		<pubDate>{time.strftime('%a, %d %b %Y %H:%M:%S +0000')}</pubDate>
		<dc:creator><![CDATA[admin]]></dc:creator>
		<guid isPermaLink="false">{xml_escape(site)}/?page_id={i}</guid>
		<description></description>
		<content:encoded><![CDATA[{page.body_html()}]]></content:encoded>
		<excerpt:encoded><![CDATA[{page.meta_description}]]></excerpt:encoded>
		<wp:post_id>{i}</wp:post_id>
		<wp:post_date><![CDATA[{now}]]></wp:post_date>
		<wp:post_date_gmt><![CDATA[{now}]]></wp:post_date_gmt>
		<wp:comment_status><![CDATA[closed]]></wp:comment_status>
		<wp:ping_status><![CDATA[closed]]></wp:ping_status>
		<wp:post_name><![CDATA[{page.slug}]]></wp:post_name>
		<wp:status><![CDATA[{status}]]></wp:status>
		<wp:post_parent>0</wp:post_parent>
		<wp:menu_order>0</wp:menu_order>
		<wp:post_type><![CDATA[page]]></wp:post_type>
		<wp:post_password><![CDATA[]]></wp:post_password>
		<wp:is_sticky>0</wp:is_sticky>
	</item>"""
        )

    xml = f"""<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/">
<channel>
	<title>Hide and Soul</title>
	<link>{xml_escape(site)}</link>
	<description>Imported from Wix</description>
	<pubDate>{time.strftime('%a, %d %b %Y %H:%M:%S +0000')}</pubDate>
	<language>en-US</language>
	<wp:wxr_version>1.2</wp:wxr_version>
	<wp:base_site_url>{xml_escape(site)}</wp:base_site_url>
	<wp:base_blog_url>{xml_escape(site)}</wp:base_blog_url>
	<wp:author>
		<wp:author_id>1</wp:author_id>
		<wp:author_login><![CDATA[admin]]></wp:author_login>
		<wp:author_email><![CDATA[admin@example.com]]></wp:author_email>
		<wp:author_display_name><![CDATA[admin]]></wp:author_display_name>
	</wp:author>
{chr(10).join(items)}
</channel>
</rss>
"""

    BUILD.mkdir(exist_ok=True)
    target = BUILD / "import.xml"
    target.write_text(xml, encoding="utf-8")

    print(f"{len(pages)} pages -> {target}")
    print("Import via wp-admin > Tools > Import > WordPress.")
    return 0


# --------------------------------------------------------------------------


def main() -> int:
    parser = argparse.ArgumentParser(
        description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter
    )
    sub = parser.add_subparsers(dest="command", required=True)

    p_parse = sub.add_parser("parse", help="read saved Wix HTML into content/ and build/")
    p_parse.add_argument("directory", help="folder holding the saved .html files")
    p_parse.set_defaults(func=cmd_parse)

    p_media = sub.add_parser("media", help="list or download full-resolution images")
    p_media.add_argument("directory", nargs="?", default=".", help="unused; kept for symmetry")
    p_media.add_argument("--download", metavar="DIR", help="download images into DIR")
    p_media.set_defaults(func=cmd_media)

    p_wxr = sub.add_parser("wxr", help="write a WordPress import file")
    p_wxr.add_argument("--site", default="https://www.hideandsoul.com", help="target site URL")
    p_wxr.add_argument("--draft", action="store_true", help="import pages as drafts")
    p_wxr.set_defaults(func=cmd_wxr)

    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    raise SystemExit(main())
