<?php

include_once('class.wp-wiki-tooltip-base.php');

/**
 * Class WP_Wiki_Tooltip_MCE
 */
class WP_Wiki_Tooltip_MCE extends WP_Wiki_Tooltip_Base {

    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );

        foreach ( array('post.php','post-new.php') as $hook ) {
            add_action( 'admin_head-' . $hook, array( $this, 'mce_admin_head' ) );
        }
    }

    public function init() {
        // check user permissions
	    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

	    // check if WYSIWYG is enabled
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
	        add_filter( 'mce_external_plugins', array( $this, 'add_buttons' ) );
            add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
            add_filter( 'mce_external_languages', array( $this, 'add_wp_wiki_tooltip_mce_locale' ) );
        }
    }

    public function mce_admin_head() {
        $this->options = get_option( 'wp-wiki-tooltip-settings' );

        ?><script type='text/javascript'>
            var wwtj_strings;
            var wp_wiki_tooltip_mce = {
                'wiki_urls': <?php echo wp_json_encode( $this->options[ 'wiki-urls' ] ); ?>,
            };
        </script><?php

	    // language support for javascript since Gutenberg
        if( function_exists( 'wp_set_script_translations' ) ) {
	        wp_register_script( 'wp-wiki-tooltip-mce-lang-js', plugins_url( 'static/js/wp-wiki-tooltip-mce-lang.js', __FILE__ ), array( 'wp-i18n' ), $this->version, false );
	        wp_set_script_translations( 'wp-wiki-tooltip-mce-lang-js', 'wp-wiki-tooltip' );
	        wp_enqueue_script( 'wp-wiki-tooltip-mce-lang-js' );
        }
    }

    public function add_buttons( $plugin_array ) {
        $plugin_array[ 'wp_wiki_tooltip' ] = plugins_url( 'static/js/wp-wiki-tooltip-mce.js', __FILE__ );
        return $plugin_array;
    }

    public function register_buttons( $buttons ) {
        array_push( $buttons, 'wp_wiki_tooltip' );
        return $buttons;
    }

    function add_wp_wiki_tooltip_mce_locale( $locales ) {
        wp_dequeue_script( 'wp-wiki-tooltip-mce-lang-js' );
        $locales[ 'wp_wiki_tooltip' ] = plugin_dir_path( __FILE__ ) . 'wp-wiki-tooltip-mce-langs.php';
        return $locales;
    }
}