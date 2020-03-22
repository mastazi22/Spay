<?php
error_reporting(E_ALL);
	class Configure{
		
		
		public function getConnection(){
			switch($_SERVER["SERVER_NAME"]) {
			  	// Testing site (dev.shurjomukhi.com) configuration
			  	case 'dev.shurjomukhi.com':
			  		$host="localhost";
					$dbuser="devsandbox";
					$dbpassword="s@ndb0xsp";
					$bdname="dev_sandbox";
			 	break;
			  	// Development site (e.g. localhost) configuration
			  	case 'localhost':
			  		$host="localhost";
					$dbuser="root";
					$dbpassword="";
					$bdname="sandbox";
			  	break;
			  	// Live site configuration
			  	default:
			  		$host="localhost";
					$dbuser="rhythm";
					$dbpassword="W4Bc9hPdqcrYM46m";
					$bdname="dev_sandbox";
			  	break;
		 	}
			
			$con=mysql_connect($host,$dbuser,$dbpassword);
			if(!$con){
				die(mysql_error());
			}
			$select_db=mysql_select_db($bdname,$con);
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
