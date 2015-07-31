<?php

/*
Plugin Name: WP Wiki Tooltip
Plugin URI: http://n1da.net/specials/wp-wiki-preview/
Description: Adds explaining tooltips querying their content from a <a href="https://www.mediawiki.org" target="_blank">MediaWiki</a> installation, e.g. <a href="https://www.wikipedia.org" target="_blank">Wikipedia.org</a>.
Version: 0.5
Author: nida78
Author URI: http://n1da.net
License: GPLv2 or later
Text Domain: wp-wiki-tooltip
*/

include_once('class.wp-wiki-tooltip.php');

if( array_key_exists( 'action', $_REQUEST ) && $_REQUEST[ 'action' ] == 'ajax-get' ) {
	WP_Wiki_Tooltip::ajax_get_wiki_page();
} else {
	if (!is_admin()) {
		add_action('wp_enqueue_scripts', array('WP_Wiki_Tooltip', 'init'));
		add_action('wp_footer', array('WP_Wiki_Tooltip', 'add_wiki_container'));
		add_shortcode('wiki', array('WP_Wiki_Tooltip', 'do_wiki_shortcode'));
	}
}