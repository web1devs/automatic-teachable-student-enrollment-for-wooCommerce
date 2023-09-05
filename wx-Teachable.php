<?php 
/*
Plugin Name: Automatic Teachable Student Enrollment for WooCommerce

Plugin URI: https://www.webonedevs.com/woocommerce-teachable-enrollment/
Description: Automatic Teachable Student Enrollment for WooCommerce plugin works to connect woocommerce store to Teachable platform. It will facilitate to sell courses from woocommerce shops and students will be automatically enrolled under right courses. It Designed, Developed, Maintained & Supported by WebOneDevs Team.

Version: 1.0.2
Author: WebOneDevs
Author URI: https://webonedevs.com/
License: GPLv2 or Later
Text Domain: wx-teachable
*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

define( 'ATSEW_TCM_ACC_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
define( 'ATSEW_TCM_ACC_PATH', plugin_dir_path( __FILE__ ) );

$atsew_apiKeyShow = get_option('teachable_fild_teachable_api_key');

define("ATSEW_TEACHABLEAPIKEY", $atsew_apiKeyShow );  
define("ATSEW_PLUGIN_BASENAME",plugin_basename(__FILE__));
if( is_admin() ){
    require_once( ATSEW_TCM_ACC_PATH . 'process/wx-admin-settings.php' );
}

require_once( ATSEW_TCM_ACC_PATH . 'process/wx-teachable.php' );
