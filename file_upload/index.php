<?php
error_reporting(E_ALL | E_STRICT);

include(dirname($_SERVER['SCRIPT_FILENAME']) . "/../config.php");
$fp = fopen("test.txt", "w");
fwrite($fp, $_SESSION['displayName']);

    	$inv_sql = "SELECT invoicePath FROM gma_send_invoices WHERE id =" . $_SESSION['sendInvoiceId'];
fwrite($fp, $inv_sql);
    	$inv_rs = mysql_query($inv_sql);
    	if (mysql_num_rows($inv_rs) > 0) {
	    	$inv_row = mysql_fetch_array($inv_rs);	
	    	$path = trim($inv_row['invoicePath']);
	    	if($path != '') {
	    		$path .= "/";
	    		
	    		if(!is_dir(dirname($_SERVER['SCRIPT_FILENAME']).'/invoices/'.$path)){
	    			mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/invoices/'.$path, 0777);
	    		}
	    		
	    		
	    	}	
    	}	  

    	
fwrite($fp, dirname($_SERVER['SCRIPT_FILENAME']).'/invoices/'.$path);
fclose($fp);

require('upload.class.php');
$options = array('upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/invoices/'.$path);
$upload_handler = new UploadHandler($options);

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        break;
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            $upload_handler->delete();
        } else {
            $upload_handler->post();
        }
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
}
