<?php

/*
##### Author : A.K.M. Nazmul Hasan
##### Email : nazmul.hasan@shurjomukhi.com.bd

*/
session_start();
$err_amount = "";
if ($_SESSION['ORDER_DETAILS']['loggedIN'] != true) {
	header('Location: login.php');
}
require_once '../includes/configure.php';

$mx_commission = 3.5;
$other_commission = 2;

function get_all_history() {
	$retArr = array ();
	$userid = $_SESSION['ORDER_DETAILS']['userID'];
	$Ucode = $_SESSION['ORDER_DETAILS']['Ucode'];
	//GET total sold amount means all Success transaction of particular merchant
	$q = "select sum(amount) as total_sold from sp_epay where uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS'";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$q));
	$retArr['total_sold'] = $res['total_sold'];

	//GET total Pending Request means all Pending Withdraw request of particular merchant
	$q2 = "select sum(amount) as total_pending from sp_withdrawal_history where user_id = '{$userid}' and status='Pending'";
	$res2 = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$q2));
	$retArr['total_pending'] = $res2['total_pending'];
	//GET total withdrawal amount means all Requests those are accepted of particular merchant
	$q3 = "select sum(amount) as total_accepted from sp_withdrawal_history where user_id = '{$userid}' and status='Accepted'";
	$res3 = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$q3));
	$retArr['total_accepted'] = $res3['total_accepted'];
	
	$q4 = "select commission from sp_merchants where id = '{$userid}' and unique_id_code ='{$Ucode}' LIMIT 0,1";
	$res4 = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$q4));
	
	//---------------New Commission Calculation -----------	
	$mx_commission = 3.5;
	$other_commission = 2;
	// Collect all Bank transaction
	$mxq = "select sum(amount) as mx_total_sold from sp_epay where method LIKE 'mx' AND  uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS'";
	$mxres = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$mxq));
	$retArr['mx_total_sold'] = $mxres['mx_total_sold'];
	// Collect all other transaction
	$nmxq = "select sum(amount) as nmx_total_sold from sp_epay where method NOT LIKE 'mx' AND  uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS'";
	$nmxres = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$nmxq));
	$retArr['nmx_total_sold'] = $nmxres['nmx_total_sold'];
	// Calculate commission for MX transaction
	
	$retArr['mx_total_commission'] = ($mx_commission * $retArr['mx_total_sold'])/100;
	// Calculate commission for other transaction
	$retArr['nmx_total_commission'] = ($other_commission * $retArr['nmx_total_sold'])/100;
	//-------------------------------------	
	
	//$retArr['commission_amount'] = $res4['commission'];
	//$retArr['total_commission'] = ($res4['commission']*$retArr['total_sold'])/100;
	
	// New total and total commission
	$retArr['total_commission'] = $retArr['mx_total_commission'] + $retArr['nmx_total_commission'];	
	// New total and total commission
	
	$retArr['avail_total'] = $retArr['total_sold'] - ($retArr['total_accepted'] + $retArr['total_pending'] + $retArr['total_commission']);

	return $retArr;
}
function sendEmailConfirmation($email, $username, $amt, $prefer_method) {
	$sub = "shurjoPay Withdrawal Request";
	$frm = "info@shurjomukhi.com.bd";
	$to = $email;
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
	$msg_body = '<html><body>
				<table>				 
				  <tr><td colspan=2>Dear Concern,<br/><br/> One of the shurjoPay client has requested for below withdrawal:
					 <br/></td></tr>
				  <tr><td colspan=2>&nbsp;</td></tr>
				  <tr><td>Merchant Name:</td><td>'.$username.'</td></tr>
				  <tr><td>Amount:</td><td>'.$amt.'</td></tr>
				  <tr><td>Preferred Method:</td><td>'.$prefer_method.'</td></tr>
				  <tr><td>Time:</td><td>'.$date->format('d-m-Y H:m:i').'</td></tr>
				</table>';
	$msg_body .= "<br/><br/>Please check the Super Admin panel.";
	$msg_body .= "<br/><br/>
				Yours Sincerely,<br/>
				<a href='shurjopay.com.bd'>shurjoPay.com.bd</a>
				<p><a href='http://shurjomukhi.com.bd'>shurjoMukhi Ltd </a> <br/>
				Corporate Office: House # 320 (6th Floor), Road # 21, Mohakhali DOHS, Dhaka - 1206, Bangladesh <br />
				Email: <a href='mailto:info@shurjomukhi.com.bd'>info@shurjomukhi.com.bd</a> <br />
				Phone: +88 02 988 7202 <br />
				Mobile: +88 018 8502 2022<br />
				</p>
				</body></html>";
	sendEmail($sub, $frm, $to, $msg_body);
}
function sendEmail($sub, $frm, $to, $body) {
	/*
	$headers = "From: ".strip_tags($frm)."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($to, $sub, $body, $headers);
	*/
	
	$emailRequestData = array(					
			'invoiceId' => time(),  
			'subjectEmail' => $sub,
			'toEmail' => $to,         
			'fromEmail' => $frm,
			'bodyEmail' => $body                      
		);
	$ch = curl_init();                                     
	$url = "http://139.59.31.211:3000/sendemail";
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($emailRequestData));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);			
	$response = curl_exec($ch);
	curl_close ($ch);		
	if($response) 
	{	
		return TRUE;
	} else {
		return FALSE;	
	} 	
	
}

