<?php
session_start();
if(!$_SESSION['ORDER_DETAILS']['loggedIN'])
{
    
 header('Location: login.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
<?php  require_once 'database.php'; 
   ?>  
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/> 
<meta name="#" content="#"/> 
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<title>..::MIS::..</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Calendar javascript Code</title>
<link rel="stylesheet" type="text/css" href="http://www.jscodes.com/codes/calendar_javascript/demo/css/datePicker.css" />
<script src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery-1.6.1.min.js" type="text/javascript"></script>
<script src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery.datePicker-min.js" type="text/javascript"></script>
<!--[if IE]><script type="text/javascript" src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery.bgiframe.min.js"></script><![endif]-->

<script type="text/javascript">
  $(window).ready(function(){
  $('#todate').datePicker({clickInput:true});
  $('#fromdate').datePicker({clickInput:true});
});
</script>

</head>

<body>

<div class="main">

	<div class="container">

		<div class="gfx">
		  <div class="logo">	
			<span class="left"><a href=""><img src="images/sand-box-logo.jpg" /></a></span>		
			<span class="right"><div id="merchantlogo"><img src="./img/merchantimage" alt="SHURJOMUKHI"></div></span>
			<div class="clearer"><span></span></div>	
		  </div>
                    
		</div>

		<div class="menu">
			<a href="loginMIS.php"><span >Search by Date</span></a>
                        <!--
			<a href="#"><span>Search by ID</span></a>
			<a href="#"><span>Bank Tx ID</span></a>
			<a  href="reconcilliation.php"><span>Reconcilliation</span></a>	
                        -->
		</div>
<div style="float:right;font-size:15px" class="menu"><a href="logout.php"><span >Logout</span></a></div>
<div style="float:right;font-size:15px" class="menu"><a href="navigation.php"><span >Home</span></a></div>
		<div class="content">

			<div class="item" style="padding-left:250px;">
			  <div>
      <noscript>
        &lt;h3&gt;Please enable JavaScript in your browser for the iPay&amp;reg; service&lt;/h3&gt;
      </noscript>	 
     
      <span class="left" style="font-weight: bold; width: 140px; padding-left: 5px; padding-right: 5px;position: relative; top: -5px;"><a href="loginMIS.php"><img src="images/mis.png" /></a></span>
      <span class="left" style="font-weight: bold; width: 140px; padding-left: 5px; padding-right: 5px;position: relative; top: -5px;"><a href="reconcilliation.php"><img src="images/reconciliation.png" /></a></span>
      <div class="clearer"><span></span></div> 
   </div>
   <div>
     
      <span class="left" style="font-weight: bold; width: 140px; text-align: center; padding-left: 5px; padding-right: 5px;"><a href="loginMIS.php">MIS Report</a></span>
      <span class="left" style="font-weight: bold; width: 140px; text-align: center; padding-left: 5px; padding-right: 5px;"><a href="reconcilliation.php"> Reconciliation</a></span>
       <div class="clearer"><span></span></div> 
     </div>
   </div>
		</div>
		
		<div class="footer">
		
			<span class="left">&copy; 2012 <a href="#">shurjoPay</a></span>
		
			<span class="right"><a href="#">Designed & Developed</a> by <a href="#">Shurjomukhi</a></span>

			<div class="clearer"><span></span></div>
		
		</div>

	</div>	

</div>

</body>

</html>