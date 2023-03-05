<?php
$apiKeyShow = get_option('teachable_fild_teachable_api_key');


define("TEACHABLEAPIKEY", $apiKeyShow ); //  


if (TEACHABLEAPIKEY != '') {
	# code...

	function wpteachable_is_secured( $nonce_field, $action, $post_id ) {
		$nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[ $nonce_field ] : '';

		if ( $nonce == '' ) {
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

	function wpteachable_add_custom_box() {
		$screens = [ 'product' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wpteachable_box_id',           // Unique ID
				__('Woocommerce to Teachable','wx-teachable'),      		// Box title
				'wpteachable_custom_box_html',  // Content callback, must be of type callable
				$screen                         // Post type
			);
		}
	}
	add_action( 'add_meta_boxes', 'wpteachable_add_custom_box' );
	// admin meta box for choosing teachable course
	function wpteachable_custom_box_html( $post ) {
		$meta_course_id = get_post_meta( $post->ID, 'teachable_course_id', true );
		

		$isPublishedShow = get_option('teachable_fild_is_published');
		if($isPublishedShow == 'yes') {
			$url2="https://developers.teachable.com/v1/courses?is_published=true";
		} else {
			$url2="https://developers.teachable.com/v1/courses";
		}

		$ch2 = curl_init();
		curl_setopt($ch2,CURLOPT_URL, $url2);

		//$apiKey =' ';

		curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
			'accept:application/json',
			'apiKey: ' . TEACHABLEAPIKEY
		));

		curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'GET');
		curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
		$response2 = curl_exec($ch2);
		$teachable_courses = json_decode($response2, true);
		
		// print_r($teachable_courses);
		curl_close($ch2);


		?>
		<div id="courseDiv" class="form-group" >
			<label for="course_id"><?=__('Choose a Teachable Course','wx-teachable')?>:</label><br/>
			<select  class="form-control form-select" aria-label="Default select example" name="course_id" id="course_id">
				<option value="" selected><?=__('Select Course','wx-teachable')?></option>
				<?php
				foreach ( $teachable_courses['courses'] as $item_id => $item ) {
					//$brand_name = get_post_meta( $item->get_product_id(), 'actual_brand_name', true );
					?>
					<option <?php echo $meta_course_id==$item['id']?'selected':''; ?> data-id="<?php echo $item['name'];?>" value="<?php echo $item['id']; ?>">
						<?php echo $item['name']; ?>
					</option>
					<?php
				}
				?>
			</select><br/><br/>
		</div>
		<?php
	}

	function wpteachable_save_postdata( $post_id ) {
		if ( array_key_exists( 'course_id', $_POST ) ) {
			update_post_meta(
				$post_id,
				'teachable_course_id',
				sanitize_text_field($_POST['course_id'])
			);
		}
	}
	add_action( 'save_post', 'wpteachable_save_postdata' );

	function wpteachable_meta_box_scripts()
	{
		// get current admin screen, or null
		$screen = get_current_screen();
		// verify admin screen object
		if (is_object($screen)) {
			// enqueue only for specific post types
			if (in_array($screen->post_type, ['shop_order'])) {
				// enqueue script
				wp_enqueue_script('wpteachable_meta_box_script', plugin_dir_url(__FILE__) . 'js/admin.js', ['jquery']);
				// localize script, create a custom js object
				wp_localize_script(
					'wpteachable_meta_box_script',
					'wpteachable_meta_box_obj',
					[
						'url' => admin_url('admin-ajax.php'),
					]
				);
			}
		}
	}
	add_action('admin_enqueue_scripts', 'wpteachable_meta_box_scripts');

	// un enrollment from teachable if order cancelled or refunded
	add_action( 'woocommerce_order_status_refunded', 'wpteachable_unenroll_user_to_teachable' );
	add_action( 'woocommerce_order_status_cancelled', 'wpteachable_unenroll_user_to_teachable' );

	function wpteachable_unenroll_user_to_teachable( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_id => $item ) {
			$meta_course_id = get_post_meta( $item->get_product_id(), 'teachable_course_id', true );

			if($meta_course_id!=null){
				// opportunity to change enrollment student's email & name using below filters
				$billing_email = apply_filters('teachable_student_email',$order->get_billing_email(),$order_id,$item_id);
				$billing_name = apply_filters('teachable_student_name',$order->get_billing_first_name(),$order_id,$item_id);

				$teachable_user_id=push_to_teachable($billing_name,$billing_email);
				if($teachable_user_id>0){
					$url2="https://developers.teachable.com/v1/unenroll";
					$ch2 = curl_init();
					curl_setopt($ch2,CURLOPT_URL, $url2);
					//$apiKey =' ';
					$data = '{"user_id":'.$teachable_user_id.',"course_id":'.$meta_course_id.'}';

					curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
						'Content-Type:application/json',
						'apiKey: ' . TEACHABLEAPIKEY
					));

					curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'POST');
					curl_setopt($ch2,CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,0);
					curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,0);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
					$response2 = curl_exec($ch2);
					curl_close($ch2);
				}
			}
		}


	}

	// enrole student data to teachable
	$orderStatusShow = get_option('teachable_fild_order_status');
	add_action( 'woocommerce_order_status_'.$orderStatusShow, 'wpteachable_enroll_user_to_teachable' );

	function wpteachable_enroll_user_to_teachable( $order_id ) {

		$order = wc_get_order( $order_id );
		foreach ( $order->get_items() as $item_id => $item ) {
			$meta_course_id = get_post_meta( $item->get_product_id(), 'teachable_course_id', true );

			if($meta_course_id!=null){
				// opportunity to change enrollment student's email & name using below filters
				$billing_email = apply_filters('teachable_student_email',$order->get_billing_email(),$order_id,$item_id);
				$billing_name = apply_filters('teachable_student_name',$order->get_billing_first_name(),$order_id,$item_id);

				$teachable_user_id=push_to_teachable($billing_name,$billing_email);
				if($teachable_user_id>0){
					$url2="https://developers.teachable.com/v1/enroll";
					$ch2 = curl_init();
					curl_setopt($ch2,CURLOPT_URL, $url2);
					//$apiKey =' ';
					$data = '{"user_id":'.$teachable_user_id.',"course_id":'.$meta_course_id.'}';

					curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
						'Content-Type:application/json',
						'apiKey: ' . TEACHABLEAPIKEY
					));

					curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'POST');
					curl_setopt($ch2,CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,0);
					curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,0);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
					$response2 = curl_exec($ch2);
					curl_close($ch2);
				}
			}
		}
	}

	function push_to_teachable($name,$email){

		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL            => 'https://developers.teachable.com/v1/users',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => '{"email":"'.$email.'","name":"'.$name.'"}',
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Accept: application/json',
				'apiKey:'.TEACHABLEAPIKEY,
				'Cookie: __cf_bm=BSIVtSUrf75lUqEvDGh99TDHv425suPXQ60h1XhAVl8-1667383909-0-ATs0aBr/yPDFcMLKjser/LUslERuqqlXXY7stq+LkO0l7/zR5HEPZ+eOq9GxLQTzsBrTAUxwFn1ffY4g4G4c/iw=; __cfruid=581d32e060f011981bbf4c83c8542c1581a192b3-1667368975; ahoy_visit=7daf92aa-e679-420e-b5f2-0847f3ca9eae; ahoy_visitor=3afdc6c1-85b1-4634-b00f-c620fa67f53a'
			),
		) );
		$response = curl_exec( $curl );
		curl_close( $curl );
		$response2 = json_decode($response, true);
		return $teachable_user_id = $response2['id'];
	}
}