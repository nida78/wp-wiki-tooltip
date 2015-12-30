<?php

include_once('class.wp-wiki-tooltip-base.php');
include_once('class.wp-wiki-tooltip-comm.php');

/**
 * Class WP_Wiki_Tooltip_Admin
 */
class WP_Wiki_Tooltip_Admin extends WP_Wiki_Tooltip_Base {

    public function __construct( $name='' ) {
        add_filter( 'plugin_action_links_' . $name, array( $this, 'add_action_links' ) );
        add_action( 'admin_menu', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        $comm = new WP_Wiki_Tooltip_Comm();
        add_action( 'wp_ajax_get_wiki_page', array( $comm, 'ajax_get_wiki_page' ) );
        add_action( 'wp_ajax_nopriv_get_wiki_page', array( $comm, 'ajax_get_wiki_page' ) );
        add_action( 'wp_ajax_test_wiki_url', array( $comm, 'ajax_test_wiki_url' ) );
        add_action( 'wp_ajax_nopriv_test_wiki_url', array( $comm, 'ajax_test_wiki_url' ) );
    }

    public function init() {
        wp_enqueue_style( 'wp-wiki-tooltip-admin-css', plugins_url( 'static/css/wp-wiki-tooltip-admin.css', __FILE__ ), array(), $this->version, 'all' );

        wp_register_script( 'wp-wiki-tooltip-admin-js', plugins_url( 'static/js/wp-wiki-tooltip-admin.js', __FILE__ ), array( 'jquery' ), $this->version, false );
        wp_localize_script( 'wp-wiki-tooltip-admin-js', 'wp_wiki_tooltip_admin', array(
            'alert_remove' => __( 'Rows that is marked as "Standard" could not be deleted!', 'wp-wiki-tooltip' ),
            'alert_test_failed' => __( 'Sorry, but the test of this URL failed!', 'wp-wiki-tooltip' ),
            'wp_ajax_url' => admin_url( 'admin-ajax.php' )
        ));
        wp_enqueue_script( 'wp-wiki-tooltip-admin-js' );

        add_options_page(
            __( 'Settings for Wiki-Tooltips', 'wp-wiki-tooltip' ),
            __( 'Wiki-Tooltips', 'wp-wiki-tooltip' ),
            'manage_options',
            'wp-wiki-tooltip-settings',
            array( $this, 'settings_page' )
        );

        $this->options = get_option( 'wp-wiki-tooltip-settings' );

        if( array_key_exists( 'btn_reset', $_REQUEST ) && $_REQUEST[ 'btn_reset' ] == __( 'Reset', 'wp-wiki-tooltip' ) ) {
            $result = ( delete_option( 'wp-wiki-tooltip-settings' ) ) ? 'true' : 'false';
            header( 'Location: options-general.php?page=wp-wiki-tooltip-settings&settings-updated=reset-' . $result );
            die();
        }

        if( array_key_exists( 'settings-updated', $_REQUEST ) ) {
            if( $_REQUEST[ 'settings-updated' ] == 'reset-true' ) {
                add_settings_error(
                    'wp-wiki-tooltip-settings-reset',
                    'settings_updated',
                    __('Settings reseted sucessfully.', 'wp-wiki-tooltip'),
                    'updated'
                );
            } else if( $_REQUEST[ 'settings-updated' ] == 'reset-false' ) {
                add_settings_error(
                    'wp-wiki-tooltip-settings-reset',
                    'settings_updated',
                    __('An error occured while resetting.', 'wp-wiki-tooltip'),
                    'error'
                );
            }
        }
    }

    public function add_action_links( $links ) {
        return array_merge(
            $links,
            array( '<a href="' . admin_url( 'options-general.php?page=wp-wiki-tooltip-settings' ) . '">' . __( 'Settings', 'wp-wiki-tooltip' ) . '</a>', )
        );
    }

    public function register_settings() {
        global $wp_wiki_tooltip_default_options;

        register_setting(
            'wp-wiki-tooltip-settings',
            'wp-wiki-tooltip-settings',
            array( $this, 'sanitize' )
        );

        /*** Base Settings ***/
        add_settings_section(
            'wp-wiki-tooltip-settings-base',
            __( 'Base Settings', 'wp-wiki-tooltip' ),
            array( $this, 'print_base_section_info' ),
            'wp-wiki-tooltip-settings-base'
        );

        add_settings_field(
            'wiki-urls',
            __( 'URLs of Wikis', 'wp-wiki-tooltip' ),
            array( $this, 'print_wiki_url_fields' ),
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'a-target',
            __( 'Open links to Wiki pages in', 'wp-wiki-tooltip' ),
            array( $this, 'print_a_target_field' ),
            'wp-wiki-tooltip-settings-base',
            'wp-wiki-tooltip-settings-base',
            $wp_wiki_tooltip_default_options
        );

        /*** Design Settings ***/
        add_settings_section(
            'wp-wiki-tooltip-settings-design',
            __( 'Design Settings', 'wp-wiki-tooltip' ),
            array( $this, 'print_design_section_info' ),
            'wp-wiki-tooltip-settings-design'
        );

        add_settings_field(
            'theme',
            __( 'Design of the tooltips', 'wp-wiki-tooltip' ),
            array( $this, 'print_theme_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'tooltip-head',
            __( 'Tooltip <em>header</em> styles', 'wp-wiki-tooltip' ),
            array( $this, 'print_tooltip_head_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'tooltip-body',
            __( 'Tooltip <em>body</em> styles', 'wp-wiki-tooltip' ),
            array( $this, 'print_tooltip_body_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'tooltip-foot',
            __( 'Tooltip <em>footer</em> styles', 'wp-wiki-tooltip' ),
            array( $this, 'print_tooltip_foot_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'a-styles',
            __( 'Wiki links styles', 'wp-wiki-tooltip' ),
            array( $this, 'print_a_style_field' ),
            'wp-wiki-tooltip-settings-design',
            'wp-wiki-tooltip-settings-design',
            $wp_wiki_tooltip_default_options
        );

        /*** Thumbnail Settings ***/
        add_settings_section(
            'wp-wiki-tooltip-settings-thumb',
            __( 'Thumbnail Settings', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_section_info' ),
            'wp-wiki-tooltip-settings-thumb'
        );

        add_settings_field(
            'thumb-enable',
            __( 'Enable thumbnails', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_enable_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'thumb-align',
            __( 'Alignment', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_align_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'thumb-width',
            __( 'Width', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_width_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb',
            $wp_wiki_tooltip_default_options
        );

        add_settings_field(
            'thumb-style',
            __( 'Styles', 'wp-wiki-tooltip' ),
            array( $this, 'print_thumb_style_field' ),
            'wp-wiki-tooltip-settings-thumb',
            'wp-wiki-tooltip-settings-thumb',
            $wp_wiki_tooltip_default_options
        );
    }

    public function print_base_section_info() {
        echo '<p>' . __( 'Set base options below:', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_design_section_info() {
        echo '<p>' . __( 'Set design / style options below:' , 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_section_info() {
        echo '<p>' . __( 'Enable thumbnails in tooltips and set some useful options below.' , 'wp-wiki-tooltip' ) . '</p>';
        echo '<p class="wiki-usage">' . __( 'Additionally an extra "<strong>thumbnail</strong>" attribute can be added to the shortcode:' , 'wp-wiki-tooltip' );
        echo '&nbsp;<span class="bold-teletyper">[wiki thumbnail="on"]WordPress[/wiki]</span>&nbsp;' . __( 'or', 'wp-wiki-tooltip' ) . '&nbsp;<span class="bold-teletyper">[wiki thumbnail="off" title="WordPress"]a nice blogging software[/wiki]</span></p>';
    }

    public function print_wiki_url_fields( $args ) {
        $standard_url = isset( $this->options[ 'wiki-urls' ][ 'standard' ] ) ? $this->options[ 'wiki-urls' ][ 'standard' ] : $args[ 'wiki-urls' ][ 'standard' ];
        $urls =  isset( $this->options[ 'wiki-urls' ][ 'data' ] ) ? $this->options[ 'wiki-urls' ][ 'data' ] : $args[ 'wiki-urls' ][ 'data' ];
        ?>

        <p><?php _e( 'Enter as much Wiki URLs as you like. Click the button "<strong>test</strong>" to let the plugin check if the given URL has access to a Wiki API.' , 'wp-wiki-tooltip' ); ?></p>
        <p class="wiki-usage"><?php _e( 'To use one of these URLs just add an "<strong>base</strong>" attribute to the shortcode:', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki base="ID"]WordPress[/wiki]</span>&nbsp;<?php _e( 'or', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki base="ID" title="WordPress"]a nice blogging software[/wiki]</span></p>
        <p class="wiki-usage"><?php _e( 'If you do not use the "<strong>base</strong>" attribute the URL that is marked as "Standard" is used.' , 'wp-wiki-tooltip' ) ?></p>

        <input type="hidden" id="wp-wiki-tooltip-url-count" name="wp-wiki-tooltip-url-count" value="<?php echo sizeof( $urls ); ?>" />
        <table id="wiki-urls-table">
            <tr>
                <th class="row1"><?php _e( 'Standard', 'wp-wiki-tooltip' ); ?></th>
                <th class="row2"><?php _e( 'Name', 'wp-wiki-tooltip' ); ?></th>
                <th class="row3"><?php _e( 'ID', 'wp-wiki-tooltip' ); ?></th>
                <th class="row4"><?php _e( 'URL', 'wp-wiki-tooltip' ); ?></th>
                <th class="row5"><?php _e( 'Check URL', 'wp-wiki-tooltip' ); ?></th>
                <th class="row6"><?php _e( 'Remove URL', 'wp-wiki-tooltip' ); ?></th>
            </tr>

            <?php foreach( $urls as $num => $url ) : if( $num != '###NEWID###' ) : ?>
                <tr id="wiki-url-row-<?php echo $num; ?>">
                    <td class="row1"><input id="rdo-wiki-url-row-<?php echo $num; ?>" type="radio" name="wp-wiki-tooltip-settings[wiki-urls][standard]" value="<?php echo $num; ?>" <?php checked( $num, $standard_url, true ); ?> class="radio"/></td>
                    <td class="row2"><input id="txt-site-wiki-url-row-<?php echo $num; ?>" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][<?php echo $num; ?>][sitename]" value="<?php echo $url[ 'sitename' ]; ?>" class="regular-text"/></td>
                    <td class="row3"><input id="txt-id-wiki-url-row-<?php echo $num; ?>" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][<?php echo $num; ?>][id]" value="<?php echo $url[ 'id' ]; ?>" class="narrow"/></td>
                    <td class="row4"><input id="txt-url-wiki-url-row-<?php echo $num; ?>" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][<?php echo $num; ?>][url]" value="<?php echo $url[ 'url' ]; ?>" class="regular-text"/></td>
                    <td class="row5"><input id="btn-test-wiki-url-row-<?php echo $num; ?>" type="button" value="<?php _e( 'test', 'wp-wiki-tooltip' ); ?>" class="button" onclick="test_wiki_url_row( 'wiki-url-row-<?php echo $num; ?>' );"/><img src="<?php echo plugins_url( '/static/images/loadingAnimationBar.gif', __FILE__ ); ?>" class="loadingAnimationBar" /></td>
                    <td class="row6"><input type="button" value="<?php _e( 'remove', 'wp-wiki-tooltip' ); ?>" class="button" onclick="remove_wiki_url_row( 'wiki-url-row-<?php echo $num; ?>' );"/></td>
                </tr>
            <?php endif; endforeach; ?>

            <tr id="wiki-url-row-template">
                <td class="row1"><input id="rdo-wiki-url-row-###NEWID###" type="radio" name="wp-wiki-tooltip-settings[wiki-urls][standard]" value="###NEWID###" class="radio"/></td>
                <td class="row2"><input id="txt-site-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][###NEWID###][sitename]" value="" class="regular-text"/></td>
                <td class="row3"><input id="txt-id-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][###NEWID###][id]" value="" class="narrow"/></td>
                <td class="row4"><input id="txt-url-wiki-url-row-###NEWID###" type="text" name="wp-wiki-tooltip-settings[wiki-urls][data][###NEWID###][url]" value="" class="regular-text"/></td>
                <td class="row5"><input id="btn-test-wiki-url-row-###NEWID###" type="button" value="<?php _e( 'test', 'wp-wiki-tooltip' ); ?>" class="button" onclick="test_wiki_url_row( 'wiki-url-row-###NEWID###' );"/><img src="<?php echo plugins_url( '/static/images/loadingAnimationBar.gif', __FILE__ ); ?>" class="loadingAnimationBar" /></td>
                <td class="row6"><input type="button" value="<?php _e( 'remove', 'wp-wiki-tooltip' ); ?>" class="button" onclick="remove_wiki_url_row( 'wiki-url-row-###NEWID###' );"/></td>
            </tr>
            <tr>
                <td colspan="6"><input type="button" value="<?php _e( 'Add new URL', 'wp-wiki-tooltip' ); ?>" class="button" onclick="add_wiki_url_row();" /></td>
            </tr>
        </table>
        <?php
    }

    public function print_a_target_field( $args ) {
        $used_target = isset( $this->options[ 'a-target' ] ) ? $this->options[ 'a-target' ] : $args[ 'a-target' ];

        echo '<p><label><input type="radio" id="rdo-a-target-blank" name="wp-wiki-tooltip-settings[a-target]" value="_blank"' . checked( $used_target, '_blank', false ) . ' />' . __( 'new window / tab', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p><label><input type="radio" id="rdo-a-target-self" name="wp-wiki-tooltip-settings[a-target]" value="_self" ' . checked( $used_target, '_self', false ) . ' />' . __( 'current window / tab', 'wp-wiki-tooltip' ) . '</label></p>';
    }

    public function print_theme_field( $args ) {
        $used_theme = isset( $this->options[ 'theme' ] ) ? $this->options[ 'theme' ] : $args[ 'theme' ];

        echo '<ul id="wiki-tooltip-admin-theme-list">';
        foreach( array( 'default', 'light', 'noir', 'punk', 'shadow' ) as $theme ) {
            echo '<li><label>';
            echo '<input type="radio" id="rdo-theme-' . $theme . '" name="wp-wiki-tooltip-settings[theme]" value="' . $theme . '"' . checked($used_theme, $theme, false) . ' />';
            echo '<span class="tooltipster-' . $theme . '-preview">' . $theme . '</span>';
            echo '</label></li>';
        }
        echo '</ul>';
    }

    public function print_tooltip_head_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-head" name="wp-wiki-tooltip-settings[tooltip-head]" value="%s" class="regular-text" /></p>',
            isset( $this->options['tooltip-head'] ) ? esc_attr( $this->options[ 'tooltip-head' ] ) : $args[ 'tooltip-head' ]
        );
        echo '<p class="description">' . __( 'All entered CSS settings will be put into the CSS class of the header in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_tooltip_body_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-body" name="wp-wiki-tooltip-settings[tooltip-body]" value="%s" class="regular-text" /></p>',
            isset( $this->options['tooltip-body'] ) ? esc_attr( $this->options[ 'tooltip-body' ] ) : $args[ 'tooltip-body' ]
        );
        echo '<p class="description">' . __( 'All entered CSS settings will be put into the CSS class of the body in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_tooltip_foot_field( $args ) {
        printf(
            '<p><input type="text" id="tooltip-foot" name="wp-wiki-tooltip-settings[tooltip-foot]" value="%s" class="regular-text" /></p>',
            isset( $this->options['tooltip-foot'] ) ? esc_attr( $this->options[ 'tooltip-foot' ] ) : $args[ 'tooltip-foot' ]
        );
        echo '<p class="description">' . __( 'All entered CSS settings will be put into the CSS class of the footer in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_a_style_field( $args ) {
        printf(
            '<p><input type="text" id="a-style" name="wp-wiki-tooltip-settings[a-style]" value="%s" class="regular-text" /></p>',
            isset( $this->options['a-style'] ) ? esc_attr( $this->options[ 'a-style' ] ) : $args[ 'a-style' ]
        );
        echo '<p class="description">' . __( 'All entered CSS settings will be put into the CSS class of the links to Wiki pages.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_enable_field( $args ) {
        $thumb_enabled = isset( $this->options[ 'thumb-enable' ] ) ? $this->options[ 'thumb-enable' ] : $args[ 'thumb-enable' ];

        echo '<p><label><input type="checkbox" id="cbo-thumb-enable" name="wp-wiki-tooltip-settings[thumb-enable]" value="on"' . checked( $thumb_enabled, 'on', false ) . ' />' . __( 'show thumbnails by default', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p class="description">' . __( 'A thumbnails will be displayed in tooltip if the Wiki article provides at least one picture.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_align_field( $args ) {
        $used_align = isset( $this->options[ 'thumb-align' ] ) ? $this->options[ 'thumb-align' ] : $args[ 'thumb-align' ];

        echo '<p><label><input type="radio" id="rdo-thumb-align-left" name="wp-wiki-tooltip-settings[thumb-align]" value="left"' . checked( $used_align, 'left', false ) . ' />' . __( 'left', 'wp-wiki-tooltip' ) . '</label></p>';
        echo '<p><label><input type="radio" id="rdo-thumb-align-right" name="wp-wiki-tooltip-settings[thumb-align]" value="right" ' . checked( $used_align, 'right', false ) . ' />' . __( 'right', 'wp-wiki-tooltip' ) . '</label></p>';
    }

    public function print_thumb_width_field( $args ) {
        printf(
            '<p><label><input type="text" id="thumb-width" name="wp-wiki-tooltip-settings[thumb-width]" value="%s" class="small-text" style="text-align:right;" />' . __( 'px', 'wp-wiki-tooltip' ) . '</label></p>',
            isset( $this->options['thumb-width'] ) ? esc_attr( $this->options[ 'thumb-width' ] ) : $args[ 'thumb-width' ]
        );
        echo '<p class="description">' . __( 'The height of the thumbnail is calculated respecting the side-ratio of the picture.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function print_thumb_style_field( $args ) {
        printf(
            '<p><input type="text" id="thumb-style" name="wp-wiki-tooltip-settings[thumb-style]" value="%s" class="regular-text" /></p>',
            isset( $this->options['thumb-style'] ) ? esc_attr( $this->options[ 'thumb-style' ] ) : $args[ 'thumb-style' ]
        );
        echo '<p class="description">' . __( 'All entered CSS settings will be put into the CSS class of the thumbnail in the tooltip.', 'wp-wiki-tooltip' ) . '</p>';
    }

    public function sanitize( $input ) {
        return $input;
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h2><?php _e( 'Settings for Wiki-Tooltips', 'wp-wiki-tooltip' ) ?></h2>
            <p class="wiki-usage"><?php _e( 'Use one of these shortcodes to enable Wiki-Tooltips:', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki]WordPress[/wiki]</span>&nbsp;<?php _e( 'or', 'wp-wiki-tooltip' ); ?>&nbsp;<span class="bold-teletyper">[wiki title="WordPress"]a nice blogging software[/wiki]</span></p>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'wp-wiki-tooltip-settings' );
                do_settings_sections( 'wp-wiki-tooltip-settings-base' );
                do_settings_sections( 'wp-wiki-tooltip-settings-design' );
                do_settings_sections( 'wp-wiki-tooltip-settings-thumb' );
                submit_button( __( 'Submit', 'wp-wiki-tooltip' ), 'primary', 'btn_submit', false );
                echo "&nbsp;&nbsp;&nbsp;";
                submit_button( __( 'Reset', 'wp-wiki-tooltip' ), 'secondary', 'btn_reset', false );
                ?>
            </form>
        </div>
        <?php
    }
}