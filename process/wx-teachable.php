<?php
/**
 * Version: 1.0.0
 */
if (ATSEW_TEACHABLEAPIKEY !== '') {
	# code...

	
	// un enrollment from teachable if order cancelled or refunded
	add_action( 'woocommerce_order_status_refunded', 'atsew_wpteachable_unenroll_user_to_teachable' );
	add_action( 'woocommerce_order_status_cancelled', 'atsew_wpteachable_unenroll_user_to_teachable' );

	if( !function_exists('atsew_wpteachable_unenroll_user_to_teachable')){
		function atsew_wpteachable_unenroll_user_to_teachable( $order_id ) {

			$order = wc_get_order( $order_id );
	
			foreach ( $order->get_items() as $item_id => $item ) {
				$meta_course_id = get_post_meta( $item->get_product_id(), 'teachable_course_id', true );
	
				if($meta_course_id!==null){
					// opportunity to change enrollment student's email & name using below filters
					$billing_email = apply_filters('atsew_teachable_student_email',$order->get_billing_email(),$order_id,$item_id);
					$billing_name = apply_filters('atsew_teachable_student_name',$order->get_billing_first_name(),$order_id,$item_id);
	
					$teachable_user_id=atsew_push_to_teachable($billing_name,$billing_email);
					if($teachable_user_id>0){
						$atsew_unenroll_url="https://developers.teachable.com/v1/unenroll";
						$atsew_unenroll_curl_init = curl_init();
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_URL, $atsew_unenroll_url);
						$data = '{"user_id":'.$teachable_user_id.',"course_id":'.$meta_course_id.'}';
	
						curl_setopt($atsew_unenroll_curl_init, CURLOPT_HTTPHEADER, array(
							'Content-Type:application/json',
							'apiKey: ' . ATSEW_TEACHABLEAPIKEY
						));
	
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_CUSTOMREQUEST,'POST');
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_POSTFIELDS, $data);
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_SSL_VERIFYPEER,0);
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_SSL_VERIFYHOST,0);
						curl_setopt($atsew_unenroll_curl_init,CURLOPT_RETURNTRANSFER, true);
						curl_exec($atsew_unenroll_curl_init);
						curl_close($atsew_unenroll_curl_init);
					}
				}
			}
	
	
		}
	}

	

	// enrole student data to teachable
	$atsew_orderStatusShow = get_option('teachable_fild_order_status');

	if( !function_exists('atsew_wpteachable_enroll_user_to_teachable')){
		add_action( 'woocommerce_order_status_'.$atsew_orderStatusShow, 'atsew_wpteachable_enroll_user_to_teachable' );

		function atsew_wpteachable_enroll_user_to_teachable( $order_id ) {
	
			$order = wc_get_order( $order_id );
			foreach ( $order->get_items() as $item_id => $item ) {
				$meta_course_id = get_post_meta( $item->get_product_id(), 'teachable_course_id', true );
	
				if($meta_course_id!==null){
					// opportunity to change enrollment student's email & name using below filters
					$billing_email = apply_filters('atsew_teachable_student_email',$order->get_billing_email(),$order_id,$item_id);
					$billing_name = apply_filters('atsew_teachable_student_name',$order->get_billing_first_name(),$order_id,$item_id);
	
					$teachable_user_id=atsew_push_to_teachable($billing_name,$billing_email);
					if($teachable_user_id>0){
						$atsew_enroll_url="https://developers.teachable.com/v1/enroll";
						$atsew_enroll_init = curl_init();
						curl_setopt($atsew_enroll_init,CURLOPT_URL, $atsew_enroll_url);
						
						$data = '{"user_id":'.$teachable_user_id.',"course_id":'.$meta_course_id.'}';
	
						curl_setopt($atsew_enroll_init, CURLOPT_HTTPHEADER, array(
							'Content-Type:application/json',
							'apiKey: ' . ATSEW_TEACHABLEAPIKEY
						));
	
						curl_setopt($atsew_enroll_init,CURLOPT_CUSTOMREQUEST,'POST');
						curl_setopt($atsew_enroll_init,CURLOPT_POSTFIELDS, $data);
						curl_setopt($atsew_enroll_init,CURLOPT_SSL_VERIFYPEER,0);
						curl_setopt($atsew_enroll_init,CURLOPT_SSL_VERIFYHOST,0);
						curl_setopt($atsew_enroll_init,CURLOPT_RETURNTRANSFER, true);
						curl_exec($atsew_enroll_init);
						curl_close($atsew_enroll_init);
					}
				}
			}
		}
	}


 if( !function_exists('atsew_push_to_teachable')){
	
	function atsew_push_to_teachable($name,$email){

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
				'apiKey:'.ATSEW_TEACHABLEAPIKEY,
				'Cookie: __cf_bm=BSIVtSUrf75lUqEvDGh99TDHv425suPXQ60h1XhAVl8-1667383909-0-ATs0aBr/yPDFcMLKjser/LUslERuqqlXXY7stq+LkO0l7/zR5HEPZ+eOq9GxLQTzsBrTAUxwFn1ffY4g4G4c/iw=; __cfruid=581d32e060f011981bbf4c83c8542c1581a192b3-1667368975; ahoy_visit=7daf92aa-e679-420e-b5f2-0847f3ca9eae; ahoy_visitor=3afdc6c1-85b1-4634-b00f-c620fa67f53a'
			),
		) );
		$response = curl_exec( $curl );
		curl_close( $curl );
		$response = json_decode($response, true);
		return $teachable_user_id = $response['id'];
	}
 }


}