$userid = $_SESSION['ORDER_DETAILS']['userID'];
$Ucode = $_SESSION['ORDER_DETAILS']['Ucode'];
//require_once "pagination.php";

$page = NULL;
$tbl_name = "sp_epay"; //your table name
// How many adjacent pages should be shown on each side?
$adjacents = 3;

/* Setup vars for query. */
$targetpage = "withdraw_history.php"; //your file name  (the name of this file)
$limit = 10; //how many items to show per page

if (isset ($_REQUEST['page'])) {
	$page = $_GET['page'];
}

if ($page)
	$start = ($page -1) * $limit; //first item to display on this page
else
	$start = 0; //if no page var is given, set start to 0

if (isset ($_REQUEST['amount']) && (!empty ($_REQUEST['amount']))) {
	$history = get_all_history();
	$total = ($history['total_sold'] - ($history['total_pending'] + $history['total_accepted'] + $history['total_commission']));
	
	if ($total >= $_REQUEST['amount']) {
		$q = "insert into sp_withdrawal_history set user_id='".$userid."',amount='".$_REQUEST['amount']."',req_mode='".$_REQUEST['pr_method']."',request_date='".date('Y-m-d H:i:s')."',status='Pending'";
		mysqli_query($GLOBALS["___mysqli_sm"],$q);
		
		$q = "select username from sp_merchants where id = '{$userid}' AND unique_id_code = '{$Ucode}' LIMIT 0,1";
		$res_mer = mysqli_fetch_object(mysqli_query($GLOBALS["___mysqli_sm"],$q));
		//$email = 'fida.haq@shurjomukhi.com.bd, asaduzzaman@shurjomukhi.com.bd, tanvir.hasan@shurjomukhi.com.bd';
		$email = 'sazid@shurjomukhi.com.bd, asif@shurjomukhi.com.bd, ebrahim.sumon@shurjomukhi.com.bd';
		sendEmailConfirmation($email, $res_mer->username,$_REQUEST['amount'],$_REQUEST['pr_method']);		
		$err_amount = "";
		header("Location: ./withdraw_history.php");
	} 
	else {
		$err_amount = "Withdrawal Limits Exceed.";
	}
}

if (isset ($_REQUEST['fromdate']) && (!empty ($_REQUEST['fromdate'])) && isset ($_REQUEST['todate']) && (!empty ($_REQUEST['todate']))) {
	$fromdate = $_REQUEST['fromdate'];
	$todate = $_REQUEST['todate'];

	$fromdate_marge = $fromdate.' 00:00:00';
	$todate_marge = $todate.' 00:00:00';

	/* 
	First get total number of rows in data table. 
	If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "select *from sp_withdrawal_history where request_date >='{$fromdate_marge}' and request_date <='{$todate_marge}'  and user_id = '{$userid}'";
	$total_pages = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_sm"],$query));
	/* 
	  First get sum of all sold amount in data table. 
	  If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query2 = "select sum(amount) as total from sp_withdrawal_history where request_date >='{$fromdate_marge}' and request_date <='{$todate_marge}' and user_id = '{$userid}'";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$query2));
	$total = $res['total'];

	/* Get data. */

	$sql = "select *from sp_withdrawal_history where request_date >='{$fromdate_marge}' and request_date <='{$todate_marge}' and user_id = '{$userid}' ORDER BY request_date ASC LIMIT $start, $limit ";
	$sql1 = mysqli_query($GLOBALS["___mysqli_sm"],$sql);

} 
else {
	$fromdate = '';
	$todate = '';
	/* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/

	$query = "select *from sp_withdrawal_history where user_id = '{$userid}'";
	$total_pages = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_sm"],$query));
	/* 
	   First get sum of all sold amount in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query3 = "select sum(amount) as total from sp_withdrawal_history where  user_id = '{$userid}'";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$query3));
	$total = $res['total'];

	/* Get data. */

	$sql = "select *from sp_withdrawal_history where user_id = '{$userid}' ORDER BY request_date ASC LIMIT $start, $limit ";
	$sql1 = mysqli_query($GLOBALS["___mysqli_sm"],$sql);

}

