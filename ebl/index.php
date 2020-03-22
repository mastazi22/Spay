<?php
	session_start();

$amount=100;

?>



<form name="qcash" id="qcash" action="http://localhost/CmsDemo/HostedCheckoutReturnToMerchant_NVP.php" method="post"  >
	<input type="hidden" name="order.amount" value="<?php echo $amount;?>"/>
	<input type="hidden" name="order.currency" value="BDT"/>
	<input type="hidden" name="customer_receipt_email" value="rubelislam301@gmail.com"/>

	</form>
<script type='text/javascript'>document.qcash.submit();</script>

