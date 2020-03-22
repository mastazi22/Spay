<?php

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
    //$result = substr($result, 67, 2);
    return $result;
}
$cardNumber=trim($_GET['cardNumber']);
$result=checkCardCountry($cardNumber);
echo "<center><h1>";
echo "Country Code=".$result->country->alpha2."</br>";
echo "Country Name=".$result->country->name."</br>";
echo "Sub Brand=".$result->scheme."</br>";
echo "Brand=".$result->brand."</br>";
echo "</center></h1>";


?>