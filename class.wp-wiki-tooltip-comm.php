<?php

include_once('class.wp-wiki-tooltip-base.php');

/**
 * Class WP_Wiki_Tooltip_Comm
 */
class WP_Wiki_Tooltip_Comm extends WP_Wiki_Tooltip_Base {

    const WIKI_API_PATH = '/w/api.php';

    private $info_query_args = array(
        'action' => 'query',
        'prop' => 'info',
        'inprop' => 'url',
        'redirects' => '',
        'format' => 'json',
        'titles' => ''
    );

    private $page_query_args = array(
        'action' => 'parse',
        'prop' => 'text|links|templates|externallinks|sections|iwlinks',
        'section' => 0,
        'disabletoc' => '',
        'mobileformat' => '',
        'noimages' => '',
        'format' => 'json',
        'pageid' => -1
    );

    public function ajax_get_wiki_page() {

        $wiki_id = $_REQUEST[ 'wid' ];
        $wiki_url = $_REQUEST[ 'wurl' ];

        if( $wiki_id == -1 ) {
            $result = array( 'code' => -1 );
        } else {
            $this->page_query_args[ 'pageid' ] = $wiki_id;
            $response = wp_remote_get( $wiki_url . self::WIKI_API_PATH . '?' . http_build_query( $this->page_query_args ) );

            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                $wiki_data = json_decode( $response['body'], true );

                $content = $wiki_data['parse']['text']['*'];
                $content = substr($content, stripos($content, '<p>'));
                $content = substr($content, 0, stripos($content, '</p>'));
                $content = preg_replace('/<\/?[^>]+>/', '', $content);
                $content = preg_replace('/\[[^\]]+\]/', '', $content);

                $result = array(
                    'code' => '1',
                    'title' => $wiki_data['parse']['title'],
                    'content' => $content
                );
            } else {
                $result = array( 'code' => -1 );
            }
        }

        echo json_encode( $result );
        wp_die();
    }

    public function get_wiki_page_info( $title = '', $wiki_url = '' ) {
        $this->info_query_args[ 'titles' ] = $title;
        $response = wp_remote_get( $wiki_url . self::WIKI_API_PATH . '?' . http_build_query( $this->info_query_args ) );

        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $wiki_data = json_decode( $response['body'], true );
            $wiki_pages = array_keys( $wiki_data[ 'query' ][ 'pages' ] );
            $wiki_page_id = $wiki_pages[ 0 ];
        } else {
            $wiki_page_id = -1;
        }

        $result = array(
            'wiki-id' => -1,
            'wiki-title' => __( 'Error!', 'wp-wiki-tooltip' ),
            'wiki-url' => ''
        );

        if( $wiki_page_id > -1 ) {
            $result = array(
                'wiki-id' => $wiki_page_id,
                'wiki-title' => $wiki_data[ 'query' ][ 'pages' ][ $wiki_page_id ][ 'title' ],
                'wiki-url' => $wiki_data[ 'query' ][ 'pages' ][ $wiki_page_id ][ 'fullurl' ]
            );
        }

        return $result;
    }
}