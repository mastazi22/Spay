<?php

	  include ("../includes/configure.php");
    include ('../includes/session_handler.php');
  
  	$id   = filter_var(trim($_GET['id']), FILTER_SANITIZE_STRING);
    $status = filter_var(trim($_GET['status']), FILTER_SANITIZE_STRING);
  	$pin_number = filter_var(trim($_GET['pin_number']), FILTER_SANITIZE_STRING);

    $ipn_data = json_encode(array('id'=>$id,'status'=>$status,'pin_number'=>$pin_number));
	// get payment details
		
	 	if(isset($id))
  	{
  		if($status == 'SUCCESS')
  		{
        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_teletalk_transactions SET ipn_data='".$ipn_data."', transaction_time='".date('Y-m-d H:i:s')."' WHERE applicant_id='".$id."'");

        echo "<status>1</status>"; 
            
            
  		}
  		else
  		{
  			
          $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_teletalk_transactions SET  ipn_data='".$ipn_data."', transaction_time='".date('Y-m-d H:i:s')."' WHERE applicant_id='".$id."'");

            echo "<status>0</status>";             
  		}
  	}
    else 
    {
       echo "<status>0</status>";      
    }


?>