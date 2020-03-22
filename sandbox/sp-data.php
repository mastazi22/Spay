<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.9.0.min.js"></script>
<script>
function checkPaymentOption(){
	if($('input[name=productType]:checked').length > 0){
		$("#frm_submit12").submit();
	}
	else{
		$("#err_check").text("Please select a payment method");
	}
}
function paymentOptionSelected(){
	$("#err_check").text("");
}
</script>
</head>
<body>
<?php
include("../payment_engine/paymentEngine.php");
if(isset($_POST['spdata'])){
echo "d";
	include("includes/configure.php");
	include("xml-parser.php");
	session_start();
	
	$xml2arr = xml2array(str_replace('<return url goes here>','return url goes here',str_replace('<uniqid goes here>','uniqid goes here',str_replace('<End user IP goes here>','End user IP goes here',$_POST['spdata']))));
	
	$_SESSION['ORDER_DETAILS']['merchantName'] = $merchantName = !is_array($xml2arr['shurjoPay']['merchantName'])?mysql_real_escape_string($xml2arr['shurjoPay']['merchantName']):"";
	$_SESSION['ORDER_DETAILS']['merchantPass'] = $merchantPass = !is_array($xml2arr['shurjoPay']['merchantPass'])?mysql_real_escape_string($xml2arr['shurjoPay']['merchantPass']):"";
	$_SESSION['ORDER_DETAILS']['returnURL'] = $returnURL = !is_array($xml2arr['shurjoPay']['returnURL'])?mysql_real_escape_string($xml2arr['shurjoPay']['returnURL']):"";
	$_SESSION['ORDER_DETAILS']['userIP'] = $userIP = !is_array($xml2arr['shurjoPay']['userIP'])?mysql_real_escape_string($xml2arr['shurjoPay']['userIP']):"";
	$_SESSION['ORDER_DETAILS']['uniqID'] = $uniqid = !is_array($xml2arr['shurjoPay']['uniqID'])?mysql_real_escape_string($xml2arr['shurjoPay']['uniqID']):"";
	$_SESSION['ORDER_DETAILS']['txnAmount'] = $amount = !is_array($xml2arr['shurjoPay']['totalAmount'])?mysql_real_escape_string($xml2arr['shurjoPay']['totalAmount']):"";
	$_SESSION['ORDER_DETAILS']['errCode'] = $errCode = !is_array($xml2arr['shurjoPay']['errCode'])?mysql_real_escape_string($xml2arr['shurjoPay']['errCode']):"";
	$_SESSION['ORDER_DETAILS']['txnRefNum'] = 'SP'.uniqid();
	$err_msg = "";

	if(!filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
		$err_msg .= "IP address is not valid<br/>";
	}
	$ex_amt=explode('.',$amount);
	if(count($ex_amt)>2){
		$err_msg .= "Amount is not valid<br/>";
	}
	else if(!is_array($ex_amt) and count($ex_amt)!=1){
		$err_msg .= "Amount is not valid<br/>";
	}
	elseif((isset($ex_amt[1]) and  isset($ex_amt[0])) and strlen($ex_amt[1])>2 or (!is_numeric($ex_amt[0]) and !is_numeric($ex_amt[1]))){
		$err_msg .= "Amount is not valid<br/>";
	}
	if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i',$returnURL)) {
		$err_msg .= "URL is not valid<br/>";
	}
	
	if(trim($err_msg)==""){        	
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_merchants WHERE username='".$merchantName."' and password='".$merchantPass."' and isactive='yes'");
		$result= mysql_fetch_object($sql_query);		
		$_SESSION['ORDER_DETAILS']['userID'] = $result->id;     	
		if($result){	
			
		       
			$date = new DateTime();
		    $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
		    $current_time = $date->format("Y-m-d H:i:s");
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "INSERT into sp_epay SET uid='".$result->id."', txid='".$uniqid."', amount='".$amount."', clientip='".$userIP."', returnurl='".$returnURL."', intime='".$current_time."'");
		            $_SESSION['valid_user']='yes';
		            
		     
		        ?>
		
		<div id="container">
		  <div id="header">
		  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
		  <div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
		  </div>
		  <div id="main_container">
		      <noscript>
		        &lt;h3&gt;Please enable JavaScript in your browser for the iPay&amp;reg; service&lt;/h3&gt;
		    </noscript>	
		   <form id="frm_submit12" name="frm_submit12" method="post" action="./bank_payment_step_2.php">
		    <fieldset id="trnsdetails">
		    <legend>Transaction Details</legend>
		           <ol>
		            <li>
		                <label>Total Transaction Amount</label>
		
		                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?> BDT</span>
		            </li>
		            <li>
		                <label>Transaction Reference Number</label>
		                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnRefNum']; ?>&nbsp;&nbsp;&nbsp;(Please preserve this for future reference)</span>
		            </li>
		 
		        </ol>
		   </fieldset>
				
		    <fieldset>
		    <legend>Select Payment Type</legend>
		
		           <!--<ol>-->
		           <table border="0" cellpadding="0" cellspacing="0" class="tbl">
		           <tbody>		        
		            <?php
		            $pe = new paymentEngine();
		            $pe_methods = $pe->getPaymentMethods();
		            foreach ($pe_methods as $method_key => $method_value) {
		                if ($method_value['status'] == 'active'):
                    ?>
		            <tr>
		            <td class="tbwidth">
		            <input name="productType" type="radio" value="<?php echo $method_key ?>" onclick="paymentOptionSelected();">	  
		                <img src="./img/<?php echo $method_value['img']; ?>"> </td>
		                <td class="tbwidth" align="right">&nbsp;</td>
		                <td width="50px">&nbsp;</td>
		            </tr>                
					 <? endif; 
					  } ?>
		            
		               <tr>
		                <td colspan="3"><div style="float:left; float: left; width: 100%; color:#ff0000;" id="err_check"> </div></td>
		                
		                                
					
		            </tr>                
		
		            
		        
		    </tbody>
				</table>
		    	<ol>
		            <li>
		                <label>&nbsp;</label>
		                
		                <input name="reset" type="reset" class="controls" value="Reset">
		                <input type="button" value="Pay Now" name="pay_now" onclick="checkPaymentOption();" class="controls" >
		                  <input name="action" type="hidden" value="">
		                  <input name="sequence" type="hidden" class="controls" value="10">
		            </li>
		        </ol>
		   </fieldset>
		   </form>
		   </div>
		  <div id="footer"></div>
		</div>
		<div id="shadow"></div>
		
		
		  <meta http-equiv="Pragma" content="no-cache">
		  <meta http-equiv="Expires" content="-1">
		
		<?php 
		} 
		else{
			
			$sql_log=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_merchants WHERE username='".$merchantName."' and password='".$merchantPass."'");
			$login_err= mysql_fetch_object($sql_log);
		       
			if(!$login_err){
				$sql_errcode=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE id=199");
				$err_code= mysql_fetch_object($sql_errcode);
				$post_data = '<?xml version="1.0" encoding="utf-8"?>
							<SP>
							  <Response>
							     <errCode>'.$err_code->return_code.'</errCode>
							     <errMsg>'.$err_code->return_status.'</errMsg>
							  </Response>
							 </SP>';
		?>
				
				<form method="post" action="<?php echo $returnURL; ?>" id="frm_submit">
					<input type="hidden" name="spdata" value='<?php echo $post_data; ?>'>
				</form>
				<script>
				document.getElementById('frm_submit').submit();
				</script>
		<?		
			}
			else{
				
				$sql_errcode=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE id=200");
				$err_code= mysql_fetch_object($sql_errcode);
				$post_data = '<?xml version="1.0" encoding="utf-8"?>
							<SP>
							  <Response>
							     <errCode>'.$err_code->return_code.'</errCode>
							     <errMsg>'.$err_code->return_status.'</errMsg>
							  </Response>
							 </SP>';
		?>
				
				<form method="post" action="<?php echo $returnURL; ?>" id="frm_submit">
					<input type="hidden" name="spdata" value='<?php echo $post_data; ?>'>
				</form>
				<script>
				document.getElementById('frm_submit').submit();
				</script>
		<?		
			
			}
		}
	}
	else{
?>
<div id="container">
		  <div id="header">
		  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
		  <div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
		  </div>
		  <div id="main_container">
		    <fieldset id="trnsdetails">
		    <legend>Error Message</legend>
<?php
				echo '<div style="color:#ff0000;padding: 10px;">'.$err_msg.'</div>';
?>
			</fieldset>
		  </div>
		  <div id="footer"></div>
		</div>
		<div id="shadow"></div>
<?php
	}
}
if($_GET['back']==1){
	session_start();
?>
<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
  <div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
  </div>
  <div id="main_container">
  <form id="frm_submit12" name="frm_submit12" method="post" action="./bank_payment_step_2.php">
		    <fieldset id="trnsdetails">
		    <legend>Transaction Details</legend>
		           <ol>
		            <li>
		                <label>Total Transaction Amount</label>
		
		                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?> BDT</span>
		            </li>
		            <li>
		                <label>Transaction Reference Number</label>
		                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnRefNum']; ?>&nbsp;&nbsp;&nbsp;(Please preserve this for future reference)</span>
		            </li>
		 
		        </ol>
		   </fieldset>
<fieldset>
		    <legend>Select Payment Type</legend>
		
		           <!--<ol>-->
		           <table border="0" cellpadding="0" cellspacing="0" class="tbl">
		           <tbody>		        
		            <?php
		            $pe = new paymentEngine();
		            $pe_methods = $pe->getPaymentMethods();
		            foreach ($pe_methods as $method_key => $method_value) {
		                if ($method_value['status'] == 'active'):
                    ?>
		            <tr>
		            <td class="tbwidth">
		            <input name="productType" type="radio" value="<?php echo $method_key ?>" onclick="paymentOptionSelected();">	  
		                <img src="./img/<?php echo $method_value['img']; ?>"> </td>
		                <td class="tbwidth" align="right">&nbsp;</td>
		                <td width="50px">&nbsp;</td>
		            </tr>                
					 <? endif; 
					  } ?>
		            
		               <tr>
		                <td colspan="3"><div style="float:left; float: left; width: 100%; color:#ff0000;" id="err_check"> </div></td>
		                
		                                
					
		            </tr>                
		
		            
		        
		    </tbody>
				</table>
		    	<ol>
		            <li>
		                <label>&nbsp;</label>
		                
		                <input name="reset" type="reset" class="controls" value="Reset">
		                <input type="button" value="Pay Now" name="pay_now" onclick="checkPaymentOption();" class="controls" >
		                  <input name="action" type="hidden" value="">
		                  <input name="sequence" type="hidden" class="controls" value="10">
		            </li>
		        </ol>
		   </fieldset>
		   </form>
		   </div>
		  <div id="footer"></div>
		</div>
		<div id="shadow"></div>
<?php
}
else{
echo "dd";
	//echo "<div style='color:#ff0000;text-align:center;font-size:25px;font-weight:bold;'>You are not allowed to access this page!!!!!!!</div>";
}
?>
</body>
</html>
