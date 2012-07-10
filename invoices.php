<?php
$invoice_btn = 'selected';
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
                if($key>0 && $_REQUEST['cost'][$key]>0)
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
                if($result)
                    $smsg = "Invoice added and mail sent successfully";                
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
        if($result)                    
            $smsg = "Invoice mail send successfully";
                    
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
        
        if($ses_loginType!='user')
            $links = '<a href="invoices.php?action=add" title="Create New Invoice">Create New Invoice</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
        break;
}
    
$page_title = 'Invoices';
include('sub_header.php');
if($action=='add' || $action=='edit') { ?>

<form name="invoicefrm" id="invoicefrm" method="post" action="" onsubmit="return validateInvoice();">
<div class="newinvoice" style="width:70%"><div id="all_forms">
<table class="send_credits" cellpadding="7" cellspacing="0" width="100%">
    <tr><td colspan="5" class="">
        <div class="fleft">
            <b>Select Client to invoice: </b>
            <select name="userId" id="userId" class="inputbox_green" onchange="selectClientDiscount(this.value)">
                <option value="" selected>Select Client</option>
                <?php
                while($user_row = mysql_fetch_array($user_rs))
                {
                    $user_id  = $user_row['userId'];
                    $name     = $user_row['businessName'].' - '.$user_row['userName'];
                    $selected = ($userId==$user_id) ? 'selected' : '';
                    
                    echo "<option value='$user_id' $selected>$name</option>";
                }
                ?>
            </select>
        </div>
        <div class="fright">
            <b>Order Number : </b><input type="text" name="order_number" maxlength="48">
        </div>
    </td></tr>
<? $i = 0;
foreach ($allGroups as $groups) {
    $i++;
    $group_id       = $groups['id'];
    $discount_box   = ($groups['discount']==0) ? 'readonly' : '';
    $discount_style = ($groups['discount']==0) ? 'invoicetextbox_green' : 'invoicetextboxtxt_green';
    $service_count  = count($groups['services']);
?>
    <tr><td colspan="5" class="sc_head">
        <?=$groups['name']?>
        <div class="fright"><a href="javascript:void(0);" onclick="addNewServiceRow(<?=$group_id?>)">Add New</a></div>
    </td></tr>
    <tr><td colspan="5" style="padding:0px;margin:0px;" id="service_list_<?=$group_id?>">
        <table cellpadding="7" cellspacing="2" width="100%">
            <tr class="sc_subhead" >
                <td class="sc_subhead">Service items</td>
                <td class="sc_subhead" width="17%" align="center">Cost</td>     
                <td class="sc_subhead" width="17%" align="center">Quantity</td>     
                <td class="sc_subhead" width="17%" align="center">Discount%</td>
                <td class="sc_subhead" width="17%" align="right">Amount</td>
            </tr>
        </table>
        <div id="service_<?=$group_id?>" style="display:none">
            <table cellpadding="7" cellspacing="3" width="100%" id="div_test">
                <tr>
                    <td class="row2">
                        <? if($service_count>0) { ?>
                            <select  class="inputbox_green" style="width:300px;" name="service_id[test]" id="service_id_test" onchange="checkAmount(test)">
                                <option value="">Select</option>
                                <?php foreach ($groups['services'] as $service) { ?>
                                    <option value='<?=$service['id']?>_<?=$group_id?>'><?=$service['service_name']?></option>
                                <? } ?>	
                            </select>
                        <? } else { ?>	
                            <input type="hidden" name="service_id[test]" value="0_<?=$group_id?>" style="width:300px;" />
                            <input type="text" size="10" class="inputbox_green" name="service_name[test]" id="service_name_test" style="width:300px;" />
                        <? } ?>
                    </td>
                    <td class="row2" align="center" id="creditquantity_div" width="17%">
                        <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="cost[test]" id="cost_test" <?=($service_count>0 ? 'readonly' : '')?> onchange="changeInvoice(test)" value="0">
                    </td>
                    <td class="row2" align="center" id="creditquantity_div" width="17%">
                        <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="quantity[test]" id="quantity_test" <?=($service_count>0 ? '' : '')?> onchange="changeInvoice(test)" value="0">
                    </td>
                    <td class="row2" align="center" id="creditquantity_div" width="17%">
                        <input type="text" size="10" style="text-align:right;" class="<?=$discount_style?>" name="discount[test]" id="discount_test" value="<?=@$user_discount[$group_id]?>" <?=$discount_box?> onchange="changeInvoice(test)" />
                    </td>
                    <td class="row2" align="right" width="17%">
                        <input type="text" size="10" style="text-align:right;" name="amount[test]" id="amount_test" class="invoicetextbox_green" value="0" readonly />
                        <!--&nbsp;<a href="javascript:void(0)" onclick="removeServiceRow('test');">Delete</a>-->
                    </td>
                </tr>
            </table>
        </div>
        <? if(isset($groups['orders']) && count($groups['orders'])>0) { $key = 0; ?>
            <? foreach ($groups['orders'] as $order) {
                if($key!=0) { $i++; }
                $key = 1;
                $amount = $order['amount'];
                ?>
                <table cellpadding="7" cellspacing="3" width="100%">
                    <tr>
                        <td class="row2">
                            <? if($service_count>0) { ?>
                                <select  class="inputbox_green" style="width:300px;" name="service_id[<?=$i?>]" id="service_id_<?=$i?>" onchange="checkAmount(<?=$i?>)">
                                    <option value="">Select</option>
                                    <?php foreach ($groups['services'] as $service) {
                                    $selected = ($service['id']==$order['service_id']) ? 'selected' : '';
                                    ?>
                                        <option value='<?=$service['id']?>_<?=$group_id?>' <?=$selected?>><?=$service['service_name']?></option>
                                    <? } ?>	
                                </select>
                            <? } else { ?>	
                                <input type="hidden" name="service_id[<?=$i?>]" value="0_<?=$group_id?>" style="width:300px;" />
                                <input type="text" size="10" class="inputbox_green" name="service_name[<?=$i?>]" id="service_name_<?=$i?>" value="<?=$order['serviceName']?>" style="width:300px;" />
                            <? } ?>
                        </td>
                        <td class="row2" align="center" id="creditquantity_div" width="17%">
                            <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="cost[<?=$i?>]" id="cost_<?=$i?>" <?=($service_count>0 ? 'readonly' : '')?> onchange="changeInvoice(<?=$i?>)" value="<?=$order['cost']?>" />
                        </td>
                        <td class="row2" align="center" id="creditquantity_div" width="17%">
                            <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="quantity[<?=$i?>]" id="quantity_<?=$i?>" <?=($service_count>0 ? '' : '')?> onchange="changeInvoice(<?=$i?>)" value="<?=$order['quantity']?>" />
                        </td>
                        <td class="row2" align="center" id="creditquantity_div" width="17%">
                            <input type="text" size="10" style="text-align:right;" class="<?=$discount_style?>" name="discount[<?=$i?>]" id="discount_<?=$i?>" value="<?=$order['discount']?>" <?=$discount_box?> onchange="changeInvoice(<?=$i?>)" />
                        </td>
                        <td class="row2" align="right" width="17%">
                            <input type="text" size="10" style="text-align:right;" name="amount[<?=$i?>]" id="amount_<?=$i?>" class="invoicetextbox_green" value="<?=$amount?>" readonly />
                        </td>
                    </tr>
                </table>
                <?
            }
            ?>
        <? } else { ?>
        <table cellpadding="7" cellspacing="3" width="100%">
            <tr>
                <td class="row2">
                    <? if($service_count>0) { ?>
                        <select  class="inputbox_green" style="width:300px;" name="service_id[<?=$i?>]" id="service_id_<?=$i?>" onchange="checkAmount(<?=$i?>)">
                            <option value="">Select</option>
                            <?php foreach ($groups['services'] as $service) { ?>
                                <option value='<?=$service['id']?>_<?=$group_id?>'><?=$service['service_name']?></option>
                            <? } ?>	
                        </select>
                    <? } else { ?>	
                        <input type="hidden" name="service_id[<?=$i?>]" value="0_<?=$group_id?>" style="width:300px;" />
                        <input type="text" size="10" class="inputbox_green" name="service_name[<?=$i?>]" id="service_name_<?=$i?>" style="width:300px;" />
                    <? } ?>
                </td>
                <td class="row2" align="center" id="creditquantity_div" width="17%">
                    <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="cost[<?=$i?>]" id="cost_<?=$i?>" <?=($service_count>0 ? 'readonly' : '')?> onchange="changeInvoice(<?=$i?>)" value="0" />
                </td>
                <td class="row2" align="center" id="creditquantity_div" width="17%">
                    <input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="quantity[<?=$i?>]" id="quantity_<?=$i?>" <?=($service_count>0 ? '' : '')?> onchange="changeInvoice(<?=$i?>)" value="0" />
                </td>
                <td class="row2" align="center" id="creditquantity_div" width="17%">
                    <input type="text" size="10" style="text-align:right;" class="<?=$discount_style?>" name="discount[<?=$i?>]" id="discount_<?=$i?>" value="<?=@$user_discount[$group_id]?>" <?=$discount_box?> onchange="changeInvoice(<?=$i?>)" />
                </td>
                <td class="row2" align="right" width="17%">
                    <input type="text" size="10" style="text-align:right;" name="amount[<?=$i?>]" id="amount_<?=$i?>" class="invoicetextbox_green" value="0" readonly />
                </td>
            </tr>
        </table>
        <? } ?>
    </td></tr>
    <tr><td></td></tr>
<? } ?>
    <tr><td colspan="5" height="40" align="right" class="total"><span style="padding-right: 40px;">INVOICE TOTAL</span><input type="text" style="text-align:right;height:20px" class="invoicetextbox_green" name="total" id="total" value="<?=$total?>" size="10" readonly /></td></tr>
    <tr><td colspan="5" align="right"><input type="submit" class="search_bt" name="save" id="save" value="Save" /><input type="submit" class="search_bt" name="sendMail" id="sendMail" value="Save & Send"/></td></tr>
</table>
</div>
</div> 
<input type="hidden" name="vvcount" id="vvcount" value="1" />
<input type="hidden" name="cccount" id="cccount" value="1" />
</form>
<script>
var count = <?=$i?>;
</script>

<? } else { ?>

<form name="frm" id="frm" method="post" action="">
<div class="pagination" align="right">
    <table border="0" width="100%">
    <tr>
        <td align="left" width="400" >
            <b>Search&nbsp;:&nbsp;</b>
            <input type="text" class="inputbox_green" name="srchtxt" value="<?=@$_REQUEST['srchtxt']?>" id="srchtxt" size="23" />
            <input type="submit"  value="Search"  class="search_bt" name="sbmt" id="sbmt" />
        </td>
        <td align="right"><?=$pagination?></td>
    </tr>
    </table>
</div>
</form>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<div class="client_display">
    <table width="100%" class="client_display_table" cellpadding="3" cellspacing="3">
        <tr height="30">
            <th class="thead" width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
            <th width="15%" class="thead"><span>Invoice Id.</span>&nbsp;<a href="?<?=$queryString?>&orderby=invoiceId&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=invoiceId&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th width="15%" class="thead"><span>Order Date</span>&nbsp;<a href="?<?=$queryString?>&orderby=orderDate&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=orderDate&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <? if($ses_loginType!='user') { ?>
                <th class="thead"><span>Client</span>&nbsp;<a href="?<?=$queryString?>&orderby=businessName&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=businessName&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <? } ?>
            <th width="10%" class="thead"><span>Total</span>&nbsp;<a href="?<?=$queryString?>&orderby=invoice_amount&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=invoice_amount&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th width="10%" class="thead"><span>Status</span>&nbsp;<a href="?<?=$queryString?>&orderby=orderStatus&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=orderStatus&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th class="thead" width="30%">Action</th>
        </tr>  
        <?php
        $j=0;
        while($order_row = mysql_fetch_array($order_rs))
        {
            $j++;
            $val++;
            
            $invoiceId = $order_row['invoiceId'];
            $orderId   = $auto_id = $order_row['id'];
            $userId    = $order_row['userId'];
            $class     = (($j%2)==0) ? 'row2' : 'row1';
            ?>
            <tr class="<?=$class?>">
                <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
                <td><?=$invoiceId?></td>
                <td><?=$order_row['orderDate']?></td>
                <? if($ses_loginType!='user') { ?> <td><?=$order_row['businessName']?></td> <? } ?>
                <td>R <?=formatMoney($order_row['invoice_amount'], true)?></td>
                <td><?=paymentStatus($order_row['orderStatus'])?></td>
                <td>
                    <a href="invoices.php?action=view&orderId=<?=$orderId?>">View</a>
                    <? if($ses_loginType!='user') { ?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;<a href="invoices.php?action=edit&orderId=<?=$orderId?>">Edit</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;<a href="invoices.php?action=delete&orderId=<?=$orderId?>">Delete</a>
                    <? } ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;<a href="invoices.php?action=resendMail&orderId=<?=$orderId?>" title="Send invoice to my email">Send</a>
                </td>
            </tr>
            <?php
        }
        if($order_count==0) { ?>
            <tr><td class="message" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
</div>
</form>
    
<? } ?>

<?php include("footer.php");  ?>