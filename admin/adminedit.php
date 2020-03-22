<?php
session_start();

if(!isset($_POST['adminlist']) and !isset($_POST['id']))
{
	
	header('Location: adminadd.php');
}

if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin'])
{
    
 header('Location: login.php');
}
?>
<?php

include("../includes/configure.php");
$userid=$_SESSION['ORDER_DETAILS']['userID'];
if(isset($_POST['id']))
{
    //$password=md5($_POST['password']);
	
    $query_sql="update sp_admin set ";
	
	if(isset($_POST['username'])){$query_sql.="username='".$_POST['username']."'";}
    if(isset($_POST['password'])){$password=md5($_POST['password']);$query_sql.=", password='".$password."'";}
	if(isset($_POST['fullname'])){$query_sql.=", fullname='".$_POST['fullname']."'";}
	if(isset($_POST['isactive'])){$query_sql.=", isactive='".$_POST['isactive']."'";}
	$query_sql.=" where id='".$_POST['id']."'";
   
  $res=mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
    
        if($res)
        {
        $error_flag="Admin updated successfully.";
        }
        else
        {
         $error_flag="Data update Failed.";
            
        }
       
	
}
else
{
  $error_flag="Please fill the required (*) information";
    
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
<?php  require_once 'database.php';
 require_once '../includes/configure.php'; 
   ?>  
<head>
<link href="css/layout.css" rel="stylesheet" type="text/css">
<link href="css/text.css" rel="stylesheet" type="text/css">
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
<div style="float:right;font-size:15px" class="menu"><a href="adminadd.php"><span >Back</span></a></div>
		<div class="content">

			<div class="item" style="padding-left:60px;">
			 
<?php  
 $query_sql="select * from sp_admin where id='$_POST[adminlist]'";
$res=mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
$data=mysql_fetch_object($res);

?>
	 
   <form id="form2" name="form2" method="post" action="adminedit.php" autocomplete="off">
    <fieldset>
		
           <legend>Please enter Merchant Details</legend>
        <ol>
            <li>
                 <?php if($error_flag!=""){echo $error_flag;} ?>
               </li>
			   <li>
                <label>Full Name</label>
				<input name="id" type="hidden" value="<?php echo $data->id; ?>">
                <input name="username" type="text" class="controls"  size="20" maxlength="40" value="<?php echo $data->fullname; ?>"><span class="RequiredFieldValidator1">*</span>
            </li>
            <li>
                <label>Username</label>
				
                <input name="username" type="text" class="controls"  size="20" maxlength="40" value="<?php echo $data->username; ?>"><span class="RequiredFieldValidator1">*</span>
            </li>

            <li>
                <label>Password</label>
                <input name="password" type="text" class="controls"  size="20" maxlength="40" value=""><span class="RequiredFieldValidator1">*</span>
                <span class="frmdata"></span>
            </li>

            <li>
                <label>Is Active</label>
				<?php
				if($data->isactive=='Yes')
				{
					?>
                <input type='radio' name='isactive' value='Yes' checked>Yes
				<input type='radio' name='isactive' value='No'>No
				<?php
				}
				else
				{
					?>
					<input type='radio' name='isactive' value='Yes' >Yes
				<input type='radio' name='isactive' value='No' checked>No
				<?php } ?>
            </li>
			
           

            <li>
                <label>&nbsp;&nbsp;</label> 
               
                              <input name="submit" type="submit" class="controls" value="Proceed" >
                             
            </li>
			
        </ol>
       		
				
   </fieldset>
    </form>
	 
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