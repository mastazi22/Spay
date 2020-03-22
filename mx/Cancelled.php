<?php
include ("../includes/session_handler.php");
include("../includes/configure.php");
include ("../includes/mx.php");
include 'Functions.php';
	if (@$_REQUEST['xmlmsg']!=""){
		$xmlResponse = simplexml_load_string($_REQUEST['xmlmsg']);
		$json = json_encode($xmlResponse);
		$array = json_decode($json,TRUE);
								
		//Update existing Order XML by Cancelled Status
		$xmlData=new DOMDocument('1.0', 'utf-8');
		$xmlData->formatOutput = true;
		$xmlData->preserveWhiteSpace = false;
		$xmlData->load('Order.xml');
		
		//Get item element
		$element=$xmlData->getElementsByTagName('Order')->item(0);

		//Load child element
		$OrderID_Data=$element->getElementsByTagName('OrderID')->item(0);
		
		if(@$array[OrderID]==$OrderID_Data->nodeValue){
			$SessionID_Data=$element->getElementsByTagName('SessionID')->item(0);
			$Status_Data=$element->getElementsByTagName('Status')->item(0);
			$ApprovalCode_Data=$element->getElementsByTagName('ApprovalCode')->item(0);
			$PAN_Data=$element->getElementsByTagName('PAN')->item(0);
			
			$data='<?xml version="1.0" encoding="UTF-8"?>';
			$data.="<TKKPG>";
			$data.="<Request>";
			$data.="<Operation>GetOrderStatus</Operation>";
			$data.="<Order>";
			$data.="<Merchant>".$_SESSION['ORDER_DETAILS']['mxID']."</Merchant>";
			$data.="<OrderID>".$OrderID_Data->nodeValue."</OrderID>";
			$data.="</Order>";
			$data.="<SessionID>".$SessionID_Data->nodeValue."</SessionID>";
			$data.="</Request></TKKPG>";
			$xmlStatus=PostQW($data);
			$StatusResponse=$xmlStatus->Response->Order->OrderStatus;			
			
			//Replace old element with new
			$element->replaceChild($Status_Data, $Status_Data);
			$element->replaceChild($ApprovalCode_Data, $ApprovalCode_Data);
			$element->replaceChild($PAN_Data, $PAN_Data);

			//Assign element with new value
			$Status_Data->nodeValue = $StatusResponse;
			$ApprovalCode_Data->nodeValue = "";
			$PAN_Data->nodeValue = "";
			$xmlData->save('Order.xml');							
		}
		$mx = new Mx();
        $mx->mxCanceled($array,$_REQUEST['xmlmsg']);	

      header("Location: ".$db->local_return_url);
	}	
?>
