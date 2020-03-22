<?php
$epay_base_path = __DIR__.DIRECTORY_SEPARATOR;

define('DBBL_TNX_TABLE', 'dbbl_transactions');
define('BRAC_TNX_TABLE', 'brac_transactions');
define('QCASH_TNX_TABLE', 'qcash');
define('QMERCHANT','SURMKH');
define('QURL','https://mpi.itcbd.com:18288/execHTML.jsp');

require ($epay_base_path.'dbconfig.php');

// this may be cause problem session for ssl and non ssl site redirection 
//if(!isset($_SESSION))
//session_start();

require ($epay_base_path.'libs/inc/DB.php');
include("mx/CreateOrder.php");
//require($epay_base_path.'createorder.php');
$DB = New DB;

if($_SERVER["SERVER_NAME"]=="localhost"){
	require ($epay_base_path.'libs/brac.php');
	//require ($epay_base_path.'libs/dbblstub.php');
	require ($epay_base_path.'libs/dbbl.php');
	//require ($epay_base_path.'libs/ibbl.php');
}
else{
	require ($epay_base_path.'libs/brac.php');
	require ($epay_base_path.'libs/dbbl.php');
	//require ($epay_base_path.'libs/dbblstub.php');
	//require ($epay_base_path.'libs/ibbl.php');
}
require ($epay_base_path.'libs/qcash.php');

// start paymentEngine class

class paymentEngine {

	private $payment_methods = array ();
	private $methodKey;
	private $userID;
	private $amount;
	private $returnPoint;
	private $productName;
	private $dbbl;
	private $brac;
	private $qcash;

	public function __construct() {

		$this->dbbl = new DBBL();
		$this->brac = new BRAC();
		$this->qcash = new QCASH();

		// initializs the available payment methods
		$this->payment_methods['visa'] = array ('text' => 'VISA', 'status' => 'active', 'img' => 'visa.jpg');
		$this->payment_methods['master_card'] = array ('text' => 'Master Card', 'status' => 'active', 'img' => 'mastercard.jpg');
		$this->payment_methods['dbbl_visa'] = array ('text' => 'DBBL VISA Debit', 'status' => 'active', 'img' => 'visa.jpg');
		$this->payment_methods['dbbl_master'] = array ('text' => 'DBBL Master Debit', 'status' => 'active', 'img' => 'mastercard.jpg');
		$this->payment_methods['dbbl_nexus'] = array ('text' => 'DBBL Nexus', 'status' => 'active', 'img' => 'dbbl-nexus.jpg');
		$this->payment_methods['paypal'] = array ('text' => 'Paypal', 'status' => 'inactive');
		$this->payment_methods['qcash'] = array ('text' => 'Q-Cash', 'status' => 'active', 'img' => 'q-cash.jpg');
		$this->payment_methods['ibbl'] = array ('text' => 'Islami Bank Bangladesh Limited', 'status' => 'active', 'img' => 'ibbl.jpg');
		$this->payment_methods['mCash_iBank'] = array ('text' => 'mCash / Islami Bank iBanking', 'status' => 'active', 'img' => 'mCash.jpg');
		$this->payment_methods['shurjomudra'] = array ('text' => 'shurjoMudra', 'status' => 'inactive', 'img' => 'shurjomudra.jpg');
		$this->payment_methods['trustmm'] = array ('text' => 'Trust Mobile Money', 'status' => 'inactive');
		$this->payment_methods['post_office'] = array ('text' => 'Post-Office E-pay', 'status' => 'inactive');
		$this->payment_methods['bkash'] = array ('text' => 'bKash Mobile Wallet', 'status' => 'active', 'img' => 'bkash.jpg');
		$this->payment_methods['dmw'] = array ('text' => 'DBBL Mobile Wallet', 'status' => 'inactive', 'img' => 'dbbl-mobile.jpg');
		$this->payment_methods['mtb'] = array ('text' => 'Mutual Trust Bank LTD', 'status' => 'active', 'img' => 'mtb.gif');
		$this->payment_methods['dbbl_mobile'] = array ('text' => 'DBBL Mobile', 'status' => 'active', 'img' => 'dbbl-nexus.jpg');
		$this->payment_methods['mx'] = array ('text' => 'MX', 'status' => 'active', 'img' => 'mx.jpg');

	}

