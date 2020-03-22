<?php
 include ('includes/session_handler.php');	
 include("includes/configure.php");
 include("includes/header.php");

?>
<div id="main_container">
	<fieldset id="trnsdetails">
		<?php
			include("payment_engine/paymentEngine.php");
			$date = new DateTime();
			$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
			$pe = new paymentEngine();
			$productName = "shurjoPay";
			$returnPoint = $db->local_return_url;
			$returnPointDbbl = $db->dbbl_return_url;
			$userID = $_SESSION['ORDER_DETAILS']['userIP'];
			//echo $userID;
			if($_SERVER["SERVER_NAME"]=="dev.shurjomukhi.com")
			{
				//$total_taka = 1;
				$total_taka = $_SESSION['ORDER_DETAILS']['txnAmount'];
				$returnIBBL = "http://dev.shurjomukhi.com/shurjorajjo/shurjopay/epay/ibbl/return.php";
			} else
			{
				$total_taka = $_SESSION['ORDER_DETAILS']['txnAmount'];
				$returnIBBL = "https://shurjopay.com/epay/ibbl/return.php";
			}
			$methodKey = mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_POST['paymentOption']);
			
			$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
			$userID = $_SESSION['ORDER_DETAILS']['userID'];

			if($methodKey=='visa' or $methodKey=='master_card' or $methodKey=='dbbl_visa' or $methodKey=='dbbl_master')
			{
				$getway="dbbl";
			}	
			else if($methodKey=='dbbl_nexus')
			{
				$getway="dbbl";
			}	
			else if($methodKey=='bkash')
			{
				$getway="bkash";
			}	
			else if($methodKey=='bkash_api')
			{
				$getway="bkash_api";
			}
			else if($methodKey=='upay')
			{
				$getway="upay";
			}
			else if($methodKey == 'teletalk')
			{
			    $getway = "teletalk";
			}
			else if($methodKey == 'nagad')
			{
			    $getway = "nagad";
			}
			else if($methodKey=='dmw')
			{
				$getway="dmw";
			}	
			else if($methodKey=='qcash')
			{
				$getway="qcash";
			}	
			else if($methodKey=='mx')
			{
				$getway="mx";
			}
			else if($methodKey=='mx_visa')
			{
				$getway="mx";
			}
			else if($methodKey=='mx_master_card')
			{
				$getway="mx";
			}
			else if($methodKey=='unionpay')
			{
				$getway="mx";
			}
			else if($methodKey=='ebl')
			{
				$getway="ebl";
			}
			else if($methodKey=='ebl_visa')
			{
				$getway="ebl";
			}
			else if($methodKey=='ebl_master')
			{
				$getway="ebl";
			}
			else if($methodKey=='ibbl')
			{
				$getway="ibbl";
			}	
			else if($methodKey=='mCash_iBank')
			{
				$getway="ibbl";
			}	
			else if($methodKey=='shurjomudra')
			{
				$getway="shurjomudra";
			}	
			else if($methodKey=='mtb')
			{
				$getway="mtb";
			}	
			else if($methodKey=='tbl')
			{
				$getway="tbl";
			}
			else if($methodKey=='dbbl_mobile')
			{
			   $getway="dbbl";
			
			} 
			else if($methodKey == 'stbl')
			{
				$getway = "tbl";
			}
			
			
			

			if($methodKey=="qcash")
			{
				$_SESSION['order_details_response']['paymentOption'] = "Qcash";
				load_animi();
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				$epay_txid = $pe->getQcashOrderId($methodKey, $userID, $total_taka, $returnPoint, $productName);
			} 
			elseif($methodKey=="mx" or $methodKey=="mx_visa" or $methodKey=="mx_master_card" or $methodKey == "unionpay")
			{
			   if($methodKey=="mx"){
			   $_SESSION['order_details_response']['paymentOption'] = "MX";
			   }elseif($methodKey=="mx_visa"){
			   $_SESSION['order_details_response']['paymentOption'] = "MX Visa";
			   }elseif($methodKey=="mx_master_card"){
			   $_SESSION['order_details_response']['paymentOption'] = "MX Master";
			   }elseif($methodKey=="unionpay"){
			   $_SESSION['order_details_response']['paymentOption'] = "UNIONPAY";	
			   }
			   
				load_animi();
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				$epay_txid = $pe->getMxOrderId($methodKey, $userID, $total_taka, $returnPoint, $productName);
			}elseif($methodKey=="ebl" or $methodKey=="ebl_visa" or $methodKey=="ebl_master")
			{
			   if($methodKey=="ebl"){
			   $_SESSION['order_details_response']['paymentOption'] = "Ebl";
			   }elseif($methodKey=="ebl_visa"){
			   $_SESSION['order_details_response']['paymentOption'] = "Ebl Visa";
			   }elseif($methodKey=="ebl_master"){
			   $_SESSION['order_details_response']['paymentOption'] = "Ebl Master";
			   }
				load_animi();
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				$epay_txid = $pe->getEblOrderId($methodKey, $userID, $total_taka, $returnPoint, $productName);
			}
			elseif($methodKey=="ibbl")
			{
				$_SESSION['order_details_response']['paymentOption'] = "IBBL";
				load_animi();
				$sql_query="UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'";
				//echo $sql_query; exit;
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],$sql_query);
				//$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				//echo "ibbl";
				$epay_txid = $pe->getIBBLOrderId($methodKey, $order_id, $total_taka, $returnIBBL, $productName);
			} elseif($methodKey=="mCash_iBank")
			{
				$_SESSION['order_details_response']['paymentOption'] = "mCash/iBanking";
				//load_animi();
				$sql_query="UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'";
			//	echo $sql_query; exit;
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],$sql_query);
				//$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				//$total_taka = "10";
				$epay_txid = $pe->getIBBLOrderId($methodKey, $order_id, $total_taka, $returnIBBL, $productName);
			} elseif($methodKey=="shurjomudra")
			{
				$_SESSION['order_details_response']['paymentOption'] = "shurjoMudra";
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				$sql_source=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT merchant_domain FROM sp_merchants WHERE id='".$userID."'");
				$source = mysqli_fetch_object($sql_source);
				switch ($_SERVER["SERVER_NAME"])
				{
					// Testing site (dev.shurjomukhi.com) configuration
					case 'dev.shurjomukhi.com' :
						$post_url = "http://dev.shurjomukhi.com/shurjomudranew/sm-api";
					break;
					// Development site (e.g. localhost) configuration
					case 'localhost' :
						$post_url = "http://localhost/shurjomudranew/sm-api";
					break;
					// Live site configuration
					default :
						$post_url = "https://shurjomudra.shurjorajjo.com.bd/sm-api";
					break;
				}
				$mudraip = isset ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
				$mudarxml='<?xml version="1.0" encoding="utf-8"?>
				<shurjoMudra><mudraSource>'.$source->merchant_domain.'</mudraSource>
				<mudraIP>'.$mudraip.'</mudraIP>
				<mudraID>'.$order_id.'</mudraID></shurjoMudra>';
			?>
			<form method="post" action="<?php echo $post_url; ?>" id="post2sm">
				<input type="hidden" name="smdata" value='<?php echo $mudarxml; ?>' />
			</form>
			<script>
				document.getElementById("post2sm").submit();
			</script>
			<?php
				exit;
			} elseif($methodKey=="bkash")
			{
				if (strpos($order_id,'VDP')){ 
                                 
	                header("location: http://www.google.com/");
	                exit();			  
				}
				$_SESSION['order_details_response']['paymentOption'] = "bKash";
				$sql_query_update=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query_insert=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				// free the memory
				//mysql_free_result($sql_query_update);
				//mysql_free_result($sql_query_insert);
				header("Location: bkash_confirmation.php");
				exit;
			} 
			elseif($methodKey == "bkash_api")
			{
				$_SESSION['order_details_response']['paymentOption'] = "bkash_api";
				$sql_query_update=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query_insert=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				header("Location: bkash_api.php");
				exit;
			}
			elseif($methodKey=="upay")
			{
				include("upay/token.php");
				$tokenReq = new TokenRequest();
				$amount	  = $_SESSION['ORDER_DETAILS']['txnAmount'];
				mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");				
				// Get token for transaction
				$tokenJson = $tokenReq->getToken($order_id,$amount);
				$token = json_decode($tokenJson);
				// Post request to gateway and redirect to gateway
				$tokenReq->pay_by_upay($token->upay_auth_token,$amount);
				exit();
			}
			elseif($methodKey=="teletalk")
            {
            	$_SESSION['order_details_response']['paymentOption'] = "teletalk";
				$sql_query_update=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query_insert=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				
				header("Location: teletalk.php");
				exit;
            }
            elseif($methodKey=="nagad")
            {
            	$_SESSION['order_details_response']['paymentOption'] = "nagad";
				$sql_query_update=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query_insert=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				
				header("Location: nagad.php");
				exit;
            }
			elseif($methodKey=="tbl") {
				$_SESSION['order_details_response']['paymentOption'] = "Trust Bank Limited";
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				header("Location: tbl_confirmation.php");
				exit;
			} elseif($methodKey=="dmw")
			{
				$_SESSION['order_details_response']['paymentOption'] = "DBBL Mobile Wallet";
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				header("Location: dmw_confirmation.php");
				exit;

                        } elseif($methodKey == "stbl") {
				
				
				  $_SESSION['order_details_response']['paymentOption'] = "Trust Bank Limited";
                                  $sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
				  $sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				  header("Location: school_tbl_confirmation.php");
                                  exit;
				
				
			} elseif($methodKey=="mtb") {
//echo "s";
                                $_SESSION['order_details_response']['paymentOption'] = "Mutual Trust Bank Ltd.";
                                           $sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."' WHERE order_id='".$order_id."'");
                                $sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");

                                //header("Location: mtb_confirmation.php");
                                include('mtb_confirmation.php');
                                //echo "hi";
                                exit;

			} 
			elseif($methodKey == "dbbl_nexus" or $methodKey == 'visa' or $methodKey == 'master_card' or $methodKey == 'dbbl_visa' or $methodKey == 'dbbl_master' or $methodKey == 'dbbl_mobile')
			{
				
				if($methodKey == "dbbl_nexus")
				{
					$_SESSION['order_details_response']['paymentOption'] = "DBBL Nexus";
				}	else if($methodKey == "dbbl_master")
				{
					$_SESSION['order_details_response']['paymentOption'] = "DBBL Master";
				}	else if($methodKey == "dbbl_visa")
				{
					$_SESSION['order_details_response']['paymentOption'] = "DBBL VISA";
				}	else if($methodKey == "visa")
				{
					$_SESSION['order_details_response']['paymentOption'] = "VISA";
				}	else if($methodKey == "master_card")
				{
					$_SESSION['order_details_response']['paymentOption'] = "Master Card";
				}else if($methodKey == 'dbbl_mobile')
				{
				$_SESSION['order_details_response']['paymentOption'] = "dbbl Mobile";
				}
				$epay_txid = $pe->getTransactionId($methodKey, $userID, $total_taka, $returnPointDbbl, $productName);
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$methodKey."', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."', bank_tx_id='".$epay_txid."' WHERE order_id='".$order_id."'");
				$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid='".$epay_txid."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
				$_SESSION['ORDER_DETAILS']['USER_VISIT_TIMES']++;
				if($epay_txid!==FALSE)
				{
			?>
					<legend>Forwording to bank</legend>
					<?php
						load_animi();
						$pe->init();
				}	 else
				{
					?>
					<legend>Error Message</legend>
					<?php
						$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='".$order_id."'");
						echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
				}
			}	elseif($methodKey=='visa' or $methodKey=='master_card')
			{
				$sql_dp = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT item_code, code_length from sp_digital_products");
				$digital_products_itemcode=array();
				$digital_products_codelength=array();
				$i=0;
				while($result_dp = mysql_fetch_assoc($sql_dp))
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
				if($digital_product == 1 and $total_taka > 4000)
				{
					echo '<div style="color:#ff0000;padding: 10px;">You are not allow to purchase digital product more than BDT 4000 using VISA/Master Card.</div>';
				}	else
				{
					$_SESSION['order_details_response']['paymentOption'] = 'VISA/Master Card';
					$epay_txid = $pe->getTransactionId($methodKey, $userID, $total_taka, $returnPoint, $productName);
					$_SESSION['ORDER_DETAILS']['bank_tx_id'] = $epay_txid;
					$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='VISA/Master Card', gateway='".$getway."', fwdtime='".$date->format('Y-m-d H:i:s')."', bank_tx_id='".$epay_txid."' WHERE order_id='".$order_id."'");
					$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_payments SET user_id='".$userID."', tc_txid='".$order_id."', epay_method='".$methodKey."', epay_txid='".$epay_txid."', epay_txid_time='".$date->format('Y-m-d H:i:s')."', epay_req_time='".$date->format('Y-m-d H:i:s')."'");
					$_SESSION['ORDER_DETAILS']['USER_VISIT_TIMES']++;
					if($epay_txid!==FALSE)
					{
					?>
						<legend>Forwording to bank</legend>
					<?php
						load_animi();
						$pe->init();
					} else
					{
					?>
						<legend>Error Message</legend>
					<?php
						$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='".$order_id."'");
						echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
					}
				}
			}	else
			{
				echo '<div style="color:#ff0000;padding: 10px;">You must choose a payment option.</div>';
			}
			function load_animi()
			{
				echo "
					<div style='text-align:center; margin:20px auto;'>
						<h2 style='color:#283a69;padding:10px;'>Forwarding you to Bank web site, please wait....</h2>
						<img src='img/loading.png' alt='Loading...' width='300' height='15' />
					</div>
				";
			}
			?>
		</fieldset>
	</div>
<?php
include("includes/footer.php");
?>
