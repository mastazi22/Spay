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

?>
<div id="main_container">
<fieldset id="trnsdetails">
	<legend>Bank Notice</legend>
	<?php
		if($_GET['type']==1){
			$type = "month";
		}
		else{
			$type = "day";
		}
		$no = $_GET['no'];
	?>
	<div style="padding:10px;color:#ff0000;text-align:justify;">We are extremely sorry because we cannot provide you this service at this moment as you have exhausted your transaction quota for this perticuler service at our site. Your maximum limit is <?php echo $no;?> transactions per <?php echo $type;?>. To increase quota for your card please <a href="https://shurjopay.com/contact_us.php" <span style="color:#0000ff;">contact us</sapn></a>.</div>
</fieldset>
</div>
<?php
include("includes/footer.php");
?>