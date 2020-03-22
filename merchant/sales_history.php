<?php

/*
##### Author : A.K.M. Nazmul Hasan
##### Email : nazmul.hasan@shurjomukhi.com.bd

*/
session_start();
error_reporting(0);
if ($_SESSION['ORDER_DETAILS']['loggedIN'] != true) {
	header('Location: login.php');
}
require_once '../includes/configure.php';

$userid = $_SESSION['ORDER_DETAILS']['userID'];
$Ucode = $_SESSION['ORDER_DETAILS']['Ucode'];
//require_once "pagination.php";

$page = NULL;
$tbl_name = "sp_epay"; //your table name
// How many adjacent pages should be shown on each side?
$adjacents = 3;
$fromdate = "";
$todate = "";
/* Setup vars for query. */
$targetpage = "sales_history.php"; //your file name  (the name of this file)
$limit = 20; //how many items to show per page

if (isset ($_REQUEST['page'])) {
	$page = $_GET['page'];
}

if ($page)
	$start = ($page -1) * $limit; //first item to display on this page
else
	$start = 0; //if no page var is given, set start to 0

if (isset ($_REQUEST['fromdate']) && (!empty ($_REQUEST['fromdate'])) && isset ($_REQUEST['todate']) && (!empty ($_REQUEST['todate']))) {
	$fromdate = $_REQUEST['fromdate'];
	$todate = $_REQUEST['todate'];

	$fromdate_marge = $fromdate.' 00:00:00';
	$todate_marge = $todate.' 23:59:59';

	/* 
	First get total number of rows in data table. 
	If you have a WHERE clause in your query, make sure you mirror it here.
	*/

	$query = "select *from sp_epay where gw_time >='{$fromdate_marge}' and gw_time <='{$todate_marge}' and bank_status='SUCCESS' and uid = '{$userid}' AND txid LIKE '{$Ucode}%'";
	$total_pages = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_sm"],$query));
	/* 
	  First get sum of all sold amount in data table. 
	  If you have a WHERE clause in your query, make sure you mirror it here.
	*/

	$query2 = "select sum(amount) as total from sp_epay where gw_time >='{$fromdate_marge}' and gw_time <='{$todate_marge}' and bank_status='SUCCESS' and uid = '{$userid}' AND txid LIKE '{$Ucode}%' ORDER BY gw_time DESC LIMIT $start, $limit ";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$query2));
	$total = $res['total'];

	/* Get data. */

	$sql = "select * from sp_epay where gw_time >='{$fromdate_marge}' and gw_time <='{$todate_marge}'  and bank_status='SUCCESS' and uid = '{$userid}' AND txid LIKE '{$Ucode}%' ORDER BY gw_time DESC LIMIT $start, $limit ";
	$sql1 = mysqli_query($GLOBALS["___mysqli_sm"],$sql);
}
else {	
	/* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/

	$query = "select * from sp_epay where uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS'";
	$total_pages = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_sm"],$query));
	/* 
	   First get sum of all sold amount in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query3 = "select sum(amount) as total from sp_epay where  bank_status='SUCCESS' and uid = '{$userid}' AND txid LIKE '{$Ucode}%'  ORDER BY gw_time DESC LIMIT $start, $limit ";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$query3));
	$total = $res['total'];

	/* Get data. */
	//$sql = "select * from sp_epay where uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS'";
	$sql = "select * from sp_epay where uid = '{$userid}' AND txid LIKE '{$Ucode}%' and bank_status='SUCCESS' ORDER BY gw_time DESC LIMIT $start, $limit ";
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
		$pagination .= "<a href=\"$targetpage?page=$prev&fromdate=$fromdate&todate=$todate\"><< previous</a>";
	else
		$pagination .= "<span class=\"disabled\"><< previous</span>";

	//pages	
	if ($lastpage < 7 + ($adjacents * 2)) //not enough pages to bother breaking it up
		{
		for ($counter = 1; $counter <= $lastpage; $counter ++) {
			if ($counter == $page)
				$pagination .= "<span class=\"current\">$counter</span>";
			else
				$pagination .= "<a href=\"$targetpage?page=$counter&fromdate=$fromdate&todate=$todate\">$counter</a>";
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
					$pagination .= "<a href=\"$targetpage?page=$counter&fromdate=$fromdate&todate=$todate\">$counter</a>";
			}
			$pagination .= "...";
			$pagination .= "<a href=\"$targetpage?page=$lpm1&fromdate=$fromdate&todate=$todate\">$lpm1</a>";
			$pagination .= "<a href=\"$targetpage?page=$lastpage&fromdate=$fromdate&todate=$todate\">$lastpage</a>";
		}
		//in middle; hide some front and some back
		elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
			$pagination .= "<a href=\"$targetpage?page=1&fromdate=$fromdate&todate=$todate\">1</a>";
			$pagination .= "<a href=\"$targetpage?page=2&fromdate=$fromdate&todate=$todate\">2</a>";
			$pagination .= "...";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter ++) {
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"$targetpage?page=$counter\">$counter</a>";
			}
			$pagination .= "...";
			$pagination .= "<a href=\"$targetpage?page=$lpm1&fromdate=$fromdate&todate=$todate\">$lpm1</a>";
			$pagination .= "<a href=\"$targetpage?page=$lastpage&fromdate=$fromdate&todate=$todate\">$lastpage</a>";
		}
		//close to end; only hide early pages
		else {
			$pagination .= "<a href=\"$targetpage?page=1&fromdate=$fromdate&todate=$todate\">1</a>";
			$pagination .= "<a href=\"$targetpage?page=2&fromdate=$fromdate&todate=$todate\">2</a>";
			$pagination .= "...";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter ++) {
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"$targetpage?page=$counter&fromdate=$fromdate&todate=$todate\">$counter</a>";
			}
		}
	}

	//next button
	if ($page < $counter -1)
		$pagination .= "<a href=\"$targetpage?page=$next&fromdate=$fromdate&todate=$todate\">next >></a>";
	else
		$pagination .= "<span class=\"disabled\">next >></span>";
	$pagination .= "</div>\n";
}

