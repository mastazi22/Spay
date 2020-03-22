<?php

// DBBL payment gateway
class DBBL {

	public $ammount;
	public $ipAddress;
	public $openDescription;
	public $hiddenDescription;
	public $DB;

	public function __construct() {
		$this->DB = new DB();
		$this->ammount = 0;
		$this->ipAddress = $_SERVER['REMOTE_ADDR'];
		$this->openDescription = '';
		$this->hiddenDescription = 'shurjomukhi';
	}

	public function setAmmount($ammount) {
		$this->ammount = floatval($ammount);
	}
	public function getAmount() {
		// it will return the paisa. example 1 taka= 1*100 paisa
		return $this->ammount * 100;
	}
	public function getIpAddress() {
		return $this->ipAddress;
	}
	public function setOpenDescription($description) {
		$this->openDescription = $description;
	}
	public function getOpenDescription() {
		return $this->openDescription;
	}
	public function setHiddenDescription($description) {
		$this->hiddenDescription = $description;
	}
	public function getHiddenDescription() {
		return $this->hiddenDescription;
	}

	public function requestPayment() {

		$trans_id = $this->getTransactionID();

		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		$current_time = $date->format('Y-m-d H:i:s');
		$data = array ('transaction_id' => $trans_id, 'transaction_time' => $current_time);
		//print_r($data);
		// insert the transaction ID into the DB		
		$inserted_db = $this->DB->insert(DBBL_TNX_TABLE, $data);
		file_put_contents('file_process.txt', print_r($_SESSION['order_details_response']['paymentOption']. "------ before Session ---", true),FILE_APPEND);
		if ($inserted_db) {
			//echo "why";
			//exit;
			$trans_id = urlencode($trans_id);
				file_put_contents('file_process.txt', print_r($_SESSION['order_details_response']['paymentOption']. "------ after Session  ---", true),FILE_APPEND);
			if ($_SESSION['order_details_response']['paymentOption'] == "DBBL Nexus") {
				$bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=1&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			} 
			else if ($_SESSION['order_details_response']['paymentOption'] == "DBBL Master") {
				$bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=2&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			} 
			else if ($_SESSION['order_details_response']['paymentOption'] == "DBBL VISA") {
				$bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=3&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			} 
			else if ($_SESSION['order_details_response']['paymentOption'] == "VISA") {
				$bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=4&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			} 
			else if ($_SESSION['order_details_response']['paymentOption'] == "Master Card") {
				$bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=5&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			}else if($_SESSION['order_details_response']['paymentOption'] == "dbbl Mobile"){
			  $bank_gateway = "https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler?card_type=6&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();
			
			}
			//$bank_gateway="https://ecom.dutchbanglabank.com/ecomm2/ClientHandler?card_type=1&trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount();

			//header("Location: https://ecom.dutchbanglabank.com/ecomm2/ClientHandler?trans_id=".$trans_id."&product_name=".$this->getOpenDescription()."&product_quantity=1&product_price=".$this->getAmount());

			// js redirect
			print ('<script language="javascript" type="text/javascript">
								window.location.href="'.$bank_gateway.'";
								</script>
								');
		}
	}

	public function generateTransactionID() {
		//$str = '/opt/jdk1.6.0_21/bin/java -jar  "/opt/DBBL/key/ecomm_merchant.jar" "/opt/DBBL/key/merchant.properties" -v '.$this->getAmount().' 050 '.$this->getIpAddress().' '.$this->getOpenDescription().' --mrch_transaction_id='.$this->getHiddenDescription();
		$str = '/opt/jdk1.6.0_21/bin/java -jar  "/opt/DBBL/keynew/ecomm_merchant.jar" "/opt/DBBL/keynew/merchant.properties" -v '.$this->getAmount().' 050 '.$this->getIpAddress().' '.$this->getOpenDescription().' --mrch_transaction_id='.$this->getHiddenDescription();
		
			// creating log
	//$fp = fopen('log.txt', 'a');
	//fwrite($fp, "test");
	//fclose($fp);

		$outputArray = array ();
		exec($str, $outputArray);

		$final = $outputArray[0];
		$trans_id = substr($final, 16, 40);
		if ($trans_id != "") {
			$_SESSION['CURRENT_TRANSACTION_TYPE'] = 'DBBL';
			$_SESSION['DBBL_transactionID'] = $trans_id;
			return $trans_id;
		} 
		else {
			return FALSE;
		}
	} // end  generateTransactionID

	public function getPaymentStatus($transaction_id) {

		$result = $this->DB->select(DBBL_TNX_TABLE, array ('transaction_id' => $transaction_id));

		$return_a_data = array ();
		$posted_data = unserialize($result[0]->posted_data);
		$returned_array_data = unserialize($result[0]->returned_array_data);
		if (is_array($returned_array_data)) {
			foreach ($returned_array_data as $rd) {
				$tempArr = explode(":", $rd);
				$return_a_data[$tempArr[0]] = trim($tempArr[1]);
			}
		}

		// check if retrun array data contain RESULT_CODE == 000 then update the status to OK. 
		// else we assume its failed transaction
		if ($return_a_data['RESULT_CODE'] == '000' && $return_a_data['RESULT'] == 'OK') {
			$data['status'] = "OK";
		} 
		else {
			$data['status'] = "FAILED";
		}
		$data['txn_id'] = $transaction_id;
		$data['txn_time'] = $result[0]->transaction_time;
		$data['data'] = array_merge($posted_data, $return_a_data);

		return $data;

	}

	public function getTransactionID() {
		if (isset ($_SESSION['DBBL_transactionID']) && $_SESSION['DBBL_transactionID'] != "")
			return $_SESSION['DBBL_transactionID'];
		else
			return false;
	}

} // end class DBBL
?>

