/**
 * Created by nida78 on July 26th, 2015
 * Last modified by nida78 on April 12th, 2020
 */

let
    $wwtj = jQuery.noConflict(),
    wp_wiki_tooltip = {
        wp_ajax_url:'',
        wiki_plugin_url:'',
        tooltip_theme:'',
        animation:'',
        footer_text:'',
        thumb_enable:'',
        thumb_width:'',
        thumb_align:'',
        trigger:'',
        trigger_hover_action:'',
        a_target:'',
        min_screen_width:'',
        error_handling:'',
        default_error_title:'',
        default_error_message:'',
        own_error_title:'',
        own_error_message:'',
        section_error_handling:''
    };

$wwtj( document ).ready(
    function() {
        /*** initialize all necessary parameters with values from PHP settings ***/
        for( let key of Object.keys( wp_wiki_tooltip ) )
            wp_wiki_tooltip[ key ] = $wwtj( '#wiki-container' ).attr( 'data-' + key );

        /*** setup all wanted tooltips in document ***/
        $wwtj( 'span[id^="wiki-tooltip-"]' ).each(
            function() {
                add_wiki_box(
                    $wwtj( this ).attr( 'data-wiki_num' ),
                    $wwtj( this ).attr( 'data-wiki_id' ),
                    $wwtj( this ).attr( 'data-wiki_title' ),
                    $wwtj( this ).attr( 'data-wiki_section' ),
                    $wwtj( this ).attr( 'data-wiki_base_url' ),
                    $wwtj( this ).attr( 'data-wiki_url' ),
                    $wwtj( this ).attr( 'data-wiki_thumbnail' ),
                    $wwtj( this ).attr( 'data-wiki_nonce' )
                );
            }
        );
    }
);

function isTooSmall() {
    return ( $wwtj( window ).width() < wp_wiki_tooltip.min_screen_width );
}

function isClickEnabled( trig, trighovact ) {
    if( isTooSmall() ) {
        return true;
    }

    return trig === 'hover' && trighovact === 'open';
}

function add_wiki_box( id, wid, title, section, wurl, purl, thumb, nonce ) {
    $wwtj( '#wiki-container' ).append( '<div id="wiki-tooltip-box-' + id + '" class="wiki-tooltip-box" data-wiki_id="' + wid + '" title="' + title + '" nonce="' + nonce + '"></div>' );

    let open_options, close_options;

    if( wp_wiki_tooltip.trigger === 'hover' ) {
        open_options = { mouseenter: true, touchstart: true };
        close_options = { mouseleave: true, originClick: true, tap: true };
    }

    if( wp_wiki_tooltip.trigger === 'click' ) {
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

            let $origin = $wwtj( helper.origin );

            if( $origin.data( 'loaded' ) !== true ) {

                const request_data = {
                    'action': 'get_wiki_page',
                    'wid': wid,
                    'section': section,
                    'serrhdl': wp_wiki_tooltip.section_error_handling,
                    'wurl': wurl,
                    'purl': purl,
                    'tenable': (thumb === 'default') ? wp_wiki_tooltip.thumb_enable : thumb,
                    'twidth': wp_wiki_tooltip.thumb_width,
                    'errtit': (wp_wiki_tooltip.error_handling === 'show-own') ? wp_wiki_tooltip.own_error_title : wp_wiki_tooltip.default_error_title,
                    'errmsg': (wp_wiki_tooltip.error_handling === 'show-own') ? wp_wiki_tooltip.own_error_message : wp_wiki_tooltip.default_error_message,
                    'nonce': nonce
                };

                $wwtj.post( wp_wiki_tooltip.wp_ajax_url, request_data, function( response_data ) {
                    let data = $wwtj.parseJSON( response_data );
                    write_tooltip_message( instance, data );
                });

                $origin.data( 'loaded', true );
            }
        }
    });
}

function write_tooltip_message( instance, data ) {
    if( data[ 'code' ] === -1 ) {
        instance.content( create_tooltip_message( 'err', data[ 'title' ], data[ 'section' ], data[ 'content' ] ) );
    } else {
        instance.content( create_tooltip_message( 'ok', data[ 'title' ], data[ 'section' ], data[ 'content' ], data[ 'url' ], data[ 'thumb' ], data[ 'thumb-width' ], data[ 'thumb-height' ] ) );
    }

    return true;
}

function create_tooltip_message( type, title, section, message, url, thumb, w, h ) {
    let tooltip_html = '<div class="wiki-tooltip-balloon"><div class="head">' + title;
    tooltip_html += ( section !== '' ) ? ( ' &raquo; ' + section ) : '';
    tooltip_html += '</div><div class="body">';

    if( type === 'init' ) {
        tooltip_html += '<p class="loadingAnimation"><img alt="loading animation" src="' + wp_wiki_tooltip.wiki_plugin_url + 'static/images/loadingAnimationBar.gif" />';
    } else {
        if( ( type !== 'err' ) && ( thumb !== '-1' ) ) {
            tooltip_html += '<p><img alt="" src="' + thumb + '" style="float:' + wp_wiki_tooltip.thumb_align + ';" class="thumb" width="' + w + '" height="' + h + '" />';
        }
        tooltip_html += message;
    }

    if( type === 'ok' ) {
        let relno = ( wp_wiki_tooltip.a_target === '_blank' ) ? ' rel="noopener noreferrer"' : '';
        tooltip_html += '</p></div><div class="foot"><a href="' + url + ( ( section !== '' ) ? ( '#' + section ) : '' ) + '" target="' + wp_wiki_tooltip.a_target + '"' + relno + '>' + wp_wiki_tooltip.footer_text + '</a></div></div>';
    } else {
        tooltip_html += '</p></div><div class="foot"></div></div>';
    }

    return $wwtj( tooltip_html );
}