<?php
include("includes/configure.php");
include ("includes/session_handler.php");
$_SESSION['ORDER_DETAILS']=$_POST;
include("includes/header.php");
include("payment_engine/paymentEngine.php");
?>
<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
<script>
function checkPaymentOption(){
	if($('input[name=paymentOption]:checked').length > 0){
		$("#frm_submit").submit();
	}
	else{
		$("#err_check").text("Please select a payment method");
	}
}
function paymentOptionSelected(){
	$("#err_check").text("");
}
</script>
<style>
#pay_now{
	padding: 5px;
    width: 200px;
}
#footer {
    height: 100px;
}
</style>
   <div id="main_container">
   		<div style="padding-left:50px;">
   		Total Amount :: <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?> BDT
	   	<form id="frm_submit" name="frm_submit" method="post" action="./payment_process_emi.php">  
	   	<div style="padding:10px 0">Choose payment option:</div> 
		  <!--<input name="paymentOption" type="hidden" value="<?php echo $_SESSION['ORDER_DETAILS']['paymentOption'] ?>"> -->
		  <?
            $pe = new paymentEngine();
            $pe_methods = $pe->getPaymentMethods();
            foreach ($pe_methods as $method_key => $method_value) {
                if ($method_value['status'] == 'active'):
                    ?>
                    <div class="form-radio-item" style="clear:left;padding-bottom: 30px;">
                        <input type="radio" style="position: relative; top: -8px;" onclick="paymentOptionSelected();"; class="form-radio validate[required]" id="input_5_<?php echo $method_key ?>" name="paymentOption" value= "<?php echo $method_key ?>"
                        <?php
                        if (isset($_SESSION['input']['paymentOption']) && $_SESSION['input']['paymentOption'] == $method_key) {
                            echo 'checked="checked"';
                        }
                        ?> />
                        <?php 
						if($method_key=="visa"){ ?>
			              <label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;"><img src="img/<?php echo $method_value['img']; ?>" alt="<?php echo $method_value['text']; ?>" height="30"></label>&nbsp;<span style="color:#ff0000;font-weight:bold;padding-left: 7px;position: relative;top: -8px;font-size: 12px;">(Transaction limit: 4 per day and 20 per month for online recharge)</span>
			             <? 
			             } 
			             else if($method_key=="master_card"){ ?>
			              <label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;"><img src="img/<?php echo $method_value['img']; ?>" alt="<?php echo $method_value['text']; ?>" height="30"></label>&nbsp;<span style="color:#ff0000;font-weight:bold;padding-left: 7px;position: relative;top: -8px;font-size: 12px;">(Transaction limit: 4 per day and 20 per month for online recharge)</span>
			             <? 
			             } 
						 else if($method_key=="dbbl_visa"){ ?>
			              <label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;"><img src="img/<?php echo $method_value['img']; ?>" alt="<?php echo $method_value['text']; ?>" height="30"></label>&nbsp;<span style="color:#ff0000;font-weight:bold;padding-left: 7px;position: relative;top: -8px;font-size: 12px;">(Transaction limit: 4 per day and 20 per month for online recharge)</span>
			             <? 
			             } 
						 else if($method_key=="dbbl_master"){ ?>
			              <label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;"><img src="img/<?php echo $method_value['img']; ?>" alt="<?php echo $method_value['text']; ?>" height="30"></label>&nbsp;<span style="color:#ff0000;font-weight:bold;padding-left: 7px;position: relative;top: -8px;font-size: 12px;">(Transaction limit: 4 per day and 20 per month for online recharge)</span>
			             <? 
			             } 
						 else if($method_key=="dbbl_nexus"){ ?>
			              <label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;"><img src="img/<?php echo $method_value['img']; ?>" alt="<?php echo $method_value['text']; ?>" height="30"></label>&nbsp;<span style="color:#ff0000;font-weight:bold;padding-left: 7px;position: relative;top: -8px;font-size: 12px;">(Transaction limit: 4 per day and 20 per month for online recharge)</span>
			             <? 
			             } 
			         <!--    //else{
			             //?>
			             //	<label for="input_5_<?php echo $method_key ?>" style="float: none; width: 50px; text-align: center;" ><img src="img/<?php echo $method_value['img']; ?>" //alt="<?php echo $method_value['text']; ?>" height="30"></label>
			             //<?php
			             //}
			             //?> --> 
                         <br><span style="float: left; padding-left: 4%; font-family: verdana; font-size: 12px;"><?php echo $method_value['text']; ?></span>
                    </div><span class="clearfix"></span>
                <? endif; ?>
			<? } ?>
					<div style="float:left; float: left; width: 100%; color:#ff0000;" id="err_check"> </div>
                    <div style="float:left; float: left; text-align: center; width: 100%;"><input type="button" value="Pay Now" name="pay_now" id="pay_now" onclick="checkPaymentOption();"></div>
	   </form>
	   </div>
	   
   </div>
<?php
include("includes/footer.php");
?>