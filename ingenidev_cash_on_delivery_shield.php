<?php
/**
 * Plugin Name: ingenidev CashOnDelivery Shield
 * Plugin URI: https://ingenidev.com/cod-shield-wordpress-plugin/
 * Author: ingenidev
 * Author URI: https://ingenidev.com
 * Description: This plugin provides an option for the default WooCommerce Cash On delivery payment method to be disabled for unknown users.
 * Version: 1.0.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('Direct access not permitted');

add_filter('woocommerce_available_payment_gateways', 'ingenidev_cods_disable_cod_for_unknown_customer_phones');

function ingenidev_cods_disable_cod_for_unknown_customer_phones($available_gateways) {
 if (!is_user_logged_in() && isset($available_gateways['cod'])) {
 unset($available_gateways['cod']);
 }

 return $available_gateways;
}
register_activation_hook(__FILE__, 'ingenidev_cods_activate');

function ingenidev_cods_activate()
{
    add_option('ingenidev_cods_welcome_displayed', false);
}

add_action('admin_notices', 'ingenidev_cods_welcome_message');

function ingenidev_cods_welcome_message()
{
    if (!get_option('ingenidev_cods_welcome_displayed') && is_admin() && current_user_can('manage_options')) {
        ?>
        <div class="notice notice-success is-dismissible" id="ingenidev-welcome-notice">
             <img src="<?php echo esc_url( plugin_dir_url(__FILE__) . 'cash_on_delivery_shield_icon.png' ); ?>" style="width: 32px; height: 32px; margin-right: 10px;" alt="" />
            <p><?php esc_html_e('Welcome! Thank you for installing ingenidev Cash on Delivery Shield', 'ingenidev_cods'); ?></p>
            <button type="button" class="notice-dismiss" id="ingenidev-dismiss-notice"></button>
        </div>
        <?php
        wp_enqueue_script(
            'dismiss-notice',
            plugin_dir_url(__FILE__) . '/js/ingenidev_cods_dismiss_notice.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_localize_script('dismiss-notice', 'ingenidev_cods_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'action' => 'ingenidev_cods_dismiss_welcome_notice'
        ));
        update_option('ingenidev_cods_welcome_displayed', true);
    }
}

add_action('wp_ajax_ingenidev_cods_dismiss_welcome_notice', 'ingenidev_cods_dismiss_welcome_notice');

function ingenidev_cods_dismiss_welcome_notice()
{
    update_option('ingenidev_cods_welcome_displayed', true);
    wp_die();
}

add_action('wp_dashboard_setup', 'ingenidev_cods_custom_dashboard_widgets');

function ingenidev_cods_custom_dashboard_widgets()
{
    global $wp_meta_boxes;
    wp_add_dashboard_widget('ingenidev-cods-welcome-widget', 'ingenidev, Cash on Delivery Shield', 'ingenidev_cods_custom_dashboard_help');
}

function ingenidev_cods_custom_dashboard_help()
{
    ?>
    <p>Thank you for installing our Plugin. Should you encounter any issues, please do not hesitate to contact us.</p>
    <?php
}


register_uninstall_hook(__FILE__, 'ingenidev_cods_uninstall');

function ingenidev_cods_uninstall()
{
    delete_option('ingenidev_cods_welcome_displayed');
}