$method_details = array(
	'bkash'=>array('bKash','bKash','2.00'),
	'dbbl_mobile'=>array('Rocket','DBBL','2.00'),
	'dbbl_nexus'=>array('Nexus','DBBL','2.50'),
	'ebl_master'=>array('Mastercard','EBL','2.50'),
	'ebl_visa'=>array('Visa','EBL','2.50'),
	'ibbl'=>array('iBanking','IBBL','2.00'),
	'mCash_iBank'=>array('Mcash','IBBL','2.00'),
	'mx'=>array('American Express','CBL','3.50'),
	'mx_master_card'=>array('Mastercard','CBL','2.50'),
	'mx_visa'=>array('Visa','CBL','2.50'),
	'tbl'=>array('TBL','TBL','2.50'),
	'tbl-ITCL'=>array('Visa','TBL ITCL','2.50'),
	'tbl-MB'=>array('t-cash','t-cash','2.00'),
	'bkash_api'=>array('bKash','bKash','2.00'),
	'upay'=>array('Upay','Upay','2.00')
	);


	// Total amount and commission calculation for robi
	if(isset($Ucode) && ($Ucode == 'RCT') )
	{ 
		$robiTotalQuery = "select sum(amount) as total,sum(commission_amount) as com,sum(paid_amount) as payable from robi_report_view ";
		$robiTotal = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_sm"],$robiTotalQuery));
		$total = number_format($robiTotal['total'],2);
		$com =   number_format($robiTotal['com'],2);
		$payable = number_format($robiTotal['payable'],2);
	}	

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
        	<?php if($Ucode != 'RCT' || $Ucode != 'BAZ'):?>
        	 <h1><a href="http://<?php echo $logo->merchant_domain; ?>"  style="height: 50px; float: right;  margin-top: 30px;  width: 200px; padding: 22px 0 0 89px; background: url(../img/merchant_logo/<?php echo $logo->merchant_logo; ?>) no-repeat top right"></a></h1>
        	<?php else:?> 
             <h1><a href="http://<?php echo $logo->merchant_domain; ?>"  style="height: 100px; float: right;  margin-top: 0px;  width: 200px; padding: 22px 0 0 89px; background: url(../img/merchant_logo/<?php echo $logo->merchant_logo; ?>) no-repeat top right"></a></h1>
			<?php endif;?>             
      </div> <!-- END -->
    </div> <!-- END of header -->
    
    <div style="padding:15px; text-align:right;">
	<div style='float:left;'><b>Merchant Admin Panel  (Logged in as : <span style="color:#ff0000;"> <?php echo $_SESSION['ORDER_DETAILS']['userName']; ?></span>)</b>
    	   </div>
	<a href="index.php" style="font-weight:bold">Home </a>|| 
	<?php if($Ucode != 'RCT' || $Ucode != 'BAZ'):?>
	<a href="reconcilliation.php" style="font-weight:bold">Reconcilliation </a>||
	<a href="withdraw_history.php" style="font-weight:bold">Withdraw History </a>||
	<?php endif;?>	 
	<a href="sales_history.php" style="font-weight:bold">Sales History </a>|| 
	
       <a href="logout.php" style="font-weight:bold">Logout </a>
    </div> <!-- end of templatemo_menu -->
    <!-- END of middle -->
