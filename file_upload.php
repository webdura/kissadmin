<?php
include("config.php");

$folder_path = "images/invoice/{$_SESSION['send_invoice']}/";
$options     = array('upload_dir' => $folder_path, 'accept_file_types'=>'/.+pdf/i', 'script_url'=>'file_upload.php');

require('upload.class.php');
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