<?php

/*
Plugin Name: WP Wiki Tooltip
Plugin URI: https://n1da.net/specials/wp-wiki-tooltip/
Description: Adds explaining tooltips querying their content from a <a href="https://www.mediawiki.org" target="_blank" rel="noopener noreferrer">MediaWiki</a> installation, e.g. <a href="https://www.wikipedia.org" target="_blank" rel="noopener noreferrer">Wikipedia.org</a>.
Version: 2.1.1
Author: Nico Danneberg
Author URI: https://n1da.net
License: GPLv2 or later
Text Domain: wp-wiki-tooltip
*/

include_once( 'config.php' );
include_once( 'class.wp-wiki-tooltip.php' );
include_once( 'class.wp-wiki-tooltip-admin.php' );
include_once( 'class.wp-wiki-tooltip-mce.php' );

function load_wiki_translation() {
    load_plugin_textdomain('wp-wiki-tooltip', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action( 'plugins_loaded', 'load_wiki_translation' );

function save_wiki_standard_options() {
    WP_Wiki_Tooltip_Base::save_standard_options();
}
register_activation_hook( __FILE__, 'save_wiki_standard_options' );

function delete_all_wiki_options() {
    WP_Wiki_Tooltip_Base::delete_all_options();
}
register_uninstall_hook( __FILE__, 'delete_all_wiki_options' );

if( is_admin() ) {
	/*** backend usage ***/
	new WP_Wiki_Tooltip_Admin( plugin_basename( __FILE__ ) );
	new WP_Wiki_Tooltip_MCE();
} else {
    /*** frontend usage ***/
	new WP_Wiki_Tooltip();
}
