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
		wp_enqueue_style( 'tooltipster-css', plugins_url( 'static/external/tooltipster/dist/css/tooltipster.bundle.min.css', __FILE__ ), array(), '4.2.6', 'all' );
		wp_enqueue_style( 'tooltipster-light-css', plugins_url( 'static/external/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css', __FILE__ ), array(), '4.2.6', 'all' );
		wp_enqueue_style( 'tooltipster-noir-css', plugins_url( 'static/external/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-noir.min.css', __FILE__ ), array(), '4.2.6', 'all' );
		wp_enqueue_style( 'tooltipster-punk-css', plugins_url( 'static/external/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css', __FILE__ ), array(), '4.2.6', 'all' );
		wp_enqueue_style( 'tooltipster-shadow-css', plugins_url( 'static/external/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css', __FILE__ ), array(), '4.2.6', 'all' );
		wp_enqueue_style( 'wp-wiki-tooltip-css', plugins_url( 'static/css/wp-wiki-tooltip.css', __FILE__ ), array( 'tooltipster-css' ), $this->version, 'all' );
		wp_add_inline_style(
			'wp-wiki-tooltip-css',
			'a.wiki-tooltip { ' . $this->options[ 'a-style' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.head { ' . $this->options[ 'tooltip-head' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.body { ' . $this->options[ 'tooltip-body' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.foot a { ' . $this->options[ 'tooltip-foot' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon img.thumb { ' . $this->options[ 'thumb-style' ] . ' }'
		);

		wp_enqueue_script( 'tooltipster-js', plugins_url( 'static/external/tooltipster/dist/js/tooltipster.bundle.min.js', __FILE__ ), array( 'jquery' ), '4.2.6', false );
		wp_register_script( 'wp-wiki-tooltip-js', plugins_url( 'static/js/wp-wiki-tooltip.js', __FILE__ ), array( 'tooltipster-js' ), $this->version, false );
		wp_localize_script( 'wp-wiki-tooltip-js', 'wp_wiki_tooltip', array(
			'wp_ajax_url' => admin_url( 'admin-ajax.php' ),
			'wiki_plugin_url' => plugin_dir_url( __FILE__ ),
			'tooltip_theme' => 'tooltipster-' . $this->options[ 'theme' ],
			'animation' => $this->options[ 'animation' ],
			'footer_text' => __( 'Click here to open Wiki page...', 'wp-wiki-tooltip' ),
			'thumb_enable' => ( $this->options[ 'thumb-enable' ] == 'on' ) ? 'on' : 'off',
			'thumb_width' => $this->options[ 'thumb-width' ],
            'thumb_align' => $this->options[ 'thumb-align' ],
			'trigger' => $this->options[ 'trigger' ],
			'trigger_hover_action' => $this->options[ 'trigger-hover-action' ],
            'a_target' => $this->options[ 'a-target' ],
			'min_screen_width' => $this->options[ 'min-screen-width' ],
			'error_handling' => $this->options[ 'page-error-handling' ],
			'default_error_title' => __( 'Error!', 'wp-wiki-tooltip' ),
			'default_error_message' => __( 'Sorry, but we were not able to find this page :(', 'wp-wiki-tooltip' ),
			'own_error_title' => $this->options[ 'own-error-title' ],
			'own_error_message' => $this->options[ 'own-error-message' ],
			'section_error_handling' => $this->options[ 'section-error-handling' ]
		));
		wp_enqueue_script( 'wp-wiki-tooltip-js' );
	}

	public function add_wiki_container() {
		echo '<div id="wiki-container"></div>';
	}

	public function do_wiki_shortcode( $atts, $content ) {
		$params = shortcode_atts( array(
			'base' => '',
			'title' => '',
			'section' => '',
			'thumbnail' => 'default'
		), $atts );

		$title = ( $params[ 'title' ] == '' ) ? $content : $params[ 'title' ];

		$wiki_base_id = $params[ 'base' ];
		$wiki_urls = $this->options[ 'wiki-urls' ];

		if( $wiki_base_id == '' ) {
			$std_num = $wiki_urls[ 'standard' ];
			$wiki_base_id = $wiki_urls[ 'data' ][ $std_num ][ 'id' ];
		}

		$wiki_url = '';
		foreach( $wiki_urls[ 'data' ] as $num => $wiki_data ) {
			if( $wiki_data[ 'id' ] == $wiki_base_id ) {
				$wiki_url = $wiki_data[ 'url' ];
			}
		}

		$cnt = $this->shortcode_count++;

		$trans_wiki_key = urlencode( $wiki_base_id . "-" . $wiki_url . '-' . $title . '-' . $this->version );
		if( ( $trans_wiki_data = get_transient( $trans_wiki_key ) ) === false ) {

			$comm = new WP_Wiki_Tooltip_Comm();
			$trans_wiki_data = $comm->get_wiki_page_info( $title, $wiki_url );
			$trans_wiki_data[ 'wiki-base-url' ] = $wiki_url;

			set_transient( $trans_wiki_key, $trans_wiki_data, WEEK_IN_SECONDS );
		}

		$output  = '<script>$wwtj( document ).ready( function() { add_wiki_box( ' . $cnt . ', "' . $trans_wiki_data[ 'wiki-id' ] . '", "' . $trans_wiki_data[ 'wiki-title' ] . '", "' . $params[ 'section' ] . '", "' . $trans_wiki_data[ 'wiki-base-url' ] . '", "' . $trans_wiki_data[ 'wiki-url' ] . '", "' . $params[ 'thumbnail' ] . '" ); } );</script>';
        $relno = ( $this->options[ 'a-target' ] == '_blank' ) ? ' rel="noopener noreferrer"' : '';

        if( ( $trans_wiki_data[ 'wiki-id' ] == '-1' ) && ( $this->options[ 'error-handling' ] == 'remove-link' ) ) {
	        $output .= $content;
        } else {
	        $output .= '<span id="wiki-tooltip-' . $cnt . '" data-tooltip-content="wiki-tooltip-box-' . $cnt . '"><a class="wiki-tooltip" href="' . $trans_wiki_data['wiki-url'] . ( ( $params[ 'section' ] != '' ) ? ( '#' . $params[ 'section' ] ) : '' ) . '" target="' . $this->options['a-target'] . '"' . $relno . ' onclick="return isClickEnabled( \'' . $this->options['trigger'] . '\', \'' . $this->options['trigger-hover-action'] . '\' );">' . $content . '</a></span>';
        }

		return $output;
	}
}
