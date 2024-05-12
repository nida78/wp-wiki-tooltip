<?php

$wp_wiki_tooltip_default_options = [

    'base' => [

        'wiki-urls' => [
            'standard' => 1,
            'data' => [
                '1' => [ // standard URL of Wiki to get contents from
                    'id' => 'EN',
                    'url' => 'https://en.wikipedia.org/w/api.php',
                    'sitename' => 'Wikipedia'
                ]
            ]
        ],

        'cache' => [ // NOT USED: cache settings - default "1 week"

            'count' => 1, // how many

            'unit' => 'week' // what
        ],

        'a-target-range' => [ '_blank', '_self' ],

        'a-target' => '_blank', // where to open links to wiki pages

        'trigger-range' => [ 'click', 'hover' ],

        'trigger' => 'click', // what triggers the tooltip

        'trigger-hover-action-range' => [ 'none', 'open' ],

        'trigger-hover-action' => 'none', // how does the link work if trigger is "hover"

        'min-screen-width' => '0', // activate tooltips only if screen is greater than this number of pixel
    ],

    'error' => [

        'page-error-handling-range' => [ 'show-default', 'show-own', 'remove-link' ],

        'page-error-handling' => 'show-default', // how should errors of pages be handled

        'own-error-title' => '', // the self-defined title of the error message

        'own-error-message' => '', // the self-defined message of the error message

        'section-error-handling-range' => [ 'show-page', 'use-page-settings' ],

        'section-error-handling' => 'show-page', // how should errors of sections be handled
    ],

    'design' => [

        'theme-range' => [ 'default', 'light', 'borderless', 'noir', 'punk', 'shadow' ],

        'theme' => 'default', // use default theme of Tooltipster

        'animation-range' => [ 'fade', 'grow', 'swing', 'slide', 'fall' ],

        'animation' => 'fade', // use standard animation when a tooltip appears

        'tooltip-head' => 'font-size: 125%; font-weight: bold;', // make the head of the tooltip a little bigger

        'tooltip-body' => '', // no special body styles in tooltip

        'tooltip-foot' => 'font-style: italic; font-weight: bold;', // make the footer link somehow nicer

        'a-style' => 'font-style: italic;',  // line of css for the style attribute
		
		'custom-go-to-wiki-link' => '',
    ],

    'thumb' => [

        'thumb-enable-range' => [ 'off', 'on' ],

        'thumb-enable' => 'off', // enable thumbnails in tooltips

        'thumb-align-range' => [ 'left', 'right' ],

        'thumb-align' => 'right', // alignment of the thumbnails

        'thumb-width' => '200', // standard width of the thumbnails

        'thumb-style' => '', // stylesheets for thumbnail images
    ]
];
