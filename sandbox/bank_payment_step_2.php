<?php
session_start();
if ($_SESSION['valid_user'] != 'yes') {

	header('Location: sp-data.php');
}
?>
<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php


include ("includes/configure.php");
include ("../payment_engine/paymentEngine.php");
$_SESSION['ORDER_DETAILS']['cardType'] = $_POST['productType'];
$uniqid = $_SESSION['ORDER_DETAILS']['uniqid'];
$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Dhaka'));
$current_time = $date->format("Y-m-d H:i:s");

if ($uniqid != "") {
	$query_sql = "update sp_epay set fwdtime='".$current_time."' where txid='".$uniqid."' ";

	mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
}
?>

        
    
<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
<div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
  </div>
   <div id="main_container">
	<div id="warning">Warning! Do not press browser back or forward button while you are in payment screens</div>
    

    <fieldset id="trnsdetails">
    <legend>Transaction Details</legend>
    <?php

$pe = new paymentEngine();
$pe_methods = $pe->getPaymentMethods();
foreach ($pe_methods as $method_key => $method_value) {
	if ($method_key == $_SESSION['ORDER_DETAILS']['cardType']) {
?>
        	<em><img src="./img/<?php echo $method_value['img']; ?>"></em>
    <?php
	}
}
?>
           <ol>
            <li>
                <label>Total Transaction Amount</label>

                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?>BDT</span>
            </li>
            <li>
                <label>Transaction Reference Number</label>
                <span class="frmdata"><?php echo $_SESSION['ORDER_DETAILS']['txnRefNum']; ?></span>
            </li>			
            <li>
                <label>Payment Type</label>

                <span class="frmdata"><?php echo strtoupper($_SESSION['ORDER_DETAILS']['cardType']); ?></span>
            </li>

        </ol>
   </fieldset>

   <form id="form1" name="form1" method="post" action="./bank_payment_step_3.php" autocomplete="off">
    <fieldset>

       <legend>Please enter your card details</legend>
	  	<?php

			foreach ($pe_methods as $method_key => $method_value) {
				if ($method_key == $_SESSION['ORDER_DETAILS']['cardType']) {
			?>
			        	<em><img src="./img/<?php echo $method_value['img']; ?>"></em>
			    <?php
				}
			}
		?>
          
        <ol>
            <li>
                <label>Card Number</label>
                <input name="cardNumber" type="text" class="controls" value="" size="20" maxlength="19"><span class="RequiredFieldValidator1">*</span>
            </li>

            <li>
                <label>Security Code</label>
                <input name="cvcNumber" type="password" class="controls" value="" size="20" maxlength="4"><span class="RequiredFieldValidator1">*</span>
                <span class="frmdata"></span>
            </li>

            <li>
                <label>Expiry Date</label>
                <select name="expMonth" class="controls">
                              
                              <option value="01">01</option>
                              
                              <option value="02">02</option>
                              
                              <option value="03">03</option>
                              
                              <option value="04" selected="selected">04</option>
                              
                              <option value="05">05</option>
                              
                              <option value="06">06</option>
                              
                              <option value="07">07</option>
                              
                              <option value="08">08</option>
                              
                              <option value="09">09</option>
                              
                              <option value="10">10</option>
                              
                              <option value="11">11</option>
                              
                              <option value="12">12</option>
                              
              </select>

                              <select name="expYear" class="controls">                                
                                
                                <option value="2013" selected="selected">2013</option>
                                
                                <option value="2014">2014</option>
                                
                                <option value="2015">2015</option>
                                
                                <option value="2016">2016</option>
                                
                                <option value="2017">2017</option>
                                
                                <option value="2018">2018</option>
                                
                                <option value="2019">2019</option>
                                
                                <option value="2020">2020</option>
                                
                                <option value="2021">2021</option>
                                
                                <option value="2022">2022</option>
                                
                                <option value="2023">2023</option>
                                
                                <option value="2024">2024</option>
                                
              </select><span class="RequiredFieldValidator1">*</span>
            </li>

            <li>
                <label>Card Holder's Name</label>
                <input name="cardHolderName" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">* (As printed on the card)</span> 
            </li>
            <li>
                <label>&nbsp;</label>
                <span class="RequiredFieldValidator1">&nbsp;</span> 
            </li>
	    
		

            <li>
                <label>&nbsp;&nbsp;</label> 
                <input name="back" type="submit" class="controls" value="Back" >
                              <input name="reset" type="reset" class="controls" value="Reset">
                              <input name="submit" type="submit" class="controls" value="Pay Now" >
                              <input name="action" type="hidden" value="">
                              <input name="sequence" type="hidden" value="15">
            </li>
			
        </ol>
        <div id="warning_1">
  		<b>Disclaimer </b>
  		<br>
  		<br>
  		It is highly recommended that all BRAC Bank Ltd VISA and Master Cardholders should use the iWallet facility offered by BRAC Bank Limited in order to make any purchase through the Internet. BRAC Bank will not be liable in any matter whatsoever if any dispute arises from this transaction as you are not using the more secure iWallet service.  
  		<br>
  		</div>				
				
   </fieldset>
    </form>
  </div>
  <div id="footer"></div>
</div>
<div id="shadow"></div>


  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">

</body>
</html>