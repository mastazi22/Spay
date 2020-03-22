<?php

  include ("includes/configure.php");
  include ('includes/session_handler.php');   
  include('bkash/configuration.php'); 

  $CRED = json_decode(CRED);

  if(isset($_SESSION['ORDER_DETAILS']['userID']))
  {
    $sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
    $logo=mysqli_fetch_object($sql);
  }

  $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
  $amount   = $_SESSION['ORDER_DETAILS']['txnAmount'];


  // Transaction already exists manupulation
  $sql = "SELECT * FROM sp_epay WHERE  order_id = '".$order_id."' AND bank_status != '' ";
  $query = mysqli_query($GLOBALS["___mysqli_sm"], $sql);
  $result_exist = mysqli_fetch_assoc($query);

  if(isset($result_exist))
  {
	  $_SESSION['bkash_message'] = 'The transaction id has already been used.';
	  // $_SESSION['order_details_response']['txID'] = $result_exist['txid'];    
	  // $_SESSION['order_details_response']['spCode'] = $result_exist['return_code'];
	  // $_SESSION['order_details_response']['spCodeDes'] = 'The transaction id has already been used.';
	  // $_SESSION['order_details_response']['bankTxID'] = $result_exist['bank_tx_id'];
	  // $_SESSION['order_details_response']['bankTxStatus'] = $result_exist['bank_status'];
	  // $_SESSION['order_details_response']['txnAmount'] =  $result_exist['amount'];
	  //header("Location: ".$db->local_return_url); exit();
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
    <style type="text/css">.rounded {  border-radius: 15px;  min-width: 200px;  font-size: 30px; background: #59bf90;   color:#fff;}
      .button { border: none; color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 17px;margin: 2px -25px; -webkit-transition-duration: 0.4s;cursor: pointer;}
      .button2 { background-color: white; color: black;}
      .button2:hover { background-color: #5859a9; color: white;}      
    </style>
      
      <!------ Include the above in your HEAD tag ---------->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <title>bKash Online Payment</title>
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
              <div class="panel-body" style="background: #1d9d54;">
                <div class="row">
                  
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

                                <button type="button" id="bKash_button" tabindex="4" class="rounded button button2" >Pay With bKash</button>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="row">                          
                            <div class="text-center">                          
                              <ul class="list-group text-left">
                                  <li class="list-group-item"><b>Step 1#</b> Click On <b>Pay With bKash</b></li>
                                  <li class="list-group-item"><b>Step 2#</b> Enter Your bKash <b>WALLET NUMBER</b></li>
                                  <li class="list-group-item"><b>Step 3#</b> Enter Your <b>OTP</b> sent to your mobile</li>
                                  <li class="list-group-item"><b>Step 4#</b> Enter Your <b>PIN</b> Number</li>                                  
                                  <li class="list-group-item"><b>Step 5#</b> Press <b>PROCEED</b> and Done! </li>  
                                  <li class="list-group-item">
                                    <center><img src="./img/bkash-logo.png" alt="bKash" title="bKash" height="50"></center>

                                  </li>  
                                </ul>
                            </div>                      
                        </div>
                      </div>   
                      <!-- <div class="form-group" style="background: #fff;">
                        <div class="row"><img src="./img/bkash-logo.png" alt="bKash" title="bKash" height="50"></div>
                      </div> -->    
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3"></div>    
        </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function () {
            //Token
            $.ajax({
                url: "bkash/token.php",
                type: 'POST',
                contentType: 'application/json',
                success: function (data) {
                //console.log('got data from token  ..');            
            }
        });
        
            var paymentConfig= {
            createCheckoutURL: "bkash/createpayment.php",
            executeCheckoutURL: "bkash/executepayment.php",
            };
            var paymentRequest;
            paymentRequest = { amount: '<?php echo $amount;?>', invoice: '<?php echo $order_id;?>' };
            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: paymentRequest,
                createRequest: function (request) {
                    //console.log('=> createRequest (request) :: ');
                    //console.log(request);
                    //console.log(paymentConfig.createCheckoutURL+"?amount="+paymentRequest.amount);
                    $.ajax({
                        url: paymentConfig.createCheckoutURL+"?amount="+paymentRequest.amount,
                        type: 'GET',
                        contentType: 'application/json',
                        beforeSend: function() {
                          $("#loading").show();
                          //console.log("Loading....");
                        },
                        success: function (data) {
                            $("#loading").hide();
                            //console.log('got data from create  ..');
                            //console.log('data ::=>');
                            //console.log(JSON.parse(data).paymentID);
                            data = JSON.parse(data);
                            if (data && data.paymentID != null) {
                                paymentID = data.paymentID;
                                bKash.create().onSuccess(data);      
                            } else {                                
                                bKash.create().onError();
                            }
                        },
                        error: function () {
                            bKash.create().onError();
                        }
                    });
                },
                executeRequestOnAuthorization: function () {
                    //console.log('=> executeRequestOnAuthorization');
                    $.ajax({
                        url: paymentConfig.executeCheckoutURL+"?paymentID="+paymentID,
                        type: 'GET',
                        contentType: 'application/json',
                        success: function (data) {
                            //console.log('got data from execute  ..');
                            //console.log('data ::=>');
                            //console.log(JSON.stringify(data));
                            data = JSON.parse(data);
                            if (data && data.paymentID != null) {
                                window.location.href = "bkash/success.php";//your success page
                            } else {                              
                                //alert(data.errorMessage);                                                                
                                bKash.execute().onError();
                                window.location.href = "bkash/success.php";//your success page

                            }
                        },
                        error: function () {
                            bKash.execute().onError();
                        }
                    });
                }
            });
      
      
       $('input[type=radio][name=paymentType]').change(function () {
                if (this.value == 'immediate') {

                    bKash.reconfigure({
                        paymentRequest: { amount: $('#amount').html(), intent: 'sale' }
                    });


                } else if (this.value == 'authNcapture') {

                    bKash.reconfigure({
                        paymentRequest: { amount: $('#amount').html(), intent: 'authorization' }
                    });


                }
            });
      
        });
</script>
  </body>
  </html>