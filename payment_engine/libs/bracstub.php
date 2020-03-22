
<form method="POST" action="/shurjopay/epay/brac/decryptstub.php">
<textarea name="bank_response" rows="20" cols="70" name="bank_response">
	<res><cur>BDT</cur><auth_code>057552</auth_code><txn_status>ACCEPTED</txn_status>
		<server_time>2013-01-14 10:48:01</server_time><bank_ref_id>406815</bank_ref_id>
		<name>ZAHID HOSSAIN CHOWDHURY</name><lang>eng</lang><ipg_txn_id>SHURJOMUKHI65161358138881418</ipg_txn_id>
		<mer_var1>http://dev.shurjomukhi.com/shurjorajjo/shurjopay/r</mer_var1><mer_var2>userid=2</mer_var2><mer_var3>shurjoPay</mer_var3>
		<mer_var4>shurjoPay</mer_var4><txn_amt><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?></txn_amt><mer_txn_id>bank ref id</mer_txn_id>
		<action>SaleTxn</action><acc_no>432149XXXXXX2526</acc_no></res></textarea>
<input type="submit" name="Submit">
</form>
<?php

class BRAC
{
	public $cur='BDT';
	public $txn_amt;
	public $mer_txn_id;
	public $mer_id='SHURJOMUKHI';
	public $action='SaleTxn';
	public $mer_var1;
	public $mer_var2;
	public $mer_var3;
	public $mer_var4;
	public $ipg_server_url='https://igate.bracbank.com/ipg/servlet_pay';
	public $ret_url;
	public $ipg_txn_id;
	public $DB;
	