	// this function will return an array for available payment methods
	public function getPaymentMethods() {
		return $this->payment_methods;
	}

	/* this function will initialize  the payment
	 * params
	 * @methodKey =  it will be payment method name e.g visa | master_card | dbbl_nexux
	 * @returnPoint =  where the payment gateway IPG server return response. it will section name 
	 				   e.g ShurjoMudra | ShurjoMudraDev |PayPoint | PayPointDev | PayPointDevTeletalk etc 
	
	*/

	public function init() {
		$methodKey = $this->methodKey;
		
		if ($methodKey == "dbbl_nexus" or $methodKey == 'visa' or $methodKey == 'master_card' or $methodKey == 'dbbl_visa' or $methodKey == 'dbbl_master' or $methodKey =='dbbl_mobile') {
			
	
			//echo "MethodKey : ".$methodKey;
			//exit;
			$this->dbbl->requestPayment();
		}

		/*if ($methodKey == 'visa' || $methodKey == 'master_card') {
			$this->brac->requestPayment();
		}*/

	} // end init function 

	// this function will provide the transaction id for gateway payment process
	// this function must call before the transaction init
	public function getTransactionId($methodKey, $userID, $amount, $returnPoint, $productName = '') {

			// Assign class variable with values
		$this->methodKey = $methodKey;
		$this->userID = $userID;
		$this->amount = $amount;
		$this->returnPoint = $returnPoint;
		$this->productName = $productName;

		$amount = number_format($amount, 2, '.', '');
		$MRCH_TRANSACTION_ID = substr(md5(uniqid()), 0, 8);
		
		if ($methodKey == 'dbbl_nexus' || $methodKey == 'visa' || $methodKey == 'master_card' || $methodKey == 'dbbl_visa' || $methodKey == 'dbbl_master' || $methodKey == 'dbbl_mobile') {
			$this->dbbl->setAmmount($amount); // total price
			if($_SERVER["SERVER_NAME"]=="dev.shurjomukhi.com"){
				$this->dbbl->setHiddenDescription($MRCH_TRANSACTION_ID.$userID.'dev');			
				if($methodKey=="dbbl_nexus"){
					$this->dbbl->setOpenDescription('1_from_dev::shurjoPay');
				}
				else if($methodKey=="dbbl_master"){
					$this->dbbl->setOpenDescription('2_from_dev::shurjoPay');
				}
				else if($methodKey=="dbbl_visa"){
					$this->dbbl->setOpenDescription('3_from_dev::shurjoPay');
				}
				else if($methodKey=="visa"){
					$this->dbbl->setOpenDescription('4_from_dev::shurjoPay');
				}
				else if($methodKey=="master_card"){
					$this->dbbl->setOpenDescription('5_from_dev::shurjoPay');
				}
				else if($methodKey=="dbbl_mobile"){
					$this->dbbl->setOpenDescription('6_from_dev::shurjoPay');
				}
			}
			else{
				$this->dbbl->setHiddenDescription($MRCH_TRANSACTION_ID.$userID);			
				if($methodKey=="dbbl_nexus"){
					$this->dbbl->setOpenDescription('1::shurjoPay');
				}
				else if($methodKey=="dbbl_master"){
					$this->dbbl->setOpenDescription('2::shurjoPay');
				}
				else if($methodKey=="dbbl_visa"){
					$this->dbbl->setOpenDescription('3::shurjoPay');
				}
				else if($methodKey=="visa"){
					$this->dbbl->setOpenDescription('4::shurjoPay');
				}
				else if($methodKey=="master_card"){
					$this->dbbl->setOpenDescription('5::shurjoPay');
				}
				else if($methodKey=="dbbl_mobile"){
					
					$this->dbbl->setOpenDescription('6::shurjoPay');
				}
			}
			//$this->dbbl->setOpenDescription('1'.$returnPoint.'::shurjoPay'); // product name
			//$this->dbbl->setOpenDescription('1::shurjoPay');  //card_type prefix=1 as requested by DBBL for preventing url mod
			return $this->dbbl->generateTransactionID();
		}

		/*if ($methodKey == 'visa' || $methodKey == 'master_card') {
			$this->brac->set_txn_amt($amount);
			$this->brac->set_mer_var1($returnPoint);
			$this->brac->set_mer_var2("userid=".$userID);
			$this->brac->set_mer_var3('shurjoPay');
			$this->brac->set_mer_var4($productName);
			return $this->brac->generateTransactionID();
		}*/

	}
	
