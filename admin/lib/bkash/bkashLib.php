<?php
		
		class bkashLib 
		{
			public  $base_uri    = 'https://checkout.pay.bka.sh/v1.0.0-beta';			
			private $password   = 'S4rM0rH1@132';
			private $username   = 'SHURJOMUKHI';			
			private $app_key    = '6nsqe5lth7l2b4gf96bgrbh9ir';
			private $app_secret = '84ohjtm4e7bp6v4qm66iftejf0mb14fhmb28ss3rb1hchonagd6';

			public function getToken() 
			{
		      	$post_token=array(
				       'app_key'=>$this->app_key,
					   'app_secret'=>$this->app_secret,
				);	
				
				$url = curl_init($this->base_uri.'/checkout/token/grant');					
				$posttoken = json_encode($post_token);
				$header    = array(
			        'Content-Type:application/json',				
					'password:'.$this->password,
					'username:'.$this->username);
					try 
					{
						curl_setopt($url,CURLOPT_HTTPHEADER, $header);
						curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
						curl_setopt($url,CURLOPT_POSTFIELDS, $posttoken);
						curl_setopt($url,CURLOPT_SSL_VERIFYPEER, false);				 
						curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
						$resultdata=curl_exec($url);		
						curl_close($url);
						if(isset($resultdata))
							return json_decode($resultdata, true);
						else
							return FALSE;
					} 
					catch (Exception $e) 
					{
						$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
						return FALSE;
					}
	    	}

	    	

	    	public function verify($token, $bank_tx_id)
	    	{
	    		

	    		$url = curl_init($this->base_uri.'/checkout/payment/search/'.$bank_tx_id);
				$header = array(
					'Content-Type:application/json',
					'authorization:'.$token,		
					'x-app-key:'.$this->app_key);
				try 
				{	
					curl_setopt($url,CURLOPT_HTTPHEADER, $header);
					curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
					curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
					curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($url,CURLOPT_SSL_VERIFYPEER, false);
					$resultdatax=curl_exec($url);
					curl_close($url);					
					if(isset($resultdatax))			
						return $resultdatax; 
					else
						return FALSE;
				} 
				catch (Exception $e) 
				{
					$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
					return $e->getMessage();
				}
	    	}

	}	





?>
