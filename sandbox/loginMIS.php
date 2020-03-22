<?php 
session_start();
if( $_SESSION['ORDER_DETAILS']['loggedIN'] != true)
{
    header( 'Location: login.php' );
}
require_once 'includes/configure.php';

$userid = $_SESSION['ORDER_DETAILS']['userID'];
//require_once "pagination.php";

        $page= NULL;  
        $tbl_name="sp_epay";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;
	
	
	/* Setup vars for query. */
	$targetpage = "loginMIS.php"; 	//your file name  (the name of this file)
	$limit = 10; 			//how many items to show per page

	if(isset($_REQUEST['page']))
        {
         $page = $_GET['page'];
        }
        
	if($page) 
		$start = ($page - 1) * $limit; 	//first item to display on this page
	else
		$start = 0;		//if no page var is given, set start to 0
	
	

if(isset($_REQUEST['fromdate']) && (!empty($_REQUEST['fromdate'])) && isset($_REQUEST['todate'])&& (!empty($_REQUEST['todate'])) )
{
        
        
       $fromdate=$_REQUEST['fromdate'];
       $todate=$_REQUEST['todate'];
                        
       $fromdate_marge=$fromdate.' 00:00:00';
       $todate_marge=$todate.' 00:00:00';
        
        /* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	
        $query = "select *from sp_epay where intime >='{$fromdate_marge}' and intime <='{$todate_marge}' and uid = '{$userid}'";
	$total_pages = mysql_num_rows(mysqli_query($GLOBALS["___mysqli_sm"], $query));
	
	     
        /* Get data. */
	 
        $sql = "select *from sp_epay where intime >='{$fromdate_marge}' and intime <='{$todate_marge}'  and uid = '{$userid}' ORDER BY intime DESC LIMIT $start, $limit ";
        $sql1=mysqli_query($GLOBALS["___mysqli_sm"], $sql); 
}
else
{
       $fromdate='';
       $todate='';
    /* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	
        $query = "select *from sp_epay where uid = '{$userid}'";
	$total_pages = mysql_num_rows(mysqli_query($GLOBALS["___mysqli_sm"], $query));
	
	
        
        /* Get data. */
	        
        $sql = "select *from sp_epay where uid = '{$userid}' ORDER BY intime DESC LIMIT $start, $limit ";
        $sql1=mysqli_query($GLOBALS["___mysqli_sm"], $sql); 
}
  
	
	/* Setup page vars for display. */
	if ($page == 0) $page = 1;	//if no page var is given, default to 1.
	$prev = $page - 1;		//previous page is page - 1
	$next = $page + 1;		//next page is page + 1

	 $lastpage = ceil($total_pages/$limit);	 //lastpage is = total pages / items per page, rounded up.
	 $lpm1 = $lastpage - 1;	//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\">";

		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?page=$prev & fromdate=$fromdate & todate=$todate\"><< previous</a>";
		else
			$pagination.= "<span class=\"disabled\"><< previous</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1 & fromdate=$fromdate & todate=$todate\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage & fromdate=$fromdate & todate=$todate\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?page=1 & fromdate=$fromdate & todate=$todate\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2 & fromdate=$fromdate & todate=$todate\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1 & fromdate=$fromdate & todate=$todate\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage & fromdate=$fromdate & todate=$todate\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?page=1 & fromdate=$fromdate & todate=$todate\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2 & fromdate=$fromdate & todate=$todate\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter & fromdate=$fromdate & todate=$todate\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"$targetpage?page=$next & fromdate=$fromdate & todate=$todate\">next >></a>";
		else
			$pagination.= "<span class=\"disabled\">next >></span>";
		$pagination.= "</div>\n";		
	}

 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
<?php  require_once 'database.php';
 require_once 'includes/configure.php'; 
   ?>  
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/> 
<meta name="#" content="#"/> 
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<title>..::MIS::..</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Calendar javascript Code</title>
<link rel="stylesheet" type="text/css" href="http://www.jscodes.com/codes/calendar_javascript/demo/css/datePicker.css" />
<script src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery-1.6.1.min.js" type="text/javascript"></script>
<script src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery.datePicker-min.js" type="text/javascript"></script>
<!--[if IE]><script type="text/javascript" src="http://www.jscodes.com/codes/calendar_javascript/demo/js/jquery.bgiframe.min.js"></script><![endif]-->

<script type="text/javascript">
  $(window).ready(function(){
  $('#todate').datePicker({clickInput:true});
  $('#fromdate').datePicker({clickInput:true});
});
</script>

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
                        <!--
			<a href="#"><span>Search by ID</span></a>
			<a href="#"><span>Bank Tx ID</span></a>
			<a  href="reconcilliation.php"><span>Reconcilliation</span></a>	
                        -->
		</div>
<div style="float:right;font-size:15px" class="menu"><a href="logout.php"><span >Logout</span></a></div>
		<div class="content">

			<div class="item">
				<h1 style="color:blue;">Title :Search By Date </h1>
				<p>
				  <fieldset>
                                      <form name="myForm" method="post" action="" id="myForm">
				    <div>
					  <span class="left">Fromdate : 
                                             <input name="fromdate" class="form-login" id="fromdate" title="Date format ( YYYY-MM-DD)" value="" size="30" maxlength="2048" />
                                    </span>		
					  <span class="left">Todate : 
                                              <!input type="text"  name="it" id="date-pick" />
                                               <input name="todate" type="text" id="todate" class="form-login" title="Date format ( YYYY-MM-DD)" value="" size="30" maxlength="2048" />
                                          </span>
					  <span><input type="submit" value="Submit" name="submit"/></span>
					  <div class="clearer"><span></span></div>
				    </div>
                                  </form>
					<br />
					<div>	
					  <!--
                                            <span class="left"><input type="file" /></span>
					  <span><input type="button" value="C&V"/></span>
					  <div class="clearer"><span></span></div>
       -->
				    </div>
                                        <br/>
                                        <div>
                                        <table name="myTable" id="myTable" border="0" width="100%" >
                       <tr class="row">
                           <td>ID</td>
                           <td>Amount</td>
                           <td>Method</td>
                           <td>Bank Name</td>
                           <td>Return URL</td>
                           <td>Remarks</td>
                           <td>Intime</td>
                           <td>Reconciliation</td>
                       </tr>
                   
                   <?php
                   //if(isset($_REQUEST['fromdate']) && isset($_REQUEST['todate']) )
                   //{
                        
                       $counter=0;
                      
    
                        //$sql = "select *from sp_epay where intime >='{$fromdate_marge}' and intime <='{$todate_marge}'";
                        //$sql1=mysqli_query($GLOBALS["___mysqli_sm"], $sql);  
                        
                        
                         while($sql2=  mysql_fetch_row($sql1)){
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
                             echo "<td>".$sql2[16]."</td>";
                            echo "</tr>";
                            
                            $counter+=1;
                            
                        }
                      
                      echo $pagination;  
                   //}
                   
                 
                   ?>
        </table>
                                        </div>
				  </fieldset>
				</p>
			</div>
                    <div><?php  ?></div>
		</div>
		
		<div class="footer">
		
			<span class="left">&copy; 2012 <a href="#">SandBox</a></span>
		
			<span class="right"><a href="#">Designed & Developed</a> by <a href="#">Shurjomukhi</a></span>

			<div class="clearer"><span></span></div>
		
		</div>

	</div>	

</div>

</body>

</html>