<div id="templatemo_main_top"></div>
    <div id="templatemo_main">
    	<div id="product_slider">
    	  <div class="cleaner">
    	
        <div class="cleaner h20"></div>
        <h3>Total Sales: Search By Date</h3>
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
						<?php if(isset($Ucode) && ($Ucode == 'RCT' )):?>
							<div style="float:right;width:auto;">
							  	<table id="myTable">
							  		<tr class="row"><td>Total1</td><td>Commission(BDT)</td><td>Net Payable</td></tr>
							  		<tr class="rowincode" style="font-size: 16px;">
							  			<td>&nbsp;<b><?=$total?></b>&nbsp;</td>
							  			<td>&nbsp;<b><?=$com?></b>&nbsp;</td>
							  			<td>&nbsp;<b><?=$payable?></b>&nbsp;</td>
							  		</tr>
							 	</table>						  	
						    </div>
						<?php endif;?>				  
					  <div class="clearer"><span></span></div>	

				    </div>
				               </form>
                 <br/>                 
                

			<br/>
				 <table name="myTable" id="myTable" border="0" width="100%" >
                       <tr class="row" >
			<?php if(isset($Ucode) && ($Ucode == 'ZEN') ):?>
				<td>POLICY ID/EMPLOYEE ID</td>
			<?php endif;?>
                           <td>ID</td>
						   <td>Transaction Time</td>
                           <td>Transaction ID</td>
							<td>
							<?php
								if ($Ucode == 'PPTSYM') {
									echo "Product Name</td><td>Quantity";
									}else{
									//echo "Order ID</td><td>Bank Name";
									echo "Order ID";
									}
							?></td>
							<!-- <td>Bank Reference ID</td> -->
						   <td>Gateway</td>
						   <td>Instrument</td>
                           <td>Amount</td>
                           <?php if(isset($Ucode) && ($Ucode == 'RCT' || $Ucode == 'BAZ') ): ?>
                           	<td>Commission %</td>
						    <td>Commission (BDT)</td>
						 	<td>Net Payable</td>
						 	<?php endif;?>
                        </tr>
                   
                   <?php

//if(isset($_REQUEST['fromdate']) && isset($_REQUEST['todate']) )
//{

$counter = 0;

//$sql = "select *from sp_epay where gw_time >='{$fromdate_marge}' and gw_time <='{$todate_marge}'";
//$sql1=mysqli_query($GLOBALS["___mysqli_sm"], $sql);  

