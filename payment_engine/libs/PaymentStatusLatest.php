<?php


class PaymentStatus {

	public function __construct() {
		//		
	}

	public function mxOrderStatus($Merchant,$OrderID,$SessionID)
	{
		$data='<?xml version="1.0" encoding="UTF-8"?>';
		$data.="<TKKPG>";
		$data.="<Request>";
		$data.="<Operation>GetOrderStatus</Operation>";
		$data.="<Order>";
		$data.="<Merchant>".$Merchant."</Merchant>";
		$data.="<OrderID>".$OrderID."</OrderID>";
		$data.="</Order>";
		$data.="<SessionID>".$SessionID."</SessionID>";
		$data.="</Request></TKKPG>";
		try 
		{
			$xml=$this->PostQW($data);
			return $xml->Response->Order->OrderStatus;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}		
	}

	public function dbblOrderStatus($BankRefID)
	{
		$node_request_data = array (     
			'action' => 'verify',	
			'trans_id' => $BankRefID,	
			'ip' => ''
		);
		$queryString =  http_build_query($node_request_data);   
		try 
		{
			$ch = curl_init();                                              		
			$host = "http://node.shurjopay.com?$queryString";
			curl_setopt($ch, CURLOPT_URL ,$host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$outputArray=array();
			$response_data = curl_exec($ch);
   		        curl_close($ch); 
		        $DbblResponseObject = $this->dbblResponseParsing($response_data);
			if(isset($DbblResponseObject->RESULT))
			    return $DbblResponseObject->RESULT;
			else
			  return FALSE;
	    } 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function eblOrderStatus($OrderID,$merchantObj)
	{
		try 
		{
			$parserObj = new Parser($merchantObj);
			$requestUrl = $parserObj->FormRequestUrl($merchantObj);
			$request_assoc_array = array(
				"apiOperation" => "RETRIEVE_ORDER",
			    "order.id" => $OrderID
			);
			$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
			$response = $parserObj->SendTransaction($merchantObj, $request);

			$new_api_lib = new api_lib;
			$parsed_array = $new_api_lib->parse_from_nvp($response);			
			if(is_array($parsed_array))
				return $parsed_array['result'];
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function bKashOrderStatus($BankRefID)
	{
		$url = 'https://www.bkashcluster.com:9081/dreamwave/merchant/trxcheck/sendmsg?user=SurjomukhiLimited&pass=november!quebec!sierra&msisdn=01845032741&trxid='.$BankRefID;
		try 
		{ 
          // Set query data here with the URL
	  		$ch = curl_init();	
          	curl_setopt($ch, CURLOPT_URL, $url); 
          	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          	curl_setopt($ch, CURLOPT_TIMEOUT, '300');
          	$response = curl_exec($ch);
          	$response= simplexml_load_string(trim($response));          
          	curl_close($ch); 
			if(is_object($response))
	        	return $response->transaction->trxStatus;
			else
				return FALSE;
        } 
        catch (Exception $e) 
        {
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	
	public function tblOrderStatus($OrderId,$MerchantId,$KeyCode) 
	{
			
		$TblPaymentInfoUrl = "https://ibanking.tblbd.com/checkout/Services/Payment_Info.asmx?WSDL";		
		try 
		{			
			$SoapObject = new SoapClient($TblPaymentInfoUrl);		
			$Params = array(
				'OrderID'    => $OrderId,			
				'MerchantID' => $MerchantId,
				'KeyCode'    => $KeyCode
			);
			$Response = $SoapObject->Get_Transaction_Ref($Params)->Get_Transaction_RefResult;
			$XmlMsg = base64_decode($Response);	
			$ResponseData = simplexml_load_string($XmlMsg,'SimpleXMLElement', LIBXML_NOCDATA);				
			$CheckStatus =  $ResponseData->TransactionInfo;
			if(isset($CheckStatus->StatusText))
				return $CheckStatus->StatusText;			
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function bKashApiOrderStatus($bank_tx_id,$json)
	{
		$CRED = json_decode($json);
		try 
		{
			$request_token = $this->bkash_Get_Token($CRED);
			$idtoken = $request_token['id_token'];
			$response = $this->bKashStatus($CRED,$bank_tx_id,$idtoken);		
			$obj = json_decode($response);
			if(is_object($obj) && isset($obj->transactionStatus))
				return $obj->transactionStatus;
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}	
	}




	public function upayOrderStatus($OrderID)
	{
		require_once("upay/token.php");		
		try 
		{
			$tokenReq = new TokenRequest();
			$VerifyResponse = $tokenReq->verify($OrderID);	
			$ResponseArrayObject = json_decode($VerifyResponse);	
			if(isset($ResponseArrayObject->data->order->status))
				return $ResponseArrayObject->data->order->status;		
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function ibblOrderStatus($Merchant,$OrderID,$SessionID)
	{

	}

	public function bkash_Get_Token($CRED){
		
	$post_token=array(
	       'app_key'=>$CRED->body->app_key,
		   'app_secret'=>$CRED->body->app_secret,
	);	
	$url=curl_init($CRED->checkout_link.'/checkout/token/grant');
		
	$posttoken = json_encode($post_token);
	$header    = array(
        'Content-Type:application/json',				
		'password:'.$CRED->headers->password,
		'username:'.$CRED->headers->username);
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
	
	
	public function bKashStatus($CRED,$bank_tx_id,$token)
	{
		$url=curl_init($CRED->checkout_link.'/checkout/payment/search/'.$bank_tx_id);
		$header=array(
			'Content-Type:application/json',
			'authorization:'.$token,		
			'x-app-key:'.$CRED->body->app_key);
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
			return FALSE;
		}
			
		
	}


	public function PostQW($data)
	{
		$hostname = '206.189.133.213'; // Address of the server with servlet used to work with orders	
		$port="743"; // Port
		$path = '/Exec';
		$content = '';
		
		// Establish a connection to the $hostname server
		$fp = fsockopen($hostname, $port, $errno, $errstr, 30);
		
		// Check if the connection is successfully established
		if (!$fp) die('<p>'.$errstr.' ('.$errno.')</p>');

		// HTTP request header
		$headers = 'POST '.$path." HTTP/1.0\r\n";
		$headers .= 'Host: '.$hostname."\r\n";
		$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
		$headers .= 'Content-Length: '.strlen($data)."\r\n\r\n";
		
		// Send HTTP request to the server
		fwrite($fp, $headers.$data);
		
		// Receive response
		while ( !feof($fp) )
		{
			$inStr= fgets($fp, 1024);
			// Cut the HTTP response headers. The string can be commented out if it is necessary to parse the header
			// In this case it is necessary to cut the response
			if (substr($inStr,0,7)!=="<TKKPG>") continue;
			// Disconnect
			$content .= $inStr;
		}
		fclose($fp);
		
		// To parse the response, use the simplexml library
		// Documentation on simplexml - http://us3.php.net/manual/ru/book.simplexml.php
		$xml = simplexml_load_string($content); // Load data from the string
		return ($xml);
	}

	// DBBL Response Parsing
	public function dbblResponseParsing($string) 
	{
		$stringArray = preg_split ('/$\R?^/m', $string);
		$response = array();
		if(is_array($stringArray))
		{
			foreach ($stringArray as $key => $value) 
			{
			  $value_exploder = explode(':', $value);
	 		  $response[trim($value_exploder[0])] = trim($value_exploder[1]);
			}
		}
		return (object) $response;
	}
	


}	

