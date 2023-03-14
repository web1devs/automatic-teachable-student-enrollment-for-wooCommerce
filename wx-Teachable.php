<?php 
/*
Plugin Name: WooCommerce Teachable Student Enrollment

Plugin URI: https://www.wooxperto.com/woocommerce-teachable-enrollment/
Description: WooCommerce Teachable Student Enrollment plugin works to connect woocommerce store Teachable platform. It will facilitate to sell courses from woocommerce shops and students will be automatically enrolled under right course. It's Designed, Developed, Maintained & Supported by WooXperto Team.

Version: 1.0.0
Author: WooXperto
Author URI: https://wooxperto.com/
License: GPLv2 or Later
Text Domain: wx-teachable
*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

define( 'TCM_ACC_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
define( 'TCM_ACC_PATH', plugin_dir_path( __FILE__ ) );

$apiKeyShow = get_option('teachable_fild_teachable_api_key');

define("TEACHABLEAPIKEY", $apiKeyShow );  
define("PLUGIN_BASENAME",plugin_basename(__FILE__));
if( is_admin() ){
    require_once( TCM_ACC_PATH . 'process/wx-admin-settings.php' );
}

require_once( TCM_ACC_PATH . 'process/wx-teachable.php' );


