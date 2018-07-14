<?php

namespace AppBundle\Services;

class ApiLydia{
	
	private $container;
	private $doctrine;

	public function __construct($container, $doctrine){
		$this->container = $container;
		$this->doctrine = $doctrine;
	}


	// create a payment request
	public function doRequest($payment){

		$urlExtension = "/api/request/do.json";

		$data = array(
		    "vendor_token" => "58385365be57f651843810",
		    "amount" => $payment->getAmount(),
		    "currency" => "EUR",
		    "type" => "email",
		    "recipient" => $payment->getEmail(),
		);

		
		$result = $this->accessApi($data, $urlExtension);
		$result = json_decode($result, true);

		if (isset($result['request_id']) && isset($result['request_uuid'])) {
			$payment->setRequestId($result['request_id']);
			$payment->setRequestUuid($result['request_uuid']);

			return $payment;
		}else{
			return false;
		}
		
	}


	// get the state of a payment request
	public function requestStatus($paymentId){

		$em = $this->doctrine->getManager();

		$payment = $em->getRepository('AppBundle:Payment')->find($paymentId);

		$data = array(
		    "request_uuid" => $payment->getRequestUuid(),
		    "vendor_token" => "58385365be57f651843810"
		);

		$urlExtension = "/api/request/state.json";

		$result = $this->accessApi($data, $urlExtension);

		$result = json_decode($result, true);

		if (isset($result['state'])) {
			return $result['state'];
		}else{
			return false;
		}

	}


	// function that get data and a url and return the response from lydia API
	public function accessApi($data, $urlExtension){

		$apiUrl = "https://homologation.lydia-app.com";

		// url-fy the data to pass throug the POST
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

			curl_setopt($ch, CURLOPT_URL, $apiUrl.$urlExtension);
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

		return $result;
	}

}