<?php

/**
 * Transaction info recieved from DBBL ECOM server
 *
 * @author    Sanjeewa Jayasinghe <sanjeewaj@interblocks.com>
 * @copyright Interblocks - http://www.interblocks.com
 *
 * Source adopted by Shurjomukhi developers from DBBL sample
 * @author: 
 * 	1. Sahedul Hasan <sahedul.hasan@shurjomukhi.com.bd>
 * 	2. Shouro Chowndhury <shouro.chowdhury@shurjomukhi.com.bd>
 * 	3. Imtiaz Rahi <imtiaz.rahi@shurjomukhi.com.bd>
 */
include ('../includes/session_handler.php');
include("../includes/configure.php");
include ("../includes/return.php");
$flagAmount=false;
$flagMonthlyLimit=false;
$flagDailyLimit=false;
$flagMaxLimit=false;
$final = array();
if (isset($_POST['product_name']) && $_POST['product_name'] == 'DBBLSTUB') {
	$outputArray=array();
	$outputArray=unserialize($_POST['stub']);
	$redirect=array('DBBLSTUB','shurjoPay');
	} else{
$ip=$_SERVER['REMOTE_ADDR'];
$str = "/opt/jdk1.6.0_21/bin/java -jar  \"/opt/DBBL/keynew/ecomm_merchant.jar\" \"/opt/DBBL/keynew/merchant.properties\" -c ".$_POST['trans_id']." $ip -mrch_transaction_id";

$outputArray=array();
exec($str, $outputArray);
$redirect=explode("::",$_POST['product_name']);
}

$bankReturnAmount=trim(end(explode(":",$outputArray[8])));
$sql=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT amount from sp_epay where bank_tx_id='".$_POST['trans_id']."'");
$result = mysqli_fetch_object($sql);
if(($bankReturnAmount/100)<$result->amount)
{
$flagAmount=true;
}
$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
$cardNumber=trim(end(explode(":",$outputArray[6])));
$cardHolderName=trim(end(explode(":",$outputArray[11])));
$sql_card_allow = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT card_number from sp_cards_allow WHERE card_number='".$cardNumber."' AND status='1' LIMIT 0,1");
$result_ca = mysqli_fetch_object($sql_card_allow);
if(trim($result_ca->card_number)==""){
		$sql_dp = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT item_code, code_length from sp_digital_products");
		$digital_products_itemcode=array();
		$digital_products_codelength=array();
		$i=0;	
		while($result_dp = mysqli_fetch_assoc($sql_dp)){
			$digital_products_itemcode[$i]=$result_dp['item_code'];
			$digital_products_codelength[$i]=$result_dp['code_length'];
			$i++;
		}
		$digital_product = 0;
		
		for($i=0; $i<count($digital_products_codelength); $i++){		
			if(in_array(substr($_SESSION['ORDER_DETAILS']['uniqID'],3,$digital_products_codelength[$i]),$digital_products_itemcode)){
				$digital_product = 1;		
			}
		}
//
	if($digital_product == 1){
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."', card_holder_name='".$cardHolderName."' WHERE order_id='".$order_id."'");
			//echo "SELECT txn_per_month, txn_per_day from sp_payment_options WHERE payment_name = '".$_SESSION['order_details_response']['paymentOption']."' AND tx_limit='1' LIMIT 0,1";exit;
			$sql_no_txn = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT txn_per_month, txn_per_day from sp_payment_options WHERE payment_name = '".$_SESSION['order_details_response']['paymentOption']."' AND tx_limit='1' LIMIT 0,1");
			$result_no_txn = mysqli_fetch_object($sql_no_txn);	
			$txn_per_month = $result_no_txn->txn_per_month;
			$txn_per_day = $result_no_txn->txn_per_day;
			//~ echo $txn_per_month;
			//~ echo "</br>";
			//~ echo $txn_per_day; exit;
			$sql_no_txn_this_month = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT COUNT(`gw_return_id`) as total_txn_in_month FROM sp_epay WHERE `gw_return_id`='000' AND card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."' AND CONCAT(YEAR(`intime`),MONTH(`intime`)) = CONCAT(YEAR(NOW()),MONTH(NOW())) GROUP BY CONCAT(YEAR(`intime`),MONTH(`intime`))");
			$result_no_txn_this_month = mysqli_fetch_object($sql_no_txn_this_month);	
		
		$sql_no_txn_this_day = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT COUNT(`gw_return_id`) as total_txn_in_day FROM sp_epay WHERE `gw_return_id`='000' AND card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."' AND CONCAT(YEAR(`intime`),MONTH(`intime`),DAY(`intime`)) = CONCAT(YEAR(NOW()),MONTH(NOW()),DAY(NOW())) GROUP BY CONCAT(YEAR(`intime`),MONTH(`intime`),DAY(`intime`))");
				$result_no_txn_this_day = mysqli_fetch_object($sql_no_txn_this_day);
		
		if($result_no_txn_this_day->total_txn_in_day >= $txn_per_day){
		$flagDailyLimit=true;
		
		}
		if($result_no_txn_this_month->total_txn_in_month >= $txn_per_month){
		$flagMonthlyLimit=true;
		
		}
		if($result->amount > 4000){
		$flagMaxLimit=true;
	
		}
}

}

