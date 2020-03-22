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
$final = array();
$ip=$_SERVER['REMOTE_ADDR'];
$str = "/opt/jdk1.6.0_21/bin/java -jar  \"/opt/DBBL/key/ecomm_merchant.jar\" \"/opt/DBBL/key/merchant.properties\" -c ".$_POST['trans_id']." $ip -mrch_transaction_id";

$outputArray=array();
exec($str, $outputArray); 
$redirect=explode("::",$_POST['product_name']);
if ($redirect[0] == '') {
    echo "Product name missing. Please contact Shurjomukhi with details";
    exit();
}
$redirectPoint=$redirect[0];
//if (isset($redirect[0]) && $redirect[0] == 'shurjomukhi') {
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
        }
        else{
        ?>
        <form id="frmDbbl" name="frmDbbl" method="post" action="https://shurjopay.com/epay/dbbl/success.php">
        <?php
        }
		
		
$returnXML = new ReturnXML();
  
switch($_SERVER["SERVER_NAME"]) {
	case 'dev.shurjomukhi.com':
		define('PUBLIC_KEY', file_get_contents('/etc/sp_key_dev/public.pem'));
		define('PRIVATE_KEY', file_get_contents('/etc/sp_key_dev/private.key'));
	break;
	case 'localhost':
	  	define('PUBLIC_KEY', file_get_contents('C:\svn\sp_key\public.pem'));
		define('PRIVATE_KEY', file_get_contents('C:\svn\sp_key\private.key'));
	break;
  	default:  
	  	define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
		define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));
	break;
}
$outputArray[13]=$_POST['trans_id'];
$outputArray[14]=$result->amount;
$outputArray[15]='FAILED';
$outputArray[16]="604";	
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

