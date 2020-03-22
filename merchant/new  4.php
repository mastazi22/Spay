<?php
session_start();
if(!$_SESSION['ORDER_DETAILS']['loggedIN'])
{
    
 header('Location: login.php');
}
?>
<?php

include("includes/configure.php");



$userid=$_SESSION['ORDER_DETAILS']['userID'];
if(isset($_REQUEST['txnID']) and $_POST['txnID']!="" and $userid!="" and $_POST['bankRefNum']!="" and  $_POST['txnAmount']!="" ){
    
    if($userid!="")
    {
    $query_sql="select * from sp_epay where uid='".$userid."' and txid='".$_POST['txnID']."' and bank_tx_id='".$_POST['bankRefNum']."' and amount='".$_POST['txnAmount']."'";

  $res=mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
  $data=  mysql_fetch_object($res);
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
    $current_time = $date->format("Y-m-d H:i:s");
    

  if(mysql_num_rows($res)==1)
  {
       $timediff=  round((strtotime($current_time)-strtotime($data->gw_time))/3600);
       
        if(($timediff)<24)
       {
        $sql="update sp_epay set Reconciliation='OPEN' where id='".$data->id."'";
        $flag=mysqli_query($GLOBALS["___mysqli_sm"], $sql);
        if($flag)
        {
        $error_flag="Your Request has been Saved.";
        }
        else
        {
            $error_flag="There is no data.";
            
        }
       }
       else
       {
           
           $error_flag="Sorry.Your time limit of 24 hrs is over.";
       }
   
  }
  else
  {
      $error_flag="There is no data.";
  }
 
    }

}
else
{
  $error_flag="Please fill the required information";
    
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
<?php  require_once 'database.php';
 require_once 'includes/configure.php'; 
   ?>  
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
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

			<div class="item">
			 <div id="main_container">
	  
   <form id="form1" name="form1" method="post" action="" autocomplete="off">
    <fieldset>

           <legend>Please enter information for reconcilliation</legend>
        <ol>
            <li>
                <?php if($error_flag!=""){echo $error_flag;} ?>
               </li>
            <li>
                <label>Txn. ID</label>
                <input name="txnID" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
            </li>

            <li>
                <label>Bank Ref. Number</label>
                <input name="bankRefNum" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
                <span class="frmdata"></span>
            </li>

            <li>
                <label>Txn. Amount</label>
                <input name="txnAmount" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
               
            </li>

            
            
		

            <li>
                <label>&nbsp;&nbsp;</label> 
               
                              <input name="submit" type="submit" class="controls" value="Proceed" >
                              <input name="action" type="hidden" value="">
                              <input name="sequence" type="hidden" value="15">
            </li>
			
        </ol>
       		
				
   </fieldset>
    </form>
  </div>
		</div>
		
		<div class="footer">
		
			<span class="left">&copy; 2012 <a href="#">SandBox</a></span>
		
			<span class="right"><a href="#">Designed & Developed</a> by <a href="#">Shurjomukhi</a></span>

			<div class="clearer"><span></span></div>
		
		</div>

	</div>	

</div>

</body>

</html>