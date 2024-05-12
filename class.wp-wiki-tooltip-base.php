<?php
/**
 * Class WP_Wiki_Tooltip_Base
 */
class WP_Wiki_Tooltip_Base {

    protected $version = '2.0.2';

    protected $tooltipster_version = '4.2.8';

    protected $options_base = false;

    protected $options_error = false;

    protected $options_design = false;

    protected $options_thumb = false;

    protected function load_single_option( $default_key, $old_options, $new_options, $default_options, $keys ) {

        if( false == $new_options ) {
            if( false != $old_options ) {
                foreach( $keys as $key ) {
                    if( isset( $old_options[ $key ] ) ) {
                        $new_options[ $key ] = $old_options[ $key ];
                    }
                }
            } else {
                $new_options = $default_options[ $default_key ];
            }
        }

        return $new_options;
    }

    public function load_all_options() {
        global $wp_wiki_tooltip_default_options;
        $old_options = get_option( 'wp-wiki-tooltip-settings' );

        $this->options_base = $this->load_single_option(
            'base',
            $old_options,
            get_option( 'wp-wiki-tooltip-settings-base' ),
            $wp_wiki_tooltip_default_options,
            array( 'wiki-urls', 'a-target', 'trigger', 'trigger-hover-action', 'min-screen-width' )
        );

        $this->options_error = $this->load_single_option(
            'error',
            $old_options,
            get_option( 'wp-wiki-tooltip-settings-error' ),
            $wp_wiki_tooltip_default_options,
            array( 'page-error-handling', 'own-error-title', 'own-error-message', 'section-error-handling' )
        );

        $this->options_design = $this->load_single_option(
            'design',
            $old_options,
            get_option( 'wp-wiki-tooltip-settings-design' ),
            $wp_wiki_tooltip_default_options,
            array( 'theme', 'animation', 'tooltip-head', 'tooltip-body', 'tooltip-foot', 'a-style', 'custom-go-to-wiki-link' )
        );

        $this->options_thumb = $this->load_single_option(
            'thumb',
            $old_options,
            get_option( 'wp-wiki-tooltip-settings-thumb' ),
            $wp_wiki_tooltip_default_options,
            array( 'thumb-enable', 'thumb-align', 'thumb-width', 'thumb-style' )
        );
    }

    public static function log( $msg = '' ) {
        if ( true === WP_DEBUG ) {
            if ( is_array( $msg ) || is_object( $msg ) ) {
                error_log( print_r( $msg, true ) );
            } else {
                error_log( $msg );
            }
        }
    }
}