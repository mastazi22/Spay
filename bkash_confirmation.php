<?php
  include ("includes/configure.php");
  include ('includes/session_handler.php');
  //include ("includes/header_bkash.php");


  if(isset($_SESSION['ORDER_DETAILS']['userID']))
  {
    $sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
    $logo=mysqli_fetch_object($sql);
  }

  if ($_SESSION['order_details_response']['paymentOption'] != "bKash")
  {
    header("location: http://www.google.com/");
    exit();
  }


      if(isset($_POST['number_send']))
      {     
        $amount = $_SESSION['ORDER_DETAILS']['txnAmount'];
        $sender = substr($_REQUEST['mobile_sender'], 1);
        $trxid = $_REQUEST['trx_id'];
        $sql = "SELECT * FROM sp_epay WHERE  bank_tx_id = '".$trxid."' AND bank_status='SUCCESS' AND gw_return_id='000'";
        $query = mysqli_query($GLOBALS["___mysqli_sm"], $sql);
        $result_exist = mysqli_fetch_assoc($query);
        // free the memory
        mysqli_free_result($query);

        if($result_exist)
        {
            $_SESSION['bkash_message'] = 'The transaction id has already been used.';
            header("Location: ".'/bkash_confirmation.php'); 
            
        }
        else
        {
          $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
          $sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$trxid."' WHERE tc_txid='".$order_id."'");
          $sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$trxid."' WHERE order_id='".$order_id."'");
          $ch = curl_init();

          $url = 'https://www.bkashcluster.com:9081/dreamwave/merchant/trxcheck/sendmsg?user=SurjomukhiLimited&pass=november!quebec!sierra&msisdn=01845032741&trxid='.$trxid;
          // Set query data here with the URL
          curl_setopt($ch, CURLOPT_URL, $url); 
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_TIMEOUT, '300');
          $response = curl_exec($ch);
          $response= simplexml_load_string(trim($response));          
          curl_close($ch);           
          $date = new DateTime();
          $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
          if($response)
          {
            $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * FROM sp_epay WHERE order_id='".$order_id."'");
            $result = mysqli_fetch_object($sql_query);

            if($response->transaction->trxStatus=='0000' and $response->transaction->sender==$_REQUEST['mobile_sender'] and $response->transaction->amount==$_SESSION['ORDER_DETAILS']['txnAmount'] and $response->transaction->amount==$result->amount)
            {

              $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
              $_SESSION['order_details_response']['bankTxID'] = $trxid;
              $_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
              $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
              $_SESSION['PSS'] = true;

              $sql2_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='000'");
              $result2 = mysqli_fetch_object($sql2_query);

              $_SESSION['order_details_response']['spCode'] = $result2->return_code;
              $_SESSION['order_details_response']['spCodeDes'] = $result2->return_status?$result2->return_status:'Success';

              //$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";
              $bank_data = json_encode($response);
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='000', gw_return_msg='".$result2->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result2->return_code."', bank_status='SUCCESS', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result2->return_code."', epay_status_text='".$result2->return_status."' WHERE tc_txid='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

              header("Location: ".$db->local_return_url); exit();

            }
            elseif($response->transaction->trxStatus == '4001' )
            {
              $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
              $_SESSION['order_details_response']['bankTxID'] = $trxid;
              $_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
              $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='001'");
              $result = mysqli_fetch_object($sql_query);

              $_SESSION['order_details_response']['spCode'] = $result->return_code;
              $_SESSION['order_details_response']['spCodeDes'] = $result->return_status?$result->return_status:'Failed';

              //$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";
              $bank_data = json_encode($response);
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='001', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
             // echo "Trensaction id incorrect";    
              $_SESSION['bkash_message'] = 'Server is busy now!.Please Try After 5 minutes.';              
              header("Location: ".'/bkash_confirmation.php');              
            }
            else
            {
              $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
              $_SESSION['order_details_response']['bankTxID'] = $trxid;
              $_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
              $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='001'");
              $result = mysqli_fetch_object($sql_query);

              $_SESSION['order_details_response']['spCode'] = $result->return_code;
              $_SESSION['order_details_response']['spCodeDes'] = $result->return_status?$result->return_status:'Failed';

              //$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";
              $bank_data = json_encode($response);
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='001', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
              $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
             // echo "Trensaction id incorrect";    
              $_SESSION['bkash_message'] = 'Transaction ID Incorrect. Please Try Again. ';              
              header("Location: ".'/bkash_confirmation.php');              
            }
            //header("Location: ".$db->local_return_url);
              //exit;
          }
          else
          {
              //include ("bkash_xmlhttp.php");
              $_SESSION['bkash_message'] = 'Transaction ID Incorrect. Please Try Again. ';              
              header("Location: ".'/bkash_confirmation.php'); 
            ?> 
            <!--
            <script type="text/javascript">
              var timeinterval = 0.5*60;       
              var timecount=0;  
              setInterval("getsms(<?php /* echo $amount; ?>,<?php  echo  $sender; ?>,'<?php  echo $trxid; */ ?>')", 10000);
            </script>
            <div style="color:#ff0000;text-align: center; padding-top:10px;">Warning! Please do not press browser back or reload button</div>
            <div id="loading" style="text-align: center; padding-top:10px;">
              <img src="img/animation_processing.gif" align="middle"/>
            </div>-->
            <?php 
          }
        }
        ?> 
        <?php
      }
      else if(isset($_GET['cancel']) && $_GET['cancel'] == 'ok')
      {

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $_SESSION['order_details_response']['txID'] =  $_SESSION['ORDER_DETAILS']['uniqID'];
        $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
        $_SESSION['order_details_response']['bankTxID'] = isset($_REQUEST['trx_id'])?$_REQUEST['trx_id']:'';
        $_SESSION['order_details_response']['bankTxStatus'] = "CANCEL";
        $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='001'");
        $result = mysqli_fetch_object($sql_query);

        $_SESSION['order_details_response']['spCode'] = '001';
        $_SESSION['order_details_response']['spCodeDes'] = 'Cancel';

        
        $bank_data = 'NULL';
        $sql_query1 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='001', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
        $sql_query2 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
        $sql_query3 = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
        header("Location: ".$db->local_return_url); exit();
      }  
      else
      {
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
    <title>bKash Online Payment</title>
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
                <a href="#" id="register-form-link"><img src="./img/bkash-sp.jpg" alt="shurjoPay" height="50"></a>
              </div>
              <div class="col-xs-4  text-center">
                <!-- <a href="#" id="register-form-link"><img src="./img/merchant_logo/<?php echo $logo->merchant_logo; ?>" alt="<?php echo $logo->merchant_domain; ?>"></a> -->
              </div>
            </div>
            <hr>
          </div>
          <?php 
            if(isset($_SESSION['bkash_message']) &&  !empty($_SESSION['bkash_message']))
            {
          ?>    
              <span style='color:#ff0000; text-align: center; float: left; width: 100%;'></span>                    
              <div class="alert alert-danger alert-dismissible text-upper" role="alert">
                <?php echo $_SESSION['bkash_message']?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>         
        <?php 
          } 
          unset($_SESSION['bkash_message']);
        ?>
          <div class="panel-body" style="background: #AE1C7E;">
            <div class="row">
              
              <div class="col-lg-12">
                <form method="POST" action="./bkash_confirmation.php">
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
                    <div class="p-3 mb-2 bg-danger text-white text-center"><br/><h4 class="font-weight-bold">01845032741</h4>Please pay to this merchant number</div>
                  </div>
                  
                  <div class="form-group">                    
                    <input type="number" name="mobile_sender" id="mobile_sender" tabindex="1" class="form-control" placeholder="Sender number" required>
                  </div>
                  <div class="form-group">
                    <input type="text" name="trx_id" id="trx_id" tabindex="2" class="form-control" placeholder="Transaction Id" required>
                  </div>
                  
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
                          <a href="javascript:;" data-toggle="modal" data-target="#exampleModal" tabindex="5" class="font-weight-bold" style="color:#fff;">How To Pay?</a>
                          <h6 class="text-white bg-dark">*If you have received the transaction SMS from bKash but our site is showing Fail please do not panic. Sometimes it takes a bit longer to synchronize the servers. </br>Try after 5 minutes with same Transaction Id for the same Amount.</h6>
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


<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">bKash Payment Instruction</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <!-- <img src="bkashNotice.png" style="margin-left: 8%;" /> -->
        <img src="bkash_how.png" style="margin-left: 1%;width: 100%;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>
   
  </body>
</html>
<?php } ?>