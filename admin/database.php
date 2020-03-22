<?php

  require_once '../includes/configure.php';

  function checkAdmin($name,$password)
  {

  /*  
    if( $name == 'admin' && $password == 'pass1234' ) 
    {
      $_SESSION['ORDER_DETAILS']['userID'] = 1;
      $_SESSION['ORDER_DETAILS']['loggedINAdmin'] = true; 
      $response['error_msg']='';
      $response['validation']='yes';
      return $response;
      exit();
    } 
    else 
    {
       return null;
       exit();
    }
  */  

   $pass=md5($password);
   $sql = "select * from sp_admin where username = '{$name}' and password = '$pass'";

   $sql1 = mysqli_query($GLOBALS["___mysqli_sm"],$sql);
   $sql2 = mysqli_fetch_row($sql1);

   $error_msg='';
   
    if(empty($sql2))
    {
      $err = "select * from sp_bankinfo where id = '199'";
      $err1=mysqli_query($GLOBALS["___mysqli_sm"],$err);
      $error_code=  mysqli_fetch_row($err1);
      $response['error_msg']="Invalid ID or Password";
      $response['validation']='no';
      $_SESSION['ORDER_DETAILS']['userID'] = ""; 
      return $response;     
    }
    else
    {
      $_SESSION['ORDER_DETAILS']['userID'] = $sql2[0];
      $_SESSION['ORDER_DETAILS']['loggedINAdmin'] = true; 
      $response['error_msg']='';
      $response['validation']='yes';
      return $response;
    }
  }


?>
