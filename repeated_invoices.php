<?php
$invoice_btn = 'selected';    
include("config.php");
if(isset($_REQUEST['popup']))
    include("functions.php");
else 
    include("header.php");
    
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

switch ($action)
{
    case 'add':
    case 'edit':

        if((isset($_REQUEST['sendMail']) || isset($_REQUEST['save'])))
        {
        	if(isset($_REQUEST['repeat']) && $_REQUEST['repeat']==1){
        		
        		if (saveRepeatedInvoice($orderId, $_POST)) {       		
		            $smsg = "Repeat Invoice saved successfully";        			
        			     		
					return header("Location: repeated_invoices.php?msg=$smsg");
	            	exit;
        		}
 
        	}
        	else {
        		if (saveRepeatedInvoice($orderId, $_POST)) {  
        			
		            $sql  = "UPDATE gma_order_repeat SET orderStatus=1 WHERE id='$orderId'"; 
		            mysql_query($sql);
		            $smsg = "Repeat Invoice saved successfully";        			
        			     		
					return header("Location: repeated_invoices.php?msg=$smsg");
	            	exit;
        		}
        		
        	}
        	
        	
        }
        
        $total = 0;
        if($orderId>0)
        { 
             $order_sql = "SELECT * FROM gma_order_repeat, gma_logins WHERE gma_logins.userId=gma_order_repeat.userId AND gma_logins.companyId='$ses_companyId' AND id='$orderId'";
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
            $orderNo  	= $order_row['order_number'];
            
            $startdate = dateFormat($order_row['startDate']);
            $how_often = $order_row['how_often'];
            $how_many = ($order_row['how_many']=='1001')?'Forever':$order_row['how_many'];
            $sentTotal = $order_row['sentTotal'];
            $chked = '';
            $display =" display:none;";
            if($order_row['orderStatus']==0){
            	$chked = 'checked="checked"';
            	$display ="";
            }
            
            $orderDetails      = array();
            $order_detail_sql  = "SELECT * FROM gma_order_repeat_details WHERE orderRepeatId='$orderId'";
            $order_detail_rs   = mysql_query($order_detail_sql);
            while($order_detail_row = mysql_fetch_assoc($order_detail_rs))
            {
                 $orderDetails[] = $order_detail_row;
            }
//            echo '<pre>'; print_r($ordersDetails); exit;
        }
        break;
        
    case 'delete':
        $order_sql = "SELECT * FROM gma_order_repeat,gma_logins WHERE gma_logins.userId=gma_order_repeat.userId AND gma_logins.companyId='$ses_companyId' AND id='$orderId'";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)!=1)
        {
            header("Location: repeated_invoices.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_order_repeat_details WHERE orderId IN (SELECT id FROM gma_order_repeat WHERE id='$orderId')";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_order_repeat WHERE id='$orderId'";
        mysql_query($sql);
        
        header("Location: repeated_invoices.php?d");        
        break;
        
    default:
        $action  = 'list';

        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY invoiceId DESC ';
        
        $order_sql   = ($userId!='') ? "gma_order_repeat.userId='$userId' AND " : '';
        $order_sql  .= ($srchtxt!='') ? "(userName LIKE '$srchtxt%' OR invoiceId LIKE '$srchtxt%') AND " : '';
        $order_sql  .= ($ses_loginType=='user') ? "gma_order_repeat.userId='$ses_userId' AND " : '';
        $order_sql  .= "companyId='$ses_companyId'";
        $order_sql   = "SELECT gma_order_repeat.*,businessName,DATE_ADD(orderDate, INTERVAL 7 HOUR) AS orderDate FROM gma_order_repeat LEFT JOIN gma_logins ON gma_order_repeat.userId=gma_logins.userId  LEFT JOIN gma_user_details ON gma_order_repeat.userId=gma_user_details.userId WHERE $order_sql $orderBy";
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
       //     $links = '<a href="invoices.php?action=add" title="Create New Repeat Invoice">Create New Repeat Invoice</a>&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
            $add_url    = 'invoices.php?action=add&userId='.$userId;
//            $del_url    = 'javascript:void(0);';
//            $del_click  = 'deleteAll();';
            $search_box = true;
            $user_search = true;
        }
        break;
}
$invoice = true;

