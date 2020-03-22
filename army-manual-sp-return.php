<?php 

error_reporting(E_ALL);
// include("includes/configure.php");
include ("includes/return.php");

	
$date = new DateTime();
$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
	



/*
$bank_result = $returnXML->getBankRefID($order_id);
$bank_ref_id = $bank_result->bank_tx_id;
$payment_method = $bank_result->method;

$txID = 'JBD_1569393655295';
$bankTxID = '6IP2431TKU';
$bankTxStatus = 'SUCCESS';
$txnAmount = '1000';
$spCode = '000';
$spCodeDes = 'Successful';
$paymentOption = 'bkash_api';
*/
$array = array( 

//array('JBD_1572417272553', 'SP5db92ef4c308c', 1000, 'dbbl_mobile', 'rCap/RFqfkLNlE4nMxyQUUKdEz4=', 'http://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572423333651', 'SP5db946a191cef', 1000, 'bkash_api', '6JU4M47IYY', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572425739935', 'SP5db95007dafb2', 1000, 'bkash_api', '6JU0M4TBXQ', 'http://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572432356212', 'SP5db969e359488', 1000, 'bkash_api', '6JU9M6Z335', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572432364642', 'SP5db969e8a5f19', 1000, 'bkash_api', '6JU8M6RLI4', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572432810262', 'SP5db96ba783636', 1000, 'bkash_api', '6JU7M6YYMT', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572432824919', 'SP5db96bb4e8eda', 1000, 'bkash_api', '6JU2M6Z1W6', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572438061157', 'SP5db9802d5087b', 1000, 'bkash_api', '6JU7M9DWZB', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572439045259', 'SP5db98405c63a6', 1000, 'bkash_api', '6JU2M9WA72', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572439050830', 'SP5db9840ab9f88', 1000, 'bkash_api', '6JU8M9SSTW', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572439083586', 'SP5db9842b8fb03', 1000, 'bkash_api', '6JU3M9U1XN', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572439106989', 'SP5db984430992f', 1000, 'bkash_api', '6JU9M9TDYH', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572440425525', 'SP5db98969a978f', 1000, 'bkash_api', '6JU0MALV38', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572442863541', 'SP5db992efa4906', 1000, 'bkash_api', '6JU5MCA74F', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572442872434', 'SP5db992f885fbc', 1000, 'bkash_api', '6JU0MBUKSE', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572442872462', 'SP5db992f8860ef', 1000, 'bkash_api', '6JU2MBMDQS', 'http://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572446574449', 'SP5db9a16eaa945', 1000, 'bkash_api', '6JU6MDBWJE', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572446815757', 'SP5db9a260e435e', 1000, 'bkash_api', '6JU3MDE4QT', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572446891438', 'SP5db9a2ab7fea4', 1000, 'bkash_api', '6JU3MDBY4T', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572449742415', 'SP5db9adcfc2e51', 1000, 'bkash_api', '6JU9MEEDFV', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572449752972', 'SP5db9add906785', 1000, 'bkash_api', '6JU9MEKJOP', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572450858956', 'SP5db9b22b00221', 1000, 'bkash_api', '6JU5MESS8F', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572451079549', 'SP5db9b30859b96', 1000, 'bkash_api', '6JU7METSOZ', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572451109382', 'SP5db9b3257e879', 1000, 'bkash_api', '6JU6MESKO8', 'http://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572451109573', 'SP5db9b325a06af', 1000, 'bkash_api', '6JU4MEZQZC', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572451172358', 'SP5db9b3646bb50', 1000, 'bkash_api', '6JU9METD1Z', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572453299419', 'SP5db9bbb358d9b', 1000, 'bkash_api', '6JU3MFD6FH', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572456416119', 'SP5db9c7e024a62', 1000, 'bkash_api', '6JU4MG1JY2', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572456764868', 'SP5db9c93ccd5b2', 1000, 'bkash_api', '6JU2MFXTD0', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572456866678', 'SP5db9c9a2d4e98', 1000, 'bkash_api', '6JU2MFYQNM', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572457613634', 'SP5db9cc8e54631', 1000, 'bkash_api', '6JU4MG11O0', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
// array('JBD_1572457963784', 'SP5db9cdebe627c', 1000, 'bkash_api', '6JU9MG2DVP', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl')

//array('JBD_1572426076943', 'SP5db95158d3220', 1000, 'bkash_api', '6JU9M4VWRD', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
//array('JBD_1572434648633', 'SP5db972d61be21', 1000, 'bkash_api', '6JU6M7XHFW', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
//array('JBD_1572449291406', 'SP5db9ac0b7eb4c', 1000, 'bkash_api', '6JU1ME8F0R', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
//array('JBD_1572450545248', 'SP5db9b0f15eb8e', 1000, 'bkash_api', '6JU0MES8KS', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl'),
//array('JBD_1572459630999', 'SP5db9d46ee2516', 1000, 'dbbl_mobile', 'lVf3iMblJUfLkU5LypSF5AgO/ho=', 'https://joinbangladesharmy.army.mil.bd/Payment/ReturnUrl')

);

foreach($array as $key => $val)
{
	spResponse($val[0], $val[4], 'SUCCESS', $val[2], '000', 'Successful', $val[3], $val[5]);
	echo "<br>";
	exit;
}

define('PUBLIC_KEY', file_get_contents('/home/smasif/sp_key/public.pem'));
define('PRIVATE_KEY', file_get_contents('/home/smasif/sp_key/private.key'));


function spResponse($txID, $bankTxID, $bankTxStatus, $txnAmount, $spCode, $spCodeDes, $paymentOption, $returnURL)
{


	$post_data = '<?xml version="1.0" encoding="utf-8"?>
	            <spResponse><txID>'.$txID.'</txID>
	            <bankTxID>'.$bankTxID.'</bankTxID>
	            <bankTxStatus>'.$bankTxStatus.'</bankTxStatus>
	            <txnAmount>'.$txnAmount.'</txnAmount>
	            <spCode>'.$spCode.'</spCode>
	            <spCodeDes>'.$spCodeDes.'</spCodeDes>
	            <paymentOption>'.$paymentOption.'</paymentOption></spResponse>';
// echo $post_data;
// $returnXML = new ReturnXML();
$data = json_encode(array ('spay_data' => $post_data));

$post_data = base64_encode(get_encrypted_data($data, file_get_contents('/home/smasif/sp_key/public.pem')));
//$returnURL = $returnXML->getReturnURL($order_id);	
//$userip = $returnXML->getReturnIP();

echo $return = "<form target='_blank' method='post' action='$returnURL' id='frm_submit'>
			<input type='hidden' name='spdata' value='$post_data'>
			</form>
		<script>
			document.getElementById('frm_submit').submit();
		</script>";

//postResponse($returnURL, $post_data);		

}


	function get_encrypted_data($msg = '', $public_key = '') {
		if ($msg == '')
			return false;
		$publicKeys[] = openssl_get_publickey($public_key);
		$res = openssl_seal($msg, $encryptedText, $encryptedKeys, $publicKeys);
		$data = json_encode(array ("msg" => base64_encode($encryptedText), 'key' => base64_encode($encryptedKeys[0])));
		return $data;
	}

	function postResponse($url, $xml_data)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		$response = curl_exec($ch);
		curl_close ($ch);
		print_r($response);
		
	}
?>


	



