<?php
include("../includes/configure.php");
include("../includes/session_handler.php");
include("../includes/mx.php");
include 'Functions.php';

if (@$_REQUEST['xmlmsg'] != "") {

    $xmlResponse = simplexml_load_string($_REQUEST['xmlmsg']);
    $json = json_encode($xmlResponse);
    $array = json_decode($json, TRUE);

    //Update existing Order XML by Approved Status
    $xmlData = new DOMDocument('1.0', 'utf-8');
    $xmlData->formatOutput = true;
    $xmlData->preserveWhiteSpace = false;
    $xmlData->load('Order.xml');

    //Get item element
    $element = $xmlData->getElementsByTagName('Order')->item(0);

    //Load child element
    $OrderID_Data = $element->getElementsByTagName('OrderID')->item(0);

    if (@$array[OrderID] == $OrderID_Data->nodeValue) {
        $SessionID_Data = $element->getElementsByTagName('SessionID')->item(0);
        $Status_Data = $element->getElementsByTagName('Status')->item(0);
        $ApprovalCode_Data = $element->getElementsByTagName('ApprovalCode')->item(0);
        $PAN_Data = $element->getElementsByTagName('PAN')->item(0);

        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= "<TKKPG>";
        $data .= "<Request>";
        $data .= "<Operation>GetOrderStatus</Operation>";
        $data .= "<Order>";
        $data .= "<Merchant>" . $_SESSION['ORDER_DETAILS']['mxID'] . "</Merchant>";
        $data .= "<OrderID>" . $OrderID_Data->nodeValue . "</OrderID>";
        $data .= "</Order>";
        $data .= "<SessionID>" . $SessionID_Data->nodeValue . "</SessionID>";
        $data .= "</Request></TKKPG>";
        $xmlStatus = PostQW($data);
        $StatusResponse = $xmlStatus->Response->Order->OrderStatus;

        //Replace old element with new
        $element->replaceChild($Status_Data, $Status_Data);
        $element->replaceChild($ApprovalCode_Data, $ApprovalCode_Data);
        $element->replaceChild($PAN_Data, $PAN_Data);

        //Assign element with new value
        $Status_Data->nodeValue = $StatusResponse;
        $ApprovalCode_Data->nodeValue = @$array[ApprovalCode];
        $PAN_Data->nodeValue = @$array[PAN];
        $xmlData->save('Order.xml');
    }

    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Asia/Dhaka"));

    $order_id = $_SESSION['ORDER_DETAILS']['order_id'];

    if (@$array[OrderID] != "") {
        $cardCountry = checkCardCountry(@$array[PAN]);
        if ($cardCountry->country->alpha2 == "BD") {
            //we can't use function i faced some problem ................thats why code reapet.......push live code with out test
            $epay_txid = @$array[OrderID];
            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
            $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");
            $mx = new Mx();
            $mx->mxApproved($order_id, $array, $_REQUEST['xmlmsg']);
            header("Location: " . $db->local_return_url);
        } else {
            $datetimecurrent = date("Y-m-d");
            $mxcardlimit = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from mx_bin_limit WHERE id=1");
            $resultmxcardlimit = mysql_fetch_object($mxcardlimit);
            $mxcardlimitspEpay = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT COUNT(`card_number`) as total_txn_in_day  from sp_epay WHERE card_number='" . @$array[PAN] . "' and bank_status='SUCCESS' and return_code='000' and gw_time LIKE '%$datetimecurrent%'");
            $resultmxcardlimitspEpay = mysql_fetch_object($mxcardlimitspEpay);
            if ($resultmxcardlimitspEpay->total_txn_in_day >= $resultmxcardlimit->limit) {
                $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_status='Block By Site Admin' ,return_code='999' WHERE order_id='" . $order_id . "'");
                header("Location: https://shurjopay.com/block.php");
            }else {
                //we can't use function i faced some problem ................thats why code reapet.......push live code with out test
                $epay_txid = @$array[OrderID];
                $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
                $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");
                $mx = new Mx();
                $mx->mxApproved($order_id, $array, $_REQUEST['xmlmsg']);
                header("Location: " . $db->local_return_url);
            }
        }
    } else {
        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='" . $order_id . "'");
        echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
    }
}


function checkCardCountry($cardNumber)
{
    $card = substr($cardNumber, 0, 6);
    $curl = curl_init('https://binlist.net/json/' . $card);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    $result=json_decode($result);
    return $result;
}

?>
				