$page_title = ($action=='add' || $action=='edit') ? ($action=='add' ? 'New Repeat Invoice' : 'Edit Repeat Invoice') : 'Repeat Invoices';
include('sub_header.php');
if($action=='add' || $action=='edit') {
		$showRepeat = true;
	
	    include_once('invoice_form.php');
} else { ?>
    <form method="POST" id="listForm" name='listForm'>
    <input type="hidden" name="action" value="deleteall">    
    <table width="100%" class="list" cellpadding="0" cellspacing="0">
        <tr>
  <!--          <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th> 
            <th width="10%">Invoice Id.<a href="?<?=$queryString?>&orderby=invoiceId&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoiceId&order=DESC" class="desc"></a></th>-->
            <th width="12%">Order Date<a href="?<?=$queryString?>&orderby=orderDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderDate&order=DESC" class="desc"></a></th>
            <? if($ses_loginType!='user') { ?>
                <th>Client<a href="?<?=$queryString?>&orderby=businessName&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=businessName&order=DESC" class="desc"></a></th>
            <? } ?>
            <th width="10%">Total<a href="?<?=$queryString?>&orderby=invoice_amount&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoice_amount&order=DESC" class="desc"></a></th>
         <!--   <th width="9%">Status<a href="?<?=$queryString?>&orderby=orderStatus&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=orderStatus&order=DESC" class="desc"></a></th> -->
            <th width="10%">How Often<a href="?<?=$queryString?>&orderby=how_often&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=how_often&order=DESC" class="desc"></a></th>
            <th width="10%">Invoices Sent<a href="?<?=$queryString?>&orderby=sentTotal&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=sentTotal&order=DESC" class="desc"></a></th>
            <th width="10%">Last Generated Date<a href="?<?=$queryString?>&orderby=invoiceSentDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=invoiceSentDate&order=DESC" class="desc"></a></th>
            <th width="27%">Action</th>
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

            if($order_row['orderStatus'] == 0) {
	        	$paid_sql = "SELECT orderId, SUM(amount) AS paidAmount FROM gma_payment_order ".
	        				" WHERE orderId= " . $order_row['id'] .
	        				" GROUP BY orderId";
	            $paid_rs  = mysql_query($paid_sql);
	            if(mysql_num_rows($paid_rs)>0) {
	            	$paid_row = mysql_fetch_assoc($paid_rs);
	            	$paidAmt = $paid_row['paidAmount'];
	            	$status = 'Paid ' . formatMoney($paidAmt);
	            }
	            else
	            	$status = paymentStatus($order_row['orderStatus']);
            }
            else {
            	
            	$status = paymentStatus($order_row['orderStatus']);
            }
            
            
            ?>
            <tr class="<?=$class?>">
            <!--    <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td> 
                <td><?=$invoiceId?></td> -->
                <td><?=dateFormat($order_row['orderDate'], 'N')?></td>
                <? if($ses_loginType!='user') { ?> <td><?=$order_row['businessName']?></td> <? } ?>
                <td><?=formatMoney($order_row['invoice_amount'], true)?></td>
              <!--  <td><?=$status?></td> -->
                <td><?=getHowOften($order_row['how_often'])?></td> 
                <td><?=$order_row['sentTotal']?></td> 
                <td><?=dateFormat($order_row['invoiceSentDate'], 'Y') ?></td>
                <td>
                    <!-- <a href="invoices.php?action=view&orderId=<?=$orderId?>&popup" class="btn_style thickbox">View</a>-->
                    <? if($ses_loginType!='user') { ?>
                        &nbsp;<a href="repeated_invoices.php?action=edit&orderId=<?=$orderId?>" class="btn_style">Edit</a>
                        &nbsp;<a href="javascript: void(0);" onClick="javascript:confirmDelete(<?=$orderId?>);" class="btn_style">Delete</a>
                    <? } ?>
                   <!-- &nbsp;<a href="invoices.php?action=resendMail&orderId=<?=$orderId?>" title="Send invoice to my email" class="btn_style">Send</a>-->
                </td>
            </tr>
            <?php
        }
        if($order_count==0) { ?>
            <tr><td class="norecords" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
    </form>    
<?
}
include("footer.php");
?>
<script>

function confirmDelete(orderId) {
		jConfirm('Checking this box will result in automatic emailing of this invoice until unchecked', 'Confirmation Dialog', function(r) {
	    	if(r===true)
 		  		window.location.href="repeated_invoices.php?action=delete&orderId="+orderId;
    });
}	

</script>