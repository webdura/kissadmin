<?php
$invoice_btn = 'selected';
if(isset($_REQUEST['popup']))
    include("functions.php");
else 
    include("header.php");
    
include("config.php");

if( strtolower(trim($_SESSION['ses_userType']))=='client'){
	if (isset($_GET['orderId']) && $_GET['orderId'] > 0) {
		
	   $order_sql = "SELECT count(id) AS orderCnt FROM gma_order WHERE gma_order.userId = " . $_SESSION['ses_userId'] .
	    " AND gma_order.id = " . $_GET['orderId'] ;
	    $order_rs  = mysql_query($order_sql);
		$order_row_count = mysql_fetch_assoc($order_rs);
		$order_row_count = $order_row_count['orderCnt'];
		if ($order_row_count == 0) {
			$smsg = "Invalid Request";
            return header("Location: invoices.php?msg=$smsg");
            exit;
		}
	}
} else if( strtolower(trim($_SESSION['ses_userType']))=='super_admin'){
	if (isset($_GET['orderId']) && $_GET['orderId'] > 0) {
		
	   $order_sql = "SELECT count(id) AS orderCnt FROM gma_order, gma_logins ".
	   " WHERE gma_order.userId = gma_logins.userId " .
	   " AND gma_logins.companyId = " . $_SESSION['ses_companyId'] .
	   " AND gma_order.id = " . $_GET['orderId'] ; 
	    $order_rs  = mysql_query($order_sql);
		$order_row_count = mysql_fetch_assoc($order_rs);
		$order_row_count = $order_row_count['orderCnt'];
		if ($order_row_count == 0) {
			$smsg = "Invalid Request";
            return header("Location: invoices.php?msg=$smsg");
            exit;
		}
	}
	
	
}


$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$perPage   = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum   = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;
$srchtxt   = (trim($_REQUEST['srchtxt'])!='') ? trim($_REQUEST['srchtxt']) : '';
$links     = '';
$userId    = (isset($_REQUEST['userId']) && $_REQUEST['userId']>0) ? $_REQUEST['userId'] : 0;
$orderId   = (isset($_REQUEST['orderId']) && $_REQUEST['orderId']>0) ? $_REQUEST['orderId'] : 0;
$userTypes = userTypes('', 0, 1);
$userTypes = "'".implode("', '", $userTypes)."'";

$allGroups = $allServices = array();
$group_sql = "SELECT * FROM gma_groups WHERE status=1 AND companyId='$ses_companyId' ORDER BY `order` ASC"; 
$group_rs  = mysql_query($group_sql);
while($group_row = mysql_fetch_assoc($group_rs))
{
    $group_id = $group_row['id'];
    $services = array();
    
    $service_sql = "SELECT * FROM `gma_services` WHERE group_id='$group_id' ORDER BY `order`,`service_name` ASC";
    $service_rs  = mysql_query($service_sql);
    while ($service_row = mysql_fetch_assoc($service_rs))
    {
        $services[$service_row['id']]    = $service_row;
        $allServices[$service_row['id']] = $service_row;
    }
    $group_row['services'] = $services;
    $allGroups[$group_id] = $group_row;
}
// echo '<pre>'; print_r($allGroups); exit;

