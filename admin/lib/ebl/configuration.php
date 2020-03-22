<?php

$configArray = array();

// possible values:
// FALSE = test mode
// TRUE = live mode
$configArray["gatewayMode"] = TRUE;

// If using a proxy server, uncomment the following proxy settings

// If no authentication is required, only uncomment proxyServer
// Server name or IP address and port number of your proxy server
//$configArray["proxyServer"] = "server:port";

// Username and password for proxy server authentication
//$configArray["proxyAuth"] = "username:password";

// If using certificate validation, modify the following configuration settings
// alternate trusted certificate file
// leave as "" if you do not have a certificate path
//$configArray["certificatePath"] = "C:/ca-cert-bundle.crt";

// possible values:
// FALSE = disable verification
// TRUE = enable verification
$configArray["certificateVerifyPeer"] = FALSE;

// possible values:
// 0 = do not check/verify hostname
// 1 = check for existence of hostname in certificate
// 2 = verify request hostname matches certificate hostname
$configArray["certificateVerifyHost"] = 0;

// Merchant ID supplied by your payments provider
$configArray["merchantId"] = "40670004";//"20070005";

// API password which can be configured in Merchant Administration
$configArray["password"] = "f032f966c37f56e10e8f43bf440468a7";//"cd8f2ab3946fe668e25492051e5b2b01";

// The debug setting controls displaying the raw content of the request and response for a transaction.
// In production you should ensure this is set to FALSE as to not display/use this debugging information
$configArray["debug"] = FALSE;

// Version number of the API being used for your integration this is the default value if it isn't being specified in process.php
$configArray["version"] = "41";
?>