<?php
include 'Functions.php';
function GoBankpage($amount){
//echo $_SESSION['ORDER_DETAILS']['mxID'];
if(isset($_SESSION['ORDER_DETAILS']['mxID']) and $_SESSION['ORDER_DETAILS']['mxID'] !="")
{
//echo $_SESSION['ORDER_DETAILS']['mxID'];
$Merchant=$_SESSION['ORDER_DETAILS']['mxID'];
$description="shurjoPay";
$currency="050";
$successUrl="https://shurjopay.com/mx/Approved.php";
$cancelUrl="https://shurjopay.com/mx/Cancelled.php";
$declineUrl="https://shurjopay.com/mx/Declined.php";
// Create Xml order to describe the order parameters:
$data='<?xml version="1.0" encoding="UTF-8"?>';
$data.="<TKKPG>";
$data.="<Request>";
$data.="<Operation>CreateOrder</Operation>";
$data.="<Language>EN</Language>";
$data.="<Order>";
$data.="<OrderType>Purchase</OrderType>";
$data.="<Merchant>".$Merchant."</Merchant>";
$data.="<Amount>". $amount * 100 ."</Amount>";
$data.="<Currency>".$currency."</Currency>";
$data.="<Description>".$description."</Description>";
$data.="<ApproveURL>".htmlentities($successUrl)."</ApproveURL>";
$data.="<CancelURL>".htmlentities($cancelUrl)."</CancelURL>";
$data.="<DeclineURL>".htmlentities($declineUrl)."</DeclineURL>";
$data.="</Order></Request></TKKPG>";

// Information on the result of the order creation in the Response object
// Examples of obtaining required fields:
$xml=PostQW($data);

$OrderID=$xml->Response->Order->OrderID;
$SessionID=$xml->Response->Order->SessionID;

$data='<?xml version="1.0" encoding="UTF-8"?>';
$data.="<TKKPG>";
$data.="<Request>";
$data.="<Operation>GetOrderStatus</Operation>";
$data.="<Order>";
$data.="<Merchant>".$Merchant."</Merchant>";
$data.="<OrderID>".$OrderID."</OrderID>";
$data.="</Order>";
$data.="<SessionID>".$SessionID."</SessionID>";
$data.="</Request></TKKPG>";
$xml=PostQW($data);
$OrderStatus=$xml->Response->Order->OrderStatus;

        $sp_order_id = $_SESSION['ORDER_DETAILS']['order_id'];
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_tx_id='" . $OrderID . "' WHERE order_id='" . $sp_order_id . "'");
		// City Bank order information store in separate table for future use
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_mx_transactions SET  transaction_id='".$sp_order_id."',  OrderID='".$OrderID."', SessionID='".$SessionID."', transaction_time='".date('Y-m-d H:i:s')."'");
		
	// Request for payment page
	if ($OrderID!="" and $SessionID!="")
	{
		//Update existing Order XML by Create Order Status
		$xml=new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		//var_dump(file_exists('Order.xml'));exit();
		$xml->load('Order.xml');
		// Add codes for saving the Order ID and Session ID in Merchant DB for future uses.
		header("Location: https://epay.thecitybank.com/index.jsp?ORDERID=" . $OrderID. "&SESSIONID=" . $SessionID . "");
		exit();
	}
}
 exit("Sorry Service Not Available Please Contact With shurjoPay Support");
}
?>
