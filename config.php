<?php
ob_start();
error_reporting(1);
session_start();

//$username  = "gnetmail_sys0510";
//$password  = "uPJSo-}BNS;E";
//$localhost = "localhost";
//$dbname    = "gnetmail_db5722";
if($_SERVER['HTTP_HOST']=='localhost') { 
    $localhost = "localhost";
    $username  = "root";
    $password  = "";
    $dbname    = "gnetmail_db5722";
    $SITE_URL = 'http://localhost/gnetmail/admin/';
} else {
    $localhost = "localhost";
    $username  = "gnetmail_sys0510";
    $password  = "uPJSo-}BNS;E";
    $dbname    = "gnetmail_db2209";
    $SITE_URL = 'http://gnetmail.co.za/beta/';
}

$con = mysql_connect($localhost,$username,$password) or die(mysql_error());
$db  = mysql_select_db($dbname,$con);

	$_SESSION['perpageval'] = 15;
?>