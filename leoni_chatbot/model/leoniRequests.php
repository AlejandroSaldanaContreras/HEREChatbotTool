<?php
/** 
+ About: File to create requests using cURL methods 
**/

class leoniRequests extends curlParameters{	

	public function getRequests($webexapiurl,$messageid){
		
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
			CURLOPT_URL => $webexapiurl.$messageid ,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array(
				"Authorization: ".$this->auth,
				"Content-Type: application/json"
					)
				)
			);
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
		
			curl_close($curl);
		
			if ($err) {
				echo "cURL Error #:" . $err;
			} 
			else {
		
				$resp=json_decode($response,true);
				return $resp;
			}
		} 
		
	public function deleteRequests($webexapiurl,$messageid){
		$curl = curl_init();
			
			curl_setopt_array($curl, array(
			CURLOPT_URL => $webexapiurl.$messageid ,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "DELETE",
			CURLOPT_HTTPHEADER => array(
				"Authorization: ".$this->auth,
				"Content-Type: application/json"
					)
				)
			);
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
		
			curl_close($curl);
		
			if ($err) {
				echo "cURL Error #:" . $err;
			} 
			else {
		
				$resp=json_decode($response,true);
				return $resp;
			}
	}	
	public function postRequests($webexapiurl,$arr){
				
				$jstring = json_encode ($arr);
				$curl =0;
				$curl = curl_init();
		
				curl_setopt_array($curl, array(
				CURLOPT_URL => $webexapiurl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS =>  $jstring,
				CURLOPT_HTTPHEADER => array(
					"Authorization: ".$this->auth,
					"Content-Type: application/json",
					)
				));
		
				curl_exec($curl);
				curl_close($curl);	
		} 
}
