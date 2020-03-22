<?php
include ("../../includes/session_handler.php");
include("../../includes/configure.php");
include ("../../includes/qcash.php");

$qcash = new Qcash();
$qcash->qcashCanceled();	

header("Location: ".$db->local_return_url);

?>