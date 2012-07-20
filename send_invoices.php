<?php
$invoice_btn = 'selected';    
include("config.php");
if(isset($_REQUEST['popup']))
    include("functions.php");
else 
    include("header.php");
    
$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'step1';
switch ($action)
{        
    case 'step2':
    	include_once('send_invoices_step2.php');
        break;
        
    case 'step3':
    	include_once('send_invoices_step3.php');
        break;
        
    case 'step4':
    	include_once('send_invoices_step4.php');
        break;
        
    default:
    	include_once('send_invoices_step1.php');
        break;
}
    
?>
<?
include("footer.php");
?>