/* Setup page vars for display. */
if ($page == 0)
	$page = 1; //if no page var is given, default to 1.
$prev = $page -1; //previous page is page - 1
$next = $page +1; //next page is page + 1

$lastpage = ceil($total_pages / $limit); //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage -1; //last page minus 1

/* 
	Now we apply our rules and draw the pagination object. 
	We're actually saving the code to a variable in case we want to draw it more than once.
*/
$pagination = "";
if ($lastpage > 1) {
	$pagination .= "<div class=\"pagination\">";

	//previous button
	if ($page > 1)
		$pagination .= "<a href=\"$targetpage?page=$prev & fromdate=$fromdate & todate=$todate\"><< previous</a>";
	else
		$pagination .= "<span class=\"disabled\"><< previous</span>";

	//pages	
	if ($lastpage < 7 + ($adjacents * 2)) //not enough pages to bother breaking it up
		{
		for ($counter = 1; $counter <= $lastpage; $counter ++) {
			if ($counter == $page)
				$pagination .= "<span class=\"current\">$counter</span>";
			else
				$pagination .= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";
		}
	}
	elseif ($lastpage > 5 + ($adjacents * 2)) //enough pages to hide some
	{
		//close to beginning; only hide later pages
		if ($page < 1 + ($adjacents * 2)) {
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter ++) {
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";
			}
			$pagination .= "...";
			$pagination .= "<a href=\"$targetpage?page=$lpm1 & fromdate=$fromdate & todate=$todate\">$lpm1</a>";
			$pagination .= "<a href=\"$targetpage?page=$lastpage & fromdate=$fromdate & todate=$todate\">$lastpage</a>";
		}
		//in middle; hide some front and some back
		elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
			$pagination .= "<a href=\"$targetpage?page=1 & fromdate=$fromdate & todate=$todate\">1</a>";
			$pagination .= "<a href=\"$targetpage?page=2 & fromdate=$fromdate & todate=$todate\">2</a>";
			$pagination .= "...";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter ++) {
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"$targetpage?page=$counter\">$counter</a>";
			}
			$pagination .= "...";
			$pagination .= "<a href=\"$targetpage?page=$lpm1 & fromdate=$fromdate & todate=$todate\">$lpm1</a>";
			$pagination .= "<a href=\"$targetpage?page=$lastpage & fromdate=$fromdate & todate=$todate\">$lastpage</a>";
		}
		//close to end; only hide early pages
		else {
			$pagination .= "<a href=\"$targetpage?page=1 & fromdate=$fromdate & todate=$todate\">1</a>";
			$pagination .= "<a href=\"$targetpage?page=2 & fromdate=$fromdate & todate=$todate\">2</a>";
			$pagination .= "...";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter ++) {
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";
			}
		}
	}

	//next button
	if ($page < $counter -1)
		$pagination .= "<a href=\"$targetpage?page=$next & fromdate=$fromdate & todate=$todate\">next >></a>";
	else
		$pagination .= "<span class=\"disabled\">next >></span>";
	$pagination .= "</div>\n";
}

$history = get_all_history();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>..::Shurjopay: Merchant Admin Panel::..</title>
<meta name="keywords" content="Brac Bank, payPoint, Brac Bank @ payPoint" />
<meta name="description" content="Brac Bank, payPoint, Brac Bank @ payPoint" />
<link href="css/templatemo_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" media="all" type="text/css" href="https://code.jquery.com/ui/1.9.1/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="js/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>

