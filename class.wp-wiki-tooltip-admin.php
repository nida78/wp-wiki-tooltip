<?php

include_once('class.wp-wiki-tooltip-base.php');
include_once('class.wp-wiki-tooltip-comm.php');

/**
 * Class WP_Wiki_Tooltip_Admin
 */
class WP_Wiki_Tooltip_Admin extends WP_Wiki_Tooltip_Base {

    public function __construct( $name='' ) {
        add_action( 'enqueue_block_assets', array( $this, 'init_gutenberg' ) );

        add_filter( 'plugin_action_links_' . $name, array( $this, 'add_action_links' ) );
        add_action( 'admin_menu', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'load_all_options' ) );
        add_action( 'admin_init', array( $this, 'register_base_settings' ) );
        add_action( 'admin_init', array( $this, 'register_error_settings' ) );
        add_action( 'admin_init', array( $this, 'register_design_settings' ) );
        add_action( 'admin_init', array( $this, 'register_thumb_settings' ) );

        $comm = new WP_Wiki_Tooltip_Comm();
        add_action( 'wp_ajax_get_wiki_page', array( $comm, 'ajax_get_wiki_page' ) );
        add_action( 'wp_ajax_nopriv_get_wiki_page', array( $comm, 'ajax_get_wiki_page' ) );
        add_action( 'wp_ajax_test_wiki_url', array( $comm, 'ajax_test_wiki_url' ) );
        add_action( 'wp_ajax_nopriv_test_wiki_url', array( $comm, 'ajax_test_wiki_url' ) );
    }

    public function init() {
        $tooltipster_base_dir = sprintf( 'static/external/tooltipster/%1$s/dist', $this->tooltipster_version );

	    wp_enqueue_style( 'tooltipster-css', plugins_url( $tooltipster_base_dir . '/css/tooltipster.bundle.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
	    wp_enqueue_style( 'tooltipster-light-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
	    wp_enqueue_style( 'tooltipster-noir-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-noir.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
	    wp_enqueue_style( 'tooltipster-punk-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );
	    wp_enqueue_style( 'tooltipster-shadow-css', plugins_url( $tooltipster_base_dir . '/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css', __FILE__ ), array(), $this->tooltipster_version, 'all' );

        wp_enqueue_style( 'wp-wiki-tooltip-admin-css', plugins_url( 'static/css/wp-wiki-tooltip-admin.css', __FILE__ ), array(), $this->version, 'all' );
        wp_enqueue_style( 'wp-wiki-tooltip-mce-css', plugins_url( 'static/css/wp-wiki-tooltip-mce.css', __FILE__ ), array(), $this->version, 'all' );

	    wp_enqueue_script( 'tooltipster-js', plugins_url( $tooltipster_base_dir. '/js/tooltipster.bundle.min.js', __FILE__ ), array( 'jquery' ), $this->tooltipster_version, false );

        wp_register_script( 'wp-wiki-tooltip-admin-js', plugins_url( 'static/js/wp-wiki-tooltip-admin.js', __FILE__ ), array( 'jquery' ), $this->version, false );
        wp_localize_script( 'wp-wiki-tooltip-admin-js', 'wp_wiki_tooltip_admin', array(
            'alert_remove' => __( 'Rows that are marked as "Standard" could not be deleted!', 'wp-wiki-tooltip' ),
            'alert_test_failed' => __( 'Sorry, but the test of this URL failed!', 'wp-wiki-tooltip' ),
            'wp_ajax_url' => admin_url( 'admin-ajax.php' )
        ));
        wp_enqueue_script( 'wp-wiki-tooltip-admin-js' );

        add_options_page(
            _x( 'Wiki Tooltips Settings', 'page title', 'wp-wiki-tooltip' ),
            _x( 'Wiki Tooltips', 'menu title', 'wp-wiki-tooltip' ),
            'manage_options',
            'wp-wiki-tooltip-settings',
            array( $this, 'settings_page_base' )
        );

        add_submenu_page(
            'wp-wiki-tooltip-settings',
            _x( 'Wiki Tooltips Error Settings', 'page title', 'wp-wiki-tooltip' ),
            _x( 'Error Handling', 'menu title', 'wp-wiki-tooltip' ),
            'manage_options',
            'wp-wiki-tooltip-settings-error',
            array( $this, 'settings_page_error' )
        );

        add_submenu_page(
            'wp-wiki-tooltip-settings',
            _x( 'Wiki Tooltips Design Settings', 'page title', 'wp-wiki-tooltip' ),
            _x( 'Design', 'menu title', 'wp-wiki-tooltip' ),
            'manage_options',
            'wp-wiki-tooltip-settings-design',
            array( $this, 'settings_page_design' )
        );

        add_submenu_page(
            'wp-wiki-tooltip-settings',
            _x( 'Wiki Tooltips Thumbnail Settings', 'page title', 'wp-wiki-tooltip' ),
            _x( 'Thumbnail', 'menu title', 'wp-wiki-tooltip' ),
            'manage_options',
            'wp-wiki-tooltip-settings-thumb',
            array( $this, 'settings_page_thumb' )
        );

        /*** check for RESET action ***/
        if ( ( isset( $_REQUEST[ 'wp-wiki-tooltip-settings-base' ][ 'nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wp-wiki-tooltip-settings-base' ][ 'nonce' ] ) ), 'wp-wiki-tooltip-settings-base-submit' ) )
          || ( isset( $_REQUEST[ 'wp-wiki-tooltip-settings-error' ][ 'nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wp-wiki-tooltip-settings-error' ][ 'nonce' ] ) ), 'wp-wiki-tooltip-settings-error-submit' ) )
          || ( isset( $_REQUEST[ 'wp-wiki-tooltip-settings-design' ][ 'nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wp-wiki-tooltip-settings-design' ][ 'nonce' ] ) ), 'wp-wiki-tooltip-settings-design-submit' ) )
          || ( isset( $_REQUEST[ 'wp-wiki-tooltip-settings-thumb' ][ 'nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wp-wiki-tooltip-settings-thumb' ][ 'nonce' ] ) ), 'wp-wiki-tooltip-settings-thumb-submit' ) )) {

            if ( isset( $_REQUEST['btn_reset'] ) && ( $_REQUEST['btn_reset'] == __('Reset', 'wp-wiki-tooltip')  ) ) {
                $result = 0;

                if (delete_option('wp-wiki-tooltip-settings')) // the single-admin-page option has to be deleted anyway
                    $result++;

                if (delete_option('wp-wiki-tooltip-settings-base'))
                    $result++;

                if (delete_option('wp-wiki-tooltip-settings-error'))
                    $result++;

                if (delete_option('wp-wiki-tooltip-settings-design'))
                    $result++;

                if (delete_option('wp-wiki-tooltip-settings-thumb'))
                    $result++;

                $redir_url = add_query_arg(array(
                    'page' => 'wp-wiki-tooltip-settings',
                    'settings-updated' => 'reset-' . (($result == 5) ? 'false' : 'true'),
                    'wiki-reset-nonce' => wp_create_nonce('wp-wiki-tooltip-settings-reset')
                ), admin_url('options-general.php'));

                header('Location: ' . $redir_url);
                die();
            }
        }

        /*** check for RESET result ***/
        if ( isset( $_REQUEST[ 'wiki-reset-nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wiki-reset-nonce' ] ) ), 'wp-wiki-tooltip-settings-reset' ) ) {
            if ( isset( $_REQUEST[ 'settings-updated' ] ) ) {
                if ($_REQUEST['settings-updated'] == 'reset-true') {
                    add_settings_error(
                        'wp-wiki-tooltip-settings-reset',
                        'settings_updated',
                        __('Settings reset successfully.', 'wp-wiki-tooltip'),
                        'updated'
                    );
                } else if ($_REQUEST['settings-updated'] == 'reset-false') {
                    add_settings_error(
                        'wp-wiki-tooltip-settings-reset',
                        'settings_updated',
                        __('An error occurred while resetting.', 'wp-wiki-tooltip'),
                        'error'
                    );
                }
            }
        }
    }

    public function init_gutenberg() {
        $asset = include('static/gutenberg/build/index.asset.php');
        $asset['dependencies'][] = 'wp-wiki-tooltip-mce-lang-js';
        wp_enqueue_script('wp-wiki-tooltip-gutenberg-script', plugins_url('static/gutenberg/build/index.js', __FILE__), $asset['dependencies'], $asset['version'], false );
        wp_enqueue_style('wp-wiki-tooltip-gutenberg-style', plugins_url('static/gutenberg/build/index.css', __FILE__), '', $asset['version']);
    }

    public function add_action_links( $links ) {
        return array_merge(
            $links,
            array( '<a href="' . admin_url( 'options-general.php?page=wp-wiki-tooltip-settings' ) . '">' . __( 'Settings', 'wp-wiki-tooltip' ) . '</a>', )
        );
    }

    public function register_base_settings() {
        global $wp_wiki_tooltip_default_options;

        add_settings_section(
            'wp-wiki-tooltip-settings-base',
            _x( 'Base Settings', 'settings section headline', 'wp-wiki-tooltip' ),
            array( $this, 'print_base_section_info' ),
            'wp-wiki-tooltip-settings-base'
        );

        add_settings_field(
            'wiki-urls',
            _x( 'URLs of Wikis', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_wiki_url_fields' ),
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base'
        );

        add_settings_field(
            'a-target',
            _x( 'Open links to Wiki pages in', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_a_target_field' ),
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base'
        );

        add_settings_field(
            'trigger',
            _x( 'Tooltips are triggered by', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_trigger_fields' ),
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base'
        );

	    add_settings_field(
		    'min-screen-width',
		    _x( 'Minimum screen width', 'settings field label', 'wp-wiki-tooltip' ),
		    array( $this, 'print_min_screen_width_field' ),
		    'wp-wiki-tooltip-settings-base',
		    'wp-wiki-tooltip-settings-base'
	    );

        register_setting(
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base',
            array(
                'type' => 'array',
                'description' => _x( 'Wiki Tooltips Base Settings', 'register setting description', 'wp-wiki-tooltip' ),
                'sanitize_callback' => array( $this, 'sanitize_base_settings' ),
                'show_in_rest' => false,
                'default' => $wp_wiki_tooltip_default_options
            )
        );
    }

    public function register_error_settings() {
        global $wp_wiki_tooltip_default_options;

        add_settings_section(
            'wp-wiki-tooltip-settings-error',
            _x( 'Error Handling Settings', 'settings section headline', 'wp-wiki-tooltip' ),
            array( $this, 'print_error_handling_section_info' ),
            'wp-wiki-tooltip-settings-error'
        );

        add_settings_field(
            'page-error-handling',
            _x( 'Page errors', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_page_error_handling_fields' ),
            'wp-wiki-tooltip-settings-error',
            'wp-wiki-tooltip-settings-error'
        );

        add_settings_field(
            'section-error-handling',
            _x( 'Section errors', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_section_error_handling_fields' ),
            'wp-wiki-tooltip-settings-error',
            'wp-wiki-tooltip-settings-error'
        );

        register_setting(
            'wp-wiki-tooltip-settings-error',
            'wp-wiki-tooltip-settings-error',
            array(
                'type' => 'array',
                'description' => _x( 'Wiki Tooltips Error Handling Settings', 'register setting description', 'wp-wiki-tooltip' ),
                'sanitize_callback' => array( $this, 'sanitize_error_settings' ),
                'show_in_rest' => false,
                'default' => $wp_wiki_tooltip_default_options
            )
        );
    }

    public function register_design_settings() {
        global $wp_wiki_tooltip_default_options;

        add_settings_section(
            'wp-wiki-tooltip-settings-design',
            _x( 'Design Settings', 'settings section headline', 'wp-wiki-tooltip' ),
            array( $this, 'print_design_section_info' ),
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'theme',
            _x( 'Design of the tooltips', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_theme_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'animation',
            _x( 'Animation', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_animation_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'tooltip-head',
            sprintf ( /* translators: parameters are used for opening and closing <em> HTML tag */
                _x( 'Tooltip %1$sheader%2$s styles', 'settings field label', 'wp-wiki-tooltip' ),
                '<em>',
                '</em>'
            ),
            array( $this, 'print_tooltip_head_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'tooltip-body',
            sprintf ( /* translators: parameters are used for opening and closing <em> HTML tag */
                _x( 'Tooltip %1$sbody%2$s styles', 'settings field label', 'wp-wiki-tooltip' ),
                '<em>',
                '</em>'
            ),
            array( $this, 'print_tooltip_body_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'tooltip-foot',
            sprintf ( /* translators: parameters are used for opening and closing <em> HTML tag */
                _x( 'Tooltip %1$sfooter%2$s styles', 'settings field label', 'wp-wiki-tooltip' ),
                '<em>',
                '</em>'
            ),
            array( $this, 'print_tooltip_foot_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'a-styles',
            _x( 'Wiki links styles', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_a_style_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design'
        );

        register_setting(
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            array(
                'type' => 'array',
                'description' => _x( 'Wiki Tooltips Design Settings', 'register setting description', 'wp-wiki-tooltip' ),
                'sanitize_callback' => array( $this, 'sanitize_design_settings' ),
                'show_in_rest' => false,
                'default' => $wp_wiki_tooltip_default_options
            )
        );
    }

    public function register_thumb_settings() {
        global $wp_wiki_tooltip_default_options;

        add_settings_section(
            'wp-wiki-tooltip-settings-thumb',
            _x( 'Thumbnail Settings', 'settings section headline', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_section_info' ),
            'wp-wiki-tooltip-settings-thumb'
        );

        add_settings_field(
            'thumb-enable',
            _x( 'Enable thumbnails', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_enable_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb'
        );

        add_settings_field(
            'thumb-align',
            _x( 'Alignment', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_align_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb'
        );

        add_settings_field(
            'thumb-width',
            _x( 'Width', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_width_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb'
        );

        add_settings_field(
            'thumb-style',
            _x( 'Styles', 'settings field label', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_style_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb'
        );

        register_setting(
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb',
            array(
                'type' => 'array',
                'description' => _x( 'Wiki Tooltips Thumbnail Settings', 'register setting description', 'wp-wiki-tooltip' ),
                'sanitize_callback' => array( $this, 'sanitize_thumb_settings' ),
                'show_in_rest' => false,
                'default' => $wp_wiki_tooltip_default_options
            )
        );
    }

    /********************************************************
     * Sections
     *******************************************************/
	public function print_base_section_info() {
		echo '<p>' . esc_html__( 'Here you can setup all basic options for the WP Wiki Tooltip plugin.', 'wp-wiki-tooltip' ) . '</p>';
	}

	public function print_error_handling_section_info() {
		echo '<p>' . esc_html__( 'There are some useful options how errors should be handled.', 'wp-wiki-tooltip' ) . '</p>';
	}

    public function print_design_section_info() {
        echo '<p>' . esc_html__( 'The design of the tooltips, their animation, and the style of content can be selected here.' , 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_section_info() {
        echo '<p>' .esc_html__( 'Enable thumbnails in tooltips and set some useful options below.' , 'wp-wiki-tooltip' ) . '</p>';
        echo '<p class="wiki-usage">';
        printf( /* translators: parameters are used for opening and closing <strong> HTML tag */
            esc_html__( 'Additionally an extra "%1$sthumbnail%2$s" attribute can be added to the shortcode:' , 'wp-wiki-tooltip' ),
            '<strong>',
            '</strong>'
        );
        echo '&nbsp;<span class="bold-teletyper">[wiki thumbnail="on"]WordPress[/wiki]</span>&nbsp;' . esc_html__( 'or', 'wp-wiki-tooltip' ) . '&nbsp;<span class="bold-teletyper">[wiki thumbnail="off" title="WordPress"]a nice blogging software[/wiki]</span></p>';
    }

    /********************************************************
     * Base Settings Fields
     *******************************************************/
    public function print_wiki_url_fields( $args ) {
        $standard_url = isset( $this->options_base[ 'wiki-urls' ][ 'standard' ] ) ? $this->options_base[ 'wiki-urls' ][ 'standard' ] : $args[ 'wiki-urls' ][ 'standard' ];
        $urls =  isset( $this->options_base[ 'wiki-urls' ][ 'data' ] ) ? $this->options_base[ 'wiki-urls' ][ 'data' ] : $args[ 'wiki-urls' ][ 'data' ];
        ?>

        <p><?php
            printf( /* translators: parameters are used for opening and closing <strong> HTML tag */
                esc_html__( 'Enter as much Wiki URLs as you like. Click the button "%1$stest%2$s" to let the plugin check if the given URL has access to a Wiki API.' , 'wp-wiki-tooltip' ),
                '<strong>',
                '</strong>'
            );
        ?></p>
        <p class="wiki-usage"><?php
            printf( /* translators: parameters are used for opening and closing <strong> HTML tag */
                esc_html__( 'To use one of these URLs just add an "%1$sbase%2$s" attribute to the shortcode:', 'wp-wiki-tooltip' ),
                '<strong>',
                '</strong>'
            );
        ?>&nbsp;<span class="bold-teletyper">[wiki base="ID"]WordPress[/wiki]</span>&nbsp;<?php esc_html_e( 'or', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki base="ID" title="WordPress"]a nice blogging software[/wiki]</span></p>
        <p class="wiki-usage"><?php
            printf( /* translators: parameters are used for opening and closing <strong> HTML tag */
                esc_html__( 'If you do not use the "%1$sbase%2$s" attribute the URL that is marked as "Standard" is used.' , 'wp-wiki-tooltip' ),
                '<strong>',
                '</strong>'
            );

            $max_id = 0;
        ?></p>

        <table id="wiki-urls-table">
            <tr>
                <th class="col1"><?php echo esc_html_x( 'Standard', 'column header','wp-wiki-tooltip' ); ?></th>
                <th class="col2"><?php echo esc_html_x( 'Name','column header', 'wp-wiki-tooltip' ); ?></th>
                <th class="col3"><?php echo esc_html_x( 'ID','column header', 'wp-wiki-tooltip' ); ?></th>
                <th class="col4"><?php echo esc_html_x( 'URL', 'column header', 'wp-wiki-tooltip' ); ?></th>
                <th class="col5" colspan="2"><?php echo esc_html_x( 'Actions', 'column header', 'wp-wiki-tooltip' ); ?></th>
            </tr>

            <?php
                $test_nonce = wp_create_nonce( 'wp-wiki-tooltip-settings-base-test-url' );
                foreach( $urls as $num => $url ) : if( $num != '###NEWID###' ) : ?>
                <tr id="wiki-url-row-<?php echo esc_html( $num ); ?>">
                    <td class="col1"><input id="rdo-wiki-url-row-<?php echo esc_html( $num ); ?>" type="radio" name="wp-wiki-tooltip-settings-base[wiki-urls][standard]" value="<?php echo esc_html( $num ); ?>" <?php checked( $num, $standard_url, true ); ?> class="radio"/></td>
                    <td class="col2"><input id="txt-site-wiki-url-row-<?php echo esc_html( $num ); ?>" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][<?php echo esc_html( $num ); ?>][sitename]" value="<?php echo esc_attr( $url[ 'sitename' ] ); ?>" class="regular-text"/></td>
                    <td class="col3"><input id="txt-id-wiki-url-row-<?php echo esc_html( $num ); ?>" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][<?php echo esc_html( $num ); ?>][id]" value="<?php echo esc_attr( $url[ 'id' ] ); ?>" class="narrow"/></td>
                    <td class="col4"><input id="txt-url-wiki-url-row-<?php echo esc_html( $num ); ?>" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][<?php echo esc_html( $num ); ?>][url]" value="<?php echo esc_attr( $url[ 'url' ] ); ?>" class="regular-text"/></td>
                    <td class="col5"><input id="btn-test-wiki-url-row-<?php echo esc_html( $num ); ?>" type="button" value="<?php echo esc_html_x( 'test', 'button', 'wp-wiki-tooltip' ); ?>" class="button" onclick="test_wiki_url_row( 'wiki-url-row-<?php echo esc_html( $num ); ?>', '<?php echo esc_attr( $test_nonce ); ?>' );"/><img src="<?php echo esc_url( plugins_url( 'static/images/loadingAnimationBar.gif', __FILE__ ) ); ?>" alt="loading animation bar" class="loadingAnimationBar" /></td>
                    <td class="col6"><input type="button" value="<?php echo esc_html_x( 'remove', 'button', 'wp-wiki-tooltip' ); ?>" class="button" onclick="remove_wiki_url_row( 'wiki-url-row-<?php echo esc_html( $num ); ?>' );"/></td>
                </tr>
            <?php
                if( $num > $max_id ) {
                    $max_id = $num;
                }
                endif; endforeach;
            ?>

            <tr id="wiki-url-row-template">
                <td class="col1"><input id="rdo-wiki-url-row-###NEWID###" type="radio" name="wp-wiki-tooltip-settings-base[wiki-urls][standard]" value="###NEWID###" class="radio"/></td>
                <td class="col2"><input id="txt-site-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][###NEWID###][sitename]" value="" class="regular-text"/></td>
                <td class="col3"><input id="txt-id-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][###NEWID###][id]" value="" class="narrow"/></td>
                <td class="col4"><input id="txt-url-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings-base[wiki-urls][data][###NEWID###][url]" value="" class="regular-text"/></td>
                <td class="col5"><input id="btn-test-wiki-url-row-###NEWID###" type="button" value="<?php echo esc_attr_x( 'test', 'button', 'wp-wiki-tooltip' ); ?>" class="button" onclick="test_wiki_url_row( 'wiki-url-row-###NEWID###', '<?php echo esc_attr( $test_nonce ); ?>' );"/><img src="<?php echo esc_url( plugins_url( 'static/images/loadingAnimationBar.gif', __FILE__ ) ); ?>" alt="loading animation bar" class="loadingAnimationBar" /></td>
                <td class="col6"><input type="button" value="<?php echo esc_html_x( 'remove', 'button', 'wp-wiki-tooltip' ); ?>" class="button" onclick="remove_wiki_url_row( 'wiki-url-row-###NEWID###' );"/></td>
            </tr>
            <tr>
                <td colspan="6"><input type="button" value="<?php echo esc_html_x( 'Add new URL', 'button', 'wp-wiki-tooltip' ); ?>" class="button" onclick="add_wiki_url_row();" /></td>
            </tr>
        </table>
        <input type="hidden" id="wp-wiki-tooltip-url-count" name="wp-wiki-tooltip-url-count" value="<?php echo esc_html( $max_id ); ?>" />
        <?php
    }

    public function print_a_target_field( $args ) {
        $used_target = isset( $this->options_base[ 'a-target' ] ) ? $this->options_base[ 'a-target' ] : $args[ 'a-target' ];

        echo '<p><label><input type="radio" id="rdo-a-target-blank" name="wp-wiki-tooltip-settings-base[a-target]" value="_blank"' . checked( $used_target, '_blank', false ) . ' />' . esc_html__( 'new window / tab', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p><label><input type="radio" id="rdo-a-target-self" name="wp-wiki-tooltip-settings-base[a-target]" value="_self" ' . checked( $used_target, '_self', false ) . ' />' . esc_html__( 'current window / tab', 'wp-wiki-tooltip' ) . '</label></p>';
    }

    public function print_trigger_fields( $args ) {
	    $used_trigger = isset( $this->options_base[ 'trigger' ] ) ? $this->options_base[ 'trigger' ] : $args[ 'trigger' ];
	    $used_action = ( ( 'hover' == $this->options_base[ 'trigger' ] ) && isset( $this->options_base[ 'trigger-hover-action' ] ) ) ? $this->options_base[ 'trigger-hover-action' ] : '';

	    echo '<p><label><input type="radio" id="rdo-a-trigger-click" name="wp-wiki-tooltip-settings-base[trigger]" value="click" ' . checked( $used_trigger, 'click', false ) . ' onclick="disable_trigger_hover_action( true );" />' . esc_html_x( 'click', 'option trigger', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p><label><input type="radio" id="rdo-a-trigger-hover" name="wp-wiki-tooltip-settings-base[trigger]" value="hover"' . checked( $used_trigger, 'hover', false ) . ' onclick="disable_trigger_hover_action( false );" />' . esc_html_x( 'hover', 'option trigger', 'wp-wiki-tooltip' ) . '</label></p>';
?>
        <p class="wiki-form-indent-left description"><?php esc_html_e( 'What happens by clicking the link, too?', 'wp-wiki-tooltip' ); ?></p>
        <ul class="wiki-form-indent-left">
            <li><label><input type="radio" id="rdo-a-trigger-hover-action-none" name="wp-wiki-tooltip-settings-base[trigger-hover-action]" value="none" <?php checked( $used_action, 'none', true ) ?> <?php disabled( $used_trigger, 'click', true ) ?> /><?php esc_html_e( 'Nothing! The link has no further function.', 'wp-wiki-tooltip' ); ?></label></li>
            <li><label><input type="radio" id="rdo-a-trigger-hover-action-open" name="wp-wiki-tooltip-settings-base[trigger-hover-action]" value="open" <?php checked( $used_action, 'open', true ) ?> <?php disabled( $used_trigger, 'click', true ) ?> /><?php esc_html_e( 'The linked Wiki page will be opened!', 'wp-wiki-tooltip' ); ?></label></li>
        </ul>
<?php
    }

    public function print_min_screen_width_field( $args ) {
        printf(
            '<p><label><input type="text" id="min-screen-width" name="wp-wiki-tooltip-settings-base[min-screen-width]" value="%s" class="small-text" style="text-align:right;" />' . esc_html__( 'px', 'wp-wiki-tooltip' ) . '</label></p>',
            isset( $this->options_base['min-screen-width'] ) ? esc_attr( $this->options_base[ 'min-screen-width' ] ) : esc_attr( $args[ 'min-screen-width' ] )
        );
        echo '<p class="description">' . esc_html__( 'Enable tooltips only if the width of the used display is greater than this defined number of pixel.', 'wp-wiki-tooltip' ) . '</p>';
    }

    /********************************************************
     * Error-Handling Settings Fields
     *******************************************************/
    public function print_page_error_handling_fields( $args ) {
	    $used_error_handling = isset( $this->options_error[ 'page-error-handling' ] ) ? $this->options_error[ 'page-error-handling' ] : $args[ 'page-error-handling' ];
	    $not_used_show_own = ( $used_error_handling === 'show-own' ) ? false : true;
	    $not_used_show_page = ( $used_error_handling === 'show-page' ) ? false : true;

        echo '<p>' . esc_html__( 'What should happen if the linked Wiki page is not available, e.g. if the Wiki is under construction?', 'wp-wiki-tooltip' ) . '</p>';
	    echo '<p><label><input type="radio" id="rdo-page-error-handling-show-default" name="wp-wiki-tooltip-settings-error[page-error-handling]" value="show-default" ' . checked( $used_error_handling, 'show-default', false ) . ' onclick="disable_page_error_handling_fields( true, true );" />' . esc_html__( 'show default error title and message in tooltip', 'wp-wiki-tooltip' ) . '</label></p>';
	    echo '<p><label><input type="radio" id="rdo-page-error-handling-show-own" name="wp-wiki-tooltip-settings-error[page-error-handling]" value="show-own" ' . checked( $used_error_handling, 'show-own', false ) . ' onclick="disable_page_error_handling_fields( false, true );" />' . esc_html__( 'show your own error title and message in tooltip', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<ul class="wiki-form-indent-left wiki-page-error-handling-list">';
	    printf(
		    '<li><label for="own-error-title">' . esc_html_x( 'Title:', 'error message', 'wp-wiki-tooltip' ) . '</label><input type="text" id="own-error-title" name="wp-wiki-tooltip-settings-error[own-error-title]" value="%s" class="regular-text" ' . disabled( true, $not_used_show_own, false ) . ' /></li>',
            ( ( 'show-own' == $used_error_handling ) && isset( $this->options_error[ 'own-error-title' ] ) ) ? esc_attr( $this->options_error[ 'own-error-title' ] ) : ''
	    );
	    printf(
		    '<li><label for="own-error-message">' . esc_html_x( 'Message:', 'error message', 'wp-wiki-tooltip' ) . '</label><textarea id="own-error-message" name="wp-wiki-tooltip-settings-error[own-error-message]" class="regular-text" ' . disabled( true, $not_used_show_own, false ) . ' >%s</textarea><br /><span id="own-error-message-desc" class="description">' . esc_html__( 'You can enter HTML here!', 'wp-wiki-tooltip' ) . '</span></span></li>',
            ( ( 'show-own' == $used_error_handling ) && isset( $this->options_error['own-error-message'] ) ) ? esc_attr( $this->options_error[ 'own-error-message' ] ) : ''
        );
        echo '</ul>';
	    echo '<p><label><input type="radio" id="rdo-page-error-handling-remove-link" name="wp-wiki-tooltip-settings-error[page-error-handling]" value="remove-link" ' . checked( $used_error_handling, 'remove-link', false ) . ' onclick="disable_page_error_handling_fields( true, true );" />' . esc_html__( 'remove the link completely', 'wp-wiki-tooltip' ) . ' (' . esc_html__( 'does not work for section errors', 'wp-wiki-tooltip' ) . ')</label></p>';
    }

	public function print_section_error_handling_fields( $args ) {
		$used_error_handling = isset( $this->options_error['section-error-handling'] ) ? $this->options_error['section-error-handling'] : $args['section-error-handling'];
		echo '<p>' . esc_html__( 'What should happen if a wanted section could not be found within Wiki page?', 'wp-wiki-tooltip' ) . '</p>';
		echo '<p><label><input type="radio" id="rdo-section-error-handling-show-default" name="wp-wiki-tooltip-settings-error[section-error-handling]" value="show-page" ' . checked( $used_error_handling, 'show-page', false ) . ' />' . esc_html__( 'show content of page', 'wp-wiki-tooltip' ) . '</label></p>';
		echo '<p><label><input type="radio" id="rdo-section-error-handling-show-own" name="wp-wiki-tooltip-settings-error[section-error-handling]" value="use-page-settings" ' . checked( $used_error_handling, 'use-page-settings', false ) . ' />' . esc_html__( 'use error handling of pages (see above)', 'wp-wiki-tooltip' ) . '</label></p>';
	}

    /********************************************************
     * Design Settings Fields
     *******************************************************/
	public function print_theme_field( $args ) {
        $used_theme = isset( $this->options_design[ 'theme' ] ) ? $this->options_design[ 'theme' ] : $args[ 'theme' ];

        $themes = array(
            'default' => _x( 'Default', 'tooltip theme name', 'wp-wiki-tooltip' ),
            'light' => _x( 'Light', 'tooltip theme name', 'wp-wiki-tooltip' ),
            'borderless' => _x( 'Borderless', 'tooltip theme name', 'wp-wiki-tooltip' ),
            'noir' => _x( 'Noir', 'tooltip theme name', 'wp-wiki-tooltip' ),
            'punk' => _x( 'Punk', 'tooltip theme name', 'wp-wiki-tooltip' ),
            'shadow' => _x( 'Shadow', 'tooltip theme name', 'wp-wiki-tooltip' )
        );

        echo '<ul id="wiki-tooltip-admin-theme-list">';
        foreach( $themes as $theme => $theme_label ) {
?>          <li>
                <label>
                    <input type="radio" id="rdo-theme-<?php echo esc_html( $theme ); ?>" name="wp-wiki-tooltip-settings-design[theme]" value="<?php echo esc_html( $theme ); ?>" <?php checked( $used_theme, $theme, true ); ?> />
                    <span id="tooltipster-theme-<?php echo esc_html( $theme ); ?>-preview" class="tooltipster-preview" title="<?php
                        printf( /* translators: parameter is used for the name of the tooltip themes */
                            esc_html__( 'This is a tooltip demo with &raquo;%s&laquo; theme&hellip;', 'wp-wiki-tooltip' ),
                            esc_html( $theme_label )
                        );
                    ?>"><?php echo esc_html( $theme_label ) ?></span>
                </label>
                <script>$wwtj( document ).ready( function() { enable_tooltip_theme_demo( '<?php echo esc_html( $theme ); ?>' ); } );</script>
            </li>
<?php
        }
        echo '</ul>';
	    echo '<p class="description">' . esc_html__( 'Hover over the icons to see a tooltip preview!', 'wp-wiki-tooltip' ) . '</p>';
    }

	public function print_animation_field( $args ) {
		$used_theme = isset( $this->options_design[ 'theme' ] ) ? $this->options_design[ 'theme' ] : $args[ 'theme' ];
		$used_animation = isset( $this->options_design[ 'animation' ] ) ? $this->options_design[ 'animation' ] : $args[ 'animation' ];

        $animations = array(
            'fade' => _x( 'Fade', 'tooltip animation name', 'wp-wiki-tooltip' ),
            'grow' => _x( 'Grow', 'tooltip animation name', 'wp-wiki-tooltip' ),
            'swing' => _x( 'Swing', 'tooltip animation name', 'wp-wiki-tooltip' ),
            'slide' => _x( 'Slide', 'tooltip animation name', 'wp-wiki-tooltip' ),
            'fall' => _x( 'Fall', 'tooltip animation name', 'wp-wiki-tooltip' ),
        );

        echo '<ul id="wiki-tooltip-admin-animation-list">';
        foreach( $animations as $animation => $animation_label ) {
?>          <li>
                <label>
                    <input type="radio" id="rdo-animation-<?php echo esc_html( $animation ); ?>" name="wp-wiki-tooltip-settings-design[animation]" value="<?php echo esc_html( $animation ); ?>" <?php checked( $used_animation, $animation, true ); ?> />
                    <span id="tooltipster-animation-<?php echo esc_html( $animation ); ?>-preview" class="tooltipster-animation-preview" title="<?php
                        printf( /* translators: parameter is used for the name of the tooltip animation */
                            esc_html__( 'This is a tooltip demo with &raquo;%s&laquo; animation...', 'wp-wiki-tooltip' ),
                            esc_html( $animation )
                        );
                    ?>"><?php echo esc_html( $animation_label ); ?></span>
                </label>
                <script>$wwtj( document ).ready( function() { enable_tooltip_animation_demo( '<?php echo esc_html( $used_theme ); ?>', '<?php echo esc_html( $animation ); ?>' ); } );</script>
            </li>
<?php
		}
		echo '</ul>';
		echo '<p class="description">' . esc_html__( 'Determines how the tooltip will animate in and out.', 'wp-wiki-tooltip' ) . '</p>';
	}

    public function print_tooltip_head_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-head" name="wp-wiki-tooltip-settings-design[tooltip-head]" value="%s" class="regular-text" /></p>',
            isset( $this->options_design['tooltip-head'] ) ? esc_attr( $this->options_design[ 'tooltip-head' ] ) : esc_attr( $args[ 'tooltip-head' ] )
        );
        echo '<p class="description">' . esc_html__( 'All entered CSS settings will be put into the CSS class of the header in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_tooltip_body_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-body" name="wp-wiki-tooltip-settings-design[tooltip-body]" value="%s" class="regular-text" /></p>',
            isset( $this->options_design['tooltip-body'] ) ? esc_attr( $this->options_design[ 'tooltip-body' ] ) : esc_attr( $args[ 'tooltip-body' ] )
        );
        echo '<p class="description">' . esc_html__( 'All entered CSS settings will be put into the CSS class of the body in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_tooltip_foot_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-foot" name="wp-wiki-tooltip-settings-design[tooltip-foot]" value="%s" class="regular-text" /></p>',
            isset( $this->options_design['tooltip-foot'] ) ? esc_attr( $this->options_design[ 'tooltip-foot' ] ) : esc_attr( $args[ 'tooltip-foot' ] )
        );
        echo '<p class="description">' . esc_html__( 'All entered CSS settings will be put into the CSS class of the footer in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_a_style_field( $args ) {
        printf(
            '<p><input type="text" id="a-style" name="wp-wiki-tooltip-settings-design[a-style]" value="%s" class="regular-text" /></p>',
            isset( $this->options_design['a-style'] ) ? esc_attr( $this->options_design[ 'a-style' ] ) : esc_attr( $args[ 'a-style' ] )
        );
        echo '<p class="description">' . esc_html__( 'All entered CSS settings will be put into the CSS class of the links to Wiki pages.', 'wp-wiki-tooltip' ) . '</p>';
    }

    /********************************************************
     * Thumbnail Settings Fields
     *******************************************************/
    public function print_thumb_enable_field( $args ) {
        $thumb_enabled = isset( $this->options_thumb[ 'thumb-enable' ] ) ? $this->options_thumb[ 'thumb-enable' ] : $args[ 'thumb-enable' ];

        echo '<p><label><input type="checkbox" id="cbo-thumb-enable" name="wp-wiki-tooltip-settings-thumb[thumb-enable]" value="on"' . checked( $thumb_enabled, 'on', false ) . ' />' . esc_html__( 'show thumbnails by default', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p class="description">' .esc_html__( 'A thumbnails will be displayed in tooltip if the Wiki article provides at least one picture.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_align_field( $args ) {
        $used_align = isset( $this->options_thumb[ 'thumb-align' ] ) ? $this->options_thumb[ 'thumb-align' ] : $args[ 'thumb-align' ];

        echo '<p><label><input type="radio" id="rdo-thumb-align-left" name="wp-wiki-tooltip-settings-thumb[thumb-align]" value="left"' . checked( $used_align, 'left', false ) . ' />' . esc_html__( 'left', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p><label><input type="radio" id="rdo-thumb-align-right" name="wp-wiki-tooltip-settings-thumb[thumb-align]" value="right" ' . checked( $used_align, 'right', false ) . ' />' . esc_html__( 'right', 'wp-wiki-tooltip' ) . '</label></p>';
    }

    public function print_thumb_width_field( $args ) {
        printf(
            '<p><label><input type="text" id="thumb-width" name="wp-wiki-tooltip-settings-thumb[thumb-width]" value="%s" class="small-text" style="text-align:right;" />' . esc_html__( 'px', 'wp-wiki-tooltip' ) . '</label></p>',
            isset( $this->options_thumb['thumb-width'] ) ? esc_attr( $this->options_thumb[ 'thumb-width' ] ) : esc_attr( $args[ 'thumb-width' ] )
        );
        echo '<p class="description">' . esc_html__( 'The height of the thumbnail is calculated respecting the side-ratio of the picture.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_style_field( $args ) {
        printf(
            '<p><input type="text" id="thumb-style" name="wp-wiki-tooltip-settings-thumb[thumb-style]" value="%s" class="regular-text" /></p>',
            isset( $this->options_thumb['thumb-style'] ) ? esc_attr( $this->options_thumb[ 'thumb-style' ] ) : esc_attr( $args[ 'thumb-style' ] )
        );
        echo '<p class="description">' . esc_html__( 'All entered CSS settings will be put into the CSS class of the thumbnail in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function sanitize_base_settings( $input ) {
        global $wp_wiki_tooltip_default_options;

        if( ! isset( $input[ 'nonce' ] ) || ! wp_verify_nonce( $input[ 'nonce' ], 'wp-wiki-tooltip-settings-base-submit' ) ) {
            $this->sanitize_stop();
        }

        // check wiki-urls
        if( isset( $input[ 'wiki-urls' ] ) && isset( $input[ 'wiki-urls' ][ 'data' ] ) ) {

            // just check if the URLs are valid
            $urls = $input[ 'wiki-urls' ][ 'data' ];
            if( is_array( $urls ) ) {
                foreach( $urls as $num => $url ) {
                    if( $num != '###NEWID###' ) {

                        // check the URL of wiki
                        if( false == wp_http_validate_url( $url[ 'url' ] ) ) {
                            $input[ 'wiki-urls' ][ 'data' ][ $num ][ 'url' ] = '';
                        }

                        // sanitize ID and SiteName of wiki
                        $url[ 'id' ] = sanitize_text_field( $url[ 'id' ] );
                        $url[ 'sitename' ] = sanitize_text_field( $url[ 'sitename' ] );
                    }
                }
            }

            // standard ID has to be numeric and less than the count of URLs
            $input[ 'wiki-urls' ][ 'standard' ] = ( int ) $input[ 'wiki-urls' ][ 'standard' ];
            if( $input[ 'wiki-urls' ][ 'standard' ] > count( $input[ 'wiki-urls' ][ 'data' ] ) - 1 ) {
                $input[ 'wiki-urls' ][ 'standard' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'wiki-urls' ][ 'standard' ];
            }
        } else {
            $input[ 'wiki-urls' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'wiki-urls' ];
        }

        // check a-target
        if( ( ! isset( $input[ 'a-target' ] ) ) || ( ! in_array( $input[ 'a-target' ], $wp_wiki_tooltip_default_options[ 'base' ][ 'a-target-range' ] ) ) ) {
            $input[ 'a-target' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'a-target' ];
        }

        // check trigger
        if( ( ! isset( $input[ 'trigger' ] ) ) || ( ! in_array( $input[ 'trigger' ], $wp_wiki_tooltip_default_options[ 'base' ][ 'trigger-range' ] ) ) ) {
            $input[ 'trigger' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'trigger' ];
        }

        // check trigger hover action
        if( 'hover' == $input[ 'trigger' ] ) {
            if( ( ! isset( $input[ 'trigger-hover-action' ] ) ) || ( ! in_array( $input[ 'trigger-hover-action' ], $wp_wiki_tooltip_default_options[ 'base' ][ 'trigger-hover-action-range' ] ) ) ) {
                $input[ 'trigger-hover-action' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'trigger-hover-action' ];
            }
        }

        // check min screen width
        $input[ 'min-screen-width' ] = ( int ) $input[ 'min-screen-width' ];
        if( 0 > $input[ 'min-screen-width' ] ) {
            $input[ 'min-screen-width' ] = $wp_wiki_tooltip_default_options[ 'base' ][ 'min-screen-width' ];
        }

        return $input;
    }

    public function sanitize_error_settings( $input ) {
        global $wp_wiki_tooltip_default_options;

        if( ! isset( $input[ 'nonce' ] ) || ! wp_verify_nonce( $input[ 'nonce' ], 'wp-wiki-tooltip-settings-error-submit' ) ) {
            $this->sanitize_stop();
        }

        // check page error handling
        if( ( ! isset( $input[ 'page-error-handling' ] ) ) || ( ! in_array( $input[ 'page-error-handling' ], $wp_wiki_tooltip_default_options[ 'error' ][ 'page-error-handling-range' ] ) ) ) {
            $input[ 'page-error-handling' ] = $wp_wiki_tooltip_default_options[ 'error' ][ 'page-error-handling-range' ];
        }

        // sanitize own error title and message
        if( 'show-own' == $input[ 'page-error-handling' ] ) {
            $input[ 'own-error-title' ] = sanitize_text_field( $input[ 'own-error-title' ] );
            $input[ 'own-error-message' ] = esc_html( $input[ 'own-error-message' ] );
        }

        // check section error handling
        if( ( ! isset( $input[ 'section-error-handling' ] ) ) || ( ! in_array( $input[ 'section-error-handling' ], $wp_wiki_tooltip_default_options[ 'error' ][ 'section-error-handling-range' ] ) ) ) {
            $input[ 'section-error-handling' ] = $wp_wiki_tooltip_default_options[ 'error' ][ 'section-error-handling-range' ];
        }

        return $input;
    }

    public function sanitize_design_settings( $input ) {
        global $wp_wiki_tooltip_default_options;

        if( ! isset( $input[ 'nonce' ] ) || ! wp_verify_nonce( $input[ 'nonce' ], 'wp-wiki-tooltip-settings-design-submit' ) ) {
            $this->sanitize_stop();
        }

        // check tooltip's design
        if( ( ! isset( $input[ 'theme' ] ) ) || ( ! in_array( $input[ 'theme' ], $wp_wiki_tooltip_default_options[ 'design' ][ 'theme-range' ] ) ) ) {
            $input[ 'theme' ] = $wp_wiki_tooltip_default_options[ 'design' ][ 'theme' ];
        }

        // check tooltip's animation
        if( ( ! isset( $input[ 'animation' ] ) ) || ( ! in_array( $input[ 'animation' ], $wp_wiki_tooltip_default_options[ 'design' ][ 'animation-range' ] ) ) ) {
            $input[ 'animation' ] = $wp_wiki_tooltip_default_options[ 'design' ][ 'animation' ];
        }

        // sanitize all style inputs
        $input[ 'tooltip-head' ] = sanitize_text_field( $input[ 'tooltip-head' ] );
        $input[ 'tooltip-body' ] = sanitize_text_field( $input[ 'tooltip-body' ] );
        $input[ 'tooltip-foot' ] = sanitize_text_field( $input[ 'tooltip-foot' ] );
        $input[ 'a-style' ] = sanitize_text_field( $input[ 'a-style' ] );

        return $input;
    }

    public function sanitize_thumb_settings( $input ) {
        global $wp_wiki_tooltip_default_options;

        if( ! isset( $input[ 'nonce' ] ) || ! wp_verify_nonce( $input[ 'nonce' ], 'wp-wiki-tooltip-settings-thumb-submit' ) ) {
            $this->sanitize_stop();
        }

        // check tooltip's enabling
        if( ( ! isset( $input[ 'thumb-enable' ] ) ) || ( ! in_array( $input[ 'thumb-enable' ], $wp_wiki_tooltip_default_options[ 'thumb' ][ 'thumb-enable-range' ] ) ) ) {
            $input[ 'thumb-enable' ] = $wp_wiki_tooltip_default_options[ 'thumb' ][ 'thumb-enable' ];
        }

        // check tooltip's alignment
        if( ( ! isset( $input[ 'thumb-align' ] ) ) || ( ! in_array( $input[ 'thumb-align' ], $wp_wiki_tooltip_default_options[ 'thumb' ][ 'thumb-align-range' ] ) ) ) {
            $input[ 'thumb-align' ] = $wp_wiki_tooltip_default_options[ 'thumb' ][ 'thumb-align' ];
        }

        // check thumbnail's width
        $input[ 'thumb-width' ] = ( int ) $input[ 'thumb-width' ];
        if( 0 > $input[ 'thumb-width' ] ) {
            $input[ 'thumb-width' ] = $wp_wiki_tooltip_default_options[ 'thumb' ][ 'thumb-width' ];
        }

        // sanitize thumbnail's style
        $input[ 'thumb-style' ] = sanitize_text_field( $input[ 'thumb-style' ] );

        return $input;
    }

    public function sanitize_stop() {
        wp_die( esc_html_x( 'Sorry, but this request seems to be invalid!', 'nonce check invalid', 'wp-wiki-tooltip' ) );
    }

    public function settings_page( $active_tab = 'base' ) {

        $tabs = array(
            'base' => esc_html_x( 'Base Settings', 'settings tab title', 'wp-wiki-tooltip' ),
            'error' => esc_html_x( 'Error Handling', 'settings tab title', 'wp-wiki-tooltip' ),
            'design' => esc_html_x( 'Design', 'settings tab title', 'wp-wiki-tooltip' ),
            'thumb' => esc_html_x( 'Thumbnail', 'settings tab title', 'wp-wiki-tooltip' )
        );

        if ( isset( $_REQUEST[ 'wiki-tab-nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'wiki-tab-nonce' ] ) ), 'wp-wiki-tooltip-settings-tab' ) ) {
            if (isset($_GET['tab']) && in_array($_GET['tab'], array_keys($tabs))) {
                $active_tab = sanitize_key($_GET['tab']);
            }
        }

        ?>
        <div class="wrap">
            <h2><?php esc_html_e( 'Wiki Tooltips Settings', 'wp-wiki-tooltip' ) ?></h2>
            <p class="wiki-usage"><?php esc_html_e( 'Use one of these shortcodes to enable Wiki Tooltips:', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki]WordPress[/wiki]</span>&nbsp;<?php esc_html_e( 'or', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki title="WordPress"]a nice blogging software[/wiki]</span></p>

            <h2 class="nav-tab-wrapper">
            <?php foreach ( $tabs as $tab => $tab_title ) {
                echo sprintf('<a href="%1$s" class="nav-tab %2$s">%3$s</a>',
                    esc_url( add_query_arg( array(
                        'page' => 'wp-wiki-tooltip-settings',
                        'tab' => $tab,
                        'wiki-tab-nonce' => wp_create_nonce( 'wp-wiki-tooltip-settings-tab' )
                    ), admin_url('options-general.php') ) ),
                    esc_html( ( $active_tab == $tab ) ? 'nav-tab-active' : '' ),
                    esc_html( $tab_title )
                );
            } ?>
            </h2>

            <form method="post" action="options.php">
                <?php

                if( $active_tab == 'error' ) {

                    wp_nonce_field('wp-wiki-tooltip-settings-error-submit', 'wp-wiki-tooltip-settings-error[nonce]' );
                    settings_fields( 'wp-wiki-tooltip-settings-error' );
                    do_settings_sections( 'wp-wiki-tooltip-settings-error' );

                } elseif( $active_tab == 'design' ) {

                    wp_nonce_field('wp-wiki-tooltip-settings-design-submit', 'wp-wiki-tooltip-settings-design[nonce]' );
                    settings_fields( 'wp-wiki-tooltip-settings-design' );
                    do_settings_sections( 'wp-wiki-tooltip-settings-design' );

                } elseif( $active_tab == 'thumb' ) {

                    wp_nonce_field('wp-wiki-tooltip-settings-thumb-submit', 'wp-wiki-tooltip-settings-thumb[nonce]' );
                    settings_fields('wp-wiki-tooltip-settings-thumb' );
                    do_settings_sections('wp-wiki-tooltip-settings-thumb' );

                } else {

                    wp_nonce_field('wp-wiki-tooltip-settings-base-submit', 'wp-wiki-tooltip-settings-base[nonce]' );
                    settings_fields( 'wp-wiki-tooltip-settings-base' );
                    do_settings_sections( 'wp-wiki-tooltip-settings-base' );
                }

                submit_button( esc_html__( 'Submit', 'wp-wiki-tooltip' ), 'primary', 'btn_submit', false );
                echo "&nbsp;&nbsp;&nbsp;";
                submit_button( esc_html__( 'Reset', 'wp-wiki-tooltip' ), 'secondary', 'btn_reset', false );
                ?>
            </form>
        </div>
        <?php
    }

    public function settings_page_base() {
        $this->settings_page( 'base');
    }

    public function settings_page_error() {
        $this->settings_page( 'error' );
    }

    public function settings_page_design() {
        $this->settings_page( 'design' );
    }

    public function settings_page_thumb() {
        $this->settings_page( 'thumb' );
    }
}