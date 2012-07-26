<?php
$invoice_btn = 'selected';    
include("config.php");
include("header.php");
    
//echo '<pre>'; print_r($_SESSION); exit;
$action = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'step1';
if(!isset($_SESSION['send_invoice']) && $action!='step1') $action = 'step1';
if(isset($_SESSION['send_invoice']) && $action=='step1') session_unregister('send_invoice');;
if(isset($_SESSION['send_invoice'])) $folder = $_SESSION['send_invoice'];

$folder_path  = "images/invoice/";
$default_file = "send_invoices.php";
//echo $folder_path;

switch ($action)
{        
    case 'step2':
        $file_name = 'send_invoices_step2.php';
        break;
    
    case 'step3':
        $file_name = 'send_invoices_step3.php';
        break;
    
    case 'step4':
        $email_sql = "SELECT * FROM gma_emails WHERE companyId=".GetSQLValueString($ses_companyId, 'text')." AND template='send_custom_invoices'";
        $email_rs  = mysql_query($email_sql);
        $email_row = mysql_fetch_assoc($email_rs);
        
        $file_name = 'send_invoices_step4.php';
        break;
    
    case 'step1':
    default:
        $file_name = 'send_invoices_step1.php';
        if(isset($_POST['action']) && $_POST['action']=='step1') {
            $_SESSION['send_invoice'] = $folder = $filename = time();
            if($_FILES['csvfile']['size']>0)
            {
                $tmp_name = $_FILES['csvfile']['tmp_name'];
                
                $folder = "$folder_path$folder";
                mkdir($folder);
                chmod($folder, 777);
                
                $filename = "$folder_path$filename.csv";
                
                copy($tmp_name, $filename);
                
                header("Location: $default_file?action=step2");
                exit;
            } else {
               header("Location: $default_file?msg=Invalid file");
               exit;
            }
        }
        break;
}

$page_title   = 'Manual Invoices & Statements';
include_once('sub_header.php');
include_once($file_name);
include("footer.php");
?>