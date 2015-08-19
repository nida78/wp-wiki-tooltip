# WP Wiki Tooltip
Contributors: nida78

Tags: wiki, wikipedia, mediawiki, tooltip, tooltipster, shortcode

Requires at least: 3.0

Tested up to: 4.3

Stable tag: 1.3.0

Donate link: http://n1da.net/specials/wp-wiki-tooltip/spenden/

License: GPLv2 or later

Adds explaining tooltips querying their content from a MediaWiki installation, e.g. Wikipedia.org.

# Description

Adds explaining tooltips querying their content from a [MediaWiki](https://www.mediawiki.org "see MediaWiki docs") installation, e.g. [Wikipedia.org](https://www.wikipedia.org "see the well-known Wikipedia"). Therefore shortcodes can be used in Posts and Pages to mark keywords and link them to public Wiki pages. The well-known package of [Tooltipster](http://iamceege.github.io/tooltipster/ "Tooltipster rocks :)") is used to create the nice and themable tooltips.

Main features of the current version are:

* Setup the wanted Wiki base and several other options at a backend page
* Integrate the Wiki tooltip using shortcodes in Posts and Pages

# Frequently Asked Questions

## Can I use any Wiki installation as base for my tooltips?

Sure, as long as the used installation provides an API structured like the [API of MediaWiki](https://www.mediawiki.org/wiki/API:Main_page "see API of MediaWiki") it will work perfectly. You can use one of the public Wikipedias or setup your own Wiki URL.

## Am I able to style the links to Wiki pages in another way than all other links in the blog?

Yes, you can define extra CSS style properties that are used at all links to Wiki pages!

# Installation

1. Upload the Wiki tooltip plugin to your blog,
2. Activate it,
3. Add some shortcodes to your Posts and Pages, and
4. See nice and helpful tooltips where ever you like

# Screenshots

1. Options and Settings page
2. Integrate the plugin by shortcodes in Posts and Pages
3. See nice and helpful tooltips

# Changelog

## 1.3.0
*Release Date - August 18th, 2015*

* Elements of tooltips (header, body, footer) are decoupled from the standard elements of WordPress (e.g. headings) now
* Three new fields in the backend can be used to set CSS options for every element of the tooltips

## 1.2.1
*Release Date - August 17th, 2015*

* Fixes a minor bug that occurred (seldom) when multiple jQuery plugins are used within the same WordPress installation

## 1.2.0
*Release Date - August 14th, 2015*

* Redesigned the requests to the WIKI API by using the [WordPress HTTP API](http://codex.wordpress.org/HTTP_API) and encapsulating them into an own class
* A base class for all class of this plugin is introduced

## 1.1.0
*Release Date - August 7th, 2015*

* Refactored the AJAX call to load tooltip content due to the rules of [WordPress AJAX API](https://codex.wordpress.org/AJAX_in_Plugins)
* Added error handling if settings reset fails

## 1.0.1
*Release Date - August 5th, 2015*

* Minor bug-fix due to compatibility problems with older PHP versions

## 1.0.0
*Release Date - August 3rd, 2015*

* Option page for WordPress backend added
* Graphical assets for WordPress plugin repository added
* Complete German translation added

## 0.5.0
*Release Date - July 31st, 2015*

* Initial release

# Upgrade Notice

Nothing special to consider.
