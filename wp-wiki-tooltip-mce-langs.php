<?php

if( ! defined( 'ABSPATH' ) )
    exit;

if( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function wp_wiki_tooltip_mce_translation() {
    $strings = array(
        'title' => __( 'WP Wiki Tooltip', 'wp-wiki-tooltip' ),
        'link_label' => __( 'Link text', 'wp-wiki-tooltip' ),
        'link_tooltip' => __( 'Set the text that will be the link to the Wiki page.', 'wp-wiki-tooltip' ),
        'title_label' => __( 'Wiki page title', 'wp-wiki-tooltip' ),
        'title_tooltip' => __( 'Set the title of the requested Wiki page.', 'wp-wiki-tooltip' ),
        'section_label' => __( 'Section title', 'wp-wiki-tooltip' ),
        'section_tooltip' => __( 'Set the title (anchor) of the requested section in Wiki page.', 'wp-wiki-tooltip' ),
        'base_standard' => __( 'Standard base', 'wp-wiki-tooltip' ),
        'base_label' => __( 'Wiki base', 'wp-wiki-tooltip' ),
        'base_tooltip' => __( 'Select one of the defined Wiki bases. Visit the settings page to create a new one.', 'wp-wiki-tooltip' ),
        'thumb_default' => __( 'use plugin default value', 'wp-wiki-tooltip' ),
        'thumb_label' => __( 'Show thumbnail', 'wp-wiki-tooltip' ),
        'thumb_tooltip' => __( 'Show a thumbnail in the tooltip?', 'wp-wiki-tooltip' ),
        'thumb_yes' => __( 'yes', 'wp-wiki-tooltip' ),
        'thumb_no' => __( 'no', 'wp-wiki-tooltip' )
    );

    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.wp_wiki_tooltip", ' . json_encode( $strings ) . ");\n";

    return $translated;
}

$strings = wp_wiki_tooltip_mce_translation();
