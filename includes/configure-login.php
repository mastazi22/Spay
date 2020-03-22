<?php
class ConfigureLogin{	
	
	public $con_login;
	public function __construct() {
		//constructor will go here
	}
	public function getConnectionLogin(){
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
		  		$host="localhost";
				$dbuser="shurjologinapi";
				$dbpassword="shurj@log1ap1";
				$bdname="dev_logindb";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
		  		$host="localhost";
				$dbuser="root";
				$dbpassword="";
				$bdname="logindb";
			break;
		  	// Live site configuration
		  	default:
		  		$host="localhost";
				$dbuser="smloginsiu";
				$dbpassword="YYUAJVfUUcXqwrVD";
				$bdname="logindb";
			break;
	 	}
		
		$this->con_login=mysql_connect($host,$dbuser,$dbpassword,true);
		if(!$this->con_login){
			die(mysql_error());
		}
		
		$select_db=mysql_select_db($bdname,$this->con_login);
		mysqli_query($GLOBALS["___mysqli_sm"], 'SET CHARACTER SET utf8');
		mysqli_query($GLOBALS["___mysqli_sm"], "SET SESSION collation_connection ='utf8_unicode_ci'") or die (mysql_error());
        
		if(!$select_db){
			die(mysql_error());
		}
	}
}	
$dblogin = new ConfigureLogin();
$dblogin->getConnectionLogin();
?>
