<?php

$reponse_data = $_POST['spdata'];
if (!(strpos($reponse_data, '<txID>') === false && strpos($reponse_data, '</txID>') === false)) {
	$txid = substr($reponse_data, (strpos($reponse_data, '<txID>') + 6), (strpos($reponse_data, '</txID>') - (strpos($reponse_data, '<txID>') + 6)));
}
if (!(strpos($reponse_data, '<bankTxID>') === false && strpos($reponse_data, '</bankTxID>') === false)) {
	$banktxid = substr($reponse_data, (strpos($reponse_data, '<bankTxID>') + 10), (strpos($reponse_data, '</bankTxID>') - (strpos($reponse_data, '<bankTxID>') + 10)));
}
if (!(strpos($reponse_data, '<bankTxStatus>') === false && strpos($reponse_data, '</bankTxStatus>') === false)) {
	$banktxstatus = substr($reponse_data, (strpos($reponse_data, '<bankTxStatus>') + 14), (strpos($reponse_data, '</bankTxStatus>') - (strpos($reponse_data, '<bankTxStatus>') + 14)));
}
if (!(strpos($reponse_data, '<txnAmount>') === false && strpos($reponse_data, '</txnAmount>') === false)) {
	$txnamount = substr($reponse_data, (strpos($reponse_data, '<txnAmount>') + 11), (strpos($reponse_data, '</txnAmount>') - (strpos($reponse_data, '<txnAmount>') + 11)));
}
if (!(strpos($reponse_data, '<spCode>') === false && strpos($reponse_data, '</spCode>') === false)) {
	$spcode = substr($reponse_data, (strpos($reponse_data, '<spCode>') + 8), (strpos($reponse_data, '</spCode>') - (strpos($reponse_data, '<spCode>') + 8)));
}
if (!(strpos($reponse_data, '<spCodeDes>') === false && strpos($reponse_data, '</spCodeDes>') === false)) {
	$spcodedes = substr($reponse_data, (strpos($reponse_data, '<spCodeDes>') + 11), (strpos($reponse_data, '</spCodeDes>') - (strpos($reponse_data, '<spCodeDes>') + 11)));
}
if (!(strpos($reponse_data, '<paymentOption>') === false && strpos($reponse_data, '</paymentOption>') === false)) {
	$paymentoption = substr($reponse_data, (strpos($reponse_data, '<paymentOption>') + 15), (strpos($reponse_data, '</paymentOption>') - (strpos($reponse_data, '<paymentOption>') + 15)));
}
echo "<br>";
echo "Transaction ID:: ".$txid."<br>";
echo "Bank Status:: ".$banktxstatus."<br>";
echo "Amount:: ".$txnamount."<br>";
echo "Response Code:: ".$spcode."<br>";
echo "Response Code Des:: ".$spcodedes."<br>";
echo "Payment Method:: ".$paymentoption."<br>";

?>
