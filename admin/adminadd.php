<?php
session_start();
if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin'])
{
    
 header('Location: login.php');
}
?>

<?php

include("../includes/configure.php");



$userid=$_SESSION['ORDER_DETAILS']['userID'];
if(isset($_POST['username']) and $_POST['password']!="")
{
    
    if($userid!="")
    {
	$password=md5($_POST['password']);

    $query_sql="insert into sp_admin (id, username, password, fullname,isactive) values('','$_POST[username]','$password','$_POST[fullname]','$_POST[isactive]')";
 
  $res=mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
    
        if($res)
        {
        $error_flag="Admin added successfully.";
        }
        else
        {
         $error_flag="Data Insertion Failed.";
            
        }
       
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
		<div class="content">

			<div class="item" style="padding-left:80px;">
			<form id="form1" name="form1" method="post" action="" autocomplete="on">
    <fieldset>
		
           <legend>Please enter Admin Details</legend>
        <ol>
		  <li>
                <?php if($error_flag!=""){echo $error_flag;} ?>
               </li>
            <li>
               
               </li>
			    <li>
                <label>Full Name</label>
                <input name="fullname" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
            </li>
            <li>
                <label>Username</label>
                <input name="username" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
            </li>

            <li>
                <label>Password</label>
                <input name="password" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
                <span class="frmdata"></span>
            </li>

           
			 <li>
                <label>Is Active</label>
                <input type='radio' name='isactive' value='Yes' checked>Yes
				<input type='radio' name='isactive' value='No'>No
               
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
	 <form id="form1" name="form1" method="post" action="adminedit.php" autocomplete="off">
    <fieldset>
		
           <legend>Please Select Admin For Update</legend>
        <ol>
          
			   <?php 
			   
			    $query_sql="select * from sp_admin";

				$res=mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
				
				$html = '<select name="adminlist">';
				$html .= '<option value='."N/A".'>'."...Select...".'</option>';
				for($i=0;$i<mysql_num_rows($res);$i++) {
					mysql_data_seek($res,$i);
					$data=  mysql_fetch_array($res);
		     
					$html .= '<option value='.$data['id'].'>'.$data['username'].'</option>';
				}
				$html .= '</select>';

				
			   ?>
			   
            <li>
                <label>Admin Username</label>
				
               <?php echo $html; ?>
            </li>
 
           

            <li>
                <label>&nbsp;&nbsp;</label> 
               
                              <input name="submit" type="submit" class="controls" value="Update this Merchant" >
                              <input name="action" type="hidden" value="">
                              <input name="sequence" type="hidden" value="15">
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