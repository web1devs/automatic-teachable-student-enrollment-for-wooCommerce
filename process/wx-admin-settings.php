<?php
/* add wx-teachable.php file added here */

require_once( TCM_ACC_PATH . 'process/wx-teachable.php' );



add_filter('woocommerce_settings_tabs_array', 'teachable_add_fild', 50,1);
function teachable_add_fild($settings_tab) {
    $settings_tab['teachable_fild'] = __('Teachable', 'wx-teachable');
    return $settings_tab;
}

add_action('woocommerce_settings_tabs_teachable_fild', 'teachable_add_fild_settings');
function teachable_add_fild_settings() {
    woocommerce_admin_fields(get_teachable_fild_settings());
}

// upload data in option table
add_action('woocommerce_update_options_teachable_fild', 'teachable_update_options_fild_settings');
function teachable_update_options_fild_settings() {
    woocommerce_update_options(get_teachable_fild_settings());
}

function get_teachable_fild_settings() {
    $settings = array(
        'section_title' => array(
            'id' => 'teachable_fild_settings_title',
            // 'desc' => 'You can control teachable course',
            'type' => 'title',
            'name' => __('Teachable settings', 'wx-teachable'),
        ),
        'teachable_api_key' => array(
            'id' => 'teachable_fild_teachable_api_key',
            'desc' => __('To get teachable API KEY - check this <a href="https://docs.teachable.com/docs/quickstart-guide"> DOC</a>', 'wx-teachable'),
            'type' => 'text',
            'desc_tip' => __('Set your teachable api key', 'wx-teachable'),
            'name' => __('Teachable API Key', 'wx-teachable'),
        ),
        'is_published' => array(
            'id' => 'teachable_fild_is_published',
            'desc' => __( 'You may choose published or all courses including draft', 'wx-teachable' ),
            'type' => 'checkbox',
            //'cbvalue'       => 'yes',
			// 'css'      => 'min-width:300px;',
            'name' =>  __( 'Get Teachable published courses only?', 'wx-teachable' ),
        ),
        'order_status' => array(
            'id'	=> 'teachable_fild_order_status',
            'desc' => __( 'When a student will be enrolled in Teachable after checkout?', 'wx-teachable' ),
            'type' => 'select', // multiselect 
            'name' => __( 'Order Status (when student will be enrolled )', 'wx-teachable' ),
            'desc_tip' => true,
            'options' => array(
                'completed' => __('Completed', 'wx-teachable' ),
                'processing' => __( 'Processing', 'wx-teachable' ),
                'pending' => __('Pending', 'wx-teachable' ),
                'on_hold' => __('On-hold', 'wx-teachable' ),
                'draft' => __('Draft', 'wx-teachable' ) 
            )

        ),
        'section_end' => array(
            'id' => 'teachable_fild_settings_sectionend',
            'type' => 'sectionend',
        ),
    );

    return apply_filters('filter_teachable_fild_settings', $settings);
}


/*===== Add code from Wx-Teachable.php filte later start======*/

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
        <p><?php esc_html_e("Please Insert Teachable API KEY","wx-teachable");?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' ); ?>"> <?php esc_html_e('Click Here.','wx-teachable');?></a></p>
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
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'wx-teachable') . '</a>';
    
    $links[] = $settings_link;

    return $links;
    
}



/*===== Add code from Wx-Teachable.php filte later  end ======*/


