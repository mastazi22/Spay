<?php
$allowedReferer = array (
    "http://me.test.com/xxx/bbb/zmy.php?",
    "https://me.why.com/xxx/bbb/zmy.axpx" ,
    "http://localhost/lab/stockoverflow/"
);

$allowedIP = array (
        "127.0.0.1",
        "192.168.0.1" ,
        "::1"
);

$file = "file.pdf"; // its can also be a value form $_GET
echo $_SERVER ['HTTP_CLIENT_IP'].'=='.$_SERVER ['HTTP_X_FORWARDED_FOR'].'=='.$_SERVER ['REMOTE_ADDR'];
if (! in_array ( quickIP (), $allowedIP )) {
    die ( "IP LockDown : " . quickIP () );
}

if (! isset ( $_SERVER ['HTTP_REFERER'] )) {
    die ( "Missing Referer" );
}

if (! in_array ($_SERVER ['HTTP_REFERER'] , $allowedReferer)) {
    die ( "Referer Lockdown "  . $_SERVER ['HTTP_REFERER']);
}

$array = parse_url ( $_SERVER ['HTTP_REFERER'] );

header ( 'Content-type: application/pdf' );
header ( 'Content-Disposition: attachment; filename="' . $file . '"' );
readfile ( $file );

function quickIP() {
    return (empty ( $_SERVER ['HTTP_CLIENT_IP'] ) ? (empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER ['REMOTE_ADDR'] : $_SERVER ['HTTP_X_FORWARDED_FOR']) : $_SERVER ['HTTP_CLIENT_IP']);
}
?>
