/**
 * Created by nida78 on 26.07.2015.
 */

var $wwtj = jQuery.noConflict();

function isTooSmall() {
    return ( $wwtj( window ).width() < wp_wiki_tooltip.min_screen_width ) ? true : false;
}

function isClickEnabled( trig, trighovact ) {
    if( isTooSmall() ) {
        return true;
    }

    if( trig == 'hover' && trighovact == 'open' ) {
        return true;
    }

    return false;
}

function add_wiki_box( id, wid, title, section, wurl, purl, thumb ) {
    $wwtj( '#wiki-container' ).append( '<div id="wiki-tooltip-box-' + id + '" class="wiki-tooltip-box" wiki-id="' + wid + '" title="' + title + '"></div>' );

    var open_options, close_options;

    if( wp_wiki_tooltip.trigger == 'hover' ) {
        open_options = { mouseenter: true, touchstart: true };
        close_options = { mouseleave: true, originClick: true, tap: true };
    }

    if( wp_wiki_tooltip.trigger == 'click' ) {
        open_options = { click: true, tap: true };
        close_options = { click: true, tap: true };
    }

    $wwtj( '#wiki-tooltip-' + id ).tooltipster({

        animation: wp_wiki_tooltip.animation,

        content: create_tooltip_message( 'init', title, section ),

        contentAsHTML: true,

        interactive: true,

        maxWidth: 500,

        repositionOnScroll: true,

        theme: wp_wiki_tooltip.tooltip_theme,

        trigger: 'custom',

        triggerOpen: open_options,

        triggerClose: close_options,

        trackTooltip: true,

        updateAnimation: null,

        functionBefore: function( instance, helper ) {

            if( isTooSmall() ) {
                return false;
            }

            var $origin = $wwtj( helper.origin );

            if( $origin.data( 'loaded' ) !== true ) {

                var request_data = {
                    'action': 'get_wiki_page',
                    'wid': wid,
                    'section': section,
                    'serrhdl': wp_wiki_tooltip.section_error_handling,
                    'wurl': wurl,
                    'purl': purl,
                    'tenable': ( thumb == 'default' ) ? wp_wiki_tooltip.thumb_enable : thumb,
                    'twidth': wp_wiki_tooltip.thumb_width,
                    'errtit': ( wp_wiki_tooltip.error_handling == 'show-own' ) ? wp_wiki_tooltip.own_error_title : wp_wiki_tooltip.default_error_title,
                    'errmsg': ( wp_wiki_tooltip.error_handling == 'show-own' ) ? wp_wiki_tooltip.own_error_message : wp_wiki_tooltip.default_error_message
                };

                $wwtj.post( wp_wiki_tooltip.wp_ajax_url, request_data, function( response_data ) {
                    data = $wwtj.parseJSON( response_data );
                    if( data[ 'code' ] == -1 ) {
                        instance.content( create_tooltip_message( 'err', data[ 'title' ], data[ 'section' ], data[ 'content' ] ) );
                    } else {
                        instance.content( create_tooltip_message( 'ok', data[ 'title' ], data[ 'section' ], data[ 'content' ], data[ 'url' ], data[ 'thumb' ], data[ 'thumb-width' ], data[ 'thumb-height' ] ) );
                    }
                });

                $origin.data( 'loaded', true );
            }
        }
    });
}

function create_tooltip_message( type, title, section, message, url, thumb, w, h ) {
    var tooltip_html = '<div class="wiki-tooltip-balloon"><div class="head">' + title;
    tooltip_html += ( section != '' ) ? ( ' &raquo; ' + section ) : '';
    tooltip_html += '</div><div class="body">';

    if( type == 'init' ) {
        tooltip_html += '<img src="' + wp_wiki_tooltip.wiki_plugin_url + '/static/images/loadingAnimationBar.gif" />';
    } else {
        if( ( type != 'err' ) && ( thumb != -1 ) ) {
            tooltip_html += '<img src="' + thumb + '" align="' + wp_wiki_tooltip.thumb_align + '" class="thumb" width="' + w + '" height="' + h + '" />';
        }
        tooltip_html += message;
    }

    if( type == 'ok' ) {
        var relno = ( wp_wiki_tooltip.a_target == '_blank' ) ? ' rel="noopener noreferrer"' : '';
        tooltip_html += '</div><div class="foot"><a href="' + url + ( ( section != '' ) ? ( '#' + section ) : '' ) + '" target="' + wp_wiki_tooltip.a_target + '"' + relno + '>' + wp_wiki_tooltip.footer_text + '</a></div></div>';
    } else {
        tooltip_html += '</div><div class="foot"></div></div>';
    }

    return $wwtj( tooltip_html );
}