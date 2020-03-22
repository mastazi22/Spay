<?php
define('SESSION_TIMEOUT',30);
class Configure{
	
	public $local_return_url;
	public $mudra_url;
	public $con_sp;
	public function __construct() {
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
				$this->local_return_url = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/return_url.php";
				$this->dbbl_return_url = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjopay/epay/dbbl/success.php";
				$this->mudra_url = "http://".$_SERVER["SERVER_NAME"]."/shurjorajjo/shurjomudra/index.php/register.aspx";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
				$this->local_return_url="http://".$_SERVER["SERVER_NAME"]."/shurjopaysr/return_url.php";
				$this->dbbl_return_url = "http://".$_SERVER["SERVER_NAME"]."/shurjopaysr/epay/dbbl/success.php";
				$this->mudra_url = "http://".$_SERVER["SERVER_NAME"]."/shurjomudra/index.php/register.aspx";
		  	break;
		  	// Live site configuration
		  	default:
				$this->local_return_url="https://".$_SERVER["SERVER_NAME"]."/return_url.php";
				$this->dbbl_return_url = "https://".$_SERVER["SERVER_NAME"]."/epay/dbbl/success.php";
				$this->mudra_url = "https://".$_SERVER["SERVER_NAME"]."/index.php/register.aspx";
		  	break;
	 	}
	}
	public function getConnection(){
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
		  		$host="localhost";
				$dbuser="dev_shurjopay";
				$dbpassword="devshurjoPay";
				$bdname="shurjopay";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
		  		$host="localhost";
				$dbuser="root";
				$dbpassword="";
				$bdname="shurjopay";
				$config['local_return_url']="http://".$_SERVER["SERVER_NAME"]."/shurjopay/return_url.php";
		  	break;
		  	// Live site configuration
		  	default:
		  		$host="localhost";
				$dbuser="smshurjopaySIU";
				$dbpassword="5cmYMSYbAzRdaYW2";
				$bdname="shurjopay";
				$config['local_return_url']="https://".$_SERVER["SERVER_NAME"]."/return_url.php";
		  	break;
	 	}
		
		$this->con_sp=mysql_connect($host,$dbuser,$dbpassword);
		if(!$this->con_sp){
			die(mysql_error());
		}
		$select_db=mysql_select_db($bdname,$this->con_sp);
		mysqli_query($GLOBALS["___mysqli_sm"], 'SET CHARACTER SET utf8');
		mysqli_query($GLOBALS["___mysqli_sm"], "SET SESSION collation_connection ='utf8_unicode_ci'") or die (mysql_error());
        
		if(!$select_db){
			die(mysql_error());
		}
	}
}	
$db = new Configure();
$db->getConnection();
?>
