<?php
include ("includes/session_handler.php");
include("includes/configure.php");
include("includes/login.php");

$login = new Login();
function openssl_decrypt_data($post){
		switch($_SERVER["SERVER_NAME"]) {
			
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
		
		$m = json_decode(base64_decode($post), true);
		
		$data = get_original_data($m['msg'], $m['key'], PRIVATE_KEY);
		
		return $data;
	}
function get_original_data($msg, $key, $private_key) {

		if ($msg == '') {
			return false;
		} 
		else {
			$privateKey = openssl_get_privatekey($private_key);
			$result = openssl_open(base64_decode($msg), $decryptedData, base64_decode($key), $privateKey);
			return $decryptedData;
		}
	}
//var_dump($_POST['spdata']);
$post_vars=openssl_decrypt_data($_POST['spdata']);
//var_dump($post_vars);
$xml = simplexml_load_string($post_vars);
$encoded_smUid = (string)$xml->smUid;
//var_dump($smUid);
//$dataPOST = $_POST['spdata'];
$dataPOST = $post_vars;
$smUid = '';
//var_dump($_POST); exit;
if ( $encoded_smUid != '' ) {
$smUid = base64_decode($_POST['smUid']);
$smUid = mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid);
$block_status = $login->chkBlocking($smUid, $db->con_sp);
$countTxStatus = $login->countTxBlocking($smUid, $db->con_sp);

if ($block_status->isAccountClosed != 'No' or $countTxStatus == 'Deny') {
	switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
		  		header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/block.php");
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
		  		header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/block.php");
		  	break;
		  	// Live site configuration
		  	default:
		  		header ("Location: https://shurjopay.com/block.php");
		  	break;
	 	}
	
	exit;
	}
	
}
 if (isset($dataPOST)) {
    include("xml-parser.php");
    $xml2arr = xml2array($dataPOST);
    $_SESSION['ORDER_DETAILS']['merchantName'] = $merchantName = !is_array($xml2arr['shurjoPay']['merchantName']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['merchantName']) : "";
    $_SESSION['ORDER_DETAILS']['merchantPass'] = $merchantPass = !is_array($xml2arr['shurjoPay']['merchantPass']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['merchantPass']) : "";
    $_SESSION['ORDER_DETAILS']['returnURL'] = $returnURL = !is_array($xml2arr['shurjoPay']['returnURL']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['returnURL']) : "";
    $_SESSION['ORDER_DETAILS']['userIP'] = $userIP = !is_array($xml2arr['shurjoPay']['userIP']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['userIP']) : "";
    $_SESSION['ORDER_DETAILS']['uniqID'] = $uniqid = !is_array($xml2arr['shurjoPay']['uniqID']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['uniqID']) : "";
    $_SESSION['ORDER_DETAILS']['txnAmount'] = $amount = !is_array($xml2arr['shurjoPay']['totalAmount']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['totalAmount']) : "";
    $_SESSION['ORDER_DETAILS']['paymentOption'] = $payment_option = !is_array($xml2arr['shurjoPay']['paymentOption']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['paymentOption']) : "";
    $_SESSION['ORDER_DETAILS']['order_id'] = $order_id = uniqid("SP");
    if (!(strpos($dataPOST, '<returnURL>') === false && strpos($dataPOST, '</returnURL>') === false)) {
		$_SESSION['ORDER_DETAILS']['returnURL'] = $returnURL = substr($dataPOST, (strpos($dataPOST, '<returnURL>') + 11), (strpos($dataPOST, '</returnURL>') - (strpos($dataPOST, '<returnURL>') + 11)));
	}
	
    if($_SERVER["SERVER_NAME"]=="dev.shurjomukhi.com"){
		//$_SESSION['ORDER_DETAILS']['txnAmount'] = $amount = 1;
	}

    $err_msg = $login->inputValidation($merchantName, $merchantPass, $userIP, $uniqid, $amount, $payment_option, $returnURL, $db->con_sp);
  
     if (trim($err_msg) == "") {
        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_merchants WHERE username='" . $merchantName . "' and password='" . md5($merchantPass) . "' and isactive='yes'");
        $result = mysqli_fetch_object($sql_query);
        $host = parse_url($_SERVER['HTTP_REFERER']);
        $ip = gethostbyname($host['host']);

        $mer_err_msg = $login->merchantVerification($result, $uniqid, $ip);
        if (trim($mer_err_msg) == "") {
            $_SESSION['ORDER_DETAILS']['userID'] = $result->id;
            $_SESSION['ORDER_DETAILS']['USER_VISIT_TIMES'] = 1;
            if ($result) {
                $date = new DateTime();
                $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
                $current_time = $date->format("Y-m-d H:i:s");
                $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_epay SET uid='" . $result->id . "', order_id='" . $order_id . "', txid='" . $uniqid . "', amount='" . $amount . "', clientip='" . $userIP . "', returnurl='" . $returnURL . "', intime='" . $current_time . "'");
                $_SESSION['ORDER_DETAILS']['current_insert_id'] = mysqli_insert_id($GLOBALS["___mysqli_sm"]);
                if ( $smUid!='') {
					$resultRid = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT user_id FROM paypoint.paypoint_users_orders WHERE spay_id = '".$_SESSION['ORDER_DETAILS']['uniqID']."'");
					$smRidObj = mysqli_fetch_object($resultRid);
					$smRid = $smRidObj->user_id;
					//print_r($_SESSION);
					//echo "SELECT user_id FROM paypoint.paypoint_users_orders WHERE spay_id = '".$_SESSION['ORDER_DETAILS']['uniqID']."'";
					//echo $smRid;exit;					
					if ($smUid == $smRid) {
						
					//start order in progress condition.
					$_SESSION['ORDER_DETAILS']['smUid'] = $smRid;
				 // $order_in_progress=orderInProgress($smUid,$_SESSION['ORDER_DETAILS']['uniqID']);  
				 $order_in_progress=false; // order in progress disabled value always false.
					
				if($order_in_progress)
					{
						switch($_SERVER["SERVER_NAME"]) {
								// Testing site (dev.shurjomukhi.com) configuration
								case 'dev.shurjomukhi.com':
										header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/halt.php");
								break;
								// Development site (e.g. localhost) configuration
								case 'localhost':
									header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/halt.php");
								break;
									// Live site configuration
								default:
									header ("Location: https://shurjopay.com/halt.php");
								break;
							}
	
						exit;
					}	
					//end order in progress condition.
					
					$smUid_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET smUid='" . $smUid . "' WHERE  txid='" . $uniqid . "'");
					} else {
						//echo $smRid;exit;
							switch($_SERVER["SERVER_NAME"]) {
								// Testing site (dev.shurjomukhi.com) configuration
								case 'dev.shurjomukhi.com':
									header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/block.php");
								break;
								// Development site (e.g. localhost) configuration
								case 'localhost':
									header ("Location: http://".$_SERVER["SERVER_NAME"]."/paypoint/block.php");
								break;
								// Live site configuration
								default:
									header ("Location: https://shurjopay.com/block.php");
								break;
							}
							exit;
					}
				}
                ?>
                <form method="post" action="./payment_option.php" id="frm_submit">
                    <?php foreach ($_SESSION['ORDER_DETAILS'] as $key => $value) { ?>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                    <?php } ?>
                    <input type="hidden" name="unique_id_code" value="<?php echo $result->unique_id_code; ?>">
                </form>
                <script>
                    document.getElementById('frm_submit').submit();
                </script>
                <?php
                exit;
            } 
            else {
                $sql_log = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_merchants WHERE username='" . $merchantName . "' and password='" . md5($merchantPass) . "'");
                $login_err = mysqli_fetch_object($sql_log);

                if (!$login_err) {
                    $sql_errcode = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE id=199");
                    $err_code = mysqli_fetch_object($sql_errcode);
                    $post_data = '<?xml version="1.0" encoding="utf-8"?><SP><Response><errCode>' . $err_code->return_code . '</errCode><errMsg>' . $err_code->return_status . '</errMsg></Response></SP>';
                } 
                else {
                    $sql_errcode = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE id=200");
                    $err_code = mysqli_fetch_object($sql_errcode);
                    $post_data = '<?xml version="1.0" encoding="utf-8"?><SP><Response><errCode>' . $err_code->return_code . '</errCode><errMsg>' . $err_code->return_status . '</errMsg></Response></SP>';
                }
                submitSPForm($post_data, $returnURL);
            }
        } 
        else {
            showErrorMsg($mer_err_msg);
        }
    } 
    else {
        showErrorMsg($err_msg);
    }
} 
else {
    include("includes/header.php");
    echo "<div style='color:#ff0000;text-align:center;font-size:25px;font-weight:bold;'>You are not allowed to access this page!!!!!!!</div>";
    include("includes/footer.php");
}

