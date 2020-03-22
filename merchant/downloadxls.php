<?php
$file_location = '/tmp/';
if (PHP_OS == 'WINNT') {
	$file_location = 'C:\svn\tmp\\';
}
$outputxlsfile = "sales_history.xls";
header('Content-disposition: attachment; filename='.$outputxlsfile);
header('Content-type: application/CSV');
readfile($file_location.$outputxlsfile);
exit;
?>
