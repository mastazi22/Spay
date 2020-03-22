<?php
		require "Request.php";

		class TokenRequest 
		{
			/*
			public $base_uri   = 'https://uat.upaybd.com';
			public $terminal_identifier = 'SM-1';
			private $api_key     = 'DTF534650875284454';
			private $secret_key  = 'Dt593a9d93be1d2d581a54dca46df08c781';			
			private $master_pin  = '';
			*/
			public $base_uri   = 'https://upaybd.com';
			public $terminal_identifier = 'Shurjopay';
			private $api_key     = 'DTF864034028942203';
			private $secret_key  = 'Dt1e54efd3e454cc8a7ffb56089b35afb31';			
			private $master_pin  = '';

			private $cancel_url  = 'https://shurjopay.com/upay/cancel.php';
			private $success_url = 'https://shurjopay.com/upay/success.php';

			public function getToken($merchant_order_number,$amount) 
			{
		      	$url = $this->base_uri . '/api/v1/online_payments.json';
		      	$options = array(
		      		'amount' => $amount,
		        	'api_key' => $this->api_key,
		        	'secret_key' => $this->secret_key,
		        	'merchant_order_number' => $merchant_order_number,		          
		        	'terminal_identifier' => $this->terminal_identifier
		      	);

			      $request = new Request($url);
			      // Enable SSL/TLS.
			      $request->enableSSL();
			      // Set the initial connection timeout (default is 10 seconds).
			      $request->connectTimeout = 5;
			      // Set the timeout (default is 15 seconds).
			      $request->timeout = 10;
			      // Send some fields as a POST request.
			      $request->setRequestType('POST');
			      $request->setPostFields($options);
			      $request->execute();

			      if($request->getHttpCode() == 200)
			      {
			        return $request->getResponse();			        
			      }
			      else
			      {
			      	echo json_encode(array('status'=>FALSE,'message'=>$request->getError()));
			      }
	    	}

	    	public function pay_by_upay($auth_token,$amount)
	    	{
	    		$url = $this->base_uri .'/api/v1/online_payments/pay_by_upay';

	    		$form = "<div style='text-align:center; margin:20px auto;'>
							<h2 style='color:#283a69;padding:10px;'>Forwarding you to Bank web site, please wait....</h2>
							<img src='../img/loading.png' alt='Loading...' width='300' height='15' />
						</div>";


				$form .="<form method='post' action='{$url}' id='frm_submit'>
		            	<input type='hidden' name='auth_token' value='{$auth_token}' />
		            	<input type='hidden' name='amount' value='{$amount}' />
		            	<input type='hidden' name='cancel_url' value='{$this->cancel_url}' />
		            	<input type='hidden' name='success_url' value='{$this->success_url}' />
		          	   </form><script>document.getElementById('frm_submit').submit();</script>";

		        echo $form;  	   
	    	}

	    	public function verify($merchant_order_number)
	    	{
	    		$url = $this->base_uri .'/api/v1/online_payments/verify';
	    		$data = array(
	    			'api_key' => $this->api_key,
	    			'secret_key' => $this->secret_key,
	    			'merchant_order_number' => $merchant_order_number
	    		);
	    		$ch = curl_init();
	    		curl_setopt($ch,CURLOPT_URL,$url);
	    		curl_setopt($ch,CURLOPT_POST, 1);
	    		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	    		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    		$response = curl_exec($ch);
	    		return $response;
	    		curl_close ($ch);
	    	}

	    	public function reverse($merchant_order_number,$tnx_reference_number)
	    	{
	    		$url = '/api/v1/online_payments/reverse';
	    		$data = array(
	    			'api_key' => $this->api_key,
	    			'secret_key' => $this->secret_key,
	    			'merchant_order_number' => $merchant_order_number,
	    			'tnx_reference_number' => $tnx_reference_number,
	    			'master_pin' =>	 $this->master_pin   			
	    		);
	    		$ch = curl_init();
	    		curl_setopt($ch,CURLOPT_URL,$url);
	    		curl_setopt($ch,CURLOPT_POST, 1);
	    		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	    		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    		$response = curl_exec($ch);
	    		return $response;
	    		curl_close ($ch);

	    	}


	}	





?>