	public function getMxOrderId($methodKey, $userID, $amount, $returnPoint, $productName = '') {
	// Assign class variable with values
		$this->methodKey = $methodKey;
		$this->userID = $userID;
		$this->amount = $amount;
		$this->returnPoint = $returnPoint;
		$this->productName = $productName;
        GoBankpage($this->amount);		
	
	}
	
	public function getEblOrderId($methodKey, $userID, $amount, $returnPoint, $productName = '') {
	// Assign class variable with values
		$this->methodKey = $methodKey;
		$this->userID = $userID;
		$this->amount = $amount;
		$this->returnPoint = $returnPoint;
		$this->productName = $productName;
        //GoBankpage($this->amount);
		
		?>
    <form name="qcash" id="qcash" action="https://shurjopay.com/ebl/HostedCheckoutReturnToMerchant_NVP.php" method="post"  >
	<input type="hidden" name="order.amount" value="<?php echo $this->amount;?>"/>
	<input type="hidden" name="order.currency" value="BDT"/>
	<input type="hidden" name="customer_receipt_email" value="support@shurjomukhi.com.bd"/>
	</form>
   <script type='text/javascript'>document.qcash.submit();</script>		
	
	<?php }
	
	public function getQcashOrderId($methodKey, $userID, $amount, $returnPoint, $productName = '') {

		
		// Assign class variable with values
		$this->methodKey = $methodKey;
		$this->userID = $userID;
		$this->amount = $amount;
		$this->returnPoint = $returnPoint;
		$this->productName = $productName;	
		
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
				$approve = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/epay/qcash/Approved.php";
				$cancel = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/epay/qcash/Cancelled.php";
				$decline = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/epay/qcash/Declined.php";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
			  	$approve = "http://".$_SERVER["SERVER_NAME"]."/shurjopay/epay/qcash/Approved.php";
				$cancel = "http://".$_SERVER["SERVER_NAME"]."/shurjopay/epay/qcash/Cancelled.php";
				$decline = "http://".$_SERVER["SERVER_NAME"]."/shurjopay/epay/qcash/Declined.php";
		  	break;
		  	// Live site configuration
		  	default:
				$approve = "https://".$_SERVER["SERVER_NAME"]."/epay/qcash/Approved.php";
				$cancel = "https://".$_SERVER["SERVER_NAME"]."/epay/qcash/Cancelled.php";
				$decline = "https://".$_SERVER["SERVER_NAME"]."/epay/qcash/Declined.php";
		  	break;
	 	}


		// Qcash gateway connection establishment data
		$data='<?xml version="1.0" encoding="UTF-8"?>';
		$data.="<TKKPG>";
		$data.="<Request>";
		$data.="<Operation>CreateOrder</Operation>";
		$data.="<Language>EN</Language>";
		$data.="<Order>";
		$data.="<OrderType>Purchase</OrderType>";
		$data.="<Merchant>". QMERCHANT ."</Merchant>";
		$data.="<Amount>". $amount * 100 ."</Amount>";
		$data.="<Currency>050</Currency>";
		$data.="<Description>shurjoPay Transaction</Description>";
		$data.="<ApproveURL>".htmlentities($approve)."</ApproveURL>";
		$data.="<CancelURL>".htmlentities($cancel)."</CancelURL>";
		$data.="<DeclineURL>".htmlentities($decline)."</DeclineURL>";
		$data.="</Order></Request></TKKPG>";

