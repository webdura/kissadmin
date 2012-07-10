<?php
include("functions.php");

// due_reminder, friendly_reminder, overdue_reminder, suspension_warning

$company_sql = "SELECT * FROM gma_company, gma_accounts WHERE gma_accounts.companyId=gma_company.companyId AND status=1 ORDER BY gma_company.companyId ASC";
$company_rs  = mysql_query($company_sql);
while ($company_row = mysql_fetch_assoc($company_rs)) {
	   $companyId         = $company_row['companyId'];
	   $paymentDue        = $company_row['paymentDue'];
	   $overdueNotice     = $company_row['overdueNotice'];
	   $suspensionWarning = $company_row['suspensionWarning'];
	   
    $invoice_sql = "SELECT * FROM gma_order, gma_logins WHERE gma_logins.userId=gma_order.userId AND gma_logins.companyId='$companyId'";
    
    // Payment Due
    $order_sql = "$invoice_sql AND DATE_ADD(DATE(orderDate), INTERVAL $paymentDue DAY)=CURDATE()";
    // echo "$order_sql<br>";
    $order_rs  = mysql_query($order_sql);
    while ($order_row = mysql_fetch_assoc($order_rs)) {
        $orderId = $order_row['id'];
        $details = array();
        $details = invoiceDetails($orderId);
        
        emailSend('due_reminder', $details, $companyId);
    }
    
    // Overdue Notice
    $order_sql = "$invoice_sql AND DATE_ADD(DATE(orderDate), INTERVAL $overdueNotice DAY)=CURDATE()";
    // echo "$order_sql<br>";
    $order_rs  = mysql_query($order_sql);
    while ($order_row = mysql_fetch_assoc($order_rs)) {
        $orderId = $order_row['id'];
        $details = array();
        $details = invoiceDetails($orderId);
        
        emailSend('overdue_reminder', $details, $companyId);
    }
    
    // Suspension Warning
    $order_sql = "$invoice_sql AND DATE_ADD(DATE(orderDate), INTERVAL $suspensionWarning DAY)=CURDATE()";
    // echo "$order_sql<br>";
    $order_rs  = mysql_query($order_sql);
    while ($order_row = mysql_fetch_assoc($order_rs)) {
        $orderId = $order_row['id'];
        $details = array();
        $details = invoiceDetails($orderId);
        
        emailSend('suspension_warning', $details, $companyId);
    }
}
?>