if(isset($Ucode) && ($Ucode == 'ZEN') )
{
	$csv_data = 'Policy ID,"ID", Order ID, TX ID, Amount, Method, Bank Name, Bank Ref, Tx Time'."\n";
	$xls_data = "Policy ID\tID\tOrder ID\tTX ID\tAmount\tMethod\tBank Name\tBank Ref\tTx Time"."\n";
} 
else 
{

	$csv_data = '"ID", Order ID, TX ID, Amount, Method, Bank Name, Bank Ref, Tx Time'."\n";
	$xls_data = "ID\tOrder ID\tTX ID\tAmount\tMethod\tBank Name\tBank Ref\tTx Time"."\n";
}

$total_commission_amount = 0;
$total_paid_amount = 0;

while ($sql2 = mysqli_fetch_row($sql1)) {

// Robi Commission and its total
if(isset($Ucode) && ($Ucode == 'RCT' || $Ucode == 'BAZ') )
{
$commission =  $method_details[$sql2[5]][2];
$commission_amount = ($sql2[4]*$commission)/100;
$paid_amount = $sql2[4] - $commission_amount;
$total_commission_amount += $commission_amount;
$total_paid_amount += $paid_amount;
}	
	if(isset($Ucode) && ($Ucode == 'ZEN') )
	{
		$reurl = explode("=",$sql2[10]);
		if(isset($reurl[1]) && !empty($reurl[1])) 
		{
			$csv_data .= '"'.$reurl[1].'" ,"';
			$xls_data .= $reurl[1]."\t";	
		} else {
			$csv_data .= '"N/A" ,"';
			$xls_data .= 'N/A'."\t";	
		}	
		
	}

	$csv_data .= '"'.$sql2[0].'" ,"';
	$xls_data .= $sql2[0]."\t";
	$csv_data .= '"'.$sql2[2].'" ,"';
	$xls_data .= $sql2[2]."\t";
	$csv_data .= '"'.$sql2[3].'" ,"';
	$xls_data .= $sql2[3]."\t";
	
	$csv_data .= '"'.$sql2[4].'" ,"';
	$xls_data .= $sql2[4]."\t";
	
	$csv_data .= '"'.$sql2[5].'" ,"';
	$xls_data .= $sql2[5]."\t";
	
	$csv_data .= '"'.$sql2[8].'" ,"';
	$xls_data .= $sql2[8]."\t";
	
	$csv_data .= '"'.$sql2[11].'" ,"';
	$xls_data .= $sql2[11]."\t";
	
	$csv_data .= '"'.$sql2[7];
	$xls_data .= $sql2[7];
	$csv_data .= "\n";
	$xls_data .= "\n";
	
	
	
	if ($counter % 2 == 0) {
		echo "<tr class='rowincode'>";
	} else {
		echo "<tr>";
	}
	//~ echo "<td>".$sql2[0]."</td>";
	//~ echo "<td>".$sql2[2]."</td>";
	//~ echo "<td>".$sql2[3]."</td>";
	//~ echo "<td>".$sql2[4]."</td>";
	//~ echo "<td>".$sql2[5]."</td>";
	//~ echo "<td>".$sql2[8]."</td>";
	//~ echo "<td>".$sql2[11]."</td>";
//~ 
	//~ echo "<td>".$sql2[7]."</td>";

// For Zenith.com Policy Number
	if(isset($Ucode) && ($Ucode == 'ZEN') ) 
	{
	  $reurl = explode("=",$sql2[10]);
	   if(isset($reurl[1]) && !empty($reurl[1])) 
	   {
		echo "<td>".$reurl[1]."</td>";
	   } else {
	    echo "<td>".'N/A'."</td>";
	  }
	}	


	echo "<td>".$sql2[0]."</td>";
	echo "<td>".date('d-M-y H:i:s',strtotime($sql2[7]))."</td>";
	echo "<td>".$sql2[3]."</td>";	
	if ($Ucode == 'PPTSYM') {
		$sym_order_id = substr($sql2[3],3);
		$sqlsym = "SELECT * FROM paypoint.sym_order_product WHERE order_id = '{$sym_order_id}'";		
		$res=mysqli_query($GLOBALS["___mysqli_sm"],$sqlsym);				
		$sym_product="";
		$sym_qty="";
		$i=1;
		while ($qsym = mysqli_fetch_assoc($res)) {
		if($i<mysqli_num_rows($res))
		{			
	    $sym_qty.=$qsym['product_qty']."</br>";
		$sym_product.= $qsym['product_name']."</br>";
	    }
	    else
	    {
		$sym_qty.=$qsym['product_qty'];
		$sym_product.= $qsym['product_name'];
		}
			$i++;
}
		echo "<td>".$sym_product."</td>";
		echo "<td style='text-align:center;'>".$sym_qty."</td>";
	} else {
		echo "<td>".$sql2[2]."</td>";
		//echo "<td style='text-align:center;'>".strtoupper($sql2[8])."</td>";
	}
	
	
	//echo "<td>".$sql2[11]."</td>";
	echo "<td>".$method_details[$sql2[5]][1]."</td>";
	echo "<td>".$method_details[$sql2[5]][0]."</td>";
	echo "<td>".number_format($sql2[4],2)."</td>";
	if(isset($Ucode) && ($Ucode == 'RCT' || $Ucode == 'BAZ') )
	{
		echo "<td>".$method_details[$sql2[5]][2]."</td>";
		echo "<td>".number_format($commission_amount,2)."</td>";
		echo "<td>".number_format($paid_amount,2)."</td>";
	}
	echo "</tr>";

	$counter += 1;

}

