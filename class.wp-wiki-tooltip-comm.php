<?php

include_once('class.wp-wiki-tooltip-base.php');

/**
 * Class WP_Wiki_Tooltip_Comm
 */
class WP_Wiki_Tooltip_Comm extends WP_Wiki_Tooltip_Base {

    private $image_query_args = array(
        'action' => 'query',
        'prop' => 'pageimages',
        'pithumbsize' => '200',
        'format' => 'json',
        'pageids' => -1
    );

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
        'prop' => 'text',
        'section' => 0,
        'disabletoc' => '',
        'mobileformat' => '',
        'noimages' => '',
        'format' => 'json',
        'pageid' => -1
    );

	private $sections_query_args = array(
		'action' => 'parse',
		'prop' => 'sections',
		'disabletoc' => '',
		'mobileformat' => '',
		'noimages' => '',
		'format' => 'json',
		'pageid' => -1
	);

    private $test_query_args = array(
        'action' => 'query',
        'meta' => 'siteinfo',
        'format' => 'json',
        'siprop' => 'general'
    );

    public function ajax_get_wiki_page() {

        $wiki_id = $_REQUEST[ 'wid' ];
        $section = $_REQUEST[ 'section' ];
        $section_errhdl = $_REQUEST[ 'serrhdl' ];
        $wiki_url = $_REQUEST[ 'wurl' ];
        $page_url = $_REQUEST[ 'purl' ];
        $thumb_enable = $_REQUEST[ 'tenable' ];
	    $thumb_width = $_REQUEST[ 'twidth' ];
	    $error_title = $_REQUEST[ 'errtit' ];
	    $error_message = $_REQUEST[ 'errmsg' ];

	    $error_result = array(
	    	'code' => -1,
            'title' => $error_title,
            'section' => '',
            'content' => $error_message
	    );

        if( $wiki_id == -1 ) {
            $result = $error_result;
        } else {
	        $section_id = -1;
	        $section_error = false;
	        if( $section != '' ) {
				$section_id = $this->get_section_id_by_name( $wiki_url, $wiki_id, $section );
				if( $section_id > -1 ) {
					$this->page_query_args[ 'section' ] = $section_id;
				} else {
					if( $section_errhdl == 'use-page-settings' ) {
						$section_error = true;
					}
				}
	        }

	        $this->page_query_args[ 'pageid' ] = $wiki_id;
            $response = wp_remote_get( $wiki_url . '?' . http_build_query( $this->page_query_args ) );

            if ( is_array( $response ) && ! is_wp_error( $response ) && ! $section_error ) {
                $wiki_data = json_decode( $response['body'], true );

                // parse the content of wiki page
                $content = $wiki_data['parse']['text']['*'];

                // remove all html tables because there can be paragraphs in
                $content = preg_replace('/<table.*<\/table>/is', '', $content);

                // find the content of the first paragraph
                $content = substr($content, stripos($content, '<p>') + 3);
                $content = substr($content, 0, stripos($content, '</p>'));

                // delete all links (<a> tags) to avoid useless links in the tooltip
                $content = preg_replace('/<\/?a[^>]*>/', '', $content);

                $result = array(
                    'code' => '1',
                    'title' => $wiki_data['parse']['title'],
                    'section' => ( $section_id > -1 ) ? $section : '',
                    'content' => $content,
                    'url' => $page_url,
                    'thumb' => '-1'
                );

                /*** Request the page thumbnail ***/
                if( $thumb_enable == 'on' ) {
                    $this->image_query_args['pageids'] = $wiki_id;
                    $this->image_query_args['pithumbsize'] = $thumb_width;
                    $response = wp_remote_get($wiki_url . '?' . http_build_query($this->image_query_args));
                    if (is_array($response) && !is_wp_error($response)) {
                        $image_data = json_decode($response['body'], true);
                        if (isset($image_data['query']['pages'][$wiki_id]["thumbnail"])) {
                            $thumb = $image_data['query']['pages'][$wiki_id]["thumbnail"];
                            $result['thumb'] = $thumb["source"];
                            $result['thumb-width'] = $thumb["width"];
                            $result['thumb-height'] = $thumb["height"];
                        }
                    }
                }

            } else {
	            $result = $error_result;
            }
        }

        echo json_encode( $result );
        wp_die();
    }

	public function ajax_test_wiki_url() {

		$wurl = ( parse_url( $_REQUEST[ 'wurl' ], PHP_URL_SCHEME ) === null ) ? "http://" . $_REQUEST[ 'wurl' ] : $_REQUEST[ 'wurl' ];
		$wiki_urls = array( $wurl, $wurl . '/api.php', $wurl . '/w/api.php' );

		foreach( $wiki_urls as $wurl ) {
			$response = wp_remote_get( $wurl . '?' . http_build_query( $this->test_query_args ) );

			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$wiki_data = json_decode( $response[ 'body' ], true );
				if( ! empty( $wiki_data[ 'query' ][ 'general' ][ 'sitename' ] ) ) {
					$result = array(
						'code' => 1,
						'url' => $wurl,
						'name' => $wiki_data['query']['general']['sitename']
					);
					echo json_encode($result);
					wp_die();
				}
			}
		}

		$result = array( 'code' => -1 );
		echo json_encode( $result );
		wp_die();
	}

	private function get_section_id_by_name( $wiki_url = '', $page_id = -1, $section_title = '' ) {
		$this->sections_query_args[ 'pageid' ] = $page_id;
		$response = wp_remote_get( $wiki_url . '?' . http_build_query( $this->sections_query_args ) );

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$wiki_data = json_decode( $response[ 'body' ], true );
			$sections = $wiki_data[ 'parse' ][ 'sections' ];
			foreach( $sections as $section ) {
				if( $section[ 'anchor' ] == $section_title ) {
					return $section[ 'index' ];
				}
			}
		}

		return -1;
	}

    public function get_wiki_page_info( $title = '', $wiki_url = '' ) {
        $this->info_query_args[ 'titles' ] = $title;
        $response = wp_remote_get( $wiki_url . '?' . http_build_query( $this->info_query_args ) );

        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $wiki_data = json_decode( $response['body'], true );
            $wiki_pages = array_keys( $wiki_data[ 'query' ][ 'pages' ] );
            $wiki_page_id = $wiki_pages[ 0 ];
        } else {
            $wiki_page_id = -1;
        }

        $result = array(
            'wiki-id' => -1,
            'wiki-title' => '',
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