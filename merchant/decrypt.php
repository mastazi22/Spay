<?php
error_reporting(E_ALL);
    $PUBLIC_KEY='/etc/sp_key/public.pem'; 
    $PRIVATE_KEY='/etc/sp_key/private.key';
    
    $data = $_GET['data'];
//echo $data;    
    define('PUBLIC_KEY', file_get_contents($PUBLIC_KEY));
    define('PRIVATE_KEY', file_get_contents($PRIVATE_KEY));		
//echo "1";
    $m = json_decode(base64_decode($data), true);		
    $data = get_original_data($m['msg'], $m['key'], PRIVATE_KEY);
    $msg_data = json_decode($data, true);
//print_r($msg_data);
echo $msg_data['spay_data'];
    //return trim($msg_data['spay_data']);    
    
    function get_original_data($msg, $key, $private_key) {
                    if ($msg == '') {
                            return false;
                    } else {
                            $privateKey = openssl_get_privatekey($private_key);
                            $result = openssl_open(base64_decode($msg), $decryptedData, base64_decode($key), $privateKey);
                            return $decryptedData;
                    }
    }
