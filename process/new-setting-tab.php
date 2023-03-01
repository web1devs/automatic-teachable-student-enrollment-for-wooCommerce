<?php
add_filter('woocommerce_settings_tabs_array', 'teachable_add_fild', 50,1);
function teachable_add_fild($settings_tab) {
    $settings_tab['teachable_fild'] = __('Teachable', 'woo-teachable');
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
            'name' => __('Teachable settings', 'woo-teachable'),
        ),
        'teachable_api_key' => array(
            'id' => 'teachable_fild_teachable_api_key',
            'desc' => __('Set your teachable api key', 'woo-teachable'),
            'type' => 'text',
            'desc_tip' => true,
            'name' => __('Teachable API Key', 'woo-teachable'),
        ),
        'is_published' => array(
            'id' => 'teachable_fild_is_published',
            'desc' => __( 'You may choose published or all courses including draft', 'woo-teachable' ),
            'type' => 'checkbox',
            //'cbvalue'       => 'yes',
			// 'css'      => 'min-width:300px;',
            'name' =>  __( 'Get Teachable published courses only?', 'woo-teachable' ),
        ),
        'order_status' => array(
            'id'	=> 'teachable_fild_order_status',
            'desc' => __( 'When a student will be enrolled in Teachable after checkout?', 'woo-teachable' ),
            'type' => 'select', // multiselect 
            'name' => __( 'Order Status (when student will be enrolled )', 'woo-teachable' ),
            'desc_tip' => true,
            'options' => array(
                'completed' => __('Completed', 'woo-teachable' ),
                'processing' => __( 'Processing', 'woo-teachable' ),
                'pending' => __('Pending', 'woo-teachable' ),
                'on_hold' => __('On-hold', 'woo-teachable' ),
                'draft' => __('Draft', 'woo-teachable' ) 
            )

        ),
        'section_end' => array(
            'id' => 'teachable_fild_settings_sectionend',
            'type' => 'sectionend',
        ),
    );

    return apply_filters('filter_teachable_fild_settings', $settings);
}










