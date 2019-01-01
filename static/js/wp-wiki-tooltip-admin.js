/**
 * Created by nida78 on 24.10.2015.
 */

var $wwtj = jQuery.noConflict();

var ActiveWikiRow = new Array();

function add_wiki_url_row() {

    var NextNum = parseInt( $wwtj( '#wp-wiki-tooltip-url-count' ).val() ) + 1;
    var NextID = 'wiki-url-row-' + NextNum;

    var NewRow = $wwtj( '#wiki-url-row-template' ).clone();
    NewRow.attr( 'id', NextID );
    NewRow.html( NewRow.html().replace( /###NEWID###/g, NextNum ) );
    NewRow.insertBefore( $wwtj( '#wiki-url-row-template' ) );

    $wwtj( '#wp-wiki-tooltip-url-count' ).val( NextNum );

    return true;
}

function remove_wiki_url_row( RowId ) {

    var row = $wwtj( '#' + RowId );
    var radio = $wwtj( '#rdo-' + RowId );

    if( radio.prop( 'checked' ) ) {
        alert( wp_wiki_tooltip_admin.alert_remove );
        return false;
    } else {
        row.remove();
        return true;
    }
}

function test_wiki_url_row( RowId ) {

    ActiveWikiRow[ 'ID'] = $wwtj( '#' + RowId );
    ActiveWikiRow[ 'txtSite' ] = $wwtj( '#txt-site-' + RowId );
    ActiveWikiRow[ 'txtUrl' ] = $wwtj( '#txt-url-' + RowId );
    ActiveWikiRow[ 'btnTest' ] = $wwtj( '#btn-test-' + RowId );

    var request_data = {
        'action': 'test_wiki_url',
        'wurl': $wwtj( ActiveWikiRow[ 'txtUrl' ] ).val()
    };

    $wwtj( ActiveWikiRow[ 'btnTest' ] ).toggle();
    $wwtj( ActiveWikiRow[ 'btnTest' ] ).next().toggle();

    $wwtj.post( wp_wiki_tooltip_admin.wp_ajax_url, request_data, function( response_data ) {
        data = $wwtj.parseJSON( response_data );
        $wwtj( ActiveWikiRow[ 'btnTest' ]).next().toggle();
        $wwtj( ActiveWikiRow[ 'btnTest' ]).toggle();
        if( data[ 'code' ] == -1 ) {
            alert( wp_wiki_tooltip_admin.alert_test_failed );
            $wwtj( ActiveWikiRow[ 'ID' ]).addClass( 'state-fail' );
            $wwtj( ActiveWikiRow[ 'ID' ]).removeClass( 'state-ok' );
            $wwtj( ActiveWikiRow[ 'txtUrl' ]).focus();
        } else {
            $wwtj( ActiveWikiRow[ 'txtSite' ] ).val( data[ 'name' ] );
            $wwtj( ActiveWikiRow[ 'txtUrl' ] ).val( data[ 'url' ] );
            $wwtj( ActiveWikiRow[ 'ID' ]).addClass( 'state-ok' );
            $wwtj( ActiveWikiRow[ 'ID' ]).removeClass( 'state-fail' );
        }
    });
}

function disable_trigger_hover_action( swt ) {
    $wwtj( '#rdo-a-trigger-hover-action-none' ).prop( 'disabled', swt );
    $wwtj( '#rdo-a-trigger-hover-action-open' ).prop( 'disabled', swt );
}

function disable_page_error_handling_fields( swt1, swt2 ) {
    $wwtj( '#own-error-title' ).prop( 'disabled', swt1 );
    $wwtj( '#own-error-message' ).prop( 'disabled', swt1 );
    $wwtj( '#error-wiki-page-title' ).prop( 'disabled', swt2 );
}

function enable_tooltip_theme_demo( theme ) {
    $wwtj( '#tooltipster-theme-' + theme + '-preview' ).tooltipster( {
        trigger: 'hover',
        theme: 'tooltipster-' + theme,
    } );
}

function enable_tooltip_animation_demo( theme, animation ) {
    $wwtj( '#tooltipster-animation-' + animation + '-preview' ).tooltipster( {
        trigger: 'hover',
        theme: 'tooltipster-' + theme,
        animation: animation,
    } );
}