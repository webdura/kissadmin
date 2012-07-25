<?php
include("functions.php");

$howOften = getHowOften();

foreach ($howOften as $key=>$value) {
	$sql = "SELECT * FROM gma_order_repeat WHERE how_often = " . $key ." AND invoiceSentDate =  '0000-00-00 00:00:00' " .
			" AND startDate <= NOW( ) ";
	
	$repeat_rs  = mysql_query($sql);
	while ($repeat_row = mysql_fetch_assoc($repeat_rs)) {
		insertInvoice($repeat_row);
	}
	
	$interval = '';
	switch ($key){
		
		case 1:
			$interval = '1 WEEK';
			break;

		case 2:
			$interval = '15 DAY';
			break;

		case 3:
			$interval = '1 MONTH';
			break;

		case 4:
			$interval = '2 MONTH';
			break;

		case 5:
			$interval = '3 MONTH';
			break;

		case 6:
			$interval = '6 MONTH';
			break;

		case 7:
			$interval = '1 YEAR';
			break;

		case 8:
			$interval = '2 YEAR';
			break;

		
	}
	
	$sql = "SELECT * FROM gma_order_repeat WHERE how_often = " . $key ." AND invoiceSentDate !=  '0000-00-00 00:00:00' " .
			" AND DATE_ADD( DATE(  `invoiceSentDate` ) , INTERVAL " . $interval ." )  <= CURDATE( ) ";
	
	$repeat_rs  = mysql_query($sql);
	while ($repeat_row = mysql_fetch_assoc($repeat_rs)) {
		insertInvoice($repeat_row);
	}
	
	
}




function insertInvoice($repeat_row){

		$invoice_sql = "SELECT gma_company.companyId, companyInvoiceNo FROM gma_company, gma_logins " .
		" WHERE gma_logins.companyId=gma_company.companyId AND " .
		" gma_logins.userId = " . $repeat_row['userId'];
		
		$invoice_rs  = mysql_query($invoice_sql);
		$invoice_row = mysql_fetch_assoc($invoice_rs);
		$invoice_id  = $invoice_row['companyInvoiceNo'];
		$invoice_id = $invoice_id + 1;
		
		$order_sql = "INSERT INTO gma_order SET " .
					  " userId = " . $repeat_row['userId'] . ", " .
					  " invoiceId = " . $invoice_id . ", " .
					  " invoice_amount = " . $repeat_row['invoice_amount'] . ", " .
					  " orderDate = NOW()" ;
					  
		mysql_query($order_sql);
		$orderId = mysql_insert_id();

        $details_sql = "SELECT * FROM gma_order_repeat_details WHERE orderRepeatId=" . $repeat_row['id'];
        $details_rs  = mysql_query($details_sql);
        if(mysql_num_rows($details_rs)>0) {
            while ($details_row = mysql_fetch_assoc($details_rs)) {
            	unset($details_row['orderRepeatId']);
                $order_details_sql = "";
                foreach ($details_row as $key=>$value) {
                    //$order_sql .= ($order_sql!='') ? ', ' : '';
                    $order_details_sql .= "`$key`=".GetSQLValueString($value, 'text').","; 
                }
                $order_details_sql .= "`orderId`='$orderId'";
                
                $order_details_sql = "INSERT INTO gma_order_details SET $order_details_sql";
                mysql_query($order_details_sql);
            }
	
        }
        
        $sql  = "UPDATE gma_order_repeat SET invoiceSentDate=NOW(), sentTotal = (sentTotal + 1) WHERE id=" . $repeat_row['id'];
        mysql_query($sql);

        if($repeat_row['sendMail']=='Y') {
	        $details = invoiceDetails($orderId);        
	        $result  = emailSend('invoice', $details);
	        if($result)  {                  
	            $sql  = "UPDATE gma_order SET sendDate=NOW()WHERE id='$orderId'";
	            mysql_query($sql);
	        }
        }
        
        
        $sql  = "UPDATE gma_company SET companyInvoiceNo='$invoice_id' WHERE companyId=" . $invoice_row['companyId'];
        mysql_query($sql);
	
	
	
}

?>