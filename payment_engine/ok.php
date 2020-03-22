<?php
error_reporting(E_ALL);

include('paymentEngine.php');
$db=new DB();


//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";


//$brac=new BRAC();
//$status=$brac->getPaymentStatus('cev9r9a629g7swf');

//echo "<pre>";
//print_r($status);
//echo "</pre>";


//$dbbl=new DBBL();
//$status=$dbbl->getPaymentStatus('JgsWGvKTq7aM7dWJQob0Ps1UkBQ=');

//echo "<pre>";
//print_r($status);
//echo "</pre>";




if($db->getTransactionType())
{
	$type=$db->getTransactionType(); // get the Transaction type. it will BRAC | DBBL return
	
	switch($type)
	{
		case 'BRAC':
					$brac=new BRAC();
					$result=$brac->getPaymentStatus($brac->getTransactionID());
					echo "<pre>";
					print_r($result);
					echo "</pre>";
					break;
		
		case 'DBBL':
					$dbbl=new DBBL();
					$result=$dbbl->getPaymentStatus($dbbl->getTransactionID());
					echo "<pre>";
					print_r($result);
					echo "</pre>";
					break;
		
	}// end switch
	
}


?>