<?php
    include("includes/session_handler.php");
    include("includes/configure.php");
    include("includes/login.php");

    $login = new Login();

    $dataPOST = $_POST['spdata'];
    $smUid = '';


    if (isset($_POST['smUid']) && $_POST['smUid'] != '') {
        $smUid = base64_decode($_POST['smUid']);
        $smUid = mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid);        
        //$block_status = $login->chkBlocking($smUid, $db->con_sp);
        $block_status =  $login->chkBlockingFromApi($smUid);             
        $countTxStatus = $login->countTxBlocking($smUid, $GLOBALS["___mysqli_sm"]);
        
        if ($block_status->isAccountClosed != 'No' or $countTxStatus == 'Deny') {            
            switch ($_SERVER["SERVER_NAME"]) {
                // Testing site (dev.shurjomukhi.com) configuration
                case 'dev.shurjomukhi.com':
                    header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/block.php");
                    break;
                // Development site (e.g. localhost) configuration
                case 'localhost':
                    header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/block.php");
                    break;
                // Live site configuration
                default:
                    //header("Location: https://shurjopay.com/block.php?ref=firstblock" . $block_status->isAccountClosed . $countTxStatus);
                    header("Location: https://shurjopay.com/block.php?ref=firstblock" . $block_status->isAccountClosed . $countTxStatus);
                    break;
            }

            exit;
        }

    }
 

    if (isset($dataPOST)) {    
        include("xml-parser.php");        
        $xml2arr = xml2array($dataPOST);
        $_SESSION['ORDER_DETAILS']['merchantName'] = $merchantName = !is_array($xml2arr['shurjoPay']['merchantName']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['merchantName']) : "";
        $_SESSION['ORDER_DETAILS']['merchantPass'] = $merchantPass = !is_array($xml2arr['shurjoPay']['merchantPass']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['merchantPass']) : "";
        $_SESSION['ORDER_DETAILS']['returnURL'] = $returnURL = !is_array($xml2arr['shurjoPay']['returnURL']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['returnURL']) : "";
        $_SESSION['ORDER_DETAILS']['userIP'] = $userIP = !is_array($xml2arr['shurjoPay']['userIP']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['userIP']) : "";
        $_SESSION['ORDER_DETAILS']['uniqID'] = $uniqid = !is_array($xml2arr['shurjoPay']['uniqID']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['uniqID']) : "";
        $_SESSION['ORDER_DETAILS']['txnAmount'] = $amount = !is_array($xml2arr['shurjoPay']['totalAmount']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['totalAmount']) : "";
        $_SESSION['ORDER_DETAILS']['paymentOption'] = $payment_option = !is_array($xml2arr['shurjoPay']['paymentOption']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['paymentOption']) : "";

        $_SESSION['ORDER_DETAILS']['bank'] = isset($xml2arr['shurjoPay']['bank']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['bank']) : "";	

        $_SESSION['ORDER_DETAILS']['school'] = isset($xml2arr['shurjoPay']['school']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['school']) : "";	
        $_SESSION['ORDER_DETAILS']['studentID'] = isset($xml2arr['shurjoPay']['studentID']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['studentID']) : "";	
    	
		// For teletalk integration
    	$_SESSION['ORDER_DETAILS']['otherOption'] = !is_array($xml2arr['shurjoPay']['otherOption']) ? mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$xml2arr['shurjoPay']['otherOption']) : "";


        $_SESSION['ORDER_DETAILS']['order_id'] = $order_id = uniqid("SP");
        if (!(strpos($dataPOST, '<returnURL>') === false && strpos($dataPOST, '</returnURL>') === false)) {
            $_SESSION['ORDER_DETAILS']['returnURL'] = $returnURL = substr($dataPOST, (strpos($dataPOST, '<returnURL>') + 11), (strpos($dataPOST, '</returnURL>') - (strpos($dataPOST, '<returnURL>') + 11)));
        }
        
        if ($_SERVER["SERVER_NAME"] == "dev.shurjomukhi.com") 
    	{
            //$_SESSION['ORDER_DETAILS']['txnAmount'] = $amount = 1;
        }
    	//if ( substr($uniqid,0,7) == 'PPT1016') {
    		//header ("Location: https://shurjopay.com/block.php");
    	//}
        $err_msg = $login->inputValidation($merchantName, $merchantPass, $userIP, $uniqid, $amount, $payment_option, $returnURL, $db->con_sp);
        if (strlen($err_msg) == 0) 
    	{
    	    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_merchants WHERE username='" . $merchantName . "' and password='" . md5($merchantPass) . "' and isactive='yes'");
    		$result = mysqli_fetch_object($sql_query);
    	    $host = parse_url(isset($_SERVER['HTTP_REFERER']));
            if ($merchantName == 'paypoint' || $merchantName == 'shurjorajjo' || $merchantName == 'spaytest') 
    		{
                //$ip = "210.4.73.118";
                $ip = "139.59.3.145";
    		} elseif ($merchantName=='ansaervdp')
    		{
    			$ip="103.48.16.225";
                   	
    		} 
            elseif ($merchantName=='joinbd')
    		{
    			$ip = '103.46.148.200';
    		} 
            elseif ($merchantName=='wasapay' || $merchantName== 'wasamis')
            {
                $ip = '103.108.144.133';
            } 
            elseif ($merchantName=='robicash')
            {
               // $ip = '202.134.12.26';
                $ip = '10.101.5.131';
                
            }
	        elseif($merchantName == 'bazars')
	        {
		      $ip = '198.54.125.200';
            } 
            elseif ($merchantName == 'dgalpha') 
            {
              $ip = '198.54.114.111';
            }
            elseif($merchantName == 'dsebd')
            {
                $ip = '202.84.39.37';
            }
            elseif($merchantName == 'mulamuli')
            {
                $ip = '212.1.211.78';
            }
            else 
    		{
                //$ip = $_SERVER['REMOTE_ADDR'];
                $ip = gethostbyname($host['host']);
                if (strlen($ip) == 0) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                //$datafile=array('username'=>$_SESSION['ORDER_DETAILS']['merchantName'],'ip'=>$ip);
                //file_put_contents('filename.txt', $datafile);
    		}
    			//file_put_contents('test.txt', $ip);
    			
    			$mer_err_msg = $login->merchantVerification($result, $uniqid, $ip);
    			//	$mer_err_msg = '';
    		if (trim($mer_err_msg) == "") 
    		{
                $_SESSION['ORDER_DETAILS']['userID'] = $result->id;
                $_SESSION['ORDER_DETAILS']['USER_VISIT_TIMES'] = 1;

                if ($result->id == "2") 
    			{
                    $sql_query_sub_marchent = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_sub_merchants_for_paypoint WHERE sm_marchent_id='" . $result->id . "' and isactive='yes' and paypoint_return_url_string='" . $returnURL . "' or paypoint_return_url_string_mobile='" . $returnURL . "'");
                    $result_sub_marchent = mysqli_fetch_object($sql_query_sub_marchent);
                    $_SESSION['ORDER_DETAILS']['mxID'] = $result_sub_marchent->mx_merchant_id;
                    $_SESSION['ORDER_DETAILS']['dbblID'] = $result_sub_marchent->dbbl_merchant_id;
                    $_SESSION['ORDER_DETAILS']['eblID'] = $result_sub_marchent->ebl_merchant_id;
                    $_SESSION['ORDER_DETAILS']['dbblTerminalID'] = $result_sub_marchent->dbbl_terminal_id;
                    $_SESSION['ORDER_DETAILS']['eblPassword'] = $result_sub_marchent->ebl_password;
                    $_SESSION['ORDER_DETAILS']['MerchantUserName'] = $result_sub_marchent->dbbl_sub_merchant_name;//"payPoint";
                    $_SESSION['ORDER_DETAILS']['bkashJson'] = $result_sub_marchent->bkash_credentials;//"payPoint";
                } else 
    			{
                    $_SESSION['ORDER_DETAILS']['mxID'] = $result->mx_merchant_id;
                    $_SESSION['ORDER_DETAILS']['dbblID'] = $result->dbbl_merchant_id;
                    $_SESSION['ORDER_DETAILS']['eblID'] = $result->ebl_merchant_id;
                    $_SESSION['ORDER_DETAILS']['dbblTerminalID'] = $result->dbbl_terminal_id;
                    $_SESSION['ORDER_DETAILS']['eblPassword'] =$result->ebl_password;
                    $_SESSION['ORDER_DETAILS']['MerchantUserName'] = $result->dbbl_sub_merchant_name;//$result->username;
                    $_SESSION['ORDER_DETAILS']['bkashJson'] = $result->bkash_credentials;//"payPoint";

                }
            
                if ($result) 
    			{
                    $date = new DateTime();
                    $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
                    $current_time = $date->format("Y-m-d H:i:s");

                    $sql_isdistrict = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_epay WHERE order_id='" . $order_id . "' OR txid='" . $uniqid . "'");
                    $isdistrict = mysqli_fetch_object($sql_isdistrict);
                    if (!$isdistrict) 
    				{
                        $data = array('id' => 'id already exist');
                        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT into sp_epay SET uid='" . $result->id . "', order_id='" . $order_id . "', txid='" . $uniqid . "', amount='" . $amount . "', clientip='" . $userIP . "', returnurl='" . $returnURL . "', intime='" . $current_time . "'");
                        // Robi Trigger set //start
                        if(strstr($uniqid,'RCT'))                        
                        {                            
                          mysqli_query($GLOBALS["___mysqli_sm"], "INSERT into robi_sp_payments SET tc_txid = '".$uniqid."', epay_order_id = '".$order_id."', amount= '".$amount."',returnurl='" . $returnURL . "', epay_res_time='".$date->format('Y-m-d H:i:s')."'"); 
                        }
                        // Robi Trigger set //end
                        $_SESSION['ORDER_DETAILS']['current_insert_id'] = mysqli_insert_id($GLOBALS["___mysqli_sm"]);
                    } else 
    				{
                        $data = array('id' => 'id does not exist');

                        header("Location: https://shurjopay.com/block.php?ref=firstblock" . $block_status->isAccountClosed . $countTxStatus);
                        die;
                    }

                    if ($smUid != '') 
    				{
                        //$resultRid = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT user_id FROM paypoint.paypoint_users_orders WHERE spay_id = '" . $_SESSION['ORDER_DETAILS']['uniqID'] . "'");
                        //$smRidObj = mysqli_fetch_object($resultRid);                        
                        $smRidObj = $login->getPaypointUserOrders( $_SESSION['ORDER_DETAILS']['uniqID'] );
                        //var_dump($smRidObj);exit();

                        $smRid = $smRidObj->user_id;

                        if ($smUid == $smRid) 
    					{

                            //start order in progress condition.
                            $_SESSION['ORDER_DETAILS']['smUid'] = $smRid;
                            // $order_in_progress=orderInProgress($smUid,$_SESSION['ORDER_DETAILS']['uniqID']);
                            $order_in_progress = false; // order in progress disabled value always false.

                            if ($order_in_progress) 
    						{
                                switch ($_SERVER["SERVER_NAME"]) 
    							{
                                    // Testing site (dev.shurjomukhi.com) configuration
                                    case 'dev.shurjomukhi.com':
                                        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/halt.php");
                                        break;
                                    // Development site (e.g. localhost) configuration
                                    case 'localhost':
                                        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/halt.php");
                                        break;
                                    // Live site configuration
                                    default:
                                        header("Location: https://shurjopay.com/halt.php");
                                        break;
                                }

                                exit;
                            }
                            //end order in progress condition.

                            $smUid_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET smUid='" . $smUid . "' WHERE  txid='" . $uniqid . "'");
                        } else 
    					{
                            //echo $smRid;exit;
                            switch ($_SERVER["SERVER_NAME"]) 
    						{
                                // Testing site (dev.shurjomukhi.com) configuration
                                case 'dev.shurjomukhi.com':
                                    header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/block.php");
                                    break;
                                // Development site (e.g. localhost) configuration
                                case 'localhost':
                                    header("Location: http://" . $_SERVER["SERVER_NAME"] . "/paypoint/block.php");
                                    break;
                                // Live site configuration
                                default:
                                    header("Location: https://shurjopay.com/block.php");
                                    break;
                            }
                            exit;
                        }
                    }
                    ?>
    				<?php
    					// Option page redirection url 
    					
                        //echo $_SERVER["SERVER_NAME"];

                        switch ($_SERVER["SERVER_NAME"]) 
    					{
    						// Development site (e.g. localhost) configuration
    						case 'localhost':
    							$payment_option_url = "http://localhost/shurjopay/payment_option.php";
    							$school_payment_option_url = "http://localhost/shurjopay/school_payment_option.php";
                                $jbd_payment_option_url = "http://localhost/shurjopay/jbd_payment_option.php";
    							break;
    						// Live site configuration
    						default:
    							$payment_option_url = "https://shurjopay.com/payment_option.php";
    							$school_payment_option_url = "https://shurjopay.com/school_payment_option.php";
                                $jbd_payment_option_url = "https://shurjopay.com/jbd_payment_option.php";
    							break;
    					}

                        
    				
                    ?>
    				<?php if(isset($_SESSION['ORDER_DETAILS']['bank']) && ( $_SESSION['ORDER_DETAILS']['bank'] =='trust' || $_SESSION['ORDER_DETAILS']['bank'] =='shurjopay')):?>
    					<form method="post" action="<?php echo $jbd_payment_option_url;?>#TBL" id="frm_submit">
    				<?php elseif(isset($_SESSION['ORDER_DETAILS']['school']) && $_SESSION['ORDER_DETAILS']['school'] =='bisc' ):?>
    					<form method="post" action="<?php echo $school_payment_option_url;?>" id="frm_submit">
    				<?php else:?>					
    					<form method="post" action="<?php echo $payment_option_url;?>" id="frm_submit">
    				<?php endif;?>
    				
    				<?php foreach ($_SESSION['ORDER_DETAILS'] as $key => $value) { ?>
    					<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
    				<?php } ?>
    						<input type="hidden" name="unique_id_code" value="<?php echo $result->unique_id_code; ?>">
    					</form>
    					<script>
    						document.getElementById('frm_submit').submit();
    					</script>
    					<?php
    					exit;
                } else 
    			{
                    $sql_log = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_merchants WHERE username='" . $merchantName . "' and password='" . md5($merchantPass) . "'");
                    $login_err = mysqli_fetch_object($sql_log);

                    if (!$login_err) 
    				{
                        $sql_errcode = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE id=199");
                        $err_code = mysql_fetch_object($sql_errcode);
                        $post_data = '<?xml version="1.0" encoding="utf-8"?><SP><Response><errCode>' . $err_code->return_code . '</errCode><errMsg>' . $err_code->return_status . '</errMsg></Response></SP>';
                    } else 
    				{
                        $sql_errcode = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE id=200");
                        $err_code = mysqli_fetch_object($sql_errcode);
                        $post_data = '<?xml version="1.0" encoding="utf-8"?><SP><Response><errCode>' . $err_code->return_code . '</errCode><errMsg>' . $err_code->return_status . '</errMsg></Response></SP>';
                    }
                    submitSPForm($post_data, $returnURL);
                }
            } else 
    		{
                showErrorMsg($mer_err_msg);
            }
        } else 
    	{
            showErrorMsg($err_msg);
        }
    } else 
    {
        include("includes/header.php");
        echo "<div style='color:#ff0000;text-align:center;font-size:25px;font-weight:bold;'>You are not allowed to access this page!!!!!!!</div>";
        include("includes/footer.php");
    }

    function showErrorMsg($err_msg)
    {
        include("includes/header.php");
        echo "<div id='main_container'>
            <fieldset id='trnsdetails'>
                <legend>Error Message</legend>
                <div style='color:#ff0000;padding: 10px;'>{$err_msg}</div>
            </fieldset>
        </div>";
        include("includes/footer.php");
    }

    function submitSPForm($post_data, $returnURL)
    {
        echo "<form method='post' action='{$returnURL}' id='frm_submit'>
            <input type='hidden' name='spdata' value='{$post_data}' />
          </form>
       <script>document.getElementById('frm_submit').submit();</script>";
    }

    function orderInProgress($user_id, $spay_id)
    {
        global $db;
        $db->getConnection();
        $query = "select time_added,try from user_lock where user_id='" . $user_id . "'";
        $res = mysqli_query($GLOBALS["___mysqli_sm"],$query);
        $result = mysqli_fetch_object($res);
        $current_time = new DateTime('now');
        $time_added = new DateTime($result->time_added);
        $current_time->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $time_added->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $interval = strtotime($current_time->format('Y-m-d H:i:s')) - strtotime($time_added->format('Y-m-d H:i:s'));
        $mins = ceil($interval / 60);


        $count = mysql_num_rows($res);

        if ($count > 0 AND $mins < 5000) // order in progress disabled : user_lock table
        {
            $q = "UPDATE user_lock SET time_added = Now(),try='" . ($result->try + 1) . "' where user_id = '" . $user_id . "'";
            $res = mysqli_query($GLOBALS["___mysqli_sm"],$q);
            return true;
        } else if ($mins > 5) {
            $comment = "1";
            $q = "UPDATE user_lock SET time_added = Now() where user_id = '" . $user_id . "'";
            $res = mysqli_query($GLOBALS["___mysqli_sm"],$q);
            return false;
        } else {
            $comment = "1";
            $q = "insert into user_lock (user_id,spay_id,comment) values('" . $user_id . "','" . $spay_id . "','" . $comment . "')";

            $res = mysqli_query($GLOBALS["___mysqli_sm"],$q);

            if ($res) {
                return false;
            } else {
                $q = "UPDATE user_lock SET time_added = Now(),try='" . ($result->try + 1) . "' where user_id = '" . $user_id . "'";
                $res = mysqli_query($GLOBALS["___mysqli_sm"],$q);
                return true;
            }

        }
    }

?>
