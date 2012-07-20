<?php
$clear    = (isset($_REQUEST['clear']) && $_REQUEST['clear']!='') ? $_REQUEST['clear'] : '0';

if ($clear > 0) {
	if (isset($_SESSION['sendInvoiceId']) && $_SESSION['sendInvoiceId'] > 0){
		
    	$del_sql = "DELETE FROM gma_send_invoices WHERE id = " . $_SESSION['sendInvoiceId'];
    	mysql_query($del_sql);			  

    	$del_sql = "DELETE FROM gma_send_invoice_details WHERE sendInvoiceId = " . $_SESSION['sendInvoiceId'];
    	mysql_query($del_sql);			  
		
    	$_SESSION['sendInvoiceId']='';
    	unset($_SESSION['sendInvoiceId']);
    	
    	header("Location: send_invoices.php?action=step3 ");
	}
}


if(isset($_FILES) && $_FILES["csvfile"]["size"] > 0 && !isset($_SESSION['sendInvoiceId'])){
	 if (($handle = fopen($_FILES["csvfile"]["tmp_name"], "r")) !== FALSE) {
	 	$row = 0;
	 	$title = '';
	 	$invoicePath = 'inv_' . strtotime("now");
    	$invoice_sql .= "INSERT INTO gma_send_invoices SET " .
    				  " companyId = " . GetSQLValueString($_SESSION['ses_companyId'], 'int') ."," . 
    				  " userId = " . GetSQLValueString($_SESSION['ses_userId'], 'int') ."," .
    				  " title = " . GetSQLValueString($title, 'text') ."," .
    				  " invoicePath = " . GetSQLValueString($invoicePath, 'text') ."," .
    				  " createdDate = NOW()";
    	mysql_query($invoice_sql);			  
    	$sendInvoiceId = mysql_insert_id();			  
    	$_SESSION['sendInvoiceId'] = $sendInvoiceId;
    				  
	    $invoice_details_sql = '';
	    
	    
	    $invoice_details_sqls = array();
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        if($row > 0) {
	        	$invoice_details_sqls[] = 
	        				  "(". GetSQLValueString($sendInvoiceId, 'int') ."," . 
	        				  GetSQLValueString($data[0], 'text') ."," .
	        				  GetSQLValueString($data[1], 'text') .","  .
	        				  GetSQLValueString($data[2], 'text') .","  .
	        				  GetSQLValueString($data[3], 'text') .")";
	        }
	        $row++;

	    }
	    if(count($invoice_details_sqls)>0) {
	    	$invoice_details_sql = "INSERT INTO gma_send_invoice_details(sendInvoiceId,firstName,lastName,email,fileName) VALUES ".implode(',', $invoice_details_sqls);
        	mysql_query($invoice_details_sql) or die(mysql_error());	
	    }
    			  
	    fclose($handle);
	}
		
}		
if (isset($_SESSION['sendInvoiceId']) && $_SESSION['sendInvoiceId'] > 0){
	$invoice_sql = "SELECT * FROM gma_send_invoice_details WHERE sendInvoiceId = " . $_SESSION['sendInvoiceId'];
   	$send_invoice = mysql_query($invoice_sql);
   	$userDetailsTable = '<table class="list" width="100%" cellspacing="0" cellpadding="0">';
	 	$userDetailsTable .= '<tr>';
		 	$userDetailsTable .= '<th>Firs tName </th>';
		 	$userDetailsTable .= '<th>Last Name</th>';
		 	$userDetailsTable .= '<th>Email</th>';
		 	$userDetailsTable .= '<th>File Name</th>';
	 	$userDetailsTable .= '</tr>';
	 $j=0;
	 while($send_invoice_row = mysql_fetch_array($send_invoice)){
            $class     = ((($j++)%2)==1) ? 'altrow' : '';
	 	$userDetailsTable .= '<tr class="'.$class.'">';
		 	$userDetailsTable .= '<td>' . $send_invoice_row['firstName'] . '</td>';
		 	$userDetailsTable .= '<td>' . $send_invoice_row['lastName'] . '</td>';
		 	$userDetailsTable .= '<td>' . $send_invoice_row['email'] . '</td>';
		 	$userDetailsTable .= '<td>' . $send_invoice_row['fileName'] . '</td>';
	 	$userDetailsTable .= '</tr>';
	 }
   	$userDetailsTable .= '<table>';
	echo $userDetailsTable;	      

    	
	echo '<a href="send_invoices.php?action=step3&clear=1" > Clear List and Upload </a>';
	echo '<a href="bulk_upload_invoices.php" >Proceed to Next Step</a> </div>';
	
}
else {
?>

    <form method="POST" id="uploadCSV" name='uploadCSV' enctype="multipart/form-data">
    <input type="file" name="csvfile" >    
    <input type="submit" value="Upload">
	</form>
<?php } ?>