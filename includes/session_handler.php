<?php
session_start();
$session_timeout = 30;//SESSION_TIMEOUT; // session timeout in minute
//session_cache_limiter('private');
//session_cache_expire($session_timeout); // php session exipre functionality 

date_default_timezone_set('Asia/Dhaka');

if (isset($_SESSION['TT_LAST_ACTIVITY']) && (time() - $_SESSION['TT_LAST_ACTIVITY'] > ($session_timeout*60))) {
    // last request was more than SESSION_TIMEOUT minates ago
	$_SESSION = array(); // assign session variable with empty array
	unset($_SESSION);
    session_destroy();   // destroy session data in storage
    session_unset();     // unset $_SESSION variable for the runtime
	//session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
	session_start();
	$_SESSION['session_time_out']='yes';
	header("Location: ./index.php");
	exit();
	
}
$_SESSION['TT_LAST_ACTIVITY'] = time(); // update last activity time stamp


?>