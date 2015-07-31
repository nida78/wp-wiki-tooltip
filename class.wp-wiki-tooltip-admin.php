<?php
/**
 * Class WP_Wiki_Tooltip_Admin
 */
class WP_Wiki_Tooltip_Admin {

    public function __construct( $name='') {
        add_filter( 'plugin_action_links_' . $name, array( $this, 'add_action_links' ) );
        add_action( 'admin_menu', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function init() {
        add_options_page( __( 'Settings for Wiki-Tooltips', 'wp-wiki-tooltip' ), __( 'Wiki-Tooltips', 'wp-wiki-tooltip' ), 'manage_options', 'wp-wiki-tooltip-settings', array( $this, 'settings_page' ) );
    }

    public function add_action_links( $links ) {
        return array_merge(
            $links,
            array( '<a href="' . admin_url( 'options-general.php?page=wp-wiki-tooltip-settings' ) . '">' . __( 'Settings', 'wp-wiki-tooltip' ) . '</a>', )
        );
    }

    public function register_settings() {
        register_setting( 'wp-wiki-tooltip-base-settings', 'wp-wiki-tooltip_wiki-url' );
    }

    public function settings_page() {
?>
        <div class="wrap">
            <h2><?php _e( 'Settings for Wiki-Tooltips', 'wp-wiki-tooltip' ) ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'wp-wiki-tooltip-base-settings' ); ?>
                <?php do_settings_sections( 'wp-wiki-tooltip-base-settings' ); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'URL of Wiki', 'wp-wiki-tooltip' ); ?></th>
                        <td><input type="text" name="wp-wiki-tooltip_wiki-url" value="<?php echo esc_attr( get_option( 'wp-wiki-tooltip_wiki-url' ) ); ?>" /></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>';
<?php
    }
}