<?php
class Login {

	public function inputValidation($merchantName, $merchantPass, $userIP, $uniqid, $amount, $payment_option, $returnURL, $con) {
		$err_msg = "";
		if(trim($merchantName)==""){
			$err_msg .= "Merchant name should not empty<br/>";
		}
		
		if(trim($merchantPass)==""){
			$err_msg .= "Merchant password should not empty<br/>";
		}
		
		if (!filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$err_msg .= "IP address is not valid<br/>";
		}
		
		if(trim($uniqid)==""){
			$err_msg .= "Unique ID should not empty<br/>";
		}
		if(trim($payment_option)==""){
			$err_msg .= "Payment option should not empty<br/>";
		}
		else{
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT payment_name, payment_status from sp_payment_options WHERE payment_code='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$payment_option)."'", $con);
			$result= mysqli_fetch_object($sql_query);
			if($result->payment_status==0){
				$err_msg .= "Sorry your choosen payment option ".$result->payment_name." currently not avaiable, please choose other payment option if you have<br/>";
			}		
		}
		if(trim($amount)!=""){
			$ex_amt = explode('.', $amount);
			if (count($ex_amt) > 2) {
				$err_msg .= "Amount is not valid<br/>";
			} 
			elseif (!is_array($ex_amt) and count($ex_amt) != 1) {
				$err_msg .= "Amount is not valid<br/>";
			}
			elseif ((isset ($ex_amt[1]) and isset ($ex_amt[0])) and strlen($ex_amt[1]) > 2 or (!is_numeric($ex_amt[0]) and !is_numeric($ex_amt[1]))) {
				$err_msg .= "Amount is not valid<br/>";
			}
		}
		else{
			$err_msg .= "Amount should not empty<br/>";
		}
		
		if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $returnURL)) {
			$err_msg .= "URL is not valid<br/>";
		}
		return $err_msg;
	}
	public function merchantVerification($result, $uniqid, $ip){
		$err_msg = "";
		//echo $uniqid;
		//echo $ip;
		//echo "<br>";
		//var_dump ($result);
		//exit ();
		if(!strstr($uniqid,'ATI') && !strstr($uniqid,'DSE'))
		{
			//echo "here";
			if($result->merchant_ip!=$ip){
				$err_msg .= "Merchant IP is not valid<br/>";
			}
		}
		if(substr($uniqid,0,3)!=$result->unique_id_code){
			$err_msg .= "Merchant unique code is not valid<br/>";
		}
		return $err_msg;
	}
	public function mudraLogin($post,$con){
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT userid from usersinfo WHERE username='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$post['username'])."' and password='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],md5($post['password']))."' and isUserActive='Yes' and isMobileValidated='Yes' and isEmailValidated='Yes' and isAccountClosed='No' and addressLine1!=''",$con);
		$result= mysqli_fetch_object($sql_query);		
		return $result;
	}
	public function chkBlocking($smUid,$con){
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT isAccountClosed from logindb.usersinfo WHERE userid=".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid),$con);
		$result= mysqli_fetch_object($sql_query);		
		return $result;
	}

	public function chkBlockingFromApi($smUid) 
	{
		$token = '6dz8TQPM5V4W';
		// get user info
		$ch  = curl_init();
		$url = "https://paypoint.shurjorajjo.com.bd/payapi/home/userInfo";		
		$req_data = http_build_query(
			array(
				'token' => $token,
				'smUid' => $smUid				
			)
		);		
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
		curl_setopt($ch,CURLOPT_POSTFIELDS,$req_data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);		
		$response = curl_exec($ch);	
		curl_close ($ch);								
		return (object) json_decode($response);
	}

	function getPaypointUserOrders($uniqID)
	{
		$token = '6dz8TQPM5V4W';
		// get user info
		$ch  = curl_init();
		$url = "https://paypoint.shurjorajjo.com.bd/payapi/home/paypointOrderInfo";		
		$req_data = http_build_query(
			array(
				'token' => $token,
				'uniqID' => $uniqID				
			)
		);		
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
		curl_setopt($ch,CURLOPT_POSTFIELDS,$req_data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);		
		$response = curl_exec($ch);	
		curl_close ($ch);								
		return (object) json_decode($response);
	}

	public function countTxBlocking($smUid,$con){
		//$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT count(smUid) as cid from shurjopay.sp_epay WHERE smUid= ".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid)." AND Date(intime) = curdate() AND bank_status = 'SUCCESS'",$con);
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT count(smUid) as cid from shurjopay.sp_epay WHERE smUid= ".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid)." AND Date(intime) = curdate() AND bank_status = 'SUCCESS'");
		$result= mysqli_fetch_object($sql_query);
		if($smUid != '10000721548') {
			if ($result->cid < 6) {     //No of transactions allowed for a userid
				return "Allow";
			} else {
				return "Deny";
			}
		} return "Allow";
	}
}
?>