echo $pagination;

$file_location = '/tmp/';
if (PHP_OS == 'WINNT') {
	$file_location = 'C:\svn\tmp\\';
}
$outputcsvfile = "sales_history.csv";
$csv_file = file_put_contents($file_location.$outputcsvfile, $csv_data);

$outputxlsfile = "sales_history.xls";
$xls_file = file_put_contents($file_location.$outputxlsfile, $xls_data);
//}
?>
				   <tr></tr>
				   <tr>
				   <td></td>
				    <td></td>
					 <td></td>
					  <td></td>
					   <td></td>
					    <!-- <td></td> -->
						<?php if(isset($Ucode) && ($Ucode == 'ZEN') ) 
							{
								echo "<td></td>";	
							}	
						?>
						 <td><h3>Total</h3></td>
						 <td><h3><?php echo round($total,2); ?> BDT</h3></td>
						 
						<?php if(isset($Ucode) && ($Ucode == 'RCT' || $Ucode == 'BAZ') ): ?>
						<td><h3><?php echo round(($total_paid_amount+$total_commission_amount),2); ?> BDT</h3></td>
						<!--<td></td>-->
						 <td><h3><?php echo round($total_commission_amount,2);?></h3></td>
						 <td><h3><?php echo round($total_paid_amount,2);?></h3></td>
						<?php endif;?>
						  
				   
				   </tr>
        </table><?php echo $pagination;  ?>        
    	<!--
        <div><a href="downloadcsv.php" class="more">Download CSV</a></div>
		<div><a href="downloadxls.php" class="more">Download XLS</a></div>   
		-->
		<div><a href='<?php echo "report-download.php?from_date=$fromdate&to_date=$todate&Ucode=$Ucode";?>' class="more">Download Report</a></div>                                  
				
			</div> 
		
    	  </div>
      
        
        <!-- END of content -->
    </div> <!-- END of main -->
    
    <div id="templatemo_footer">
        <center>
			Copyright &copy 2018 payPoint | Developed by <a href="http://www.shurjomukhi.com.bd" target="_parent">shurjoMukhi</a>
		</center>
    </div> <!-- END of footer -->   
   
</div>

</body>
</html>
