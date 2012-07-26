<?php
$invoice_btn = 'selected';    
include("config.php");
include("header.php");
    
$action = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'step1';

switch ($action)
{        
    case 'step2':
        $file_name = 'send_invoices_step2.php';
        break;
    
    case 'step3':
        $file_name = 'send_invoices_step3.php';
        break;
    
    case 'step4':
        $file_name = 'send_invoices_step4.php';
        break;
    
    default:
        $file_name = 'send_invoices_step2.php';
        break;
}

$page_title   = 'Manual Invoices & Statements';
include_once('sub_header.php');
include_once($file_name);
include("footer.php");
?>