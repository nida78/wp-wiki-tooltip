<?php

/*
Plugin Name: WP Wiki Tooltip
Plugin URI: https://n1da.net/specials/wp-wiki-tooltip/
Description: Adds explaining tooltips querying their content from a <a href="https://www.mediawiki.org" target="_blank" rel="noopener noreferrer">MediaWiki</a> installation, e.g. <a href="https://www.wikipedia.org" target="_blank" rel="noopener noreferrer">Wikipedia.org</a>.
Version: 1.9.0
Author: Nico Danneberg
Author URI: https://n1da.net
License: GPLv2 or later
Text Domain: wp-wiki-tooltip
*/

include_once('config.php');
include_once('class.wp-wiki-tooltip.php');
include_once('class.wp-wiki-tooltip-admin.php');
include_once('class.wp-wiki-tooltip-mce.php');

load_plugin_textdomain( 'wp-wiki-tooltip', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

if( is_admin() ) {
	/*** backend usage ***/
	new WP_Wiki_Tooltip_Admin( plugin_basename( __FILE__ ) );
	new WP_Wiki_Tooltip_MCE();
} else {
	/*** frontend usage ***/
	new WP_Wiki_Tooltip();
}
