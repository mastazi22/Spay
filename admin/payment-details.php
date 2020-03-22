<?php
	session_start();
	include("../includes/configure.php");
	require_once 'database.php';
	if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin']) {    
	 	header('Location: login.php');
	}

	require_once('lib/ORM.php');
	$db = new ORM($GLOBALS["___mysqli_sm"]);
	$error_flag = '';
	if(!empty($_POST['update']))
	{
		
		
		$dataUpdate = array_diff($_POST,[update]);	
		$dataUpdate['remarks'] = 'Update from Panel';			
		if($db->updateLog($dataUpdate, $_POST['order_id']))
		{
			$error_flag = 'Update Successful!';
		}	
		else
		{
			$error_flag = 'Update Failed!';
		}
		
	}

	$txid = $_GET['txid'];
	$res = $db->getPaymentDetails($txid);
	if(!isset($res) && empty($res))
	{
		$error_flag="No data found!.";	
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/layout.css" rel="stylesheet" type="text/css">
<link href="css/text.css" rel="stylesheet" type="text/css">
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/> 
<meta name="#" content="#"/> 
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<title>..::MIS::..</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Calendar javascript Code</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/overlay.css"/>
</head>

<body>

<div class="main">

	<div class="container">

		<div class="gfx">
		  <div class="logo">	
			<span class="left"><a href=""><img src="images/sand-box-logo.jpg" /></a></span>		
			<span class="right"><div id="merchantlogo"><img src="./img/merchantimage" alt="SHURJOMUKHI"></div></span>
			<div class="clearer"><span></span></div>	
		  </div>                    
		</div>

		<div class="menu">
			<a href="loginMIS.php"><span >Search by Date</span></a>                        
		</div>
		<div style="float:right;font-size:15px" class="menu"><a href="logout.php"><span >Logout</span></a></div>
		<div style="float:right;font-size:15px" class="menu"><a href="navigation.php"><span >Home</span></a></div>
		<div class="content">

			<div class="item" style="padding-left:80px;">
			<form id="form1" name="form1" method="post" action="" autocomplete="off">
		    <fieldset>				
		           <legend>Payment Details</legend>
		        <ol>
		            <li>
		                <?php if($error_flag!=""){echo $error_flag;} ?>
		            </li>

		            <?php foreach($res as $key => $value):?>	
		            	 <li>
			                <label><?=$key?></label>
			                <input name="<?=$key?>" type="text" class="controls" id="<?=$key?>" value='<?=$value?>' size="20" maxlength="200">
			            </li>
		            <?php endforeach;?>	
		            <li id="res">
		            	
		            </li>
		            <li>
		                <label>&nbsp;&nbsp;</label>		                
		                <input name="update" type="submit" class="controls" value="update"
		                 onclick="document.form1.submit()">
		                <input name="get_status" type="button" class="controls" value="Get Status" onclick="getStatus('<?=$txid?>')" >
		                <a href="response-to-client.php?txid=<?=$txid?>">Response to Client</a>	
		            </li>					
		        </ol>
						
		   </fieldset>
    </form>
	 	</div>
		<div class="footer">		
			<span class="left">&copy; 2012 <a href="#">shurjoPay</a></span>		
			<span class="right"><a href="#">Designed & Developed</a> by <a href="#">Shurjomukhi</a></span>
			<div class="clearer"><span></span></div>		
		</div>

	</div>	

</div>

<div id="overlay">
	<div class="cv-spinner">
		<span class="spinner"></span>
	</div>
</div>

</body>

<script type="text/javascript">
	function getStatus()
	{
		var $loading = $('#loadingDiv').hide();
		(function($){

				$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
				});


	            $.ajax({
	                url: 'payment-details-api.php',
	                dataType: "json",
	                type: 'POST',
	                // contentType: 'application/x-www-form-urlencoded',
	                data: $("form").serialize(),
	                success: function( response, textStatus, jQxhr ){	                	
	                   	$.each(response, function (index, value) {
	                   		$('#'+index).val(value);		                    
		                });
		                $("#overlay").fadeOut(300);			
	                },
	                error: function( jqXhr, textStatus, errorThrown ){
	                    console.log( errorThrown );
	                }
	            });

	            //e.preventDefault();
    	})(jQuery);
	}

</script>

</html>