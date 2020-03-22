<?php
  include ("includes/configure.php");
  include ('includes/session_handler.php');  

    // send data
      $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
      $txid = $_SESSION['ORDER_DETAILS']['uniqID'];

      if(isset($_SESSION['ORDER_DETAILS']['otherOption']) && !empty($_SESSION['ORDER_DETAILS']['otherOption']))
      {

        list($applicant_id, $applicant_pass, $course_id, $course_name, $mobile, $applicant_name, $start_time, $end_time) = explode("|", $_SESSION['ORDER_DETAILS']['otherOption']);

        // set id in session
        $_SESSION['ORDER_DETAILS']['jbaApplicationId'] = $applicant_id;
        
              
        $application_info = http_build_query(
            array(
              'user_key'   => 'rmy',
              'pass_key'   => '1rmy1',            
              'id'         =>  $applicant_id,
              'user'       =>  '',
              'password'   =>  '',
              'mobile'     =>  $mobile,
              'name'       =>  $applicant_name,
              'courseid'   =>  $course_id,
              'course'     =>  $course_name,
              'amount'     =>  $_SESSION['ORDER_DETAILS']['txnAmount'],
              'start_time' =>  $start_time,
              'end_time'   =>  $end_time

            )
        );
          
          if(checkApplicantInfo($order_id, $applicant_id) == FALSE)
          {
            $teletalkUrl = "http://armytbl.teletalk.com.bd:9999/rmy/rmytbl.php?".$application_info;

            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET remarks = '".$applicant_id."'  WHERE order_id='".$order_id."'");

            $ch  = curl_init();     
            curl_setopt($ch,CURLOPT_URL,$teletalkUrl);
            curl_setopt($ch,CURLOPT_POST, 0);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);     
            curl_close ($ch); 

            // Tracking in teletalk table
            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_teletalk_transactions SET txid='".$txid."', applicant_id='" . $applicant_id . "', user='" . $applicant_name . "', password='" . $applicant_pass . "', mobile='" . $mobile . "', name='" . $applicant_name . "', courseid='" . $course_id . "', amount='" . $_SESSION['ORDER_DETAILS']['txnAmount'] . "', start_time='".$start_time."', end_time='".$end_time."', info_response='".$response."'");

          }


      } 
      else 
      {

        // prompt no information found
        $_SESSION['teletalk_message'] = 'No Application information found!';
        header("Location: ".'/teletalk.php'); 

      }

    //  send sms for payment

    // verify payment
    if(isset($_POST['pin']) && !empty($_POST['pin']))
    {
       
        $application_info = http_build_query(
            array(
              'user_key'   => 'rmy',
              'pass_key'   => '1rmy1',            
              'id'         =>  $_SESSION['ORDER_DETAILS']['jbaApplicationId']
            )
        );
        $teletalkCheckUrl  = 'http://armytbl.teletalk.com.bd:9999/rmy/rmychk.php?'.$application_info;
        $ch  = curl_init();     
        curl_setopt($ch,CURLOPT_URL,$teletalkCheckUrl);
        curl_setopt($ch,CURLOPT_POST, 0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch); 
        $result = simplexml_load_string(trim($response));
        curl_close ($ch); 
        if(isset($response) && $result[0] != '0')
        {
          list($status, $paytime, $id, $pin, $courseid) = explode("|", $result[0]);
        }
        else{
          $_SESSION['teletalk_message'] = 'Payment not completed!';
	  header("Location: ".'/teletalk.php');	
        }
        

        if($status == 'PAID')
        {

            // update shurjoPay
            $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
            $_SESSION['order_details_response']['bankTxID'] = $pin;
            $_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
            $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];            
            $_SESSION['order_details_response']['spCode'] = '000';
            $_SESSION['order_details_response']['spCodeDes'] = 'Success';

            $bank_data = json_encode($response);
            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='000', gw_return_msg='".$response."', gw_time='".date('Y-m-d H:i:s')."', return_code='000', bank_status='SUCCESS', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");

            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".date('Y-m-d H:i:s')."', epay_status='000', epay_status_text='SUCCESS' WHERE tc_txid='".$order_id."'");

            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_teletalk_transactions SET response_data='".$bank_data."', transaction_time='".$paytime."' WHERE txid='".$txid."'");

            // send information to client
            header("Location: ".$db->local_return_url); 
            exit();
          
        }
        else
        {
            $_SESSION['teletalk_message'] = 'Payment is not successful.Please give correct Pin Number!';
            header("Location: ".'/teletalk.php'); 
        }
    }  

    // if cancel button pressed
    if(isset($_GET['cancel']) && $_GET['cancel'] == 'ok')
    {

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $_SESSION['order_details_response']['txID'] =  $_SESSION['ORDER_DETAILS']['uniqID'];
        $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
        $_SESSION['order_details_response']['bankTxID'] = isset($_REQUEST['trx_id'])?$_REQUEST['trx_id']:'';
        $_SESSION['order_details_response']['bankTxStatus'] = "CANCEL";
        $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

       
        $_SESSION['order_details_response']['spCode'] = '001';
        $_SESSION['order_details_response']['spCodeDes'] = 'Cancel';

        
        $bank_data = 'NULL';
        $sql_query1 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='001', gw_return_msg='001', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='001', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
        $sql_query2 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='001', epay_status_text='Fail' WHERE tc_txid='".$order_id."'");      

        header("Location: ".$db->local_return_url);
        exit;  
    }
    // response to JBA

    function checkApplicantInfo($order_id, $applicant_id) 
    {
        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * FROM sp_epay WHERE order_id='".$order_id."'");
        $result = mysqli_fetch_object($sql_query);        
        if(isset($result->remarks) && ($result->remarks == $applicant_id))
        {
          return TRUE;
        }
        else
        {
          return FALSE;
        }
        
    }

 ?>    

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
  
  <!------ Include the above in your HEAD tag ---------->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <title>Teletalk Online Payment</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-login">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-4  text-center">
                <!-- <a href="#" class="active" id="login-form-link"><img src="./img/shurjoPay.png" alt="shurjoPay" height="50"></a> -->
              </div>
              <div class="col-xs-4  text-center">
                <a href="#" id="register-form-link">
                  <img src="../../img/teletalk-sp.png" alt="shurjoPay" height="50">                  
                </a>
              </div>
              <div class="col-xs-4  text-center">
                
              </div>
            </div>
            <hr>
          </div>
          <?php 
            if(isset($_SESSION['teletalk_message']) &&  !empty($_SESSION['teletalk_message']))
            {
          ?>    
              <span style='color:#ff0000; text-align: center; float: left; width: 100%;'></span>                    
              <div class="alert alert-danger alert-dismissible text-upper" role="alert">
                <?php echo $_SESSION['teletalk_message'];?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>         
        <?php 
          } 
         	//$_SESSION['teletalk_message'] = '';
        ?>
          <div class="panel-body" style="background: #71BD44;">
            <div class="row">
              
              <div class="col-lg-12">
                <form method="POST" action="./teletalk.php">
                  <input type="hidden" name="cancel_payment" id="cancel_payment"  value="">
                  <div class="form-group text-center">                                        
                    <label class="form-check-label text-center" for="exampleCheck1">
                      <center>
                        <span style="font-size:27px;color:#fff;">&#2547; 
                          <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?>                        
                        </span> 
                      </center>
                    </label> 
                  </div>
                  <div class="form-group">                                        
                    <div class="p-3 mb-2 bg-danger text-white text-center" style="background-color: #fff;">
                      <h3 class="font-weight-bold">Teletalk Payment Options</h3>
                        Please follow the follwoing instruction to pay in teletalk

                        
                        <div  style="text-align: left;margin: 2px 0 0 20px;">
                        <h5 style="text-align: left;"> SMS to teletalk mobile</h5>
                        <p># Go to SMS option from any Teltalk mobile and write the  following Msg.</p>
                        <p style="width: 300px;background-color: #71bd44;"># Type ARMY< space >Application ID</p>
                        <p style="border-color: black;"><b># Your application ID is <b style="font-size: 20px;"><?php echo $_SESSION['ORDER_DETAILS']['jbaApplicationId'];?></b></b></p>
                        <p># Eamaple: <b>ARMY <?php echo $_SESSION['ORDER_DETAILS']['jbaApplicationId'];?></b></p>
                        <p># Send SMS to 16222</p>
                        <p># Teletalk will send you s SMS with a Pin No to confirmation that Taka <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?> will be charged from your mobile balance.</p>
                        <p># Again go to SMS option of your Teletalk mobile and write the following Msg.</p>
                        <p style="width: 300px;background-color: #71bd44;"># Type ARMY< space >YES < space > Pin No</p>
                        <p style="border-color: black;"># Example: <b>ARMY YES 987854</b></p>
                        <p># Send SMS to 16222</p>
                        <p># Now give your Pin Number and Press Submit button below.</p>
                        </div>
                        
                    </div>
                  </div>
                   
                  <div class="form-group">                    
                    <input type="text" name="pin" id="pin" tabindex="1" class="form-control" placeholder="Pin number" required>
                  </div>
                  <!--
                  <div class="form-group">
                    <input type="text" name="trx_id" id="trx_id" tabindex="2" class="form-control" placeholder="Transaction Id" required>
                  </div> -->
                  
                  <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6 col-sm-offset-3">
                          <input type="submit" name="number_send" id="number_send" value="Submit" tabindex="4" class="form-control btn">                            
                        </div>
                      <div class="col-sm-6 col-sm-offset-3">
                        <div class="text-center">
                          <a href="?cancel=ok" class="font-weight-bold" style="color:#fff;">Cancel</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="text-center">
                          <!-- <a href="javascript:;" data-toggle="modal" data-target="#exampleModal" tabindex="5" class="font-weight-bold" style="color:#fff;">How To Pay?</a> -->
                          <h6 class="text-white bg-dark">*Teletalk.</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



   
  </body>
</html>
