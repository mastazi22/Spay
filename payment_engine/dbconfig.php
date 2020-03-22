<?php
$_OS=PHP_OS;
switch($_SERVER["SERVER_NAME"]) {
  case 'dev.shurjomukhi.com':
       require('/etc/shurjodbconfs/dev-payment_engine-db.php');
  break;
  case 'localhost':
       //if($_OS=='WINNT')
		    //require_once("C:\svn\db_configs\dev-payment_engine-db.php");
	   //else
		   // require_once('/etc/shurjodbconfs/payment_engine-db.php');
  break;
  
  default:
       require('/etc/shurjodbconfs/payment_engine-db.php');
  break;
}


?>
