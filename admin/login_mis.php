<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php  require_once 'database.php';?>




<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>..::MIS::..</title>
<link href="login.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <form name="myForm" id="myForm" method="post" action="">
<div style="padding: 60px 0 0 250px;">
	<div id="mis-box">
	      <div id="header_logo_image">
			<img src="images/sand-box-logo.jpg" />
		  </div>	
		  <div>Welcome tom SandBox MIS.</div>
		  <br />
		  <div> </div>
			<div id="login-box-name" style="margin-top:20px;">From Date</div>
				<div id="login-box-field" style="margin-top:20px;">
				  <input name="fromdate" class="form-login" title="Date format ( YYYY-MM-DD)" value="" size="30" maxlength="2048" />
				</div>
			<div id="login-box-name" style="margin-top:20px;">To Date</div>
				<div id="login-box-field" style="margin-top:20px;">
				  <input name="todate" type="text" class="form-login" title="Date format ( YYYY-MM-DD)" value="" size="30" maxlength="2048" />
				</div>
			<br />
			<span class="login-box-options">
			  
		
		<input type="submit" name="submit" id="submit" value="Submit" src="images/btn.png" width="103" height="42" style="margin-left:90px;">
	
               <div style="padding-top:20px; padding-left:5px">
                   <table name="myTable" id="myTable" border="0" width="100%" >
                       <tr class="row">
                           <td>ID</td>
                           <td>Amount</td>
                           <td>Method</td>
                           <td>Bank Name</td>
                           <td>Return URL</td>
                           <td>Remarks</td>
                           <td>Intime</td>
                       </tr>
                   
                   <?php if(isset($_REQUEST['fromdate']) && isset($_REQUEST['todate']) )
                   {
                        
                       $counter=0;
                       $fromdate=$_REQUEST['fromdate'];
                       $todate=$_REQUEST['todate'];
                        
                       $fromdate_marge=$fromdate.' 00:00:00';
                       $todate_marge=$todate.' 00:00:00';
    
                        $sql = "select *from sp_epay where intime >='{$fromdate_marge}' and intime <='{$todate_marge}'";
                        $sql1=mysqli_query($GLOBALS["___mysqli_sm"],$sql);
                        
                        
                        
                        while($sql2=  mysqli_fetch_row($sql1)){
                            if($counter%2 == 0){
                              echo "<tr class='rowincode'>";
                            }
                            else
                            {
                                echo "<tr>";
                            }
                            echo "<td>".$sql2[1]."</td>";
                            echo "<td>".$sql2[3]."</td>";
                            echo "<td>".$sql2[4]."</td>";
                            echo "<td>".$sql2[7]."</td>";
                            echo "<td>".$sql2[9]."</td>";
                            echo "<td>".$sql2[15]."</td>";
                            echo "<td>".$sql2[6]."</td>";
                            echo "</tr>";
                            
                            $counter+=1;
                            
                        }
                        
                   }
                   ?>
        </table>
                    </div>
</div>
    </form>
    
        
    </div>
</body>
</html>
