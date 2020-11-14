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
        $tooltipster_base_dir = sprintf( 'static/external/tooltipster/%1$s/dist', $this->tooltipster_version );

		wp_enqueue_style( 'tooltipster-css', plugins_url( $tooltipster_base_dir . '/css/tooltipster.bundle.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
		wp_enqueue_style( 'tooltipster-light-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
		wp_enqueue_style( 'tooltipster-noir-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-noir.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
		wp_enqueue_style( 'tooltipster-punk-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
		wp_enqueue_style( 'tooltipster-shadow-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
		wp_enqueue_style( 'wp-wiki-tooltip-css', plugins_url( 'static/css/wp-wiki-tooltip.css', __FILE__ ), array( 'tooltipster-css' ), $this->version, 'all' );
		wp_add_inline_style(
			'wp-wiki-tooltip-css',
			'a.wiki-tooltip { ' . $this->options[ 'a-style' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.head { ' . $this->options[ 'tooltip-head' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.body { ' . $this->options[ 'tooltip-body' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.foot a { ' . $this->options[ 'tooltip-foot' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon img.thumb { ' . $this->options[ 'thumb-style' ] . ' }'
		);

		wp_enqueue_script( 'tooltipster-js', plugins_url( $tooltipster_base_dir . '/js/tooltipster.bundle.min.js', __FILE__ ), array( 'jquery' ), $this->tooltipster_version, false );
        wp_enqueue_script( 'wp-wiki-tooltip-js', plugins_url( 'static/js/wp-wiki-tooltip.js', __FILE__ ), array( 'tooltipster-js' ), $this->version, false );
	}

	public function add_wiki_container() {
        echo sprintf(
            '<div id="wiki-container"
                        data-wp_ajax_url="%s"
                        data-wiki_plugin_url="%s"
                        data-tooltip_theme="tooltipster-%s"
                        data-animation="%s"
                        data-footer_text="%s"
                        data-thumb_enable="%s"
                        data-thumb_width="%s"
                        data-thumb_align="%s"
                        data-trigger="%s"
                        data-trigger_hover_action="%s"
                        data-a_target="%s"
                        data-min_screen_width="%s"
                        data-error_handling="%s"
                        data-default_error_title="%s"
                        data-default_error_message="%s"
                        data-own_error_title="%s"
                        data-own_error_message="%s"
                        data-section_error_handling="%s"
                     ></div>',
            admin_url( 'admin-ajax.php' ),
			plugin_dir_url( __FILE__ ),
			$this->options[ 'theme' ],
			$this->options[ 'animation' ],
			__( 'Click here to open Wiki page&hellip;', 'wp-wiki-tooltip' ),
			( $this->options[ 'thumb-enable' ] == 'on' ) ? 'on' : 'off',
			$this->options[ 'thumb-width' ],
            $this->options[ 'thumb-align' ],
			$this->options[ 'trigger' ],
			$this->options[ 'trigger-hover-action' ],
            $this->options[ 'a-target' ],
			$this->options[ 'min-screen-width' ],
			$this->options[ 'page-error-handling' ],
			__( 'Error!', 'wp-wiki-tooltip' ),
			__( 'Sorry, but we were not able to find this page :(', 'wp-wiki-tooltip' ),
            ( $this->options[ 'page-error-handling' ] == 'show-own' ) ? $this->options[ 'own-error-title' ] : '',
            ( $this->options[ 'page-error-handling' ] == 'show-own' ) ? $this->options[ 'own-error-message' ] : '',
			$this->options[ 'section-error-handling' ]
        );
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

		$num = $this->shortcode_count++;

		$trans_wiki_key = urlencode( $wiki_base_id . "-" . $wiki_url . '-' . $title . '-' . $this->version );
		if( ( $trans_wiki_data = get_transient( $trans_wiki_key ) ) === false ) {

			$comm = new WP_Wiki_Tooltip_Comm();
			$trans_wiki_data = $comm->get_wiki_page_info( $title, $wiki_url );
			$trans_wiki_data[ 'wiki-base-url' ] = $wiki_url;

			set_transient( $trans_wiki_key, $trans_wiki_data, WEEK_IN_SECONDS );
		}

        $output  = 'data-wiki_num="' . $num . '" data-wiki_id="' . $trans_wiki_data[ 'wiki-id' ] . '" data-wiki_title="' . $trans_wiki_data[ 'wiki-title' ] . '" data-wiki_section="' . $params[ 'section' ] . '" data-wiki_base_url="' . $trans_wiki_data[ 'wiki-base-url' ] . '" data-wiki_url="' . $trans_wiki_data[ 'wiki-url' ] . '" data-wiki_thumbnail="' . $params[ 'thumbnail' ] . '"';

		$relno = ( $this->options[ 'a-target' ] == '_blank' ) ? ' rel="noopener noreferrer"' : '';

        if( ( $trans_wiki_data[ 'wiki-id' ] == '-1' ) && ( $this->options[ 'page-error-handling' ] == 'remove-link' ) ) {
	        $output = $content;
        } else {
	        $output = '<span id="wiki-tooltip-' . $num . '" data-tooltip-content="#wiki-tooltip-box-' . $num . '" ' . $output . '><a class="wiki-tooltip" href="' . $trans_wiki_data['wiki-url'] . ( ( $params[ 'section' ] != '' ) ? ( '#' . $params[ 'section' ] ) : '' ) . '" target="' . $this->options['a-target'] . '"' . $relno . ' onclick="return isClickEnabled( \'' . $this->options['trigger'] . '\', \'' . $this->options['trigger-hover-action'] . '\' );">' . $content . '</a></span>';
        }

		return $output;
	}
}
