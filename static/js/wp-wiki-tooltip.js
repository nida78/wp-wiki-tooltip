/**
 * Created by nida78 on 26.07.2015.
 */

var $wwtj = jQuery.noConflict();

function isTooSmall() {
    return ( $wwtj( window ).width() < wp_wiki_tooltip.min_screen_width ) ? true : false;
}

function add_wiki_box( id, wid, title, wurl, purl, thumb ) {
    $wwtj( '#wiki-container' ).append( '<div id="wiki-tooltip-box-' + id + '" class="wiki-tooltip-box" wiki-id="' + wid + '" title="' + title + '"></div>' );

    $wwtj( '#wiki-tooltip-' + id ).tooltipster({

        maxWidth: 500,

        theme: wp_wiki_tooltip.tooltip_theme,

        trigger: wp_wiki_tooltip.trigger,

        contentAsHTML: true,

        interactive: true,

        content: create_tooltip_message( 'init', title ),

        functionBefore: function( origin, continueTooltip ) {

            if( isTooSmall() ) {
                return false;
            }

            continueTooltip();

            if( origin.data( 'ajax' ) !== 'cached' ) {

                var request_data = {
                    'action': 'get_wiki_page',
                    'wid': wid,
                    'wurl': wurl,
                    'purl': purl,
                    'tenable': ( thumb == 'default' ) ? wp_wiki_tooltip.thumb_enable : thumb,
                    'twidth': wp_wiki_tooltip.thumb_width
                };

                $wwtj.post( wp_wiki_tooltip.wp_ajax_url, request_data, function( response_data ) {
                    data = $wwtj.parseJSON( response_data );
                    if( data[ 'code' ] == -1 ) {
                        origin.tooltipster( 'content', create_tooltip_message( 'err', wp_wiki_tooltip.error_title, wp_wiki_tooltip.page_not_found_message ) ).data( 'ajax', 'cached' );
                    } else {
                        origin.tooltipster( 'content', create_tooltip_message( 'ok', data[ 'title' ], data[ 'content' ], data[ 'url' ], data[ 'thumb' ], data[ 'thumb-width' ], data[ 'thumb-height' ] ) ).data( 'ajax', 'cached' );
                    }
                });
            }
        }
    });
}

function create_tooltip_message( type, title, message, url, thumb, w, h ) {
    var tooltip_html = '<div class="wiki-tooltip-balloon"><div class="head">' + title + '</div><div class="body">';

    if( type == 'init' ) {
        tooltip_html += '<img src="' + wp_wiki_tooltip.wiki_plugin_url + '/static/images/loadingAnimationBar.gif" />';
    } else {
        if( thumb != -1 ) {
            tooltip_html += '<img src="' + thumb + '" align="' + wp_wiki_tooltip.thumb_align + '" class="thumb" width="' + w + '" height="' + h + '" />';
        }
        tooltip_html += message;
    }

    if( type == 'ok' ) {
        var relno = ( wp_wiki_tooltip.a_target == '_blank' ) ? ' rel="noopener noreferrer"' : '';
        tooltip_html += '</div><div class="foot"><a href="' + url + '" target="' + wp_wiki_tooltip.a_target + '"' + relno + '>' + wp_wiki_tooltip.footer_text + '</a></div></div>';
    }

    return $wwtj( tooltip_html );
}