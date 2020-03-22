<?php
$file_location = '/tmp/';
if (PHP_OS == 'WINNT') {
	$file_location = 'C:\svn\tmp\\';
}
$outputcsvfile = "sales_history.csv";
header('Content-disposition: attachment; filename='.$outputcsvfile);
header('Content-type: application/CSV');
readfile($file_location.$outputcsvfile);
exit;
?>
