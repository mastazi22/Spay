<?php
	$url = 'https://app.islamibankbd.com:8998/paygateRest/services/InitPaymentProcessService';
	$method = 'POST';
	$encodeValue=base64_encode("R3stT3stUs3r:R3stT3stUs3rP@ss");
	$auth='Basic '.$encodeValue;
	$headers = array(
		'Content-Type:application/xml',
		'clientId:IBB.MRCNT.99131204164840',
		'productName:Samsung',
		'amount:201.00',
		'paymentMethod:101',
		'clientRefId:ewr4ae6gd67gdgf444rr',
		'merchantSecret:7f486103d1f0ed36276677e9d5a44f66c45ea065',
		'returnUrl:www.google.com/success.php',
		'Authorization:'.$auth,
	    );
   
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	switch($method) {
		case 'POST':
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, null);
		break;
			}
	$response = curl_exec($handle);
	$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	echo $response;
?> 
