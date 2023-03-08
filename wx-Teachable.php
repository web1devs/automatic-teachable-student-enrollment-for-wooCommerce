<?php 
/*
Plugin Name: WooCommerce Teachable Enrollment

Plugin URI: https://wooxperto.com/
Description: This plugin for Teachable Course Management. wpTeachableCM is the fastest, fully customizable & beautiful plugin suitable for e-commerce websites. Designed, Developed, Maintained & Supported by WooXperto.

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