if ($redirect[0] == '') {
    echo "Product name missing. Please contact Shurjomukhi with details";
    exit();
}
$redirectPoint=$redirect[0];
if ((isset($redirect[1]) && $redirect[1] == 'shurjoPay') or (isset($redirect[0]) && $redirect[0] == 'shurjomukhi')) {
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
   $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_dbbl_transactions SET transaction_id='".$_POST['trans_id']."', posted_data='".serialize($_POST)."', returned_array_data='".serialize($outputArray)."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
    

?>
	<html>
	<body>
		<?php
		
        if($redirectPoint=='1_from_dev' or $redirectPoint=='2_from_dev' or $redirectPoint=='3_from_dev' or $redirectPoint=='4_from_dev' or $redirectPoint=='5_from_dev'){
        ?>
        <form id="frmDbbl" name="frmDbbl" method="post" action="http://dev.shurjomukhi.com/shurjorajjo/shurjopay/epay/dbbl/success.php">
        <?php
        }elseif ($redirectPoint == 'DBBLSTUB') {
			?>
        <form id="frmDbbl" name="frmDbbl" method="post" action="http://localhost/shurjopay/epay/dbbl/success.php">
        <?php
			}
        else{
			?>
        <form id="frmDbbl" name="frmDbbl" method="post" action="https://shurjopay.com/epay/dbbl/success.php">
        <?php
        }
        ?>

		<?php if($flagAmount or $flagMonthlyLimit or $flagDailyLimit) {
		$outputArray[15]='OverLimit';
		$outputArray[16]='555';			
			}
                        else
                        {
                        $outputArray[15]='N/A';
		        $outputArray[16]="N/A";	    
                            
                        }
$outputArray[13]=$_POST['trans_id'];
$outputArray[14]=$result->amount;

$returnXML = new ReturnXML();
switch($_SERVER["SERVER_NAME"]) {
	case 'dev.shurjomukhi.com':
		define('PUBLIC_KEY', file_get_contents('/etc/sp_key_dev/public.pem'));
		define('PRIVATE_KEY', file_get_contents('/etc/sp_key_dev/private.key'));
	break;
	case 'localhost':
	if (PHP_OS == 'WINNT') {
	  	define('PUBLIC_KEY', file_get_contents('C:\svn\sp_key\public.pem'));
		define('PRIVATE_KEY', file_get_contents('C:\svn\sp_key\private.key'));
	} else {
		define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
		define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));
	}
	break;
  	default:  
	  	define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
		define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));
	break;
}
$data = json_encode(array ('output' => $outputArray));
$post_data = base64_encode($returnXML->get_encrypted_data($data, PUBLIC_KEY));

	?>
		
		<input type="hidden" name="output" value="<?php echo $post_data; ?>">		
		
	</form> 
	<script>
		document.getElementById("frmDbbl").submit();
	</script>
	</body>
	</html>
<?php
} 

?>
