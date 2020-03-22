<?php
include ("includes/session_handler.php");
include("includes/configure.php");
error_reporting(E_ALL);

if(count($_POST)==0)
{
    echo "<center><h3>This page requires parameters to access.</h3></center>";
    die();
}

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
	<title>Blocked by Site Admin</title>
    <link rel="stylesheet" type="text/css" href="css_2/main_idea.css">
    <style>
        @media (min-width: 300px) {
			.full_content { background-image: url("../images/bg.png"); background-repeat: repeat; border-radius: 10px;margin: 5% auto;padding: 4% 1% 1%;width: 90%;}
            .row img {display: block;margin: 10px auto;float: none;width: auto;}
        }
        @media (min-width: 700px) {
			.full_content { background-image: url("../images/bg.png");
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
            width: auto;
            float: inherit;

        }

        .paypoint img {
            max-width: 100%;
            width: auto;
            float: inherit;
        }
    </style>
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

    <div id="content">
        <h2 class="form-header" id="header_1" style="text-align: center; margin-top:20%;"><font color = "Blue">Please wait atleast 5 minutes for next transaction.</font></h2>
        <div class="info_msg" style="margin-top:20%;"> 
            Report issues to <a href=mailto:support@paypoint.com.bd>support@paypoint.com.bd</a> and attach <strong>screenshot</strong> of problem for resolution of submitted report.
        </div>
    </div>

        <div class="copyright">&copy;shurjoPay</div>
</div>


</body>
</html>

