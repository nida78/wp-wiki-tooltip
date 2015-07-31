/**
 * Created by nida78 on 26.07.2015.
 */

function add_wiki_box( id, wid, title ) {
    jQuery( '#wiki-container' ).append( '<div id="wiki-tooltip-box-' + id + '" class="wiki-tooltip-box" wiki-id="' + wid + '" title="' + title + '"></div>' );

    jQuery( '#wiki-tooltip-' + id ).tooltipster({

        maxWidth: 500,

        theme: wp_wiki_tooltip.tooltip_theme,

        content: create_tooltip_message( title, "" ),

        functionBefore: function( origin, continueTooltip ) {

            continueTooltip();

            if( origin.data( 'ajax' ) !== 'cached' ) {
                jQuery.ajax({
                    type: 'GET',
                    url: wp_wiki_tooltip.wiki_plugin_url + '/wp-wiki-tooltip.php?action=ajax-get&wid=' + wid,
                    success: function( data ) {
                        data = jQuery.parseJSON( data );
                        origin.tooltipster( 'content', create_tooltip_message( data[ 'title' ], data[ 'content' ] ) ).data( 'ajax', 'cached' );
                    }
                });
            }
        }
    });
}

function create_tooltip_message( title, message ) {
    if( message == '' ) {
        message = '<img src="' + wp_wiki_tooltip.wiki_plugin_url + '/static/images/loadingAnimationBar.gif" />';
        footer = '';
    } else {
        footer = wp_wiki_tooltip.footer_text;
    }
    return jQuery( '<div class="wiki-tooltip-balloon"><div class="head"><h1>' + title + '</h1></div><div class="content">' + message + '</div><div class="footer">' + footer + '</div></div></span>' );
}