<?php
class ReturnXML {

	public function __construct() {
		//
	}

	public function getReturnURL($order_id) {
		$sql_log = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT returnurl from sp_epay WHERE order_id='".$order_id."'");
		$return_url = mysqli_fetch_object($sql_log);

		return $return_url->returnurl;
	}
	
	public function getBankRefID($order_id) {
		$sql_log = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT bank_tx_id, method from sp_epay WHERE order_id='".$order_id."'");
		$return_url = mysqli_fetch_object($sql_log);

		return $return_url;
	}

	public function getReturnIP() {
		$sql_ip = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT merchant_ip from sp_merchants WHERE id='".$_SESSION['ORDER_DETAILS']['userID']."'");
		$return_ip = mysqli_fetch_object($sql_ip);

		return $return_ip->merchant_ip;
	}

	public function pingAddress($ip) {

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$ip);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (200 == $retcode) {
			return 1;
		} 
		else {
			return 0;
		}
	}
	
	function get_encrypted_data($msg = '', $public_key = '') {
		if ($msg == '')
			return false;
		$publicKeys[] = openssl_get_publickey($public_key);
		$res = openssl_seal($msg, $encryptedText, $encryptedKeys, $publicKeys);
		$data = json_encode(array ("msg" => base64_encode($encryptedText), 'key' => base64_encode($encryptedKeys[0])));
		return $data;
	}

}
?>


