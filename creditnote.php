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
$creditnoteId   = (isset($_REQUEST['creditnoteId']) && $_REQUEST['creditnoteId']>0) ? $_REQUEST['creditnoteId'] : 0;
$userTypes = userTypes('', 0, 1);
$userTypes = "'".implode("', '", $userTypes)."'";

$userId = userCheck($userId);

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
     
        if((isset($_REQUEST['sendMail']) || isset($_REQUEST['save']))) {
         
            $company_sql     = "SELECT * FROM gma_company WHERE companyId='$ses_companyId'";
            $company_rs      = mysql_query($company_sql);
            $company_row     = mysql_fetch_assoc($company_rs);
            $companyCreditNo = $company_row['companyCreditNo'];
            
            $creditnoteDate = date('Y-m-d H:i:s');
            $userId         = $_REQUEST['userId'];
            $comments       = GetSQLValueString($_REQUEST['comments'], 'text');
            
            $creditId = 0;
            if($creditnoteId>0)
            {
                $credit_sql = "SELECT * FROM gma_creditnote WHERE id='$creditnoteId'";
                $credit_rs  = mysql_query($credit_sql);
                if(mysql_num_rows($credit_rs)>0)
                {
                    $credit_row = mysql_fetch_array($credit_rs);
                    $creditId  = $credit_row['creditId'];
                }
                else 
                    $creditId = $creditnoteId = 0;
            }
            if($creditId==0)
            {
                $companyCreditNo = $companyCreditNo + 1;
                $creditId        = $companyCreditNo;
            }
            
            mysql_query("DELETE FROM gma_creditnote_details WHERE creditnoteId='$creditnoteId' AND creditnoteId>0");    
            if($creditnoteId==0)
            {        
                $creditnote_sql = "INSERT INTO gma_creditnote SET userId='$userId',creditId='$companyCreditNo',creditnoteDate='$creditnoteDate',comments=$comments";
                mysql_query($creditnote_sql);
                $creditnoteId = mysql_insert_id();
            }
            
            $credit_amount = $total = 0;
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
                    
                    
                    $credit_sql = "INSERT INTO gma_creditnote_details SET creditnoteId='$creditnoteId',group_id='$group_id',service_id='$service_id',serviceName='$serviceName',cost='$cost',quantity='$quantity',discount='$discount',amount='$amount'";
                    mysql_query($credit_sql);
                    
                    $credit_amount += $amount;
                }
            }
            $credit_sql = "UPDATE gma_creditnote SET userId='$userId',creditnote_amount='$credit_amount',comments=$comments WHERE id='$creditnoteId'";
            mysql_query($credit_sql); 
            
            $smsg = ($creditnoteId>0) ? "updated" : "added";
            $sql  = "UPDATE gma_company SET companyCreditNo='$companyCreditNo' WHERE companyId='$ses_companyId'";
            mysql_query($sql);
        
            $smsg = "Credit note successfully added !";    
//            if(isset($_REQUEST['sendMail']))
//            {	
//                $details = invoiceDetails($creditnoteId);
//                $result  = emailSend('invoice', $details);
//                if($result){
//                    $smsg = "Invoice added and mail sent successfully";  
//       	            $sql  = "UPDATE gma_creditnote SET sendDate=NOW()WHERE id='$creditnoteId'";
//       	            mysql_query($sql);
//                 }              
//            }
            return header("Location: creditnote.php?msg=$smsg");
            exit;
        }
        
        $total = 0;
        if($creditnoteId>0)
        { 
            $credit_sql = "SELECT * FROM gma_creditnote, gma_logins WHERE gma_logins.userId=gma_creditnote.userId AND gma_logins.companyId='$ses_companyId' AND id='$creditnoteId'";
            if($ses_loginType=='user')
                $credit_sql .= " AND gma_logins.userId='$ses_userId'";
            $credit_rs  = mysql_query($credit_sql);
            if(mysql_num_rows($credit_rs)==0) {
                $smsg = "Invalid Request";
                return header("Location: creditnote.php?msg=$smsg");
                exit;
            }
            $credit_row = mysql_fetch_assoc($credit_rs);
            $userId    = $credit_row['userId'];
            $total     = $credit_row['creditnote_amount'];
            $comments  = $credit_row['comments']; 
            
            $orderDetails      = array();
            $credit_detail_sql  = "SELECT * FROM gma_creditnote_details WHERE creditnoteId='$creditnoteId'";
            $credit_detail_rs   = mysql_query($credit_detail_sql);
            while($credit_detail_row = mysql_fetch_assoc($credit_detail_rs))
            {
                 $orderDetails[] = $credit_detail_row;
            }
        }
        break;

    case 'delete':
        $credit_sql = "SELECT * FROM gma_creditnote, gma_logins WHERE gma_logins.userId=gma_creditnote.userId AND gma_logins.companyId='$ses_companyId' AND id='$creditnoteId'";
        $credit_rs  = mysql_query($credit_sql);
        if(mysql_num_rows($credit_rs)==0) {
            $smsg = "Invalid Request";
            return header("Location: creditnote.php?msg=$smsg");
            exit;
        }
        
        $credit_sql = "DELETE FROM gma_creditnote WHERE id='$creditnoteId'";
        mysql_query($credit_sql);
        $credit_sql = "DELETE FROM gma_creditnote_details WHERE creditnoteId='$creditnoteId'";
        mysql_query($credit_sql);
        
        return header("Location: creditnote.php?msg=Details successfully deleted !.");
        exit;
        
        break;
        
    case 'view':
        $details = creditNoteDetails($creditnoteId);