function showErrorMsg($err_msg) {
    include("includes/header.php");
    echo "<div id='main_container'>
        <fieldset id='trnsdetails'>
            <legend>Error Message</legend>
            <div style='color:#ff0000;padding: 10px;'>{$err_msg}</div>
        </fieldset>
    </div>";
    include("includes/footer.php");
}

function submitSPForm($post_data, $returnURL) {
    echo "<form method='post' action='{$returnURL}' id='frm_submit'>
        <input type='hidden' name='spdata' value='{$post_data}' />
      </form>
   <script>document.getElementById('frm_submit').submit();</script>";
}

function orderInProgress($user_id,$spay_id)
{
global $db;
$db->getConnection();	
$query="select time_added,try from user_lock where user_id='".$user_id."'";
$res=mysqli_query($GLOBALS["___mysqli_sm"], $query);
$result=mysql_fetch_object($res);
$current_time = new DateTime('now');
    $time_added = new DateTime($result->time_added);
     $current_time->setTimezone(new DateTimeZone('Asia/Dhaka'));
          $time_added->setTimezone(new DateTimeZone('Asia/Dhaka'));
	$interval = strtotime($current_time->format('Y-m-d H:i:s'))-strtotime($time_added->format('Y-m-d H:i:s'));   
	$mins=ceil($interval/60); 
            
            
$count=mysql_num_rows($res);

	if($count>0 AND $mins<5000) // order in progress disabled : user_lock table
		{
		$q="UPDATE user_lock SET time_added = Now(),try='".($result->try+1)."' where user_id = '".$user_id."'";
		$res=mysqli_query($GLOBALS["___mysqli_sm"],$q);		
		return true;
		}
	else if($mins>5)
		{
		$comment="1";
		$q="UPDATE user_lock SET time_added = Now() where user_id = '".$user_id."'";
		$res=mysqli_query($GLOBALS["___mysqli_sm"],$q);
		return false;
		}
	else
		{
		$comment="1";
		$q="insert into user_lock (user_id,spay_id,comment) values('".$user_id."','".$spay_id."','".$comment."')";
		
		$res=mysqli_query($GLOBALS["___mysqli_sm"],$q);
		
		if($res)
			{
			return false;
			}
		else
			{
				$q="UPDATE user_lock SET time_added = Now(),try='".($result->try+1)."' where user_id = '".$user_id."'";
		$res=mysqli_query($GLOBALS["___mysqli_sm"],$q);	
		return true;
			}
	
		}	
}

?>
