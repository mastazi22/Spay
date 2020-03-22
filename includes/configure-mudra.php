<?php
class ConfigureMudra{	
	
	public $con_mudra;
	
	public function __construct() {
		//constructor will go here
	}
	public function getConnectionMudra(){
		switch($_SERVER["SERVER_NAME"]) {
		  	// Testing site (dev.shurjomukhi.com) configuration
		  	case 'dev.shurjomukhi.com':
		  		$host="localhost";
				$dbuser="shurjomudra";
				$dbpassword="Mudra@786";
				$bdname="dev_shurjomudra";
		 	break;
		  	// Development site (e.g. localhost) configuration
		  	case 'localhost':
		  		$host="localhost";
				$dbuser="root";
				$dbpassword="";
				$bdname="dev_shurjomudra";
			break;
		  	// Live site configuration
		  	default:
		  		$host="localhost";
				$dbuser="smudraSIU";
				$dbpassword="XnBzXwDtdLhKRYDL";
				$bdname="shurjomudra";
			break;
	 	}
		
		$this->con_mudra=mysql_connect($host,$dbuser,$dbpassword,true);
		if(!$this->con_mudra){
			die(mysql_error());
		}
		$select_db=mysql_select_db($bdname,$this->con_mudra);
		mysqli_query($GLOBALS["___mysqli_sm"], 'SET CHARACTER SET utf8');
		mysqli_query($GLOBALS["___mysqli_sm"], "SET SESSION collation_connection ='utf8_unicode_ci'") or die (mysql_error());
        
		if(!$select_db){
			die(mysql_error());
		}
	}
}	
$dbmudra = new ConfigureMudra();
$dbmudra->getConnectionMudra();
?>
