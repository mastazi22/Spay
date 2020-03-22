<?php
	$url = 'https://ipaysafe-ws.islamibankbd.com:8998/services/CheckPaymentStatusService';
	$method = 'POST';
	$encodeValue=base64_encode('1bbl1P@ys@f3Cl13nt:1bbl1P@ys@f3Cl13ntP@ss');
	$auth='Basic '.$encodeValue;
	$headers = array(
		'Content-Type:application/xml',
		'clientId:IBB.MRCNT.99131204164840',
		'token:df67cce65ff4bb5d45512b2a9572a48582bdd6a16bd6af4a631c2ed5f7c33127',
		'merchantSecret:7f486103d1f0ed36276677e9d5a44f66c45ea065',
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
