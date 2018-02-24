=== WP Wiki Tooltip ===
Contributors: nida78
Tags: wiki, wikipedia, mediawiki, tooltip, tooltipster, shortcode
Requires at least: 3.0
Tested up to: 4.9.4
Stable tag: 1.8.1
Donate link: https://n1da.net/specials/wp-wiki-tooltip/spenden/
License: GPLv2 or later

Adds explaining tooltips querying their content from a MediaWiki installation, e.g. Wikipedia.org.

== Description ==

Adds explaining tooltips querying their content from a [MediaWiki](https://www.mediawiki.org "see MediaWiki docs") installation, e.g. [Wikipedia.org](https://www.wikipedia.org "see the well-known Wikipedia"). Therefore shortcodes can be used in Posts and Pages to mark keywords and link them to public Wiki pages. The well-known package of [Tooltipster](http://iamceege.github.io/tooltipster/ "Tooltipster rocks :)") is used to create the nice and themable tooltips.

Main features of the current version are:

* Setup at least one wanted Wiki base and several other options at a backend page
* Integrate the Wiki tooltip using shortcodes in Posts and Pages
* shortcodes can be created by a TinyMCE plugin

== Frequently Asked Questions ==

= Can I use any Wiki installation as base for my tooltips? =

Sure, as long as the used installation provides an API structured like the [API of MediaWiki](https://www.mediawiki.org/wiki/API:Main_page "see API of MediaWiki") it will work perfectly! You can use one of the public Wikipedias or setup your own Wiki URL.

= Can I use several Wikis at the same time within my WordPress? =

Since version 1.4.0 the plugin provides the opportunity to manage multiple Wiki URLs! The wanted Wiki can be chosen via an attribute in the shortcode.

= Am I able to style the links to Wiki pages in another way than all other links in the blog? =

Yes, you can define extra CSS style properties that are used at all links to Wiki pages!

= Can I disable tooltips for mobile access? =

Since version 1.7.0 you can define a minimum screen width that is necessary to show the tooltips!

== Installation ==

1. Upload the Wiki tooltip plugin to your blog,
2. Activate it,
3. Create at least one Wiki base and review the global options on the settings page
4. Add some shortcodes to your Posts and Pages, and
5. See nice and helpful tooltips where ever you like

== Screenshots ==

1. Options and Settings page: manage several Wiki URLs
2. Options and Settings page: set some options how to show tooltips
3. Options and Settings page: set styling of tooltips
4. Options and Settings page: enable and style thumbnails
5. Integrate the plugin by shortcodes in Posts and Pages
6. Use the [TinyMCE](https://codex.wordpress.org/TinyMCE) plugin to get help by a popup form
7. See nice and helpful tooltips

== Changelog ==
The last three major releases are listed here, only. Find complete log of all changes in the [extra changelog file](https://github.com/nida78/wp-wiki-tooltip/blob/master/CHANGELOG.md)!

= 1.8.0 - C6H9N3O2 | Histidine =
*Release Date - February 23rd, 2018*

* if tooltip trigger 'hover' is selected you can set explicitly how the link has to work
* special options for handling errors are available
* a new version of Tooltipster plugin was released that leads to some programmatic and design changes
* a preview for every tooltip designs is available at options page now

= 1.7.0 - C2H5NO2 | Glycine =
*Release Date - October 22nd, 2016*

* you can set if tooltips are triggered by click or hover
* a minimum screen width can defined that is necessary to enable tooltips

= 1.6.0 - C5H9NO4 | Glutamic Acid =
*Release Date - January 30th, 2016*

* the plugin comes with a [TinyMCE](https://codex.wordpress.org/TinyMCE) plugin that helps users creating the shortcodes
* some new graphical assets have been added to support high-DPI displays (aka ‘retina’) and Right-to-Left languages

== Upgrade Notice ==

= Upgrade to 1.4.0 =
The former Wiki URL is not transferred into this version. Review the settings page after update to insert the wanted Wiki URL again!

= Elder Upgrades =
Nothing special to consider.
