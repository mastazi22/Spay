<?php
if(count($_POST)==0)
{
	echo "<center><h3>This page requires parameters to access.</h3></center>";
	die();
}
	include("includes/configure.php");
	include ("includes/session_handler.php");
	$_SESSION['ORDER_DETAILS']=$_POST;
	$gpspl = substr(($_SESSION['ORDER_DETAILS']['uniqID']),0,10);
	$display_none = '';
	//include("includes/header.php");
	include("payment_engine/paymentEngine.php");
      	$hasLogo = false;
	if(isset($_SESSION['ORDER_DETAILS']['userID'])){
		$sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
//echo "select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'";
		
		if(mysqli_num_rows($sql)>0) 
		{
			$logo=mysqli_fetch_object($sql);
			$hasLogo = true;
		}
	}
	
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css_2/main_idea.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script>
        function selectMethod(method) {
            // Disabaled
            //console.log ("option " + method);
			//$('#paymentOption').val(method);
            //$('#frmMethod').submit();
            alert("Disabaled due to Maintenance!");
        }
    </script>
    <style>
        @media (min-width: 300px) {
            .full_content {
                background-image: url("../images/bg.png");
                background-repeat: repeat;
                border-radius: 10px;
                margin: 5% auto;
                padding: 4% 1% 1%;
                width: 90%;
            }
            .row img {
                display: block;
                margin: 10px auto;
                float: none;
                width: auto;
            }
        }
        @media (min-width: 700px) {
            .full_content {
                background-image: url("../images/bg.png");
                background-repeat: repeat;
                border-radius: 10px;
                margin: 5% auto;
                padding: 4% 1% 1%;
                width: 58%;
            }
            .row img {
                float: left;
                margin: 0 1%;
                width: 22.10%;
            }
        }
        .paypoint {
            float: left;
            margin-bottom: 40px;
            width: 50%;
            box-sizing: border-box;
            padding: 0 10px;
        }
        .surjopay {
            float: right;
            left: 4.5%;
            width: 50%;
            position: relative;
            top: 0px !important;
            box-sizing: border-box;
            padding: 15px 10px;
        }
        .surjopay img {
            max-width: 100%;
            width: 100px;
            float: inherit;
        }
        .paypoint img {
            max-width: 100%;
            width: auto;
            float: inherit;
        }
    </style>
	<title>ShurjoPay</title>
</head>
<body>
<div class="full_content">
    <div class="up_content">
        <div class="paypoint">
            <img src="images/shurjopay-logo.png">
        </div>
        <div class="surjopay">
            <?php
            if ($hasLogo):
                ?>
                <img src="./img/merchant_logo/<?php echo $logo->merchant_logo; ?>"/>
                <?php
            else:
                ?>
                <img src="images/payPoint_logo.png"/>
                <?php
            endif;
            ?>
        </div>
    </div>
    <h3 style="width: 100%;   float: left;padding: 4px;text-align: justify;color: red;
    font-size: 23px;">Please be inform that, due to Network and Data Center maintenance/up-gradation works, all transaction including Credit/Debit Card, ATM, POS, iBanking, eCommerce, etc. will be unavailable from <b>Dec 13, 2019 at 02:30 AM to Dec 14, 2019 at 09:00 AM (30:30 hours)</b>. Sorry for the inconvenience.</h3>
    <form action="./payment_process.php" method="POST" id="frmMethod">
        <input type="hidden" name="paymentOption" id="paymentOption"/>
        <div class="main_content">
            <div class="row">
              <b>  Total Amount :: <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?> BDT </b>
            </div>            
            <fieldset class=row>
                <legend class="title">Mobile Wallets</legend>                
				<a href="javascript:void(0)" name="TBMM" id="TBMM" onclick="selectMethod('stbl')">
					<div class="image1" style="display:block;">
						<!--<img src="images/trust-bank-mobile-banking.png">-->
						<img src="images/logo_tbmm_172x73.png">
					</div>
				</a>                 
		<div style="margin: 70px 0 0 20px;font-weight: bold;">* For mobile payment You will be charged extra 10 Taka for online service charge.</div>
            </fieldset>
			<fieldset class=row>
				<legend class="title">Trust Bank Cards</legend>
			<!--<div style="margin: 0 0 6px 20px;font-weight: bold;color:red;">* Payment with Card Will be available By 11<sup>th</sup> January. Sorry for invovenience.</div>-->
					<a href="javascript:void(0)"  name="TBL" id="TBL"  onclick="selectMethod('stbl')">
						<div class="image1"><img src="images/trust-bank.png"></div>
					</a>	
			<div style="margin: 70px 0 0 20px;font-weight: bold;">* You can pay by any Q-Cash Member Banks.</div>
			 <div style="margin: 4px 0 0 20px;font-weight: bold;">* For using Card , You will be charged extra 15 Taka for online service charge.</div>

			</fieldset>                 
        </div>
        <div class="copyright">&copy;shurjoPay</div>
</div>
</form>
</body>
</html>

