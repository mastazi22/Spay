<?php

	class paypointApi 
	{

		private $apiUrl = 'https://paypoint.shurjorajjo.com.bd/payapi/ShurjopayDashboard/';
		private $token  = '6dz8TQPM5V4W';


		function getPaypointUserInfo($smUid) 
		{
			$ch = curl_init();			
			$dataArray = array('token'=>$this->token,'smUid'=>$smUid);
			$data = http_build_query($dataArray);
			$url = $this->apiUrl.'userInfo?'.$data;
			curl_setopt($ch,CURLOPT_URL,$url);		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 80);
			$data = curl_exec($ch);			
  			curl_close ($ch);  			
  			return json_decode($data);
		}

		function getPaypointUserOrders($smUid)
		{
			$ch = curl_init();			
			$dataArray = array('token'=>$this->token,'userId'=>$smUid);
			$data = http_build_query($dataArray);
			$url = $this->apiUrl.'PaypointUserOrders?'.$data;
			curl_setopt($ch,CURLOPT_URL,$url);		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 80);
			$data = curl_exec($ch);	
			//print_r($data);		
  			curl_close ($ch);  			
  			return json_decode($data);
		}

		function putAddToBlocklist()
		{

		}

		function getPaypointMobileTopUpByUser()
		{

		}

		function putPaypointUserBlock($smUid,$comment)
		{
			$ch = curl_init();			
			$dataArray = array('token'=>$this->token,'userId'=>$smUid,'comment'=>$comment);
			$data = http_build_query($dataArray);
			$url = $this->apiUrl.'UserBlock?'.$data;
			curl_setopt($ch,CURLOPT_URL,$url);		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 80);
			$data = curl_exec($ch);					
  			curl_close ($ch);  			
  			return json_decode($data);
		}

	}


?>