		// Call method to connect using stunnel and reply xml
		// Define in file paymentEngine/libs/qcash.php
		$xml = $this->qcash->Postdata_ecomgateway($data);
		
		$OrderID   = $xml->Response->Order->OrderID;
		$SessionID = $xml->Response->Order->SessionID;
		$URL       = $xml->Response->Order->URL;
		header("Location: " . $URL . "?ORDERID=" . $OrderID. "&SESSIONID=" . $SessionID . "");		

?>
	<!-- Code block is commet out due to newly code base for connectivity is define 
		<form name="qcash" id="qcash" method="POST" action="<?php /* echo QURL; ?>">
		<input type="hidden" name="Request" id="Request" value='<TKKPG>
			  <Request>
			   <Operation>CreateOrder</Operation> 
			   <Language>EN</Language>
			   <Order>
			     <Merchant><?php echo QMERCHANT; ?></Merchant>
			     <Amount><?php echo $amount*100; ?></Amount>
			     <Currency>050</Currency>
			     <Description>shurjoPay Transaction</Description>
			     <ApproveURL><?php echo $approve; ?></ApproveURL>
			     <CancelURL><?php echo $cancel; ?></CancelURL>
			     <DeclineURL><?php echo $decline; */ ?></DeclineURL>
			    </Order>
			  </Request>
			 </TKKPG>' />
		</form>
		
		<script>
			function qcashSubmit(){
				 document.forms["qcash"].submit();
			}
			qcashSubmit();
		</script>
	-->
<?php

	}

	public function getIBBLOrderId($methodKey, $order_id, $total_taka, $returnPoint, $productName)
	{
		global $DB;
		switch($_SERVER["SERVER_NAME"])
		{
			// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
				$url = 'https://ipaysafe-ws.islamibankbd.com:8998/services/InitPaymentProcessService';
			break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
				$url = 'http://localhost/debug/ibbl.php';
			break;
			default:
			$url = 'https://ipaysafe-ws.islamibankbd.com:8998/services/InitPaymentProcessService';
	//			$url = 'https://app.islamibankbd.com:8998/paygateRest/services/InitPaymentProcessService';
			break;
		}
		$method = 'POST';
        $encodeValue=base64_encode("1bbl1P@ys@f3Cl13nt:1bbl1P@ys@f3Cl13ntP@ss");
        $auth='Basic '.$encodeValue;
	//$total_taka=$total_taka.'00';
	//echo $total_taka;exit;
	$headers = array(
                'Content-Type:application/xml',
                'clientId:IBB.MRCNT.99131204164840',
                'productName:'.$productName,
                'amount:'.$total_taka.'.00',
                'paymentMethod:101',
                'clientRefId:'.$order_id,
                'merchantSecret:7f486103d1f0ed36276677e9d5a44f66c45ea065',
                'returnUrl:'.$returnPoint,
                'Authorization:'.$auth,
            );
		//echo $total_taka;exit;
     	//print_r($headers); echo "===="."\n";
		$handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        switch($method)
        {
			case 'POST':
				curl_setopt($handle, CURLOPT_POST, true);
				curl_setopt($handle, CURLOPT_POSTFIELDS, null);
			break;
		}
		$response = curl_exec($handle);
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		//print_r($response); exit;
		$xml = simplexml_load_string($response);
		//print_r($xml); exit;
		if (($xml->status == "SUCCESS") and ($xml->errorCode == "0"))
        {
			$token = $xml->token;
			//echo $token;
			$data = array("bank_tx_id"=>$token);
			$where = array("order_id"=>$order_id);
			$return = $DB->update("sp_epay",$data,$where);
			//echo $return;
			//exit;
		//	echo "http://app.islamibankbd.com:8800/ibblpayment/ecomInitPay.action?token=".$token;
		switch($_SERVER["SERVER_NAME"])
		{
			// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
				if ($methodKey == "mCash_iBank")
				{
					header ("Location: http://ipaysafe.islamibankbd.com/ibblpayment/ecomInitPay.action?token=".$token);
				} else
				{
					header ("Location: http://app.islamibankbd.com:8800/ibblpayment/ecomInitPay.action?token=".$token);
				}
			break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
					header ("Location: http://localhost/shurjopay/epay/ibbl/return.php?responseCode=100&token=".$token);
			break;
			default:
				if ($methodKey == "mCash_iBank")
				{
					header ("Location: http://ipaysafe.islamibankbd.com/ibblpayment/ecomInitPay.action?token=".$token);
				} else
				{
						header ("Location: http://ipaysafe.islamibankbd.com/ibblpayment/ecomInitPay.action?token=".$token);
				}
			break;
		}
		
        } else
        {
			echo $xml->status;
			echo $xml->errorCode;
		}
	}

	
