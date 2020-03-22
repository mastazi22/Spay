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
	include("../includes/configure.php");
	include ('../includes/session_handler.php');
	include ("../includes/return.php");
	include("../includes/login.php");
	$login = new Login();
	//$db->getConnection();
	$flagAmount=false;
	$flagMonthlyLimit=false;
	$flagDailyLimit=false;
	$flagMaxLimit=false;
	$flagMaxTx=false;
	$final = array();
	//my country find by cardnumber
	function checkCardCountry($cardNumber)
	{
		$card = substr($cardNumber, 0, 6);
		$curl = curl_init('https://binlist.net/json/' . $card);
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		$result=json_decode($result);
		return $result;
	}
	//end find country by cardnumer

	// DBBL Response Parsing
		function dbblResponseParsing($string) 
		{
			$array = preg_split ('/$\R?^/m', $string);
			$response = array();
			foreach ($array as $key => $value) 
			{
			 $value_exploder = explode(':', $value);
			 $response[trim($value_exploder[0])] = trim($value_exploder[1]);
			}
			return (object) $response;
		}
	
	// DBBL Response Parsing // End
	$q="SELECT COUNT(id) as cid FROM DBBL_tx_holder WHERE bank_tx_id ='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['trans_id'])."' AND order_id = '".$_SESSION['ORDER_DETAILS']['order_id']."' AND result IS NULL";
	$sqlq=mysqli_query($GLOBALS["___mysqli_sm"],$q);
	$sqltxcheck=mysqli_fetch_object($sqlq);
	if ($sqltxcheck->cid == 1) 
	{
		$q2="UPDATE DBBL_tx_holder SET returntime = NOW() WHERE bank_tx_id ='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['trans_id'])."' AND order_id = '".$_SESSION['ORDER_DETAILS']['order_id']."' AND result IS NULL";
		$sqlq2=mysqli_query($GLOBALS["___mysqli_sm"],$q2);
		if ($sqlq2 != 1)
		{
			echo "Transaction Declined for Interruption";
			header("Location: https://shurjopay.com/payment_declined.php?cancel=ok");
			exit();
			/*
			unset($_SESSION);
			session_unset;
			session_destroy;
			exit;
			*/
		}
	}
	else
	{
		//$q3="UPDATE DBBL_tx_holder SET remark = 'manipulation try at '".date('Y-m-d H:i:s')." with '".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['trans_id'])."' WHERE order_id = '".$_SESSION['ORDER_DETAILS']['order_id']."'";
		$q3="UPDATE DBBL_tx_holder SET remark = 'manipulation try at ".date('Y-m-d H:i:s')." with ".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['trans_id'])."' WHERE order_id = '".$_SESSION['ORDER_DETAILS']['order_id']."'";
		$sqlq3=mysqli_query($GLOBALS["___mysqli_sm"],$q3);
		echo "Transaction Declined";
		header("Location: https://shurjopay.com/payment_declined.php?cancel=ok");
		exit();
		/*
		unset($_SESSION);
		session_unset;
		session_destroy;
		exit;
		*/
	}

	if (isset($_POST['product_name']) && $_POST['product_name'] == 'DBBLSTUB') 
	{
		$outputArray=array();
		$outputArray=unserialize($_POST['stub']);
		$redirect=array('DBBLSTUB','shurjoPay');
	}
	else
	{
		$ip=$_SERVER['REMOTE_ADDR'];
		
		/****
		$str = "/opt/jdk1.6.0_21/bin/java -jar  \"/opt/DBBL/keynew/ecomm_merchant.jar\" \"/opt/DBBL/keynew/merchant.properties\" -c ".$_POST['trans_id']." $ip -mrch_transaction_id";
		$outputArray=array();
		exec($str, $outputArray);
		$redirect=explode("::",$_POST['product_name']);
		*/
		// transaction id verification

			$node_request_data = array(     
				'action' => 'verify',	
				'trans_id' => $_POST['trans_id'],	
				'ip' => $ip
			);
			
			$queryString =  http_build_query($node_request_data);   
			$ch = curl_init();                                              		
			$host = "http://node.shurjopay.com?$queryString";
			curl_setopt($ch, CURLOPT_URL ,$host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$outputArray=array();
		    $response_data = curl_exec($ch);
		    // DBBL Response Parsing
		    $DbblResponseObject = dbblResponseParsing($response_data);
	        $outputArray = explode("\n",$response_data);

			curl_close($ch);
			
			$redirect=explode("::",$_POST['product_name']);

	}
	//my file system code here.

	$epoch_time = $DbblResponseObject->TRANS_DATE;//trim(end(explode(":",$outputArray[10])));
	$gwUtime=date('Y-m-d H:i:s', @$epoch_time/1000);
	//$gwUtime=date('Y-m-d H:i:s', $epoch_time);

	$sql_queryU = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_time='".$gwUtime."',remarks='".$epoch_time." ".$gwUtime."' WHERE order_id='".$_SESSION['ORDER_DETAILS']['order_id']."'");

	$bankReturnAmount=trim(end(explode(":",$outputArray[8])));
	//$sql=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT amount from sp_epay where bank_tx_id='".$_POST['trans_id']."'");
	$sql=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT smUid,amount from sp_epay where bank_tx_id='".$_POST['trans_id']."'");
	$result = mysqli_fetch_object($sql);
	if(($bankReturnAmount/100)<$result->amount)
	{
	$flagAmount=true;
	}
	$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
	$cardNumber = $DbblResponseObject->CARD_NUMBER;//trim(end(explode(":",$outputArray[6])));
	$cardHolderName = $DbblResponseObject->CARDNAME;//trim(end(explode(":",$outputArray[9])));

	//dbbl fraud card protection logic by country code
	$cardCountry = checkCardCountry($cardNumber);
	if ($cardCountry->country->alpha2 !== "BD") 
	{
		$datetimecurrent = date("Y-m-d");
		$dbblcardlimit = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from dbbl_bin_limit WHERE id=1");
		$resultdbblcardlimit = mysqli_fetch_object($dbblcardlimit);
		$dbblcardlimitspEpay = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT COUNT(`card_number`) as total_txn_in_day  from sp_epay WHERE card_number='" . $_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber . "' and bank_status='SUCCESS' and return_code='000' and gw_time LIKE '%$datetimecurrent%'");
		$resultdbblcardlimitspEpay = mysqli_fetch_object($dbblcardlimitspEpay);
		if ($resultdbblcardlimitspEpay->total_txn_in_day >= $resultdbblcardlimit->limit) 
		{
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_status='Block By Site Admin' ,return_code='999' WHERE order_id='" . $order_id . "'");
			header("Location: https://shurjopay.com/block.php");
		}

	}
	//end dbbl fraud card protection logic by country code

	$sql_card_allow = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT card_number from sp_cards_allow WHERE card_number='".$cardNumber."' AND status='1' LIMIT 0,1");
	$result_ca = mysqli_fetch_object($sql_card_allow);
	if(trim($result_ca->card_number)=="")
	{
			$sql_dp = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT item_code, code_length from sp_digital_products");
			$digital_products_itemcode=array();
			$digital_products_codelength=array();
			$i=0;	
			while($result_dp = mysqli_fetch_assoc($sql_dp))
			{
				$digital_products_itemcode[$i]=$result_dp['item_code'];
				$digital_products_codelength[$i]=$result_dp['code_length'];
				$i++;
			}
			$digital_product = 0;
			
			for($i=0; $i<count($digital_products_codelength); $i++)
			{		
				if(in_array(substr($_SESSION['ORDER_DETAILS']['uniqID'],3,$digital_products_codelength[$i]),$digital_products_itemcode))
				{
					$digital_product = 1;		
				}
			}
	//
		if($digital_product == 1)
		{
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."', card_holder_name='".$cardHolderName."' WHERE order_id='".$order_id."'");
				$chkBin=substr($cardNumber,0,6);
				$chkSql = "SELECT COUNT(id) as cid from blocked_bins where bin_no = '".$chkBin."' AND block = '1'";
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],$chkSql);
				$binstatus = mysqli_fetch_object($sql_query);
				if ($binstatus->cid > 0 ) 
				{
					$block_query = "UPDATE sp_epay SET gw_return_id='555', gw_return_msg='BlockedBin', return_code='555', bank_status='BlockedBin' WHERE order_id='".$order_id."'";
					$doneSql=mysqli_query($GLOBALS["___mysqli_sm"],$block_query);
					switch($_SERVER["SERVER_NAME"]) 
					{
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
				//echo "SELECT txn_per_month, txn_per_day from sp_payment_options WHERE payment_name = '".$_SESSION['order_details_response']['paymentOption']."' AND tx_limit='1' LIMIT 0,1";exit;
				$sql_no_txn = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT txn_per_month, txn_per_day from sp_payment_options WHERE payment_name = '".$_SESSION['order_details_response']['paymentOption']."' AND tx_limit='1' LIMIT 0,1");
				$result_no_txn = mysqli_fetch_object($sql_no_txn);	
	/*
				$txn_per_month = $result_no_txn->txn_per_month;
				$txn_per_day = $result_no_txn->txn_per_day;
	*/
				if($_SESSION['order_details_response']['paymentOption']=='Master Card' || $_SESSION['order_details_response']['paymentOption']=='Visa')  
				{
					$txn_per_month = 10;
					$txn_per_day = 2;
				} 
				else 
				{
					$txn_per_month = $result_no_txn->txn_per_month;
					$txn_per_day = $result_no_txn->txn_per_day;
				}
				//~ echo $txn_per_month;
				//~ echo "</br>";
				//~ echo $txn_per_day; exit;
				$sql_no_txn_this_month = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT COUNT(`gw_return_id`) as total_txn_in_month FROM sp_epay WHERE `gw_return_id`='000' AND card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."' AND CONCAT(YEAR(`intime`),MONTH(`intime`)) = CONCAT(YEAR(NOW()),MONTH(NOW())) GROUP BY CONCAT(YEAR(`intime`),MONTH(`intime`))");
				$result_no_txn_this_month = mysqli_fetch_object($sql_no_txn_this_month);	
			
				$sql_no_txn_this_day = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT COUNT(`gw_return_id`) as total_txn_in_day FROM sp_epay WHERE `gw_return_id`='000' AND card_number='".$_SESSION['ORDER_DETAILS']['unique_id_code'].$cardNumber."' AND CONCAT(YEAR(`intime`),MONTH(`intime`),DAY(`intime`)) = CONCAT(YEAR(NOW()),MONTH(NOW()),DAY(NOW())) GROUP BY CONCAT(YEAR(`intime`),MONTH(`intime`),DAY(`intime`))");
					$result_no_txn_this_day = mysqli_fetch_object($sql_no_txn_this_day);
			    $smUid=$result->smUid;
			
			$data=array('user_id'=>$smUid,'outside'=>'outside');
			file_put_contents('filename1.txt', print_r($data, true));
			if($smUid != '10000721548') {
			$data=array('user_id'=>$smUid,'Inside'=>'Inside');
			file_put_contents('filename2.txt', print_r($data, true));
				if($result_no_txn_this_day->total_txn_in_day >= $txn_per_day)
				{
					$flagDailyLimit=true;			
				}
				if($result_no_txn_this_month->total_txn_in_month >= $txn_per_month)
				{
					$flagMonthlyLimit=true;
			
				}
				if($result->amount > 4000)
				{
					$flagMaxLimit=true;
		
				}
				
				if(countTxBlocking($_SESSION['ORDER_DETAILS']['smUid']) == 'Deny') 
				{
					$flagMaxTx=true;
				}
		}
			
	}

	}

	if ($redirect[0] == '') 
	{
	    echo "Product name missing. Please contact Shurjomukhi with details";
	    exit();
	}

	$redirectPoint=$redirect[0];
	if ((isset($redirect[1]) && $redirect[1] == 'shurjoPay') or (isset($redirect[0]) && $redirect[0] == 'shurjomukhi')) 
	{
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

			<?php if($flagAmount or $flagMonthlyLimit or $flagDailyLimit or $flagMaxTx) 
			{
				$outputArray[15]='OverLimit';
				$outputArray[16]='555';
				$q2="UPDATE DBBL_tx_holder SET result = 'OverLimit' WHERE bank_tx_id ='".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['trans_id'])."' AND order_id = '".$_SESSION['ORDER_DETAILS']['order_id']."' AND result IS NULL";
				$sqlq2=mysqli_query($GLOBALS["___mysqli_sm"],$q2);
			}
	        else
	        {
	            $outputArray[15]='N/A';
				$outputArray[16]="N/A";	    
	                            
	        }
	
		$outputArray[13]=$_POST['trans_id'];
		$outputArray[14]=$result->amount;

		$returnXML = new ReturnXML();
		switch($_SERVER["SERVER_NAME"]) 
		{

			case 'dev.shurjomukhi.com':
				define('PUBLIC_KEY', file_get_contents('/etc/sp_key_dev/public.pem'));
				define('PRIVATE_KEY', file_get_contents('/etc/sp_key_dev/private.key'));
			break;
			case 'localhost':
				if (PHP_OS == 'WINNT') 
				{
			  		define('PUBLIC_KEY', file_get_contents('C:\svn\sp_key\public.pem'));
					define('PRIVATE_KEY', file_get_contents('C:\svn\sp_key\private.key'));
				} 
				else 
				{
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
<?php	} 	?>
