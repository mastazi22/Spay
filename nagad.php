<?php

  include ("includes/configure.php");
  include ('includes/session_handler.php');  

    // send data
      $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
      $txid = $_SESSION['ORDER_DETAILS']['uniqID'];

    // verify payment
    if(isset($_POST['ok']) && !empty($_POST['ok']))
    {
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $_SESSION['order_details_response']['txID'] =  $_SESSION['ORDER_DETAILS']['uniqID'];
        $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
        $_SESSION['order_details_response']['bankTxID'] = isset($_REQUEST['trx_id'])?$_REQUEST['trx_id']:'';
        $_SESSION['order_details_response']['bankTxStatus'] = "INITIALIZE";
        $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

       
        $_SESSION['order_details_response']['spCode'] = '002';
        $_SESSION['order_details_response']['spCodeDes'] = 'Initialize';

        
        $bank_data = 'NULL';
        $sql_query1 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='002', gw_return_msg='002', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='002', bank_status='INITIALIZE', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
        $sql_query2 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='002', epay_status_text='Initialize' WHERE tc_txid='".$order_id."'");   

        // send information to client
        header("Location: ".$db->local_return_url); 
        exit();
      
       
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



 ?>    
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/prism.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
   
    <!-- <script src="https://scripts.sandbox.bka.sh/versions/1.0.0-beta/checkout/bKash-checkout-sandbox.js"></script> -->
    <script src="<?=$CRED->script_link?>"></script> 

    

    <style>body { padding-top: 20px; } .container { text-align: center;} .container pre { max-height: 30em; overflow: auto; } button { width: 10em; }</style>
    <style type="text/css">.rounded {  border-radius: 15px;  min-width: 200px;  font-size: 30px; background: #ed1c23;   color:#fff;}
      .button { border: none; color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 17px;margin: 2px -25px; -webkit-transition-duration: 0.4s;cursor: pointer;}
      .button2 { background-color: white; color: black;}
      .button2:hover { background-color: #5859a9; color: white;}
      .example_button { border:1px solid green;padding: 2px 15px;color:#000;background-color: #fff;border-radius: 50px;}
      .modal-dialog { width: 900px; background-color: none;}     
      a {
        color:#000; text-decoration: none; 
      }
      a:hover {
        color:#ed1c23; text-decoration: none;
      } 
    </style>
      
      <!------ Include the above in your HEAD tag ---------->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <title>Nagad Online Payment</title>
    </head>
    <body class="text-center">
    <div class="container">
      <div class="row">
            <div class="col-md-4"></div>    
            <div class="col-md-4">
            <div class="panel panel-login">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-4  text-center">
                    <!-- <a href="#" class="active" id="login-form-link"><img src="./img/shurjoPay.png" alt="shurjoPay" height="50"></a> -->
                  </div>
                  <div class="col-xs-4  text-center">
                    <img src="./img/shurjoPay.png" alt="shurjoPay" height="50">
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
              <div class="panel-body" style="background: #f7941d;">
                <div class="row">
                  <form method="POST" action="./nagad.php">
                  <div class="col-lg-12">                
                      <div class="form-group text-center">                                        
                        <label class="form-check-label text-center" for="exampleCheck1">
                            <p class="rounded">&#2547;<?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?></p>                      
                        </label> 
                      </div>
                      <div class="alert alert-danger" id="error-display" style="display: none;">
                        <strong>Danger!</strong> Indicates a dangerous or potentially negative action.
                      </div>
                      <div class="form-group">
                        <div class="row">
                          <div class="col-sm-6 col-sm-offset-3">
                            <input style="display: none" type="radio" name="paymentType" value="immediate" checked>
                                <div id="loading" style="display: none">Loading....</div>
                                <p style="font-weight:bold;font-size:25px;color:#fff;">01844&nbsp;219&nbsp;387</p>  
                                <img src="./img/nogo-sm-qr.png" width="120">                                
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="row">                          
                            <div class="text-center">                          
                              <ul class="list-group text-left">
                                  <li class="list-group-item">
                                    <a href="javascript:;" data-toggle="modal" 
                                        data-target="#appModal" tabindex="5" 
                                        class="font-weight-bold" ># How To Pay from APP ?</a>
                                  </li>
                                   <li class="list-group-item">
                                    <a href="javascript:;" data-toggle="modal" 
                                        data-target="#ussdModal" tabindex="5" 
                                        class="font-weight-bold" ># How To Pay from USSD ?</a>
                                  </li>
                                  <li class="list-group-item">
                                    <center>
                                      <img src="./img/nagad-logo.png" width="150">
                                    </center>

                                  </li>  
                                </ul>
                            </div>                      
                        </div>
                                <div class="form-group">
                                <div class="row">
                                  <div class="col-sm-6 col-sm-offset-3">
                                      <input type="submit" name="ok" id="ok" value="Submit" tabindex="4" class="form-control btn">                            
                                    </div>
                                  <div class="col-sm-6 col-sm-offset-3">
                                    <div class="text-center">
                                      <a href="?cancel=ok" class="font-weight-bold" style="color:#fff;">Cancel</a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                      </div>   
                      <!-- <div class="form-group" style="background: #fff;">
                        <div class="row"><img src="./img/bkash-logo.png" alt="bKash" title="bKash" height="50"></div>
                      </div> -->    
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3"></div>    
        </div>
  </div>


  
<!-- APP Modal -->
<div class="modal fade" id="appModal" tabindex="-1" role="dialog" aria-labelledby="appModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">        
        <img src="./img/nagad_how_to/guide_app.jpg" style="margin-left: 1%;width: 100%;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>

<!-- USSD Modal -->
<div class="modal fade" id="ussdModal" tabindex="-1" role="dialog" aria-labelledby="ussdModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h5 class="modal-title" id="ussdModalLabel">Nagad Payment Instruction using USSD</h5>        
      </div> -->
      <div class="modal-body">        
        <img src="./img/nagad_how_to/guide_USSD_Online.jpg" style="margin-left: 1%;width: 90%;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>

  </body>
  </html>



