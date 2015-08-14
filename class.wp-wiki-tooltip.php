<?php

include_once('class.wp-wiki-tooltip-base.php');
include_once('class.wp-wiki-tooltip-comm.php');

/**
 * Class WP_Wiki_Tooltip
 */
class WP_Wiki_Tooltip extends WP_Wiki_Tooltip_Base {

	private $shortcode_count;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'init' ) );
		add_action( 'wp_footer', array( $this, 'add_wiki_container' ) );
		add_shortcode( 'wiki', array( $this, 'do_wiki_shortcode' ) );

		$this->options = get_option( 'wp-wiki-tooltip-settings' );
		if( $this->options == false ) {
			global $wp_wiki_tooltip_default_options;
			$this->options = $wp_wiki_tooltip_default_options;
		}
		$this->shortcode_count = 1;
	}

	public function init() {
		wp_enqueue_style( 'tooltipster-css', plugins_url( 'static/external/tooltipster/css/tooltipster.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-light-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-light.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-noir-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-noir.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-punk-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-punk.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'tooltipster-shadow-css', plugins_url( 'static/external/tooltipster/css/themes/tooltipster-shadow.css', __FILE__ ), array(), '3.0', 'all' );
		wp_enqueue_style( 'wp-wiki-tooltip-css', plugins_url( 'static/css/wp-wiki-tooltip.css', __FILE__ ), array( 'tooltipster-css' ), $this->version, 'all' );

		wp_enqueue_script( 'tooltipster-js', plugins_url( 'static/external/tooltipster/js/jquery.tooltipster.min.js', __FILE__ ), array( 'jquery' ), '3.0', false );
		wp_register_script( 'wp-wiki-tooltip-js', plugins_url( 'static/js/wp-wiki-tooltip.js', __FILE__ ), array( 'tooltipster-js' ), $this->version, false );
		wp_localize_script( 'wp-wiki-tooltip-js', 'wp_wiki_tooltip', array(
			'wiki_url' => $this->options[ 'wiki-url' ],
			'wp_ajax_url' => admin_url( 'admin-ajax.php' ),
			'wiki_plugin_url' => plugin_dir_url( __FILE__ ),
			'tooltip_theme' => 'tooltipster-' . $this->options[ 'theme' ],
			'footer_text' => __( 'Just click to open wiki page...', 'wp-wiki-tooltip' ),
			'error_title' => __( 'Error!', 'wp-wiki-tooltip' ),
			'page_not_found_message' => __( 'Sorry, but we were not able to find this page :(', 'wp-wiki-tooltip' )
		));
		wp_enqueue_script( 'wp-wiki-tooltip-js' );
	}

	public function add_wiki_container() {
		echo '<div id="wiki-container"></div>';
	}

	public function do_wiki_shortcode( $atts, $content ) {
		$params = shortcode_atts( array(
			'title' => ''
		), $atts );

		$title = ( $params[ 'title' ] == '' ) ? $content : $params[ 'title' ];

		$cnt = $this->shortcode_count++;

		$trans_wiki_key = urlencode( $this->options[ 'wiki-url' ] . '-' . $title . $this->version );
		if( ( $trans_wiki_data = get_transient( $trans_wiki_key ) ) === false ) {

			$comm = new WP_Wiki_Tooltip_Comm();
			$trans_wiki_data = $comm->get_wiki_page_info( $title, $this->options[ 'wiki-url' ] );

			set_transient( $trans_wiki_key, $trans_wiki_data, WEEK_IN_SECONDS );
		}

		$output  = '<script>jQuery( document ).ready( function() { add_wiki_box( ' . $cnt . ', "' . $trans_wiki_data[ 'wiki-id' ] . '", "' . $trans_wiki_data[ 'wiki-title' ] . '" ); } );</script>';
		$output .= '<a id="wiki-tooltip-' . $cnt . '" class="wiki-tooltip" href="' . $trans_wiki_data[ 'wiki-url' ] . '" target="' . $this->options[ 'a-target' ] . '" style="' . $this->options[ 'a-style' ] . '">' . $content . '</a>';

		return $output;
	}
}
