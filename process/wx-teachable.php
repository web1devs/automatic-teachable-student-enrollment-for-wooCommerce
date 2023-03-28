<?php
/**
 * Version: 1.0.0
 */
if (TEACHABLEAPIKEY !== '') {
	# code...

	
	// un enrollment from teachable if order cancelled or refunded
	add_action( 'woocommerce_order_status_refunded', 'wpteachable_unenroll_user_to_teachable' );
	add_action( 'woocommerce_order_status_cancelled', 'wpteachable_unenroll_user_to_teachable' );

	function wpteachable_unenroll_user_to_teachable( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_id => $item ) {
			$meta_course_id = get_post_meta( $item->get_product_id(), 'teachable_course_id', true );

			if($meta_course_id!==null){
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

			if($meta_course_id!==null){
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
