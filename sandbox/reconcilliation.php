<?php
session_start();
if(!$_SESSION['ORDER_DETAILS']['loggedIN'])
{
    
 header('Location: login.php');
}
?>
<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

include("includes/configure.php");



$userid=$_SESSION['ORDER_DETAILS']['userID'];
if(isset($_REQUEST['txnID']) and $_POST['txnID']!="" and $userid!="" and $_POST['bankRefNum']!="" and  $_POST['txnAmount']!="" ){
    
    if($uniqid!="")
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

        
    
<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
<div id="merchantlogo"><img src="./img/merchantimage" alt="SHURJOMUKHI"></div>
  </div>
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
  <div id="footer"></div>
</div>
<div id="shadow"></div>


  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">

</body>
</html>