switch ($action)
{
    case 'add':
    case 'edit':
        $title = ($action='add') ? 'Add Details' : 'Edit Details';
        
        if((isset($_REQUEST['sendMail']) || isset($_REQUEST['save'])))
        {
            // echo '<pre>'; print_r($_REQUEST); exit;
            $invoice_sql = "SELECT invoiceno FROM gm_last_invoice";
            $invoice_rs  = mysql_query($invoice_sql);
            $invoice_row = mysql_fetch_assoc($invoice_rs);
            $invoice_id  = $invoice_row['invoiceno'];
            
            $orderDate    = date('Y-m-d H:i:s');
            $userId       = $_REQUEST['userId'];
            $order_number = $_REQUEST['order_number'];
            $invoiceId = 0;
            if($_REQUEST['editInvoice']!='')
            {
                $order_sql = "SELECT * FROM gma_order WHERE id='$orderId'";
                $order_rs  = mysql_query($order_sql);
                if(mysql_num_rows($order_rs)>0)
                {
                    $order_row = mysql_fetch_array($order_rs);
                    $invoiceId = $order_row['invoiceId'];
                }
                else 
                    $orderId = 0;
            }
            if($invoiceId==0)
            {
                $invoice_id = $invoice_id + 1;
                $invoiceId  = $invoice_id;
            }
            
            mysql_query("DELETE FROM gma_order_details WHERE orderId='$orderId' AND orderId>0");    
            if($orderId==0)
            {
                $order_sql = "INSERT INTO gma_order SET userId='$userId',invoiceId='$invoiceId',order_number='$order_number',orderDate='$orderDate'";
                mysql_query($order_sql);
                $orderId = mysql_insert_id();
            }
            
            $invoice_amount = $total = 0;
            foreach ($_REQUEST['service_id'] as $key=>$service_group_id)
            {
                if($key>0 )
                {
                    $request     = explode('_', $service_group_id);
                    $service_id  = $request[0];
                    $group_id    = $request[1];
                    
                    $serviceName = ($service_id>0) ? $allServices[$service_id]['service_name'] : $_REQUEST['service_name'][$key];
                    $service_id  = ($service_id>0) ? $service_id : 0;
                    $cost        = $_REQUEST['cost'][$key];
                    $quantity    = $_REQUEST['quantity'][$key];
                    $discount    = $_REQUEST['discount'][$key];
                    $amount      = $_REQUEST['amount'][$key];
                    
                    $order_sql = "INSERT INTO gma_order_details SET orderId='$orderId',group_id='$group_id',service_id='$service_id',serviceName='$serviceName',cost='$cost',quantity='$quantity',discount='$discount',amount='$amount'";
                    mysql_query($order_sql);
                    
                    $invoice_amount = $invoice_amount + $amount;
                }
            }
            $order_sql = "UPDATE gma_order SET userId='$userId',order_number='$order_number',orderDate='$orderDate',invoice_amount='$invoice_amount' WHERE id='$orderId'";
            $order_sql = "UPDATE gma_order SET userId='$userId',order_number='$order_number',invoice_amount='$invoice_amount' WHERE id='$orderId'";
            mysql_query($order_sql);
            
            $smsg = ($orderId>0) ? "updated" : "added";
            $sql  = "UPDATE gm_last_invoice SET invoiceno='$invoice_id'";
            mysql_query($sql);
        
            $smsg = "Quotation added successfully";    
            if(isset($_REQUEST['sendMail']))
            {	
                $details = invoiceDetails($orderId);
                $result  = emailSend('invoice', $details);
                if($result){
                    $smsg = "Invoice added and mail sent successfully";  
		            $sql  = "UPDATE gma_order SET sendDate=NOW()WHERE id='$orderId'";
		            mysql_query($sql);
                }              
            }
            return header("Location: invoices.php?msg=$smsg");
            exit;
        }
        
        $total = 0;
        if($orderId>0)
        {
            $order_sql = "SELECT * FROM gma_order WHERE id='$orderId'";
            $order_rs  = mysql_query($order_sql);
            $order_row = mysql_fetch_assoc($order_rs);
            $userId    = $order_row['userId'];
            $total     = $order_row['invoice_amount'];
            $orderNo	= $order_row['order_number'];
             
            $order_details     = array();
            $order_detail_sql  = "SELECT * FROM gma_order_details WHERE orderId='$orderId'";
            $order_detail_rs   = mysql_query($order_detail_sql);
            while($order_detail_row = mysql_fetch_assoc($order_detail_rs))
            {
                $service_id = ($order_detail_row['service_id']>0) ? $order_detail_row['service_id'] : 0;
                
                if($service_id>0)
                    $order_details[$order_detail_row['group_id']][$service_id] = $order_detail_row;
                else
                    $order_details[$order_detail_row['group_id']][] = $order_detail_row;
            }
            foreach ($order_details as $group_id=>$orders)
            {
                $allGroups[$group_id]['orders'] = $orders;
            }
            //echo '<pre>'; print_r($allGroups); exit;
        }
        
        $user_discount = array();
        $discount_sql  = "SELECT * FROM gma_user_discount WHERE userId='$userId'";
        $discount_rs   = mysql_query($discount_sql);
        while($discount_row = mysql_fetch_assoc($discount_rs))
        {
            $user_discount[$discount_row['group_id']] = $discount_row['discount'];
        }
        
        $user_sql = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId AND  userType IN ($userTypes) AND companyId='$ses_companyId' GROUP BY userName ORDER BY businessName,userName ASC"; 
        $user_rs  = mysql_query($user_sql);
        
        break;
        
    case 'view':
        $details = invoiceDetails($orderId);
        $details = emailSend('invoice', $details, null, 1);
        echo "<div align='center'>$details</div>";
        exit;
        break;
        
    case 'delete':
        $order_sql = "SELECT * FROM gma_order,gma_logins WHERE gma_logins.userId=gma_order.userId AND gma_logins.companyId='$ses_companyId' AND id='$orderId'";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)!=1)
        {
            header("Location: invoices.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_order_details WHERE orderId IN (SELECT id FROM gma_order WHERE id='$orderId')";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_order WHERE id='$orderId'";
        mysql_query($sql);
        
        header("Location: invoices.php?d");        
        break;
        
    case 'deleteall':
        $orderId    = implode(',', $_REQUEST['delete']);
        $order_sql = "SELECT * FROM gma_order,gma_logins WHERE gma_logins.userId=gma_order.userId AND gma_logins.companyId='$ses_companyId' AND id IN ($orderId)";
        $orderId   = 0;
        $order_rs   = mysql_query($order_sql);
        while($order_row = mysql_fetch_assoc($order_rs))
        {
            $orderId .= ','.$order_row['id'];
        }
        if($orderId=='0')
        {
            header("Location: invoices.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_order_details WHERE orderId IN (SELECT id FROM gma_order WHERE id IN ($orderId))";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_order WHERE id IN ($orderId)";
        mysql_query($sql);
        
        header("Location: invoices.php?d");        
        break;
        
    case 'resendMail':
        $details = invoiceDetails($orderId);        
        $result  = emailSend('invoice', $details);
        if($result)  {                  
            $sql  = "UPDATE gma_order SET sendDate=NOW()WHERE id='$orderId'";
            mysql_query($sql);
            $smsg = "Invoice mail send successfully";
        }
                    
        return header("Location: invoices.php?msg=$smsg");
        exit;
        break;
        
    case 'delete':
        header("Location: services.php?d");        
        break;
        
    default:
        $action  = 'list';

        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY invoiceId DESC ';
        
        $order_sql   = ($srchtxt!='') ? "(userName LIKE '$srchtxt%' OR invoiceId LIKE '$srchtxt%')" : '1';
        $order_sql  .= ($ses_loginType=='user') ? " AND gma_order.userId='$ses_userId'" : '';
        $order_sql  .= " AND companyId='$ses_companyId'";
        $order_sql   = "SELECT gma_order.*,businessName,DATE_ADD(orderDate, INTERVAL 7 HOUR) AS orderDate FROM gma_order LEFT JOIN gma_logins ON gma_order.userId=gma_logins.userId  LEFT JOIN gma_user_details ON gma_order.userId=gma_user_details.userId WHERE $order_sql GROUP BY invoiceId $orderBy";
        //echo $order_sql;
        $order_rs    = mysql_query($order_sql);
        $order_count = mysql_num_rows($order_rs);
        
        $pagination = '';
        if($order_count>$perPage)
        {
            $order_sql  .= " LIMIT $offset, $perPage";
            $order_rs    = mysql_query($order_sql);
            
            $maxPage     = ceil($order_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($order_count, $perPage, 5);
        }
        
        if($ses_loginType!='user') {
            $links = '<a href="invoices.php?action=add" title="Create New Invoice">Create New Invoice</a>&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
            $add_url    = 'invoices.php?action=add';
            $del_url    = 'javascript:void(0);';
            $del_click  = 'deleteAll();';
            $search_box = true;
        }
        break;
}
    
$page_title = 'Invoices';
include('sub_header.php');
if($action=='add' || $action=='edit') { 
	include_once('new_invoice_form.php');
} else { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">

<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr height="30">
        <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
        <th width="15%"><span>Invoice Id.</span>&nbsp;<a href="?<?=$queryString?>&orderby=invoiceId&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoiceId&order=DESC" class="desc"></a></th>
        <th width="15%"><span>Order Date</span>&nbsp;<a href="?<?=$queryString?>&orderby=orderDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderDate&order=DESC" class="desc"></a></th>
        <? if($ses_loginType!='user') { ?>
            <th><span>Client</span>&nbsp;<a href="?<?=$queryString?>&orderby=businessName&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=businessName&order=DESC" class="desc"></a></th>
        <? } ?>
        <th width="10%"><span>Total</span>&nbsp;<a href="?<?=$queryString?>&orderby=invoice_amount&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoice_amount&order=DESC" class="desc"></a></th>
        <th width="10%"><span>Status</span>&nbsp;<a href="?<?=$queryString?>&orderby=orderStatus&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderStatus&order=DESC" class="desc"></a></th>
        <th width="10%"><span>Sent</span>&nbsp;</th>
        <th width="20%">Action</th>
    </tr>  
    <?php
    $j=0;
    while($order_row = mysql_fetch_array($order_rs))
    {
        $val++;
        
        $invoiceId = $order_row['invoiceId'];
        $orderId   = $auto_id = $order_row['id'];
        $userId    = $order_row['userId'];
        $class     = ((($j++)%2)==1) ? 'altrow' : '';
        ?>
        <tr class="<?=$class?>">
            <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
            <td><?=$invoiceId?></td>
            <td><?=dateFormat($order_row['orderDate'], 'N')?></td>
            <? if($ses_loginType!='user') { ?> <td><?=$order_row['businessName']?></td> <? } ?>
            <td>R <?=formatMoney($order_row['invoice_amount'], true)?></td>
            <td><?=paymentStatus($order_row['orderStatus'])?></td>
            <td><?=dateFormat($order_row['sendDate'], 'Y') ?></td>
            <td>
                <a href="invoices.php?action=view&orderId=<?=$orderId?>" class="btn_style">View</a>
                <? if($ses_loginType!='user') { ?>
                    &nbsp;<a href="invoices.php?action=edit&orderId=<?=$orderId?>" class="btn_style">Edit</a>
                    &nbsp;<a href="invoices.php?action=delete&orderId=<?=$orderId?>" class="btn_style">Delete</a>
                <? } ?>
                &nbsp;<a href="invoices.php?action=resendMail&orderId=<?=$orderId?>" title="Send invoice to my email" class="btn_style">Send</a>
            </td>
        </tr>
        <?php
    }
    if($order_count==0) { ?>
        <tr><td class="norecords" colspan="10">No Records Found</td></tr>
    <? } ?>
</table>
</form>
    
<? } ?>

<?php include("footer.php");  ?>