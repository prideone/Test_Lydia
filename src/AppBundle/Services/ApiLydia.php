<?php

namespace AppBundle\Services;

class ApiLydia{
	
	private $container;

	public function __construct($container){
		$this->container = $container;
	}


	public function doRequest($payment){

		$apiUrl = "https://homologation.lydia-app.com";
		$initPaymentUrl = "/api/request/do.json";

		$data = array(
		    "vendor_token" => "58385365be57f651843810",
		    "amount" => "12.33",
		    "currency" => "EUR",
		    "type" => "email",
		    "recipient" => $payment->getEmail(),
		);


		//url-ify the data for the POST
		$data_string = '';
		foreach($data as $key=>$value) { 
			$data_string .= $key.'='.$value.'&'; 
		}
		rtrim($data_string, '&');

		try{
			$ch = curl_init();

			if ($ch === false) {
	        	throw new Exception('failed to initialize');
	    	}

			curl_setopt($ch, CURLOPT_URL, $apiUrl.$initPaymentUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
			curl_setopt($ch, CURLOPT_POST, count($data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

			$result = curl_exec($ch);

			if ($result === false) {
		        throw new \Exception(curl_error($ch), curl_errno($ch));
		    }


		} catch(Exception $e) {
		    trigger_error(sprintf('Curl failed with error #%d: %s',$e->getCode(), $e->getMessage()),E_USER_ERROR);
		}
		
		$result = json_decode($result, true);

		$payment->setRequestId($result['request_id']);
		$payment->setRequestUuid($result['request_uuid']);

		return $payment;
	}
}