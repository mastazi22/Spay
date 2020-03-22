<?php
include ("includes/configure.php");
include ('includes/session_handler.php');
include ("includes/header_dmw.php");
?>
<div id="main_container">
<fieldset id="trnsdetails">
<?php
if(isset($_POST['number_send'])){
include ("dmw_xmlhttp.php");
$amount = $_SESSION['ORDER_DETAILS']['txnAmount'];
$sender = substr($_REQUEST['mobile_sender'], 1);
$trxid = $_REQUEST['trx_id'];

?> 

<script type="text/javascript">
	var timeinterval = 0.5*60;       
	var timecount=0;
	setInterval("getsms(<?php  echo $amount; ?>,<?php  echo  $sender; ?>,<?php  echo $trxid; ?>)", 10000);
</script>
<div style="color:#ff0000;text-align: center; padding-top:10px;">Warning! Please do not press browser back or reload button</div>
<div id="loading" style="text-align: center; padding-top:10px;">
	<img src="img/animation_processing.gif" align="middle"/>
</div>
<?php
}
else{
?>
<SCRIPT TYPE="text/javascript">
<!--
// copyright 1999 Idocs, Inc. http://www.idocs.com
// Distribute this script freely but keep this notice in place
function numbersonly(myfield, e, dec)
{
var key;
var keychar;

if (window.event)
   key = window.event.keyCode;
else if (e)
   key = e.which;
else
   return true;
keychar = String.fromCharCode(key);

// control keys
if ((key==null) || (key==0) || (key==8) || 
    (key==9) || (key==13) || (key==27) )
   return true;

// numbers
else if ((("0123456789").indexOf(keychar) > -1))
   return true;

// decimal point jump
else if (dec && (keychar == "."))
   {
   myfield.form.elements[dec].focus();
   return false;
   }
else
   return false;
}

//-->
</SCRIPT>
<form method="POST" action="./dmw_confirmation.php">
<ul>
	<li id="id_4" class="form-line" style="z-index: 0;">
	    <label for="input_4" id="label_4" class="form-label-left"> Amount:: </label>
	    <div class="form-input" id="cid_4"> &nbsp;&nbsp;<span style="font-size:18px;">&#2547; <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?></span> </div>   
	</li>
	<li id="id_4" class="form-line" style="z-index: 0;">
	    <label for="input_4" id="label_4" class="form-label-left"> Receiver number:: </label>
	    <div class="form-input" id="cid_4"> &nbsp;&nbsp;Biller Code: 500  <span style="font-size: 10px; padding-left: 43px;"> Send money to this number    </span> </div>   
	</li>
	<li id="id_4" class="form-line" style="z-index: 0;">
	    <label for="input_4" id="label_4" class="form-label-left">Sender number:: </label>
	    <div class="form-input" id="cid_4">&nbsp;&nbsp;<input type="text" name="mobile_sender"  id="mobile_sender" value="" maxlength="11" size="12" onKeyPress="return numbersonly(this, event)"/> &nbsp &nbsp &nbsp  <span style="font-size: 10px; margin-left: -10px;"> From which number do you want to send money    </span>  </div>
	</li>  
	<li id="id_4" class="form-line" style="z-index: 0;">
	    <label for="input_4" id="label_4" class="form-label-left">Transaction Id:: </label>
	    <div class="form-input" id="cid_4">&nbsp;&nbsp;<input type="text" name="trx_id"  id="trx_id" value="" maxlength="12" size="12" /> &nbsp &nbsp &nbsp  <span style="font-size: 10px; margin-left: -10px;"> Give trxId from received message of DBBL Mobile Wallet    </span>  </div>
	</li>
	<li id="id_4" class="form-line" style="z-index: 0;">
	    <label for="input_4" id="label_4" class="form-label-left">&nbsp; </label>
	    <div class="form-input" id="cid_4"><input type="submit" name="number_send"  id="number_send" value="Send"/> &nbsp &nbsp &nbsp  <span style="font-size:10px;"> &nbsp;    </span>  </div>
	</li>
</ul>
</form>

</fieldset>
<br>
<br>
<img align="middle" src="./img/dmw.jpg"  width= "100%">


<?php
}
include ("includes/footer.php");
?>