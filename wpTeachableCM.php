<?php 
/*
Plugin Name: WooCommerce Teachable Enrollment

Plugin URI: https://wooxperto.com/
Description: This plugin for Teachable Course Management. wpTeachableCM is the fastest, fully customizable & beautiful plugin suitable for e-commerce websites. Designed, Developed, Maintained & Supported by WooXperto.

Version: 1.0.0
Author: WooXperto
Author URI: https://wooxperto.com/
License: GPLv2 or 564.505
Text Domain: woo-teachable
*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

define( 'TCM_ACC_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
define( 'TCM_ACC_PATH', plugin_dir_path( __FILE__ ) );

require_once( TCM_ACC_PATH . 'process/wp-teachable.php' );
require_once( TCM_ACC_PATH . 'process/new-setting-tab.php' );

//echo plugin_basename( __FILE__ );
/**
 *  
 *  Desc: Redirected to woocommerce settings page after plugin loaded 
 * 
*/
function wooexparto_redirect_settings_page( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' ) ) );
    }
}
add_action( 'activated_plugin', 'wooexparto_redirect_settings_page' );




function woo_exparto_teach_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e("Please Insert Teachable API KEY","woo-teachable");?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' ); ?>"> <?php esc_html_e('Click Here.','woo-teachable');?></a></p>
    </div>
    <?php
}


$woo_settings_page = admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' );

$current_page_url = admin_url(basename($_SERVER['REQUEST_URI']));

if(TEACHABLEAPIKEY==null && $current_page_url != $woo_settings_page ){
    add_action( 'admin_notices', 'woo_exparto_teach_notice' );
}

/**
 *  Add plugin setting link on the plugin.php file.
*/

add_filter('plugin_action_links_'. plugin_basename(__FILE__), 'woo_exparto_teachable_settings_link');

function woo_exparto_teachable_settings_link( array $links ){
    $url = get_admin_url() . "admin.php?page=wc-settings&tab=teachable_fild";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'woo-teachable') . '</a>';
    
    $links[] = $settings_link;

    return $links;
    
}