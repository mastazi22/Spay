<?php session_start(); 
if( $_SESSION['ORDER_DETAILS']['loggedIN'] != true)
{
    header( 'Location: login.php' );
}
else{
 header( 'Location: navigation.php' ) ;    
}
?>
<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
</head>
<body>

<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
  <div id="merchantlogo"><img src="./img/merchantimage" alt="SHURJOMUKHI"></div>
  </div>
  <div id="main_container">
      <noscript>
        &lt;h3&gt;Please enable JavaScript in your browser for the iPay&amp;reg; service&lt;/h3&gt;
    </noscript>	
   
    <a href="send_request.php" style="padding-left: 10px;padding-right: 10px;font-weight: bold;">Send Request</a> <a href="login.php" style="padding-left: 10px;padding-right: 10px; font-weight: bold;">MIS Login</a>
    
    
   </div>
  <div id="footer"></div>
</div>
<div id="shadow"></div>


  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">


</body>
</html>