//        echo '<pre>'; print_r($details); exit;
        $details = emailSend('creditnote', $details, null, 1);
        echo "<div align='center'>$details</div>";
        exit;
        break;
        
    default:
        $action  = 'list';

        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY creditId DESC ';
        
        $credit_sql   = ($userId!='') ? "gma_creditnote.userId='$userId' AND " : '';
        $credit_sql  .= ($srchtxt!='') ? "(userName LIKE '$srchtxt%' OR creditId LIKE '$srchtxt%') AND " : '';
        $credit_sql  .= ($ses_loginType=='user') ? "gma_creditnote.userId='$ses_userId' AND " : '';
        $credit_sql  .= "companyId='$ses_companyId'"; // AND gma_creditnote.status=1";
        $credit_sql   = "SELECT gma_creditnote.*,businessName,DATE_ADD(creditnoteDate, INTERVAL 7 HOUR) AS creditnoteDate FROM gma_creditnote LEFT JOIN gma_logins ON gma_creditnote.userId=gma_logins.userId LEFT JOIN gma_user_details ON gma_creditnote.userId=gma_user_details.userId WHERE $credit_sql GROUP BY creditId $orderBy";
        $credit_rs    = mysql_query($credit_sql);
        $credit_count = mysql_num_rows($credit_rs);
        
        $pagination = '';
        if($credit_count>$perPage)
        {
            $credit_sql  .= " LIMIT $offset, $perPage";
            $credit_rs    = mysql_query($credit_sql);
            
            $maxPage     = ceil($credit_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($credit_count, $perPage, 5);
        }
        
        if($ses_loginType!='user') {
            $add_url    = 'creditnote.php?action=add&userId='.$userId;
            $search_box = true;
            $user_search = true;
        }
        break;
}

$page_title = ($action=='add' || $action=='edit') ? ($action=='add' ? 'New Credit Note' : 'Edit Credit Note') : 'Credit Notes';
include('sub_header.php');
if($action=='add' || $action=='edit') {
    $showRepeat = $invoice = false;
    $creditnote = true;
    include_once('invoice_form.php');
    
} else { ?>
    <form method="POST" id="listForm" name='listForm'>
    <input type="hidden" name="action" value="deleteall">    
    <table width="100%" class="list" cellpadding="0" cellspacing="0">
        <tr>
            <!--<th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>-->
            <th width="10%">Invoice Id.<a href="?<?=$queryString?>&orderby=creditId&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=creditId&order=DESC" class="desc"></a></th>
            <th width="12%">Order Date<a href="?<?=$queryString?>&orderby=creditnoteDate&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=creditnoteDate&order=DESC" class="desc"></a></th>
            <? if($ses_loginType!='user') { ?>
                <th>Client<a href="?<?=$queryString?>&orderby=businessName&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=businessName&order=DESC" class="desc"></a></th>
            <? } ?>
            <th width="10%">Total<a href="?<?=$queryString?>&orderby=creditnote_amount&order=ASC" class="asc"></a><a href="?<?=$queryString?>&orderby=creditnote_amount&order=DESC" class="desc"></a></th>
            <th width="27%">Action</th>
        </tr>  
        <?php
        $j=0;
        while($credit_row = mysql_fetch_array($credit_rs))
        {
            $val++;
            
            $creditId = $credit_row['creditId'];
            $creditnoteId   = $auto_id = $credit_row['id'];
            $userId    = $credit_row['userId'];
            $class     = ((($j++)%2)==1) ? 'altrow' : '';

            if($credit_row['orderStatus'] == 0) {
	        	$paid_sql = "SELECT creditnoteId, SUM(amount) AS paidAmount FROM gma_payment_order ".
	        				" WHERE creditnoteId= " . $credit_row['id'] .
	        				" GROUP BY creditnoteId";
	            $paid_rs  = mysql_query($paid_sql);
	            if(mysql_num_rows($paid_rs)>0) {
	            	$paid_row = mysql_fetch_assoc($paid_rs);
	            	$paidAmt = $paid_row['paidAmount'];
	            	$status = 'Paid ' . formatMoney($paidAmt);
	            }
	            else
	            	$status = paymentStatus($credit_row['orderStatus']);
            }
            else {
            	
            	$status = paymentStatus($credit_row['orderStatus']);
            }
            
            $status = ($credit_row['status']==0) ? '<span>Cancelled</span>' : $status;
            
            ?>
            <tr class="<?=$class?>">
                <!--<td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>-->
                <td><?=$creditId?></td>
                <td><?=dateFormat($credit_row['creditnoteDate'], 'N')?></td>
                <? if($ses_loginType!='user') { ?> <td><?=$credit_row['businessName']?></td> <? } ?>
                <td><?=formatMoney($credit_row['creditnote_amount'], true)?></td>
                <td>
                    <a href="creditnote.php?action=view&creditnoteId=<?=$creditnoteId?>&popup" class="btn_style thickbox">View</a>
                    &nbsp;<a href="creditnote.php?action=edit&creditnoteId=<?=$creditnoteId?>" class="btn_style">Edit</a>
                    &nbsp;<a href="creditnote.php?action=delete&creditnoteId=<?=$creditnoteId?>" class="btn_style">Delete</a>
                    <? if(1==2) { ?>
                        &nbsp;<a href="creditnote.php?action=resendMail&creditnoteId=<?=$creditnoteId?>" title="Send invoice to my email" class="btn_style">Send</a>
                    <? } ?>
                </td>
            </tr>
            <?php
        }
        if($credit_count==0) { ?>
            <tr><td class="norecords" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
    </form>    
<?
}
include("footer.php");
?>