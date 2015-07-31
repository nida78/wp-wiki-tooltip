/**
 * Created by nida78 on 26.07.2015.
 */

function add_wiki_box( id, wid, title ) {
    jQuery( '#wiki-container' ).append( '<div id="wiki-tooltip-box-' + id + '" class="wiki-tooltip-box" wiki-id="' + wid + '" title="' + title + '"></div>' );

    jQuery( '#wiki-tooltip-' + id ).tooltipster({

        maxWidth: 500,

        theme: wp_wiki_tooltip.tooltip_theme,

        content: create_tooltip_message( 'init', title ),

        functionBefore: function( origin, continueTooltip ) {

            continueTooltip();

            if( origin.data( 'ajax' ) !== 'cached' ) {
                jQuery.ajax({
                    type: 'GET',
                    url: wp_wiki_tooltip.wiki_plugin_url + '/wp-wiki-tooltip.php?action=ajax-get&wid=' + wid,
                    success: function( data ) {
                        data = jQuery.parseJSON( data );
                        if( data[ 'code' ] == -1 ) {
                            origin.tooltipster( 'content', create_tooltip_message( 'err', wp_wiki_tooltip.error_title, wp_wiki_tooltip.page_not_found_message ) ).data( 'ajax', 'cached' );
                        } else {
                            origin.tooltipster( 'content', create_tooltip_message( 'ok', data[ 'title' ], data[ 'content' ] ) ).data( 'ajax', 'cached' );
                        }
                    }
                });
            }
        }
    });
}

function create_tooltip_message( type, title, message ) {
    var tooltip_html = '<div class="wiki-tooltip-balloon"><div class="head"><h1>' + title + '</h1></div>';

    if( type == 'init' ) {
        tooltip_html += '<img src="' + wp_wiki_tooltip.wiki_plugin_url + '/static/images/loadingAnimationBar.gif" />';
    } else {
        tooltip_html += '<div class="content">' + message + '</div>';
    }

    if( type == 'ok' ) {
        tooltip_html += '<div class="footer">' + wp_wiki_tooltip.footer_text + '</div></div></span>';
    }

    return jQuery( tooltip_html );
}