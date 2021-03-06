<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include ("includes/session_handler.php");
include("includes/configure.php");
include ("includes/return.php");

$date = new DateTime();
$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
$order_id = $_SESSION['ORDER_DETAILS']['order_id'];

$returnXML = new ReturnXML();
$bank_result = $returnXML->getBankRefID($order_id);
$bank_ref_id = $bank_result->bank_tx_id;
$payment_method = $bank_result->method;

/*$post_data = '&lt;?xml version="1.0" encoding="utf-8"?&gt;
	            &lt;spResponse&gt;&lt;txID&gt;'.$_SESSION['order_details_response']['txID'].'&lt;/txID&gt;
	            &lt;bankTxID&gt;'.$bank_ref_id.'&lt;/bankTxID&gt;
	            &lt;bankTxStatus&gt;'.$_SESSION['order_details_response']['bankTxStatus'].'&lt;/bankTxStatus&gt;
	            &lt;txnAmount&gt;'.$_SESSION['order_details_response']['txnAmount'].'&lt;/txnAmount&gt;
	            &lt;spCode&gt;'.$_SESSION['order_details_response']['spCode'].'&lt;/spCode&gt;
	            &lt;spCodeDes&gt;'.$_SESSION['order_details_response']['spCodeDes'].'&lt;/spCodeDes&gt;
	            &lt;paymentOption&gt;'.$payment_method.'&lt;/paymentOption&gt;&lt;/spResponse&gt;';*/
if(strstr($_SESSION['order_details_response']['txID'],'DSE'))
{
$post_data = '<?xml version="1.0" encoding="utf-8"?>
                    <spResponse><txID>'.$_SESSION['order_details_response']['txID'].'</txID>
                    <bankTxID>'.$bank_ref_id.'</bankTxID>
                    <bankTxStatus>'.$_SESSION['order_details_response']['bankTxStatus'].'</bankTxStatus>
                    <txnAmount>'.$_SESSION['order_details_response']['txnAmount'].'</txnAmount>
                    <spCode>'.$_SESSION['order_details_response']['spCode'].'</spCode>
                    <spCodeDes>'.$_SESSION['order_details_response']['spCodeDes'].'</spCodeDes>
		    <orderID>'.$_SESSION['ORDER_DETAILS']['order_id'].'</orderID>
		    <time>'.$date->format('Y-m-d H:i:s').'</time>
                    <paymentOption>'.$payment_method.'</paymentOption></spResponse>';

}
else
{	            
$post_data = '<?xml version="1.0" encoding="utf-8"?>
	            <spResponse><txID>'.$_SESSION['order_details_response']['txID'].'</txID>
	            <bankTxID>'.$bank_ref_id.'</bankTxID>
	            <bankTxStatus>'.$_SESSION['order_details_response']['bankTxStatus'].'</bankTxStatus>
	            <txnAmount>'.$_SESSION['order_details_response']['txnAmount'].'</txnAmount>
	            <spCode>'.$_SESSION['order_details_response']['spCode'].'</spCode>
	            <spCodeDes>'.$_SESSION['order_details_response']['spCodeDes'].'</spCodeDes>
	            <paymentOption>'.$payment_method.'</paymentOption></spResponse>';
}
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
$data = json_encode(array ('spay_data' => $post_data));
$post_data = base64_encode($returnXML->get_encrypted_data($data, PUBLIC_KEY));
$returnURL = $returnXML->getReturnURL($order_id);	
$userip = $returnXML->getReturnIP();

$server_response="notok";

if($_SERVER["SERVER_NAME"]=="localhost"  or $userip=="103.46.148.200" or $userip == "52.36.168.100" or $userip == "148.72.232.38" or $userip == "10.101.5.131" or $userip=="198.54.125.200" or $userip == '198.54.114.111' or $userip == '202.84.39.37' or $userip == '212.1.211.78' )
{
	$server_response="ok";
        
}
else
{
	$ipstatus = $returnXML->pingAddress($userip);
    file_put_contents('filename.txt', print_r($ipstatus, true));
	if($ipstatus===1) 
	{
		$server_response="ok";
	}
}

unset($_SESSION['order_details_response']);

if($server_response == "notok")
{
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET outtime='".$date->format('Y-m-d H:i:s')."',ip_status='Dead' WHERE order_id='".$order_id."'");
	echo "IP :$userip ". '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as Merchant server is down. If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
}
else
{
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET outtime='".$date->format('Y-m-d H:i:s')."',ip_status='Alive' WHERE order_id='".$order_id."'");
	// Robi Trigger set //start    
	    mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE robi_sp_payments SET is_triggered = 'yes' WHERE epay_order_id='".$order_id."'");	
    // Robi Trigger set //end
?>

	<form method="post" action="<?php echo $returnURL; ?>" id="frm_submit">
		<input type="hidden" name="spdata" value='<?php echo $post_data; ?>'>
	</form>
	<script>
		document.getElementById('frm_submit').submit();
	</script>


<?php
}

?>