<script type="text/javascript">
$(function(){
	$('#todate').datetimepicker({
		showSecond: false,
		showMinute: false,
		showHour: false,
		dateFormat: 'yy-mm-dd'
		
	});	
	$('#fromdate').datetimepicker({
		showSecond: false,
		showMinute: false,
		showHour: false,
		dateFormat: 'yy-mm-dd'
		
	});		
});
function frmSubmit(act){
	document.getElementById("myForm").action="search-adm.php?orderfield="+act;
	document.getElementById("myForm").submit();
}
</script>

</head>

<body id="home">
<div id="templatemo_wrapper">
	<div id="templatemo_header">
    	<div id="site_title"><h1><a href="http://shurjopay.com.bd/"></a></h1></div>
        
        <div id="header_right">
             <h1><a href="http://<?php echo $logo->merchant_domain; ?>"  style="height: 50px; float: right;  margin-top: 30px;  width: 200px; padding: 22px 0 0 89px; background: url(../img/merchant_logo/<?php echo $logo->merchant_logo; ?>) no-repeat top right"></a></h1>
      </div> <!-- END -->
    </div> <!-- END of header -->
    
    <div style="padding:15px; text-align:right;">
	<div style='float:left;'><b>Merchant Admin Panel  (Logged in as : <span style="color:#ff0000;"> <?php echo $_SESSION['ORDER_DETAILS']['userName']; ?> </span> )</b>
    	   </div>
	<a href="index.php" style="font-weight:bold">Home </a>|| 
	<a href="reconcilliation.php" style="font-weight:bold">Reconcilliation </a>||
	
	<a href="sales_history.php" style="font-weight:bold">Sales History </a>|| 
	<a href="withdraw_history.php" style="font-weight:bold">Withdraw History </a>|| 
	
       <a href="logout.php" style="font-weight:bold">Logout </a>
    </div> <!-- end of templatemo_menu -->
    <!-- END of middle -->
<div id="templatemo_main_top"></div>
    <div id="templatemo_main">
    	<div id="product_slider">
    	  <div class="cleaner">
    	
        <div class="cleaner h20"></div>
        <h3>Submit new withdraw request:</h3>
			 <fieldset style="border:solid 1px #EEEEEE;">
			  <div style="float:left;">
                 <form name="myForm" method="post" action="" id="myForm">
					<table>
					<tr>
					<td></td>
					<td style='color:red;'><?php if($err_amount!=""){ echo $err_amount;} ?></td>
					</tr>
										<tr>
										
					<td> Withdrawal Amount : </td>
					<td>  <input name="amount" type="text" id="amount" class="form-login" title="Order ID" value="" size="15" maxlength="2048" /> (BDT)</td>
					</tr>
					<tr>
					<td>Preferred Method : </td>
					<td><input name="pr_method" type="text" id="pr_method" class="form-login" title="TX ID" value="" size="15" maxlength="2048" />
                                         (Cash or Cheque)</td>
					</tr>
					<tr>
					<td></td>
					<td> <input type="submit" value="Submit" name="submit"/></td>
					</tr>
					</table>
				</form>
				   </div>
				   <div style="float:right;display:inline;margin-right:50px;">
				  	<table>
					<tr>
					<td align="right" style="font-weight:bold;">Total MX Sold Amount :</td>
					<td class='highlight'><?php if(trim($history['mx_total_sold'])==""){ echo "0"; } else{ echo $history['mx_total_sold']; } ?>  Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Total Other Sold Amount :</td>
					<td class='highlight'><?php if(trim($history['nmx_total_sold'])==""){ echo "0"; } else{ echo $history['nmx_total_sold']; } ?>  Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Total Sold Amount :</td>
					<td class='highlight'><?php if(trim($history['total_sold'])==""){ echo "0"; } else{ echo $history['total_sold']; } ?>  Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">MX Commission Amount (<?php echo $mx_commission ?>%) :</td>
					<td class='highlight'><?php if(trim($history['mx_total_commission'])==""){ echo "0"; } else{ echo $history['mx_total_commission']; } ?> Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Others Commission Amount (<?php echo $other_commission; ?>%) :</td>
					<td class='highlight'><?php if(trim($history['nmx_total_commission'])==""){ echo "0"; } else{ echo $history['nmx_total_commission']; } ?> Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Total Commission Amount :</td>
					<td class='highlight'><?php if(trim($history['total_commission'])==""){ echo "0"; } else{ echo $history['total_commission']; } ?> Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Total Withdrawal Amount :</td>
					<td class='highlight'><?php if(trim($history['total_accepted'])==""){ echo "0"; } else{ echo $history['total_accepted']; } ?> Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Total Pending Request :</td>
					<td class='highlight'><?php if(trim($history['total_pending'])==""){ echo "0"; } else{ echo $history['total_pending']; } ?> Tk.</td>
					</tr>
					<tr>
					<td align="right" style="font-weight:bold;">Remaining balance : </td>
					<td class='highlight'><?php if(trim($history['avail_total'])==""){ echo "0"; } else{ echo $history['avail_total']; } ?> Tk.</td>
					</tr>
					</table>
								   
								   </div>
					<br />
					</fieldset>
			<br/>
				<h3>Title :Search By Date </h3><br/>
				
				  <fieldset style="border:solid 1px #EEEEEE;">
                                      <form name="myForm" method="post" action="" id="myForm">
				    <div>
					  <span class="left">Fromdate : 
                                             <input name="fromdate" class="form-login" id="fromdate" title="Date format ( YYYY-MM-DD)" value="" size="15" maxlength="2048" />
                                    </span>		
					  <span class="left">Todate : 
                                              <!input type="text"  name="it" id="date-pick" />
                                               <input name="todate" type="text" id="todate" class="form-login" title="Date format ( YYYY-MM-DD)" value="" size="15" maxlength="2048" />
                                          </span>
										  <!--
										    <span class="left">Order ID : 
                                              <!input type="text"  name="order_id" id="date-pick" />
                                               <input name="order_id" type="text" id="order_id" class="form-login" title="Order ID" value="" size="15" maxlength="2048" />
                                          </span>
										    <span class="left">TX ID : 
                                              <!input type="text"  name="it" id="date-pick" />
                                               <input name="tx_id" type="text" id="tx_id" class="form-login" title="TX ID" value="" size="15" maxlength="2048" />
                                          </span>
										  -->
					  <span><input type="submit" value="Submit" name="submit"/></span>
					  <div class="clearer"><span></span></div>
				    </div>
                                  </form>
					<br />
					<div>	
					  <!--
                                            <span class="left"><input type="file" /></span>
					  <span><input type="button" value="C&V"/></span>
					  <div class="clearer"><span></span></div>
       -->
				    </div>
                                        <br/>
                                        <div>
                                        <table class="tablesorter" id="myTable" border="0" width="100%" >
                       <tr class="row" >
                           <td>ID</td>
						   <td>Amount</td>
                           <td>Req. Mode</td>
						   <td>Tx. Mode</td>
                           <td>Tx. Details</td>
                           <td>Req. Date</td>
						   <td>Process Date</td>
                           <td>Status</td>
                                                </tr>
                   
                   <?php

