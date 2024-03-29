<?php
/**
 * Version: 1.0.1
 */



if( ! function_exists( ' atsew_teachable_add_fild ' )){

    add_filter('woocommerce_settings_tabs_array', 'atsew_teachable_add_fild', 50,1);

	function atsew_teachable_add_fild($settings_tab) {
		$settings_tab['teachable_fild'] = __('Teachable', 'wx-teachable');
		return $settings_tab;
	}
}




if( ! function_exists('atsew_teachable_add_fild_settings')){

	add_action('woocommerce_settings_tabs_teachable_fild', 'atsew_teachable_add_fild_settings');

	function atsew_teachable_add_fild_settings() {
		woocommerce_admin_fields(atsew_get_teachable_fild_settings());
	}
}


// upload data in option table


if( !function_exists('atsew_teachable_update_options_fild_settings')){

 add_action('woocommerce_update_options_teachable_fild', 'atsew_teachable_update_options_fild_settings');
	
 function atsew_teachable_update_options_fild_settings() {
		woocommerce_update_options(atsew_get_teachable_fild_settings());
	}
}

if(! function_exists('atsew_get_teachable_fild_settings')){
	
	function atsew_get_teachable_fild_settings() {
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
	
		return apply_filters('atsew_filter_teachable_fild_settings', $settings);
	}
}



/*===== Add code from Wx-Teachable.php filte later start======*/

/**
 *  
 *  Desc: Redirected to woocommerce settings page after plugin loaded 
 * 
*/

if( !function_exists('atsew_wx_teachable_redirect_settings_page')){

	add_action( 'activated_plugin', 'atsew_wx_teachable_redirect_settings_page' );

	function atsew_wx_teachable_redirect_settings_page( $plugin ) {
		if( $plugin === plugin_basename( __FILE__ ) ) {
			exit( wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' ) ) );
		}
	}
}



if( !function_exists('atsew_wx_teachable_notice')){
	function atsew_wx_teachable_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e("Please Insert Teachable API KEY","wx-teachable");?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' ); ?>"> <?php esc_html_e('Click Here.','wx-teachable');?></a></p>
		</div>
		<?php
	}
}



$woo_settings_page = admin_url( 'admin.php?page=wc-settings&tab=teachable_fild' );

$current_page_url = admin_url(basename($_SERVER['REQUEST_URI']));

if(!ATSEW_TEACHABLEAPIKEY && $current_page_url !== $woo_settings_page ){
    add_action( 'admin_notices', 'atsew_wx_teachable_notice' );
}

/**
 *  Add plugin setting link on the plugin.php file.
*/



if( !function_exists('atsew_wx_teachable_settings_link')){

	add_filter('plugin_action_links_'. ATSEW_PLUGIN_BASENAME, 'atsew_wx_teachable_settings_link');

	function atsew_wx_teachable_settings_link( array $links ){
		$url = get_admin_url() . "admin.php?page=wc-settings&tab=teachable_fild";
		$settings_link = '<a href="' . $url . '">' . __('Settings', 'wx-teachable') . '</a>';
		
		$links[] = $settings_link;
	
		return $links;
		
	}
}



/*===== Add code from Wx-Teachable.php filte later  end ======*/

if (ATSEW_TEACHABLEAPIKEY !== '') {
    function atsew_wpteachable_is_secured( $nonce_field, $action, $post_id ) {
		$nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[ $nonce_field ] : '';

		if ( $nonce === '' ) {
			return false;
		}
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		return true;

	}

    if( !function_exists('atsew_wpteachable_add_custom_box') ){
		function atsew_wpteachable_add_custom_box() {
			$screens = [ 'product' ];
			foreach ( $screens as $screen ) {
				add_meta_box(
					'wpteachable_box_id',           // Unique ID
					__('Woocommerce to Teachable','wx-teachable'),      		// Box title
					'atsew_wpteachable_custom_box_html',  // Content callback, must be of type callable
					$screen                         // Post type
				);
			}
		}
		add_action( 'add_meta_boxes', 'atsew_wpteachable_add_custom_box' );
	}


	if( !function_exists('atsew_wpteachable_custom_box_html')){
		// admin meta box for choosing teachable course
		function atsew_wpteachable_custom_box_html( $post ) {
			$meta_course_id = (int) get_post_meta( $post->ID, 'teachable_course_id_'.$post->ID, true );
			

			$isPublishedShow = get_option('teachable_fild_is_published');
			if($isPublishedShow === 'yes') {
				$url="https://developers.teachable.com/v1/courses?is_published=true";
			} else {
				$url="https://developers.teachable.com/v1/courses";
			}

			
			$args = array(
					'headers' => array(
						'accept'=>'application/json',
						'apiKey'=>ATSEW_TEACHABLEAPIKEY
					)
				);
				$response=wp_remote_get( $url, $args );
				$response=wp_remote_retrieve_body($response);
			$teachable_courses = json_decode($response, true);
			?>
			<div id="courseDiv" class="form-group" >
				<label for="course_id"><?php echo __('Choose a Teachable Course','wx-teachable');?>:</label><br/>
				<select  class="form-control form-select" aria-label="Default select example" name="course_id" id="course_id">
					<option value=""><?php echo __('Select a Course','wx-teachable');?></option>
					<?php
					foreach ( $teachable_courses['courses'] as $item_id => $item ) {
						?>
						<option <?php echo esc_attr(($meta_course_id===$item['id']?'selected':'')); ?> data-id="<?php echo esc_attr($item['name']);?>" value="<?php echo esc_attr($item['id']); ?>">
							<?php echo esc_html($item['name']); ?>
						</option>
						<?php
					}
					?>
				</select><br/><br/>
			</div>
			<?php
		}
	}

	

	if(!function_exists('atsew_wpteachable_save_postdata')){

		function atsew_wpteachable_save_postdata( $post_id ) {
			if ( array_key_exists( 'course_id', $_POST ) ) {
				update_post_meta(
					$post_id,
					'teachable_course_id_'.$post_id,
					sanitize_text_field($_POST['course_id'])
				);
			}
		}
		add_action( 'save_post', 'atsew_wpteachable_save_postdata' );

	}

	
}
