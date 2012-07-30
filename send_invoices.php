<?php
$invoice_btn = 'selected';    
include("config.php");
include("header.php");
    
$action = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'step1';
if(!isset($_SESSION['send_invoice']) && $action!='step1') $action = 'step1';
if(isset($_SESSION['send_invoice']) && $action=='step1') session_unregister('send_invoice');;
if(isset($_SESSION['send_invoice'])) $folder = $_SESSION['send_invoice'];

$folder_path  = "images/invoice/";
$default_file = "send_invoices.php";

switch ($action)
{        
    case 'step2':
        $file_name = 'send_invoices_step2.php';
        break;
    
    case 'step3':
        if(isset($_POST['task']) && $_POST['task']=='update') {
            session_unregister('invoice_users');
            unset($_POST['action']);
            unset($_POST['task']);
            unset($_POST['back']);
            unset($_POST['sbmt']);
            for($i=0;$i<$_POST['rows'];$i++) {
                $_POST['file'][$i] = (isset($_POST['file'][$i])) ? 1 : 0;
                $_POST['send'][$i] = (isset($_POST['send'][$i])) ? 1 : 0;
            }
            $_SESSION['invoice_users'] = $_POST;
            
            header("Location: $default_file?action=step4");
            exit;
        }
        //echo '<pre>'; print_r($_SESSION['invoice_users']); exit;
        
        $file_name = 'send_invoices_step3.php';
        
        $pdf_files = readFiles("$folder_path$folder/");
        
        $filename  = "$folder_path$folder.csv";
        $csv_file  = fopen($filename, 'r');
        
        break;
    
    case 'step4':
        if(isset($_POST['task']) && $_POST['task']=='update') {
            $subject = GetSQLValueString($_POST['subject'], 'text');
            $content = GetSQLValueString($_POST['content'], 'text');
            $invoicePath = GetSQLValueString($_SESSION['send_invoice'], 'text');
            
            $send_invoice_sql = "INSERT INTO gma_send_invoices SET companyId='$ses_companyId',userId='$ses_userId',subject=$subject,content=$content,invoicePath=$invoicePath,createdDate=NOW()";
            mysql_query($send_invoice_sql);
            $sendInvoiceId = mysql_insert_id();
            
            foreach ($_SESSION['invoice_users']['userId'] as $key=>$userId) {
                $fileName    = GetSQLValueString($_SESSION['invoice_users']['filename'][$key], 'text');
                $send_flag   = GetSQLValueString($_SESSION['invoice_users']['send'][$key], 'int');
                $file_flag   = GetSQLValueString($_SESSION['invoice_users']['file'][$key], 'int');
                $send_status = 0;
                
                if($userId>0) {
                    $send_invoice_details_sql = "INSERT INTO gma_send_invoice_details SET sendInvoiceId='$sendInvoiceId',userId='$userId',fileName=$fileName,send_flag=$send_flag,file_flag=$file_flag,send_status=$send_status";
                    mysql_query($send_invoice_details_sql);
                }
            }
            
            session_unregister('send_invoice');
            session_unregister('invoice_users');
            header("Location: $default_file?msg=Details successfully added !");
            exit;
        }
        $email_sql = "SELECT * FROM gma_emails WHERE companyId=".GetSQLValueString($ses_companyId, 'text')." AND template='send_custom_invoices'";
        $email_rs  = mysql_query($email_sql);
        $email_row = mysql_fetch_assoc($email_rs);
        
        $file_name = 'send_invoices_step4.php';
        break;
    
    case 'step1':
    default:
        $file_name = 'send_invoices_step1.php';
        if(isset($_POST['action']) && $_POST['action']=='step1') {
            $_SESSION['send_invoice']  = $folder = $filename = time();
            $_SESSION['invoice_users'] = array();
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