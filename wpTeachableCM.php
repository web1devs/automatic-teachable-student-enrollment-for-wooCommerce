<?php 
/*
Plugin Name: WooCommerce Teachable Enrollment

Plugin URI: https://wooxperto.com/plugins/
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