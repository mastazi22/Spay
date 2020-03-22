<?php

// Gmail SMTP Auth details: (username, password)
$gmail_smtp_auth = array("email@domain.com", "password");

// Shurjo Payment Engine Library (API) location: spelib_path
switch($_SERVER["SERVER_NAME"]) {
  // Testing site (dev.shurjomukhi.com) configuration
  case 'dev.shurjomukhi.com':
    define('ENVIRONMENT', 'testing');
    error_reporting(E_ALL | E_STRICT);
    define('SPELIB_PATH',       '/usr/local/lib/shurjolib-dev/payment_engine/paymentEngine.php');
    define('EMAIL_LIB_PATH',	'/usr/local/lib/SwiftMailer/lib/swift_required.php');
    define('APP_URLS',		'/etc/shurjomukhi/smpe_app_urls.php');
    define('EPAY_LOG',		'/var/log/shurjomukhi/epay.log');
  break;
  // Development site (e.g. localhost) configuration
  case 'localhost':
    define('ENVIRONMENT', 'development');
    error_reporting(E_ALL | E_STRICT);
    if ( PHP_OS == 'WINNT' ) {	// Windows workstations
      define('SPELIB_PATH',	'C:\svn\shurjolib\payment_engine\paymentEngine.php');
      define('EMAIL_LIB_PATH',	'C:\svn\SwiftMailer\lib\swift_required.php');
      define('APP_URLS',	'C:\svn\configs\smpe_app_urls.php');
      define('EPAY_LOG',	'C:\svn\log\epay.log');
    } else {			// Linux workstations
      define('SPELIB_PATH',       '/usr/local/lib/shurjolib/payment_engine/paymentEngine.php');
      define('EMAIL_LIB_PATH',	'/usr/local/lib/SwiftMailer/lib/swift_required.php');
      define('APP_URLS',	'/etc/shurjomukhi/smpe_app_urls.php');
      define('EPAY_LOG',	'/var/log/shurjomukhi/epay.log');
    }
  break;
  // Live site configuration
  default:
    define('ENVIRONMENT', 'production');
    error_reporting(0);
    define('SPELIB_PATH',       '/usr/local/lib/shurjolib/payment_engine/paymentEngine.php');
    define('EMAIL_LIB_PATH',	'/usr/local/lib/SwiftMailer/lib/swift_required.php');
    define('APP_URLS',		'/etc/shurjomukhi/smpe_app_urls.php');
    define('EPAY_LOG',		'/var/log/shurjomukhi/epay.log');
  break;
}

?>