//if(isset($_REQUEST['fromdate']) && isset($_REQUEST['todate']) )
//{

$counter = 1;

//$sql = "select *from sp_epay where intime >='{$fromdate_marge}' and intime <='{$todate_marge}'";
//$sql1=mysqli_query($GLOBALS["___mysqli_sm"], $sql);  

while ($sql2 = mysqli_fetch_row($sql1)) {

	if ($counter % 2 == 0) {
		echo "<tr class='rowincode'>";
	} else {
		echo "<tr>";
	}
	echo "<td>".$sql2[0]."</td>";
	echo "<td>".$sql2[2]."</td>";
	echo "<td>".$sql2[3]."</td>";
	echo "<td>".$sql2[4]."</td>";
	echo "<td>".$sql2[5]."</td>";
	echo "<td>".$sql2[6]."</td>";
	echo "<td>".$sql2[7]."</td>";

	echo "<td>".$sql2[8]."</td>";

	echo "</tr>";

	$counter += 1;

}

echo $pagination;
//}
?>
				   <tr></tr>
				   <tr>
				   <td></td>
				    <td></td>
					 <td></td>
					  <td></td>
					   <td></td>
					    <td></td>
						 <td><h3>Total</h3></td>
						  <td><h3><?php echo $total; ?> BDT</h3></td>
				   
				   </tr>
        </table><?php echo $pagination;  ?>
                                        </div>
				  </fieldset>
				
			</div> 
		
    	  </div>
      
        
        <!-- END of content -->
    </div> <!-- END of main -->
    
    <div id="templatemo_footer">
        <center>
			Copyright &copy 2018 payPoint  | Developed by <a href="http://www.shurjomukhi.com.bd" target="_parent">shurjoMukhi</a>
		</center>
    </div> <!-- END of footer -->   
   
</div>

</body>
</html>
