<?php
$user_flag   = true;
$quotation_btn = 'selected';
if(isset($_REQUEST['popup']))
    include("functions.php");
else 
    include("header.php");
    
include("config.php");

$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$perPage   = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum   = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;
$srchtxt   = (trim($_REQUEST['srchtxt'])!='') ? trim($_REQUEST['srchtxt']) : '';
$links     = '';
$userId    = (isset($_REQUEST['userId']) && $_REQUEST['userId']>0) ? $_REQUEST['userId'] : 0;
$quotationId   = (isset($_REQUEST['quotationId']) && $_REQUEST['quotationId']>0) ? $_REQUEST['quotationId'] : 0;
$userTypes = userTypes('', 0, 1);
$userTypes = "'".implode("', '", $userTypes)."'";

if($ses_loginType=='user' && ($action=='add' || $action=='edit' || $action=='delete' || $action=='deleteall')) $action = 'list';

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

switch ($action)
{
    case 'add':
    case 'edit':
        $title = ($action='add') ? 'Add Details' : 'Edit Details';
        
        if((isset($_REQUEST['sendMail']) || isset($_REQUEST['save'])))
        {
            $invoice_sql = "SELECT companyQuotationNo FROM gma_company WHERE companyId='$ses_companyId'";
            $company_rs  = mysql_query($company_sql);
            $company_row = mysql_fetch_assoc($company_rs);
            $companyQuotationNo = $company_row['companyQuotationNo'];
            
            $orderDate    = date('Y-m-d H:i:s');
            $userId       = $_REQUEST['userId'];
            $order_number = $_REQUEST['order_number'];
            $comments = GetSQLValueString($_REQUEST['comments'], 'text');
            
            $invoiceId = 0;
            if($quotationId>0)
            {
                $order_sql = "SELECT * FROM gma_quotation WHERE id='$quotationId'";
                $order_rs  = mysql_query($order_sql);
                if(mysql_num_rows($order_rs)>0)
                {
                    $order_row = mysql_fetch_array($order_rs);
                    $invoiceId = $order_row['invoiceId'];
                }
                else 
                    $quotationId = 0;
            }
            if($invoiceId==0)
            {
                $companyQuotationNo = $companyQuotationNo + 1;
                $invoiceId  = $companyQuotationNo;
            }
            
            mysql_query("DELETE FROM gma_quotation_details WHERE quotationId='$quotationId' AND quotationId>0");    
            if($quotationId==0)
            {
                $order_sql = "INSERT INTO gma_quotation SET userId='$userId',invoiceId='$invoiceId',order_number='$order_number', " .
                			" orderDate='$orderDate', comments=$comments";
                mysql_query($order_sql);
                $quotationId = mysql_insert_id();
            }
            
            $invoice_amount = $total = 0;
            foreach ($_REQUEST['service_id'] as $key=>$service_group_id)
            {
                if($service_group_id!='' && $service_group_id!='0')
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
                    
                    $order_sql = "INSERT INTO gma_quotation_details SET quotationId='$quotationId',group_id='$group_id',service_id='$service_id',serviceName='$serviceName',cost='$cost',quantity='$quantity',discount='$discount',amount='$amount'";
                    mysql_query($order_sql);
                    
                    $invoice_amount = $invoice_amount + $amount;
                }
            }
            $order_sql = "UPDATE gma_quotation SET userId='$userId',order_number='$order_number',invoice_amount='$invoice_amount',comments=$comments WHERE id='$quotationId'";
            mysql_query($order_sql);
            
            $smsg = ($quotationId>0) ? "updated" : "added";
            $sql  = "UPDATE gma_company SET companyQuotationNo='$companyQuotationNo' WHERE companyId='$ses_companyId'";
            mysql_query($sql);
            
            $smsg = "Quotation added successfully";
            if(isset($_REQUEST['sendMail']))
            {	
                $details = quotationDetails($quotationId);
                $result  = emailSend('quotation', $details);
                if($result) {
                    $sql  = "UPDATE gma_quotation SET sendDate=NOW()WHERE id='$quotationId'";
                    mysql_query($sql);
                    $smsg = "Quotation added and mail sent successfully";
                }                
            }
            return header("Location: quotations.php?msg=$smsg");
            exit;
        }
        
        $total = 0;
        if($quotationId>0)
        {
            $order_sql = "SELECT * FROM gma_quotation, gma_logins WHERE gma_logins.userId=gma_quotation.userId AND gma_logins.companyId='$ses_companyId' AND id='$quotationId'";
            if($ses_loginType=='user')
                $order_sql .= " AND gma_logins.userId='$ses_userId'";
            $order_rs  = mysql_query($order_sql);
            if(mysql_num_rows($order_rs)==0) {
                $smsg = "Invalid Request";
                return header("Location: invoices.php?msg=$smsg");
                exit;
            }
            $order_row = mysql_fetch_assoc($order_rs);
            $userId    = $order_row['userId'];
            $total     = $order_row['invoice_amount'];
            $comments = $order_row['comments']; 
            
            $orderDetails      = array();
            $order_detail_sql  = "SELECT * FROM gma_quotation_details WHERE quotationId='$quotationId'";
            $order_detail_rs   = mysql_query($order_detail_sql);
            while($order_detail_row = mysql_fetch_assoc($order_detail_rs))
            {
                 $orderDetails[] = $order_detail_row;
            }
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
        
    case 'convert':
        $invoice_sql = "SELECT companyInvoiceNo FROM gma_company WHERE companyId='$ses_companyId'";
        $invoice_rs  = mysql_query($invoice_sql);
        $invoice_row = mysql_fetch_assoc($invoice_rs);
        $invoiceId   = $invoice_row['companyInvoiceNo'] + 1;
        
        $quotation_id  = $quotationId;
        $order_no      = (isset($_GET['order_no']) && $_GET['order_no']!='') ? $_GET['order_no'] : '';
        $quotation_sql = "SELECT * FROM gma_quotation WHERE id='$quotation_id'";
        $quotation_rs  = mysql_query($quotation_sql);
        if(mysql_num_rows($quotation_rs)==1) {
            $quotation_row = mysql_fetch_assoc($quotation_rs);
            unset($quotation_row['id']);
            unset($quotation_row['orderDate']);
            unset($quotation_row['order_number']);
            unset($quotation_row['invoiceId']);
            
            $order_sql = "";
            foreach ($quotation_row as $key=>$value) {
                $order_sql .= "`$key`=".GetSQLValueString($value, 'text').","; 
            }
            $order_sql .= "`invoiceId`=".GetSQLValueString($invoiceId, 'text').",`order_number`=".GetSQLValueString($order_no, 'text').",`orderDate`='".date('Y-m-d H:i:s')."'";
            
            $order_sql = "INSERT INTO gma_order SET $order_sql";
            mysql_query($order_sql);
            $orderId = mysql_insert_id();
            
            $quotation_sql = "SELECT * FROM gma_quotation_details WHERE quotationId='$quotation_id'";
            $quotation_rs  = mysql_query($quotation_sql);
            if(mysql_num_rows($quotation_rs)>0) {
                while ($quotation_row = mysql_fetch_assoc($quotation_rs)) {
                	   unset($quotation_row['quotationId']);
                    $order_sql = "";
                    foreach ($quotation_row as $key=>$value) {
                        //$order_sql .= ($order_sql!='') ? ', ' : '';
                        $order_sql .= "`$key`=".GetSQLValueString($value, 'text').","; 
                    }
                    $order_sql .= "`orderId`='$orderId'";
                    
                    $order_sql = "INSERT INTO gma_order_details SET $order_sql";
                    mysql_query($order_sql);
                }
            }
            $sql = "DELETE FROM gma_quotation_details WHERE quotationId IN (SELECT id FROM gma_quotation WHERE id='$quotation_id')";
            mysql_query($sql);
            
            $sql = "DELETE FROM gma_quotation WHERE id='$quotation_id'";
            mysql_query($sql);
            
            $sql = "UPDATE gma_company SET companyInvoiceNo='$invoiceId' WHERE companyId='$ses_companyId'";
            mysql_query($sql);
            
            return header("Location: quotations.php?msg=Quotation successfully converted to order");
            exit;
        }
        return header("Location: quotations.php?msg=Invalid Quotation");
        exit;
        
        break;
        
    case 'view':
        $details = quotationDetails($quotationId);
        $details = emailSend('quotation', $details, null, 1);
        echo "<div align='center'>$details</div>";
        exit;
        break;
        
    case 'delete':
        $order_sql = "SELECT * FROM gma_quotation,gma_logins WHERE gma_logins.userId=gma_quotation.userId AND gma_logins.companyId='$ses_companyId' AND id='$quotationId'";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)!=1)
        {
            header("Location: quotations.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_quotation_details WHERE quotationId IN (SELECT id FROM gma_quotation WHERE id='$quotationId')";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_quotation WHERE id='$quotationId'";
        mysql_query($sql);
        
        header("Location: quotations.php?d");        
        break;
        
    case 'deleteall':
        $quotationId    = implode(',', $_REQUEST['delete']);
        $order_sql = "SELECT * FROM gma_quotation,gma_logins WHERE gma_logins.userId=gma_quotation.userId AND gma_logins.companyId='$ses_companyId' AND id IN ($quotationId)";
        $quotationId   = 0;
        $order_rs   = mysql_query($order_sql);
        while($order_row = mysql_fetch_assoc($order_rs))
        {
            $quotationId .= ','.$order_row['id'];
        }
        if($quotationId=='0')
        {
            header("Location: quotations.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_quotation_details WHERE quotationId IN (SELECT id FROM gma_quotation WHERE id IN ($quotationId))";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_quotation WHERE id IN ($quotationId)";
        mysql_query($sql);
        
        header("Location: quotations.php?d");        
        break;
        
    case 'resendMail':
        $details = quotationDetails($quotationId);
        $result  = emailSend('quotation', $details);
        if($result) {
        	   $sql  = "UPDATE gma_quotation SET sendDate=NOW()WHERE id='$quotationId'";
            mysql_query($sql);
            $smsg = "Quotation mail send successfully";
        }
       
        return header("Location: quotations.php?msg=$smsg");
        exit;
        break;
        
    case 'delete':
        header("Location: services.php?d");        
        break;
        
    default:
        $action  = 'list';

        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY invoiceId DESC ';
        
        $order_sql   = ($userId!='') ? "gma_quotation.userId='$userId' AND " : '';
        $order_sql  .= ($srchtxt!='') ? "(userName LIKE '$srchtxt%' OR invoiceId LIKE '$srchtxt%') AND " : '';
        $order_sql  .= ($ses_loginType=='user') ? "gma_quotation.userId='$ses_userId' AND " : '';
        $order_sql  .= "companyId='$ses_companyId'";
        $order_sql   = "SELECT gma_quotation.*,businessName,DATE_ADD(orderDate, INTERVAL 7 HOUR) AS orderDate FROM gma_quotation LEFT JOIN gma_logins ON gma_quotation.userId=gma_logins.userId  LEFT JOIN gma_user_details ON gma_quotation.userId=gma_user_details.userId WHERE $order_sql GROUP BY invoiceId $orderBy";
        // echo $order_sql; exit;
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
            $links = '<a href="quotations.php?action=add" title="Create New Quotation">Create New Quotation</a>&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
            $add_url    = 'quotations.php?action=add&userId='.$userId;
            $del_url    = 'javascript:void(0);';
            $del_click  = 'deleteAll();';
            $search_box = true;
            $user_search = true;
        }
        break;
}
$invoice = false;

$page_title = 'Quotation';
include('sub_header.php');
if($action=='add' || $action=='edit') { 
	include_once('invoice_form.php');
} else { ?>

    <form method="POST" id="listForm" name='listForm'>
    <input type="hidden" name="action" value="deleteall">    
    <table width="100%" class="list" cellpadding="0" cellspacing="0">
        <tr>
            <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
            <th width="10%">Invoice Id.<a href="?<?=$queryString?>&orderby=invoiceId&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoiceId&order=DESC" class="desc"></a></th>
            <th width="12%">Order Date<a href="?<?=$queryString?>&orderby=orderDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderDate&order=DESC" class="desc"></a></th>
            <? if($ses_loginType!='user') { ?>
                <th>Client<a href="?<?=$queryString?>&orderby=businessName&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=businessName&order=DESC" class="desc"></a></th>
            <? } ?>
            <th width="10%">Total<a href="?<?=$queryString?>&orderby=invoice_amount&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoice_amount&order=DESC" class="desc"></a></th>
            <th width="9%">Status<a href="?<?=$queryString?>&orderby=orderStatus&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderStatus&order=DESC" class="desc"></a></th>
            <th width="9%">Sent<a href="?<?=$queryString?>&orderby=sendDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=sendDate&order=DESC" class="desc"></a></th>
            <th width="27%">Action</th>
        </tr>  
        <?php
        $j=0;
        while($order_row = mysql_fetch_array($order_rs))
        {
            $val++;
            
            $quotationId = $order_row['invoiceId'];
            $orderId   = $auto_id = $order_row['id'];
            $userId    = $order_row['userId'];
            $class     = ((($j++)%2)==1) ? 'altrow' : '';
            ?>
            <tr class="<?=$class?>">
                <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
                <td><?=$quotationId?></td>
                <td><?=dateFormat($order_row['orderDate'], 'N')?></td>
                <? if($ses_loginType!='user') { ?> <td><?=$order_row['businessName']?></td> <? } ?>
                <td><?=formatMoney($order_row['invoice_amount'], true)?></td>
                <td><?=paymentStatus($order_row['orderStatus'])?></td>
                <td><?=dateFormat($order_row['sendDate'], 'Y') ?></td>
                <td>
                <a href="quotations.php?action=view&quotationId=<?=$orderId?>&popup" class="btn_style thickbox">View</a>
                <? if($ses_loginType!='user') { ?>
                    &nbsp;<a href="quotations.php?action=edit&quotationId=<?=$orderId?>" class="btn_style">Edit</a>
                    &nbsp;<a href="quotations.php?action=delete&quotationId=<?=$orderId?>" class="btn_style">Delete</a>
                    &nbsp;<a href="javascript:void(0);" onclick="convertOrder('<?=$orderId?>');" class="btn_style">Convert to Order</a>
                <? } ?>
                &nbsp;<a href="quotations.php?action=resendMail&quotationId=<?=$orderId?>" title="Send invoice to my email" class="btn_style">Send</a>
                </td>
            </tr>
            <?php
        }
        if($order_count==0) { ?>
            <tr><td class="norecords" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
    </form>    
    <script>
    function convertOrder(order_id) {
        jPrompt('', '', 'Enter Order No', function(order_no) {
            var url = 'quotations.php?action=convert&quotationId=' + order_id + '&order_no=' + order_no;
            window.location = url;
        });
    }
    </script>    
<? } ?>

<?php include("footer.php");  ?>