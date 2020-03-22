<?php 
session_start();

		if (isset($_SESSION['session_time_out']) && $_SESSION['session_time_out'] == 'yes') 
		{

?>
	    <div id="subHeader_1" class="form-subHeader">
			<span style='color:red'>You have waited too long, so your session has timed out.
			Please fill up top-up info again and hit recharge</span>
		</div>
<?php   }
		else
		{ 
			header('location: https://shurjopay.com.bd'); 
		} 
?>