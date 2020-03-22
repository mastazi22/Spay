<?php 
session_start();
$orderID = $_SESSION['orderID'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <!-- <link rel="stylesheet" type="text/css" href="../assets/paymentstyle.css" /> -->
    <head>
        <title>EBL Order Search Page.</title>
        <meta http-equiv="Content-Type" content="text/html, charset=iso-8859-1">
    </head>
    <body>

			<p style="text-align:center;"><a href="../index.php"><img src="http://ebl.com.bd/images/eastern-bank-ltd.gif" /></a></p>
      	  	<p style="text-align:center;"><a href="../index.php">Return to the Main Order Page</a></p>
        		<form action="Order_retrive_NVP.php" method="post">

       	 		<table width="60%" align="center" cellpadding="5" border="0">
            <!-- Credit Card Fields -->
					<tr class="title">
						
					<td align="center" colspan="2" height="25"><h1><strong>Retrieve Order by ID</strong></h1></td>
					</tr>
				  <br>
				</tr>

            <tr>
              <tr>
                <td align="right"><strong>Give Your Order ID</strong></td>
                <td><input type="text" value="<?php echo $orderID; ?>" name="text1" id="text1" /><br /><b</td>
              </tr>
            
            <tr>
                <td colspan="2"><center><input type="submit" name="submit" value="Submit to Get info" id="form"/></center></td>
            </tr>

        </table>

        </form>
        <br/><br/>

    </body>
</html>
<script >
    $('#form').submit();

    </script>