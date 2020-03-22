<?php

//define('SESSION_TIMEOUT',30);

class Configure{
	
	public $local_return_url;
	public $mudra_url;
	//public $con_sp;
	public function __construct() {
		switch($_SERVER["SERVER_NAME"]) 
		{
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
				$this->local_return_url="http://localhost/shurjotest/return_url.php";
				$this->dbbl_return_url = "http://localhost/shurjotest/epay/dbbl/success.php";
				$this->mudra_url = "http://localhost/shurjomudra/index.php/register.aspx";
				$this->payment_option_url = "http://localhost/shurjotest/payment_option.php";
		  	break;
		  	// Live site configuration
		  	default:
				$this->local_return_url = "https://shurjotest.com/return_url.php";				
				$this->dbbl_return_url = "https://shurjotest.com/epay/dbbl/success.php";
				$this->mudra_url = "https://shurjotest.com/index.php/register.aspx";
				$this->payment_option_url = "https://shurjotest.com/payment_option.php";
		  	break;
	 	}
	}
	
	public function getConnection()
	{
		switch($_SERVER["SERVER_NAME"]) 
		{
		  	// Development site (e.g. localhost) configuration
		  	case 'php.localhost':
		  		$host="localhost";
				if (PHP_OS == 'WINNT') 
				{
					$dbuser="root";
					$dbpassword="";
				} else 
				{
					$dbuser="sm";
					$dbpassword="";
				}
				$bdname="shurjopaytest";
				$config['local_return_url']="http://localhost/shurjotest/return_url.php";
		  	break;
		  	// Live site configuration
		  	default:
		  		$host="localhost";
				$dbuser="shurjopay";
				$dbpassword="dHxrGC$D4#";
				$bdname="load_test_shurjopay";
				$config['local_return_url']="https://shurjotest.com/return_url.php";
		  	break;
	 	}
	
		//$this->con_sp = mysqli_connect($host,$dbuser,$dbpassword);
		$con = ($GLOBALS["___mysqli_sm"] = mysqli_connect($host, $dbuser, $dbpassword)) or die("Problem occur in connection");  
		if(!$con)
		{
			echo "mysql error";
			die(mysqli_error());
		}

		
		$select_db=mysqli_select_db($con,$bdname);
		mysqli_set_charset($con, "utf8");

		//$select_db=mysql_select_db($bdname,$this->con_sp);
		//mysqli_query($GLOBALS["___mysqli_sm"], 'SET CHARACTER SET utf8');		
		mysqli_query($con,"SET SESSION collation_connection ='utf8_unicode_ci'") or die (mysqli_error($GLOBALS["___mysqli_sm"]));


                 
		if(!$select_db){
			echo "error"; exit ();
			die(mysqli_error($GLOBALS["___mysqli_sm"]));
		}
	}
}

	$db = new Configure();
	$db->getConnection();
	if(isset($_SESSION['ORDER_DETAILS']['userID'])) 
	{
		$sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
		$logo=mysqli_fetch_object($sql);
	}

function countTxBlocking($smUid)
{
	global $db;
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT count(smUid) as cid from shurjopay.sp_epay WHERE smUid= ".mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$smUid)." AND Date(intime) = curdate() AND bank_status = 'SUCCESS'");
	$result= mysqli_fetch_object($sql_query);
	if ($result->cid < 6) 
	{ 
		return "Allow";    //No of transactions allowed for a userid
	} 
	else 
	{
		return "Deny";
	}
		
}
?>
