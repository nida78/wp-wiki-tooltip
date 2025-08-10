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
        add_filter( 'the_content', array( $this, 'filter_the_content_for_wiki_tags' ) );

        $this->load_all_options();
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
			'a.wiki-tooltip { ' . $this->options_design[ 'a-style' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.head { ' . $this->options_design[ 'tooltip-head' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.body { ' . $this->options_design[ 'tooltip-body' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon div.foot a { ' . $this->options_design[ 'tooltip-foot' ] . ' }' . "\n" .
			'div.wiki-tooltip-balloon img.thumb { ' . $this->options_thumb[ 'thumb-style' ] . ' }'
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
            esc_attr( admin_url( 'admin-ajax.php' ) ),
			esc_attr( plugin_dir_url( __FILE__ ) ),
			esc_attr( $this->options_design[ 'theme' ] ),
            esc_attr($this->options_design[ 'animation' ] ),
            esc_attr__( 'Click here to open Wiki page&hellip;', 'wp-wiki-tooltip' ),
            esc_attr( ( $this->options_thumb[ 'thumb-enable' ] == 'on' ) ? 'on' : 'off' ),
            esc_attr( $this->options_thumb[ 'thumb-width' ] ),
            esc_attr( $this->options_thumb[ 'thumb-align' ] ),
            esc_attr( $this->options_base[ 'trigger' ] ),
            esc_attr( ( $this->options_base[ 'trigger' ] == 'hover' ) ? $this->options_base[ 'trigger-hover-action' ] : '' ),
            esc_attr( $this->options_base[ 'a-target' ] ),
            esc_attr( $this->options_base[ 'min-screen-width' ] ),
            esc_attr( $this->options_error[ 'page-error-handling' ] ),
            esc_attr__( 'Error!', 'wp-wiki-tooltip' ),
            esc_attr__( 'Sorry, but we were not able to find this page :(', 'wp-wiki-tooltip' ),
            esc_attr( ( $this->options_error[ 'page-error-handling' ] == 'show-own' ) ? $this->options_error[ 'own-error-title' ] : '' ),
            esc_attr( ( $this->options_error[ 'page-error-handling' ] == 'show-own' ) ? $this->options_error[ 'own-error-message' ] : '' ),
            esc_attr( $this->options_error[ 'section-error-handling' ] )
        );
	}

	public function do_wiki_shortcode( $atts, $content ) {
		$params = shortcode_atts( array(
			'base' => '',
			'title' => '',
			'section' => '',
			'thumbnail' => 'default'
		), $atts );

        // sanitize all incoming parameters
        $params[ 'base' ] = sanitize_text_field( $params[ 'base' ] );
        $params[ 'title' ] = sanitize_text_field( $params[ 'title' ] );
        $params[ 'section' ] = sanitize_text_field( $params[ 'section' ] );
        $params[ 'thumbnail' ] = sanitize_text_field( $params[ 'thumbnail' ] );

		$title = ( $params[ 'title' ] == '' ) ? $content : $params[ 'title' ];

		$wiki_base_id = $params[ 'base' ];

        $this->load_all_options();
		$wiki_urls = $this->options_base[ 'wiki-urls' ];

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

        // $output  = 'data-wiki_num="' . $num . '" data-wiki_id="' . $trans_wiki_data[ 'wiki-id' ] . '" data-wiki_title="' . $trans_wiki_data[ 'wiki-title' ] . '" data-wiki_section="' . $params[ 'section' ] . '" data-wiki_base_url="' . $trans_wiki_data[ 'wiki-base-url' ] . '" data-wiki_url="' . $trans_wiki_data[ 'wiki-url' ] . '" data-wiki_thumbnail="' . $params[ 'thumbnail' ] . '"';
        $output  = sprintf(
            'data-wiki_num="%1$s" data-wiki_id="%2$s" data-wiki_title="%3$s" data-wiki_section="%4$s" data-wiki_base_url="%5$s" data-wiki_url="%6$s" data-wiki_thumbnail="%7$s" data-wiki_nonce="%8$s"',
            $num,
            esc_attr( $trans_wiki_data[ 'wiki-id' ] ),
            esc_attr( $trans_wiki_data[ 'wiki-title' ] ),
            esc_attr( $params[ 'section' ] ),
            esc_attr( $trans_wiki_data[ 'wiki-base-url' ] ),
            esc_attr( $trans_wiki_data[ 'wiki-url' ] ),
            esc_attr( $params[ 'thumbnail' ] ),
            esc_attr( wp_create_nonce( 'wp-wiki-tooltip-nonce-' . $trans_wiki_data[ 'wiki-id' ] ) )
        );

		$relno = ( $this->options_base[ 'a-target' ] == '_blank' ) ? ' rel="noopener noreferrer"' : '';

        if( ( $trans_wiki_data[ 'wiki-id' ] == '-1' ) && ( $this->options_error[ 'page-error-handling' ] == 'remove-link' ) ) {
	        $output = $content;
        } else {
            // $output = '<span id="wiki-tooltip-' . $num . '" data-tooltip-content="#wiki-tooltip-box-' . $num . '" ' . $output . '><a class="wiki-tooltip" href="' . $trans_wiki_data['wiki-url'] . ( ( $params[ 'section' ] != '' ) ? ( '#' . $params[ 'section' ] ) : '' ) . '" target="' . $this->options_base[ 'a-target' ] . '"' . $relno . ' onclick="return isClickEnabled( \'' . $this->options_base[ 'trigger' ] . '\', \'' . $this->options_base[ 'trigger-hover-action' ] . '\' );">' . $content . '</a></span>';
            $output = sprintf(
                '<span id="wiki-tooltip-%1$s" data-tooltip-content="#wiki-tooltip-box-%1$s" %2$s><a class="wiki-tooltip" href="%3$s" target="%4$s"%5$s onclick="return isClickEnabled( \'%6$s\', \'%7$s\' );">%8$s</a></span>',
                $num,
                $output,
                esc_url( $trans_wiki_data['wiki-url'] . ( ( '' != $params[ 'section' ] ) ? ( '#' . $params[ 'section' ] ) : '' ) ),
                $this->options_base[ 'a-target' ],
                $relno,
                $this->options_base[ 'trigger' ],
                ( 'hover' == $this->options_base[ 'trigger' ] ) ? $this->options_base[ 'trigger-hover-action' ] : '',
                $content
            );
        }

		return $output;
	}

    public function filter_the_content_for_wiki_tags( $content ) {
        // check if we're inside the main loop in a single post
        if( in_the_loop() && is_main_query() ) {
            // search for all <wiki>-tags
            $content = preg_replace_callback(
                '/<wiki.*?\/wiki>/',
                array( $this, 'convert_wiki_tag' ),
                $content
            );
        }

        return $content;
    }

    private function convert_wiki_tag( $tag_content ) {
        // need this function only for probably more complex replacements
        return preg_replace( '/<(\/?)wiki([^>]*)>/', '[$1wiki$2]', $tag_content[ 0 ] );
    }
}
