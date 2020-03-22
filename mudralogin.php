<?php
include("includes/header.php");
include("includes/configure.php");
include("includes/configure-login.php");
include("includes/configure-mudra.php");
include("includes/session_handler.php");
include("includes/login.php");
include("includes/mudraBalance.php");
	
$mudraBalance = new mudraBalance();

$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Dhaka'));

if(isset($_POST['mudralogin'])){
	$login = new Login();
	$userInfo = $login->mudraLogin($_POST,$dblogin->con_login);
	
	if($userInfo){
		$_SESSION['mudra_user_id'] = $userInfo->userid;
		$currentBalance = $mudraBalance->getUserBalance($userInfo->userid,$dbmudra->con_mudra);
		$sum = $currentBalance - $_SESSION['ORDER_DETAILS']['txnAmount'];
		$_SESSION['ORDER_DETAILS']['txnAmountAvailable'] = $sum;
?>
		<div id="main_container">
			<form method="POST" action="./mudralogin.php">
			<fieldset id="trnsdetails">
				<legend>Balance in shurjoMudra</legend>
				<div class="login_area">	
					<div class="txt_area">
						<span class="input_level">Current Balance:</span>
						<span class="inputbox"><?php echo $currentBalance; ?></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Require Balance:</span>
						<span class="inputbox"><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Sum: </span>
						<span class="inputbox"><?php echo $sum; ?></span>
					</div>
					<div class="txt_area">						
						<span class="input_level">Status:</span>
						<span class="inputbox">
							<?php
								if($sum >= 0){
									echo "SUCCESS";
								}
								else{
									echo "FAIL";
								}
							?>
						</span>
					</div>
					<div class="txt_area">
						<span class="input_level">&nbsp;</span>
						<span class="inputbox"><input type="submit" name="mudrabalance" id="mudrabalance" value="Confirm"></span>
					</div>
				</div>
			</fieldset>
		</form>
		</div>
<?php				
	}
	else{
?>
		<div id="main_container">
		<fieldset id="trnsdetails">
			<legend>Login to shurjoMudra</legend>
			<form method="POST" action="./mudralogin.php">
				<div class="login_area">					
					<div style="color: #FF0000;font-size: 12px;">Username/Password Invalid.</div>
					<div class="txt_area">
						<span class="input_level">Username:</span>
						<span class="inputbox"><input type="text" name="username" id="username" value=""></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Password:</span>
						<span class="inputbox"><input type="password" name="password" id="password" value=""></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">&nbsp;</span>
						<span class="inputbox"><input type="submit" name="mudralogin" id="mudralogin" value="Login"></span>
					</div>
					<div class="txt_area">
						<span class="input_level">&nbsp;</span>
						<span class="inputbox"  style="font-size: 11px; padding-left: 20%;"><a href="<?php echo $db->mudra_url?>" target="_blank">Create new account to shurjoMudra</a></span>
					</div>
				</div>
			</form>
		</fieldset>
		</div>
<?php
	}
}
else if(isset($_POST['mudrabalance'])){
	
	$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
	
	if($_SESSION['ORDER_DETAILS']['txnAmountAvailable']>0){		
		$transactionID = $mudraBalance->doTransaction($_SESSION['mudra_user_id'], $_SESSION['ORDER_DETAILS']['txnAmount'], 6, 'shurjoPay' ,null, 'SUCCESS', $dbmudra->con_mudra);
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$transactionID."' WHERE tc_txid='".$order_id."'", $db->con_sp);
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$transactionID."' WHERE order_id='".$order_id."'", $db->con_sp); 
		
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] = $transactionID;
		$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
		$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
		
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='shurjomudra' and return_code='000'", $db->con_sp);
		$result = mysql_fetch_object($sql_query);

		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;

		$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";

		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='".$result->return_code."', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='SUCCESS', bank_response='".$bank_data."' WHERE order_id='".$order_id."'", $db->con_sp);
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'", $db->con_sp);
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_shurjomudra_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'", $db->con_sp);
		unset($_SESSION['mudra_user_id']);
		header("Location: ".$db->local_return_url);
	}
	else{				
		$transactionID = $mudraBalance->doTransaction($_SESSION['mudra_user_id'], $_SESSION['ORDER_DETAILS']['txnAmount'], 6, 'shurjoPay' ,null, 'FAIL', $dbmudra->con_mudra);
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$transactionID."' WHERE tc_txid='".$order_id."'", $db->con_sp);
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$transactionID."' WHERE order_id='".$order_id."'", $db->con_sp); 
		
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] = $transactionID;
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
		
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='shurjomudra' and return_code='001'", $db->con_sp);
		$result = mysql_fetch_object($sql_query);

		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;

		$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|FAIL||ApprovalCode|";

		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='".$result->return_code."', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'", $db->con_sp);
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'", $db->con_sp);
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_shurjomudra_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'", $db->con_sp);
		unset($_SESSION['mudra_user_id']);
		header("Location: ".$db->local_return_url);
	}
}
else if(isset($_POST['backoptions'])){
	unset($_SESSION['mudra_user_id']);
	header("Location: ./payment_option.php");
}
else if(isset($_SESSION['mudra_user_id'])){
		$mudraBalance = new mudraBalance();
		$currentBalance = $mudraBalance->getUserBalance($_SESSION['mudra_user_id'], $dbmudra->con_mudra);
		$sum = $currentBalance - $_SESSION['ORDER_DETAILS']['txnAmount'];
		$_SESSION['ORDER_DETAILS']['txnAmountAvailable'] = $sum;
?>
		<div id="main_container">
			<form method="POST" action="./mudralogin.php">
			<fieldset id="trnsdetails">
				<legend>Balance in shurjoMudra</legend>
				<div class="login_area">	
					<div class="txt_area">
						<span class="input_level">Current Balance:</span>
						<span class="inputbox"><?php echo $currentBalance; ?></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Require Balance:</span>
						<span class="inputbox"><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Sum: </span>
						<span class="inputbox"><?php echo $sum; ?></span>
					</div>
					
					<div class="txt_area">
						<span class="input_level">Status:</span>
						<span class="inputbox">
							<?php
								if($sum >= 0){
									echo "SUCCESS";
								}
								else{
									echo "FAIL";
								}
							?>
						</span>
					</div>
					<div class="txt_area">
						<span class="input_level">&nbsp;</span>
						<span class="inputbox"><input type="submit" name="mudrabalance" id="mudrabalance" value="Confirm"></span>
					</div>
				</div>
			</fieldset>
			</form>
		</div>
<?php		
}
else{
?>
<div id="main_container">
<fieldset id="trnsdetails">
	<legend>Login to shurjoMudra</legend>
	<form method="POST" action="./mudralogin.php">	
		<div class="login_area">
			<div class="txt_area">
				<span class="input_level">Username:</span>
				<span class="inputbox"><input type="text" name="username" id="username" value=""></span>
			</div>
			
			<div class="txt_area">
				<span class="input_level">Password:</span>
				<span class="inputbox"><input type="password" name="password" id="password" value=""></span>
			</div>
			<div class="txt_area">
				<span class="input_level">&nbsp;</span>
				<span class="inputbox"><input type="submit" name="mudralogin" id="mudralogin" value="Login"></span>
			</div>
			<div class="txt_area">
				<span class="input_level">&nbsp;</span>
				<span class="inputbox"  style="font-size: 11px; padding-left: 20%;"><a href="<?php echo $db->mudra_url?>" target="_blank">Create new account to shurjoMudra</a></span>
		</div>
		</div>
	</form>
</fieldset>
</div>
<?php
}
include("includes/footer.php");
?>