<?php session_start();
if( $_SESSION['ORDER_DETAILS']['loggedIN'] != true)
{
    header( 'Location: login.php' );
}
?>
<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.9.0.min.js"></script>
<script>
function checkPaymentOption(opt){
	var cnt =0;
	if(opt=="ip"){
		if($("#ip").val() == ""){
			var cnt =1;
			$("#frmIP").text("Please enter a valid IP");
		}
		else {
			$("#frmIP").text(" ");
		}
	}
	else if(opt=="order_id"){
		if($("#order_id").val() == ""){
			var cnt =1;
			$("#frmOrder").text("Please enter a Order ID");
		}
		else {
			$("#frmOrder").text(" ");
		}
	}
	else if(opt=="amount"){
		if($("#amount").val() == ""){
			var cnt =1;
			$("#frmAMT").text("Please enter a amount");
		}
		else {
			$("#frmAMT").text(" ");
		}
	}
	/*else if(opt=="err_code"){
		if($("#err_code").val() == ""){
			var cnt =1;
			$("#frmerr_code").text("Please enter a error code");
		}
		else {
			$("#frmerr_code").text(" ");
		}
	}
	else if(opt=="return_url"){
		if($("#return_url").val() == ""){
			var cnt =1;
			$("#frmreturn_url").text("Please enter a return url");
		}
		else {
			$("#frmreturn_url").text(" ");
		}
	}*/
	else if(opt=="all"){
		if($("#ip").val() == ""){
			var cnt =1;
			$("#frmIP").text("Please enter a valid IP");
		}
		if($("#order_id").val() == ""){
			var cnt =1;
			$("#frmOrder").text("Please enter a Order ID");
		}
		if($("#amount").val() == ""){
			var cnt =1;
			$("#frmAMT").text("Please enter a amount");
		}
		/*if($("#err_code").val() == ""){
			var cnt =1;
			$("#frmerr_code").text("Please enter a error code");
		}
		if($("#return_url").val() == ""){
			var cnt =1;
			$("#frmreturn_url").text("Please enter a valid URL");
		}*/
		if( cnt ==0){
			$("#frm_submit12").submit();
		}
	}
}
</script>
</head>
<body>

<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
<div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
  </div>
  <div id="main_container">
      <noscript>
        &lt;h3&gt;Please enable JavaScript in your browser for the iPay&amp;reg; service&lt;/h3&gt;
    </noscript>	
   
    <h1>Test Page For shurjoPay</h1>
    <form id="frm_submit12" name="frm_submit12" method="post" action="./send_request.php">
<ol>
            <li>
                <label>User IP</label>
                <input name="ip" id="ip"  type="text" class="controls" value="" size="20" maxlength="19" onblur="checkPaymentOption('ip');"><span class="RequiredFieldValidator1">*</span>
                <span id="frmIP"></span>
            </li>

            <li>
                <label>Order ID</label>
                <input name="order_id" id="order_id" type="text" class="controls" value="" size="20" maxlength="40" onblur="checkPaymentOption('order_id');"><span class="RequiredFieldValidator1">*</span>
                <span id="frmOrder"></span>
            </li>
            <li>
                <label>Amount</label>
                <input name="amount" id="amount" type="text" class="controls" value="" size="20" maxlength="4" onblur="checkPaymentOption('amount');"><span class="RequiredFieldValidator1">*</span>
                <span id="frmAMT"></span>
            </li>
            <!--<li>
                <label>Error Code</label>
                <input name="err_code" id="err_code" type="text" class="controls" value="" size="20" maxlength="4" onblur="checkPaymentOption('err_code');"><span class="RequiredFieldValidator1">*</span>
                <span id="frmerr_code"></span>
            </li>
            <li>
                <label>Return URL</label>
                <input name="return_url" id="return_url" type="text" class="controls" value="" size="20" maxlength="200" onblur="checkPaymentOption('return_url');"><span class="RequiredFieldValidator1">*</span>
                <span id="frmreturn_url"></span>
            </li>-->
            <li>
                <label>&nbsp</label>
                <input type="button" value="Submit"  onclick="checkPaymentOption('all');" />
                <span class="frmdata"></span>
            </li>
            
 </ol>
 		</form>

    
    
   </div>
  <div id="footer"></div>
</div>
<div id="shadow"></div>


  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">


</body>
</html>