function load_animi() {
echo<<<SSS
	<div style='text-align:center; margin:20px auto;'>
	   <h2 style='color:#283a69;padding:10px;'>Forwarding you to Bank web site, please wait....</h2>
	   <img src='img/loading.png' alt='Loading...' width='300' height='15' />
	</div>
SSS;
}
	// this fuunction will return payment IPG gateway response as a associated array
	// must be run init method first and need to call this method seperate php page

	public function getPayemntStatus($ePay_transaction_ID = '') {

			// flag for transaction id set
	$via_ePayID = FALSE;
		$ePay_Type = NULL;
		// trac the transaction type according the ePay_transaction_ID
		if (!empty ($ePay_transaction_ID)) {
			$ePay_transaction_ID = str_replace(" ", "+", urldecode($ePay_transaction_ID));
			$via_ePayID = TRUE;
			if (substr($ePay_transaction_ID, 0, 2) == 'BR' && strlen($ePay_transaction_ID) == 15) {
				$ePay_Type = 'BRAC';
			}
			elseif (strlen($ePay_transaction_ID) == 28) {
				$ePay_Type = 'DBBL';
			}
		}

		$db = new DB();

		if ($via_ePayID)
			$type = $ePay_Type;
		else
			$type = $db->getTransactionType(); // get the Transaction type. it will BRAC | DBBL return

		$result = array ();
		if (!empty ($type)) {

			switch ($type) {

				case 'BRAC' :
					if ($via_ePayID)
						$result = $this->brac->getPaymentStatus($ePay_transaction_ID);
					else
					$result = $this->brac->getPaymentStatus($this->brac->getTransactionID());
					$result['gateway'] = 'BRAC';
					$result['amount'] = $result['data']['txn_amt'];
					$result['userID'] = str_replace("userid=", '', $result['data']['mer_var2']);
					$result['productName'] = $result['data']['mer_var4'];
					break;

				case 'DBBL' :
					if ($via_ePayID)
						$result = $this->dbbl->getPaymentStatus($ePay_transaction_ID);
					else
					$result = $this->dbbl->getPaymentStatus($this->dbbl->getTransactionID());
					$result['gateway'] = 'DBBL';
					$result['amount'] = ((int) ($result['data']['product_price'])) / 100;
					$result['userID'] = substr($result['data']['MRCH_TRANSACTION_ID'], 8);
					$product_name_aRR = explode("::", $result['data']['product_name']);
					$result['productName'] = end($product_name_aRR);
					$RESULT_CODE = $result['data']['RESULT_CODE'];
					$where = array ('error_code' => $RESULT_CODE);
					$result_code_text = $db->select('error_code_dbbl', $where);
					$result['RESULT_CODE_details'] = $result_code_text[0];
					break;

			} // end switch

		} // end if	

		if ($result)
			return $result;
		else
			return false;

	} // end getPayemntStatus methids 
	function getOrderId($methodKey, $userID, $total_taka, $returnPoint, $productName, $approved_url, $cancelled_url, $declined_url){
		$this->qcash->getPaymentStatus($methodKey, $userID, $total_taka, $returnPoint, $productName, $approved_url, $cancelled_url, $declined_url);
	}
	
	function bKashPaymentInfo($trxid){
		
	}

} // end class
?>