	public function __construct() {
		$this->DB=new DB();
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
				$this->ret_url = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/epay/brac/decrypt.php";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
				$this->ret_url = "http://".$_SERVER["SERVER_NAME"]."/shurjopay/epay/brac/decrypt.php";
		  	break;
		  	// Live site configuration
		  	default:
				$this->ret_url = "https://".$_SERVER["SERVER_NAME"]."/epay/brac/decrypt.php";
		  	break;
	 	}
	}
	
	
	public function generateTransactionID() {
		$transaction_id = uniqid("BR"); // BRxxxxxxxxxxxxx 15 char unique transaction id
		$_SESSION['CURRENT_TRANSACTION_TYPE']='BRAC';
		$_SESSION['BRAC_transactionID']=$transaction_id;
		return $transaction_id;	
	
	}
	
	public function get_mer_txn_id() {
		$transaction_id = $this->getTransactionID();
		$this->mer_txn_id=$transaction_id;
		return $this->mer_txn_id;
				
	} // end get_mer_txn_id
	
	
	
	public function set_txn_amt($amount){ $this->txn_amt=$amount;}
	public function get_txn_amt(){ return $this->txn_amt;}
	
	public function set_currency($code='BDT') {$this->cur=$code; }
	public function get_currency() { return $this->cur; }
	
	public function set_action($action='SaleTxn'){ $this->action=trim($action);}
	public function get_action(){ return $this->action;}
	
	public function set_mer_var1($var){ $this->mer_var1=$var; }
	public function set_mer_var2($var){ $this->mer_var2=$var; }
	public function set_mer_var3($var){ $this->mer_var3=$var; }
	public function set_mer_var4($var){ $this->mer_var4=$var; }
	
	public function get_mer_var1(){return $this->mer_var1;}
	public function get_mer_var2(){return $this->mer_var2;}
	public function get_mer_var3(){return $this->mer_var3;}
	public function get_mer_var4(){return $this->mer_var4;}
	
	public function set_ipg_txn_id($tnx_id){ $this->ipg_txn_id=$tnx_id; }
	public function get_ipg_txn_id(){ return $this->ipg_txn_id; }
	
	
	
	public function requestPayment() {
		$action=$this->get_action();
				$IPGClientIP = 'shurjomukhi.com';
				$IPGClientPort = "10000";
				
				$ERRNO = "";
				$ERRSTR = "";
				$SOCKET_TIMEOUT = 2;
				$IPGSocket = "";
				
				$error_message = "";
				$invoice_sent_error = "";
				$encryption_ERR = "";
				
				$Invoice = "";
				$EncryptedInvoice = "";
				
				$IPGServerURL = $this->ipg_server_url;
		
		
				/**
				* Create invoice for sale transaction
				*/
				if($action == "SaleTxn") {
					 $Invoice = "<req>".
							"<mer_id>".$this->mer_id ."</mer_id>".
							"<mer_txn_id>".$this->get_mer_txn_id()."</mer_txn_id>".
							"<action>".$this->get_action()."</action>".
							"<txn_amt>".$this->get_txn_amt()."</txn_amt>".
							"<cur>".$this->get_currency()."</cur>" .
							"<lang>en</lang>".
							"<ret_url>".$this->ret_url."</ret_url>".
							"<mer_var1>".$this->get_mer_var1()."</mer_var1>".
							"<mer_var2>".$this->get_mer_var2()."</mer_var2>".
							"<mer_var3>".$this->get_mer_var3()."</mer_var3>".
							"<mer_var4>".$this->get_mer_var4()."</mer_var4>".
							"</req>";

				}
				
				/**
				* Create invoice for sale merchant updated
				*/
				if($action == "SaleMerchUpdated") {
						$Invoice = "<req>".
								"<mer_id>". $this->mer_id ."</mer_id>".
								"<mer_txn_id>".$this->get_mer_txn_id()."</mer_txn_id>".
								"<action>".$this->get_action()."</action>".
								"<ipg_txn_id>".$this->get_ipg_txn_id()."</ipg_txn_id>".
								"<ret_url>".$this->ret_url."</ret_url>".
								"</req>";
				
				}
				
				/**
				* Create invoice for sale transaction verify
				*/
				if($action == "SaleTxnVerify") {
					$Invoice = "<req>".
							"<mer_id>".$this->mer_id."</mer_id>".
							"<mer_txn_id>".$this->get_mer_txn_id()."</mer_txn_id>".
							"<action>".$this->get_action()."</action>".
							"<ret_url>".$this->ret_url."</ret_url>".
							"</req>";

				}
						
						
			// end invoice creation
			//================================
			
			/**
			* Step 1 : Create the socket connection with IPG client
			*/
			
				if ($IPGClientIP != "" && $IPGClientPort != "") {
					$IPGSocket = fsockopen($IPGClientIP, $IPGClientPort, $ERRNO, $ERRSTR, $SOCKET_TIMEOUT);
				} else {
					$error_message = "Could not establish a socket connection for given IPGClientIP = ". $IPGClientIP . "and IPGClientPort = ".$IPGClientPort; 
					$socket_creation_err = true;
				}
			
			/**
			* Step 2 : Send Invoice to IPG client 
			*/
			
				if(!$socket_creation_err) {
					socket_set_timeout($IPGSocket, $SOCKET_TIMEOUT);
					// Write the invoice to socket connection
					//echo $Invoice.$IPGServerURL;
					if(fwrite($IPGSocket,$Invoice) === false) {
						$error_message .= "Invoice could not be written to socket connection";
						$invoice_sent_error = true;
					}
				}
				
			
			/**
			* Step 3 : Recieve the encrypted Invoice from IPG client
			*/
			
				if(!$socket_creation_err && !$invoice_sent_error) {
					while (!feof($IPGSocket)) {
						$EncryptedInvoice .= fread($IPGSocket, 8192);
					}    
				}
			
			/**
			* Step 4 : Close the socket connection
			*/
				if(!$socket_creation_err) {
					fclose($IPGSocket);
				}
			
			/**
			* Step 5 : Check for Encryption errors
			*/
			
				if (!(strpos($EncryptedInvoice, '<error_code>') === false && strpos($EncryptedInvoice, '</error_code>') === false && strpos($EncryptedInvoice, '<error_msg>') === false && strpos($EncryptedInvoice, '</error_msg>') === false)) 
				{
					$encryption_ERR = true;
					
					$Error_code = substr($EncryptedInvoice, (strpos($EncryptedInvoice, '<error_code>')+12), (strpos($EncryptedInvoice, '</error_code>') - (strpos($EncryptedInvoice, '<error_code>')+12)));
				
					$Error_msg = substr($EncryptedInvoice, (strpos($EncryptedInvoice, '<error_msg>')+11), (strpos($EncryptedInvoice, '</error_msg>') - (strpos($EncryptedInvoice, '<error_msg>')+11)));
				
				}			
			
				
			/**
			* Step 6 : Submit Encripted invoice to IPG server
			*/
			
				if(!$socket_creation_err && !$invoice_sent_error && !$encryption_ERR) {	
				
				$date=new DateTime();
				$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
				$current_time=$date->format('Y-m-d H:i:s');
				// insert the transaction ID into the DB
				$data=array('transaction_id'=>$this->mer_txn_id,
							'transaction_time'=>$current_time);
							
				$inserted_db=$this->DB->insert(BRAC_TNX_TABLE,$data);
				if($inserted_db){			
					?>
					<html>
					<head>
					</head>
						<body onLoad="document.send_form.submit();">
						  <form name="send_form" method="post" action="<?php echo $IPGServerURL?>" >
						    <input type="hidden" value="<?php echo $EncryptedInvoice?>" name="encryptedInvoicePay">
						  </form>
						</body>
			
					</html>
					<?php
				 }// end if
				 
				}else {
				
				// handale fsiled db insertion 
			}
	} // end requestPayment
	
	
	public function getPaymentStatus($transaction_id){
		
		$result=$this->DB->select(BRAC_TNX_TABLE,array('transaction_id'=>$transaction_id));
		
		$posted_xml_data=$result[0]->posted_xml_data;
		$xml_obj=simplexml_load_string($posted_xml_data);
		$status=(array)$xml_obj->txn_status;
		$data['status']=$status[0];
		$data['txn_id']=$transaction_id;
		$data['txn_time']=$result[0]->transaction_time;
		$data['data']=(array)$xml_obj;

		return $data;
		
	}
	
	public function getTransactionID(){
		if(isset($_SESSION['BRAC_transactionID']) && $_SESSION['BRAC_transactionID']!="" )
		return $_SESSION['BRAC_transactionID'];
		else
		return false;
	}
	
	
	
} // end class

?>
