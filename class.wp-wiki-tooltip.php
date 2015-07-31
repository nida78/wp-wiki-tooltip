<?php

/**
 * Class WP_Wiki_Tooltip
 */
class WP_Wiki_Tooltip {

	const WIKI_URL = 'https://en.wikipedia.org';

	const WIKI_API_PATH = '/w/api.php';

	const WIKI_API_INFO_QUERY = 'action=query&prop=info&inprop=url&redirects=&format=json&titles=';

	const WIKI_API_PAGE_QUERY = 'action=parse&prop=text%7Clinks%7Ctemplates%7Cexternallinks%7Csections%7Ciwlinks&section=0&disabletoc=&mobileformat=&noimages=&format=json&pageid=';

	private static $shortcode_count = 0;

	public static function init() {
		load_plugin_textdomain( 'wp-wiki-tooltip', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		wp_enqueue_style( 'tooltipster-css', plugins_url( 'static/external/tooltipster/css/tooltipster.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-light-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-light.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-noir-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-noir.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-punk-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-punk.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-shadow-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-shadow.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'wp-wiki-tooltip-css', plugins_url( 'static/css/wp-wiki-tooltip.css', __FILE__ ), array( 'tooltipster-css' ), '0.5', 'all' );

		wp_enqueue_script( 'tooltipster-js', plugins_url( 'static/external/tooltipster/js/jquery.tooltipster.min.js', __FILE__ ), array( 'jquery' ), '3.0', false );
		wp_register_script( 'wp-wiki-tooltip-js', plugins_url( 'static/js/wp-wiki-tooltip.js', __FILE__ ), array( 'tooltipster-js' ), '0.5', false );
		wp_localize_script( 'wp-wiki-tooltip-js', 'wp_wiki_tooltip', array(
			'wiki_plugin_url' => plugin_dir_url( __FILE__ ),
			'tooltip_theme' => 'tooltipster-shadow',
			'footer_text' => __( 'Just click to open wiki page...', 'wp-wiki-tooltip' )
		));
		wp_enqueue_script( 'wp-wiki-tooltip-js' );
	}

	public static function add_wiki_container() {
		echo '<div id="wiki-container"></div>';
	}

	public static function ajax_get_wiki_page() {
		$wp_path = explode( 'wp-content', dirname( __FILE__ ) );
		require( $wp_path[ 0 ] . 'wp-load.php' );

		$wiki_id = ( array_key_exists( 'wid', $_REQUEST ) ) ? $_REQUEST[ 'wid' ] : -1;

		if( $wiki_id == -1 ) {
			$result = array(
				'code' => -1,
				'title' => __( 'Error!', 'wp-wiki-tooltip' ),
				'content' => __( 'Sorry, but we were not able to find this page :(', 'wp-wiki-tooltip' )
			);
		} else {

			$wiki_data = json_decode(
				file_get_contents( self::WIKI_URL . self::WIKI_API_PATH . '?' . self::WIKI_API_PAGE_QUERY . $wiki_id ),
				true
			);

			$content = $wiki_data[ 'parse' ][ 'text' ][ '*' ];
			$content = substr( $content, stripos( $content, '<p>' ) );
			$content = substr( $content, 0, stripos( $content, '</p>' ) );
			$content = preg_replace( '/<\/?[^>]+>/', '', $content );
			$content = preg_replace( '/\[[^\]]+\]/', '', $content );

			$result = array(
				'code' => '1',
				'title' => $wiki_data[ 'parse' ][ 'title' ],
				'content' => $content
			);
		}

		echo json_encode( $result );
	}

	public static function do_wiki_shortcode( $atts, $content ) {

		$params = shortcode_atts( array(
			'title' => ''
		), $atts );

		$title = ( $params[ 'title' ] == '' ) ? $content : $params[ 'title' ];

		$cnt = ++WP_Wiki_Tooltip::$shortcode_count;

		$trans_wiki_key = urlencode( self::WIKI_URL . "-" . $title );
		if( ( $trans_wiki_data = get_transient( $trans_wiki_key ) ) === false ) {
			$wiki_data = json_decode(
				file_get_contents( self::WIKI_URL . self::WIKI_API_PATH . '?' . self::WIKI_API_INFO_QUERY . $title ),
				true
			);

			$wiki_page_id = array_keys( $wiki_data[ 'query' ][ 'pages' ] )[ 0 ];

			if( $wiki_page_id > -1 ) {
				$trans_wiki_data = array(
					'wiki-id' => $wiki_page_id,
					'wiki-title' => $wiki_data[ 'query' ][ 'pages' ][ $wiki_page_id ][ 'title' ],
					'wiki-url' => $wiki_data[ 'query' ][ 'pages' ][ $wiki_page_id ][ 'fullurl' ]
				);
				set_transient( $trans_wiki_key, $trans_wiki_data, MINUTE_IN_SECONDS );
			}
		}

		$output  = '<script>jQuery( document ).ready( function() { add_wiki_box( ' . $cnt . ', "' . $trans_wiki_data[ 'wiki-id' ] . '", "' . $trans_wiki_data[ 'wiki-title' ] . '" ); } );</script>';
		$output .= '<a id="wiki-tooltip-' . $cnt . '" class="wiki-tooltip" href="' . $trans_wiki_data[ 'wiki-url' ] . '" target="_wiki">' . $content . '</a>';

		return $output;
	}
}
