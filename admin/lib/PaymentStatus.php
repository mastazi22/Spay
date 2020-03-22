<?php

	require_once("upay/token.php");	
	require_once("bkash/bkashLib.php");	
	require_once("ebl/skypay.php");	
class PaymentStatus {

	private $failed_array;
	private $success_array;
	private $bKash_json;
	private $bKash_api_key;
	public $configArray = array();		

	public function __construct() {
		//		
		$this->failed_array = 
			array(
				'gw_return_id' => '001',
				'gw_return_msg'  => 'Failed',
				'return_code' => '001',
				'bank_status' => 'FAIL',				
				'return_code' => '001'
			);	
		$this->success_array = 
			array(
				'gw_return_id' => '000',
				'gw_return_msg' => 'Approved',
				'return_code' => '000',
				'bank_status' => 'SUCCESS',				
				'return_code' => '000'
			);	
		//EBL configuration	
		$this->configArray["gatewayMode"] = TRUE;
		$this->configArray["certificateVerifyPeer"] = FALSE;
		$this->configArray["certificateVerifyHost"] = 0;		
		$this->configArray["debug"] = FALSE;
		$this->configArray["version"] = "41";
	

	}

	public function getPaymentStatus($order_id, $gateway, $bank_tx_id, $city_session_id, $city_order_id, $cred = null)
	{
		
		if($gateway == 'mx' && !is_null($bank_tx_id) )
		{
			
			$result = $this->mxOrderStatus($cred['CityBankMerchant'],$city_order_id,$city_session_id);
			$bank_response = array('bank_response' => json_encode($result));

			if( isset($result->OrderStatus) && $result->OrderStatus == 'APPROVED' )
			{
				$response = array_merge($this->success_array, $bank_response, array('card_number'=>$result->CardHolderName,'card_holder_name'=>$result->CardHolderName));
			}
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}			
		}
		elseif($gateway == 'dbbl' && !is_null($bank_tx_id) )
		{			
			
			$result = $this->dbblOrderStatus($bank_tx_id);
			$bank_response = array('bank_response' => json_encode($result));

			if( $result->RESULT == "OK" )
			{
				$response = array_merge($this->success_array, $bank_response, 
					array('card_number'=>$result->CARD_NUMBER,'card_holder_name'=>$result->CARDNAME)
				);				
			} 
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}			
			
		}
		elseif( (substr($gateway,0,5) == 'bkash') && !is_null($bank_tx_id) )
		{
			$result = $this->bKashApiOrderStatus($bank_tx_id);				
			$bank_response = array('bank_response' => json_encode($result));

			if( isset($result->transactionStatus) && ( $result->transactionStatus == "Completed" ) )
			{
				$response = array_merge($this->success_array, $bank_response);				
			} 
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}

		}
		elseif($gateway == 'tbl' && !is_null($order_id))
		{
			$result = $this->tblOrderStatus($order_id,$cred['TBLMerchantId'],$cred['TBLKeyCode']);
			$bank_response = array('bank_response' => json_encode($result));
			$card = isset($result->PAN)?array('card_number'=>$result->PAN):'';
			
			if( isset($result->StatusText) && ($result->StatusText == "PAID"))
			{
				$response = array_merge($this->success_array, $bank_response, $card);				
			} 
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}
		}
		elseif($gateway == 'upay' && !is_null($order_id))
		{
			$result = $this->upayOrderStatus($order_id);
			$bank_response = array('bank_response' => json_encode($result));			

			if( isset($result->status) && ($result->status  == "paid" ) )
			{
				$response = array_merge($this->success_array, $bank_response);				
			} 
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}		
		}
		elseif($gateway == 'ebl' && !is_null($bank_tx_id))
		{
			
			$result = $this->eblOrderStatus($cred, $bank_tx_id);
			$bank_response = array('bank_response' => json_encode($result));			

			if( isset($result['result']) && ($result['result']  == "SUCCESS" ) )
			{
				$response = array_merge($this->success_array, $bank_response);				
			} 
			else
			{
				$response = array_merge($this->failed_array, $bank_response);
			}	
		}
		return $response;	
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
			return $xml->Response->Order;
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
			if(isset($DbblResponseObject) && is_object($DbblResponseObject))
			    return $DbblResponseObject;
			else
			  return FALSE;
	    } 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function eblOrderStatusOld($OrderID,$merchantObj)
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
				return $parsed_array;
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function eblOrderStatus($cred, $bank_tx_id)
	{
		try 
		{
			$this->configArray["merchantId"] = $cred['eblMerchantId'];
			$this->configArray["password"]   = $cred['eblPassword'];			
			$skypay = new skypay($this->configArray);
    		$responseArray = $skypay->RetrieveOrder($bank_tx_id);
			if(is_array($obj) && isset($responseArray['result']))
				return $obj;
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
				return $CheckStatus;			
			else
				return FALSE;
		} 
		catch (Exception $e) 
		{
			$msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			return FALSE;
		}
	}

	public function bKashApiOrderStatus($bank_tx_id)
	{
		try 
		{
			$tokenReq = new bkashLib();
			$token    = $tokenReq->getToken();		
			$response = $tokenReq->verify($token['id_token'], $bank_tx_id);					
			$obj = json_decode($response);			
			// if(is_object($obj) && isset($obj->transactionStatus))
			if(is_object($obj))
				return $obj;
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
			
		try 
		{
			$tokenReq = new TokenRequest();
			$VerifyResponse = $tokenReq->verify($OrderID);	
			$ResponseArrayObject = json_decode($VerifyResponse);	
			if(isset($ResponseArrayObject->data->order->status))
				return $ResponseArrayObject->data->order;		
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

