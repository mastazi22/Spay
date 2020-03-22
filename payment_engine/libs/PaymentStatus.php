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
		$xml=$this->PostQW($data);
		return $xml->Response->Order->OrderStatus;		
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

}	


