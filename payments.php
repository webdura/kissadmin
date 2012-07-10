<?php
$action   = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list'; 
$payment_btn = 'selected';
include("config.php");
if($action!='export') {
    include("header.php");
} else {
    include("functions.php");
}

$action     = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$userId     = trim(($_REQUEST['userId']!='') ? $_REQUEST['userId'] : 0);
$payment_id = trim(($_REQUEST['payment_id']!='') ? $_REQUEST['payment_id'] : 0);
$perPage    = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum    = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;
$pending_amount = 0;

$payment_sql = "SELECT * FROM gma_payments,gma_logins,gma_user_details WHERE gma_logins.userId=gma_user_details.userId AND gma_logins.userId=gma_payments.userId AND gma_logins.companyId='$ses_companyId'";
switch ($action)
{
    case 'add':
        $title     = 'Add Payment Details';
        if(isset($_POST['userId']) && $_POST['userId']>0)
        {
            $orderIds      = isset($_POST['orderId']) ? $_POST['orderId'] : array();
            $_POST['date'] = convertToMysqlDate($_POST['date']);
            unset($_POST['sbmt']);
            unset($_POST['orderId']);
            unset($_POST['payment_id']);
            unset($_POST['pending_amount']);
            
            $values = '';
            foreach ($_POST AS $name=>$value)
            {
                if($values!='') $values .= ',';
                $values .= "$name=".GetSQLValueString($value, 'text');
            }
            $values .= ",created=NOW()";
            if($values!='')
            {
                $sql = "INSERT INTO gma_payments SET $values";
                mysql_query($sql);
                $payment_id = mysql_insert_id();
            
                if($payment_id>0) {
                    foreach ($orderIds as $orderId) {
                        
                        $order_sql = "SELECT * FROM gma_order WHERE id='$orderId'";
                        $order_rs  = mysql_query($order_sql);
                        $order_row = mysql_fetch_assoc($order_rs);
                        $amount    = $order_row['invoice_amount'];
                        
                        $sql = "INSERT INTO gma_payment_order SET orderId='$orderId', paymentId='$payment_id', amount='$amount'";
                        mysql_query($sql);
                        $sql = "UPDATE gma_order SET orderStatus='1' WHERE id='$orderId'";
                        mysql_query($sql);    
                    }
                }
                
                header("Location: payments.php?msg=added");
                exit;
            }
        }
        $payment_row['date']   = date('Y-m-d');
        $payment_row['amount'] = 0;
        break;
        
    case 'edit':
        $title     = 'Edit Payment Details';
        
        $payment_sql .= " AND paymentId='$payment_id'";
        $payment_rs   = mysql_query($payment_sql);
        if(mysql_num_rows($payment_rs)==0) {
            header("Location: payments.php?msg=invalid");
            exit;
        }
        
        if(isset($_POST['userId']) && $_POST['userId']>0)
        {
            $orderIds      = isset($_POST['orderId']) ? $_POST['orderId'] : array();
            $_POST['date'] = convertToMysqlDate($_POST['date']);
            unset($_POST['sbmt']);
            unset($_POST['orderId']);
            unset($_POST['payment_id']);
            unset($_POST['pending_amount']);
            
            $values = '';
            foreach ($_POST AS $name=>$value)
            {
                if($values!='') $values .= ',';
                $values .= "$name=".GetSQLValueString($value, 'text');
            }
            if($values!='')
            {
                $sql = "UPDATE gma_payments SET $values WHERE paymentId='$payment_id'";
                mysql_query($sql);
                
                $order_sql = "SELECT * FROM gma_payment_order WHERE paymentId='$payment_id'";
                $order_rs  = mysql_query($order_sql);
                if(mysql_num_rows($order_rs)>0) {
                    while ($order_row = mysql_fetch_assoc($order_rs)) {
                        $orderId = $order_row['orderId'];
                        $sql     = "UPDATE gma_order SET orderStatus='0' WHERE id='$orderId'";
                        mysql_query($sql);
                    }
                }
                $sql = "DELETE FROM gma_payment_order WHERE paymentId='$payment_id'";
                mysql_query($sql);
                
                foreach ($orderIds as $orderId) {                    
                    $order_sql = "SELECT * FROM gma_order WHERE id='$orderId'";
                    $order_rs  = mysql_query($order_sql);
                    $order_row = mysql_fetch_assoc($order_rs);
                    $amount    = $order_row['invoice_amount'];
                    
                    $sql = "INSERT INTO gma_payment_order SET orderId='$orderId', paymentId='$payment_id', amount='$amount'";
                    mysql_query($sql);
                    $sql = "UPDATE gma_order SET orderStatus='1' WHERE id='$orderId'";
                    mysql_query($sql);    
                }
                        
                header("Location: payments.php?msg=updated");
                exit;
            }
        }
        $payment_row = mysql_fetch_assoc($payment_rs);
        break;
        
    case 'delete':
        $payment_id   = $_GET['payment_id']; 
        $payment_sql .= " AND paymentId='$payment_id'";
        $payment_rs   = mysql_query($payment_sql);
        if(mysql_num_rows($payment_rs)!=1)
        {
            header("Location: payments.php?&userId=$userId&page=$pageNum&i");     
            exit;
        }
        
        $order_sql = "SELECT * FROM gma_payment_order WHERE paymentId='$payment_id'";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)>0) {
            while ($order_row = mysql_fetch_assoc($order_rs)) {
                $orderId = $order_row['orderId'];
                $sql     = "UPDATE gma_order SET orderStatus='0' WHERE id='$orderId'";
                mysql_query($sql);
            }
        }
        $sql = "DELETE FROM gma_payment_order WHERE paymentId='$payment_id'";
        mysql_query($sql);
        
        $payment_sql = "DELETE FROM gma_payments WHERE paymentId='$payment_id'";
        mysql_query($payment_sql);
        
        return header("Location: payments.php?&userId=$userId&page=$pageNum&msg=deleted");     
        break;
        
    case 'deleteall':
        $payment_id   = implode(',', $_REQUEST['delete']);
        $payment_sql .= " AND paymentId IN ($payment_id)";
        $payment_id   = 0;
        $payment_rs   = mysql_query($payment_sql);
        while($payment_row = mysql_fetch_assoc($payment_rs))
        {
            $payment_id .= ','.$payment_row['paymentId'];
            
            $order_sql = "SELECT * FROM gma_payment_order WHERE paymentId='{$payment_row['paymentId']}'";
            $order_rs  = mysql_query($order_sql);
            if(mysql_num_rows($order_rs)>0) {
                while ($order_row = mysql_fetch_assoc($order_rs)) {
                    $orderId = $order_row['orderId'];
                    $sql     = "UPDATE gma_order SET orderStatus='0' WHERE id='$orderId'";
                    mysql_query($sql);
                }
            }
            $sql = "DELETE FROM gma_payment_order WHERE paymentId='{$payment_row['paymentId']}'";
            mysql_query($sql);            
        }
        if($payment_id=='0')
        {
            header("Location: payments.php?&userId=$userId&page=$pageNum&i");
            exit;
        }
        
        $payment_sql = "DELETE FROM gma_payments WHERE paymentId IN ($payment_id)";
        mysql_query($payment_sql);
        
        return header("Location: payments.php?&userId=$userId&page=$pageNum&msg=deleted");     
        break;
        
    case 'export':
        $res .='Payment Id, Client, Date, Total'."\n";
        
        $paymentId   = $_GET['parm'];
        $payment_sql = "SELECT * FROM gma_payments LEFT JOIN gma_user_details ON gma_payments.userId=gma_user_details.userId WHERE paymentId IN ($paymentId)"; 
        $payment_rs  = mysql_query($payment_sql);
        while ($payment_row = mysql_fetch_assoc($payment_rs))
        {
            $res .= $payment_row['paymentId'].','.$payment_row['businessName'].','.dateFormat($payment_row['date']).','.$payment_row['amount']."\n";		
        }
        $filename = "Payments_".date("Y-m-d_H-i",time());
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"".$filename.".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $res;
        exit;
        break;
        
    case 'import':
        $import = 0;
        if(isset($_POST['sbmt']))
        {
            $import = 1;
            unset($_POST['sbmt']);
            if($_FILES['file']['size']>0)
            {
                $tmp_name = $_FILES['file']['tmp_name'];
                $filename = 'admin_'.$ses_userId.'.csv';
                
                copy($tmp_name, $filename);
            }
        }
        if(isset($_POST['upload']))
        {
            foreach ($_POST['add'] as $key)
            {
                $values  = "userId=".GetSQLValueString($_POST['userId'][$key], 'text');
                $values .= ",date=".GetSQLValueString(convertToMysqlDate($_POST['date'][$key]), 'text');
                $values .= ",amount=".GetSQLValueString($_POST['amount'][$key], 'text');
                $values .= ",created=NOW()";
                
                $sql = "INSERT INTO gma_payments SET $values";
                mysql_query($sql);
            }
            header("Location: payments.php?msg=import");
            exit;
        }
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $rowsPerPage;
        $initial = 0;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY date DESC ';
        
        $payment_sql   .= ($userId>0) ? " AND gma_payments.userId='$userId'" : '';
        $payment_sql   .= " $orderBy";
        $payment_rs     = mysql_query($payment_sql);
        $payment_count  = mysql_num_rows($payment_rs);
        
        $pagination = '';
        if($payment_count>$perPage)
        {
            $payment_sql .= " LIMIT $offset, $perPage";
            $payment_rs   = mysql_query($payment_sql);
            
            $maxPage      = ceil($payment_count/$perPage);
            $pagination   = pagination($maxPage, $pageNum);
            $pagination   = paginations($payment_count, $perPage, 5);
        }
        
        $links = '<a href="#" onclick="javascript:return paymentExport();">Export</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="payments.php?action=import" title="Import batch payments">Import batch payments</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="payments.php?action=add" title="Add manual payment">Add manual payment</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
        
        break;
}
$page_title = 'Payments';

$user_rows = array();
$user_sql  = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId AND companyId='$ses_companyId' GROUP BY userName ORDER BY businessName ASC"; 
$user_rs   = mysql_query($user_sql);
while ($user_row = mysql_fetch_assoc($user_rs)) {
	   $user_rows[] = $user_row;
}

include('sub_header.php');
if($action=='list') { ?>

<form method="GET" name='searchform'>
<div class="pagination" align="right">
    <table border="0" width="100%">
    <tr>
        <td align="left" width="600">
            <b>Select Client : </b>
            <select name="userId" id="userId" class="inputbox_green" style="width:;" onchange="document.searchform.submit();">
                <option value="">Select All</option>
                <? foreach ($user_rows as $user) {
                    $user_Id  = $user['userId'];
                    $name     = $user['businessName'].' - '.$user['userName'];
                    $selected = ($userId==$user_Id) ? 'selected' : '';
                    
                    echo "<option value='$user_Id' $selected>$name</option>";
                }
                ?>
            </select>
            <!--&nbsp;<input type="submit"  value="Search"  class="search_bt" name="" id="sbmt" />-->
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
            <th class="thead"width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
            <th class="thead"><span>Client</span>&nbsp;<a href="?<?=$queryString?>&orderby=businessName&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=businessName&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <!--<th class="thead">Description</th>-->
            <th width="20%" class="thead"><span>Date</span>&nbsp;<a href="?<?=$queryString?>&orderby=date&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=date&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th width="20%" class="thead"><span>Total</span>&nbsp;<a href="?<?=$queryString?>&orderby=amount&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString?>&orderby=amount&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th width="10%" class="thead" align="center">Delete</th>
        </tr>  
        <?php
        $j=0; $val=0;
        while($payment_row = mysql_fetch_assoc($payment_rs))
        {
            $j++;
            $val++;
            
            $class     = (($j%2)==0) ? 'row2' : 'row1';
            $total     = $payment_row['amount'];
            $paymentId = $auto_id = $payment_row['paymentId'];
            ?>
            <tr class="<?=$class?>">
                <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
                <td><?=$payment_row['businessName']?></td>
                <td><?=$payment_row['date']?></td>
                <td>R <?=formatMoney($total, true)?></td>
                <td align="center"><a href="payments.php?action=edit&payment_id=<?=$paymentId?>&userId=<?=$userId?>&page=<?=$pageNum?>">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="payments.php?action=delete&payment_id=<?=$paymentId?>&userId=<?=$userId?>&page=<?=$pageNum?>" onclick="return window.confirm('Are you sure to delete this ?');">Delete</a></td>
            </tr>
            <?php
        }
        if($j==0) { ?>
        <tr><td class="message" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
</div>

<div class="pagination">
    <table border="0" width="100%">
    <tr>
        <td align="left" width="600"></td>
        <td align="right"><?=$pagination?></td>
    </tr>
    </table>
</div>

<? } else if($action=='edit' || $action=='add') { ?>

<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<div class="newinvoice">
<form method="POST" id="userForm" name='userForm' onsubmit="return checkOrderAmount();">
<input type="hidden" name="payment_id" id="payment_id" value="<?=$payment_id?>">
<input type="hidden" name="pending_amount" id="pending_amount" value="<?=$pending_amount?>">
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3">
    <tr><td colspan="12" align="center" class="msg"><?php echo $msg; ?></td></tr>
    <tr><td colspan="13" class="sc_head">Add manual payment&nbsp;<span class="back"><a href="payments.php">Back</a></span></td></tr>
</table>
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3">
<tr>
    <th class="row1" align="left" width="200">Client</th>
    <td class="row1">
        <select name="userId" id="userId" class="textbox required" style="width:260px;" onchange="userOrders(this.value);">
            <option value="">Select Client</option>
            <? foreach ($user_rows as $user) {
                $user_Id  = $user['userId'];
                $name     = $user['businessName'].' - '.$user['userName'];
                $selected = ($userId==$user_Id || $payment_row['userId']==$user_Id) ? 'selected' : '';
                
                echo "<option value='$user_Id' $selected>$name</option>";
            }
            ?>
        </select>
    </td>  
</tr>
<tr>
    <th class="row2" align="left">Date</th>
    <td class="row2"><input type="text" name="date" id="date" class="textbox required" value="<?=dateFormat($payment_row['date'])?>" readonly /></td>  
</tr> 
<tr>
    <td colspan="2" height="0">
    
        <table id="pending_amount_div" style="display:none"><tr>
            <td class="row1" align="left" width="215">Previous Balance</th>
            <td class="row1" id="pending_amount_td"></td>  
        </tr></table>
    
    </td>
</tr> 
<tr>
    <th class="row2" align="left">Amount</th>
    <td class="row2"><input type="text" name="amount" id="amount" value="<?=$payment_row['amount']?>" class="textbox number required" /></td>  
</tr> 
<tr valign="top">
    <th class="row1" align="left">Orders</th>
    <td class="row1" id="order_div">
        Please select any user
    </td>  
</tr> 
<tr>
    <th class="row2">&nbsp;</th>
    <td class="row2"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td>  
</tr>
</table>
</form>

</div>

<script>
$(document).ready(function() {
    userOrders($('#userId').val());
    jQuery("#userForm").validate();
    $('#date').datePicker({startDate: start_date, dateFormat: date_format});
});
</script>

<?
} else if($action=='import') { ?>

<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<div class="newinvoice">
<? if($import==0) { ?>
<form method="POST" id="userForm" name='userForm' enctype="multipart/form-data">
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3">
    <tr><td colspan="12" align="center" class="msg"><?php echo $msg; ?></td></tr>
    <tr><td colspan="13" class="sc_head">Import batch payments<span class="back"><a href="payments.php">Back</a></span></td></tr>
</table>
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3">
<tr>
    <th class="row1" align="left" width="20%">File (csv files only) <a rel="tooltip" style="padding-top:9px;"><img src="images/icn_help.png"></a></th>
    <td class="row1"><input type="file" name="file" id="file" class="required"></td>  
</tr> 
<tr>
    <th class="row1"></th>
    <td class="row1"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td>  
</tr>
</table>
</form>
<? } elseif($import==1) { ?>
<form method="POST" id="batchForm" name='batchForm' enctype="multipart/form-data">
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3" style="width:900px">
    <tr><td colspan="12" align="center" class="msg"><?php echo $msg; ?></td></tr>
    <tr><td colspan="13" class="sc_head">Import batch payments<span class="back"><a href="payments.php">Back</a></span></td></tr>
</table>
<table width="100%" class="send_credits" cellpadding="3" cellspacing="3" style="width:900px">
<tr>
    <th class="row1" align="center" width="10%" nowrap>Add</th>
    <th class="row1" align="left" width="30%">Client</th>
    <th class="row1" align="left" width="30%">Date</th>
    <th class="row1" align="left" width="30%">Amount</th>
</tr>
<?
$i=1;
$filename = 'admin_'.$ses_userId.'.csv';
$fp = fopen($filename, 'r');
while ($row = fgetcsv($fp)) {
    if($i==2) {
        //echo '<pre>'; print_r($row); exit;
    }
    $userId = $row[0];
    $dates  = explode('-', $row[1]);
    $date   = date('d/m/Y', mktime(0, 0, 0, $dates[1], $dates[0], $dates[2]));
    $amount = $row[2]; 
    
    $user_details  = '<select name="userId['.$i.']" id="userId_'.$i.'" class="textbox required">';
    $user_details .= '<option value="">Select Client</option>';
    foreach ($user_rows as $user_row)
    {
        $user_Id  = $user_row['userId'];
        $name     = $user_row['businessName'].' - '.$user_row['userName'];
        
        $selected = ($user_row['businessName']==$userId) ? 'selected' : '';
        
        $user_details .= "<option value='$user_Id' $selected>$name</option>";
    }
    $user_details .= '</select>';
    if($amount>0) { 
        ?>
        <tr>
            <td class="row1" align="center"><input type="checkbox" name="add[<?=$i?>]" id="add_<?=$i?>" value="<?=$i?>" checked /></td>  
            <td class="row1"><?=$user_details?></td>  
            <td class="row1"><input type="text" name="date[<?=$i?>]" id="date_<?=$i?>" class="textbox required" value="<?=$date?>" readonly /></td>  
            <td class="row1"><input type="text" name="amount[<?=$i?>]" id="amount_<?=$i?>" class="textbox number required" value="<?=$amount?>" /></td>  
        </tr> 
        <script> $('#date_<?=$i?>').datePicker({startDate: start_date, dateFormat: date_format}); </script>
        <?
        $i++; 
    }
}
?>
<tr>
    <th class="row1"></th>
    <td class="row1"><input type="submit" name="upload" id="upload" value="Submit" class="search_bt" /></td>  
</tr>
</table>
</form>
<? } ?>

</div>

<div id="tooltip_help" style="display:none">
<b>Create a csv:</b><br>
1.Open Excel and Save As from the file menu<br>
2.Select Comma separated values (csv) from the 'File Type' dropdown (below the file name input)<br>
3.Save<br><br>
<b>Payment data:</b><br>
1.Export payments from online banking as csv<br>
2.Or create you payment file from scratch in Excel then Save As csv<br>
3.Three columns required<br>
&nbsp;&nbsp;1.Client<br>
&nbsp;&nbsp;2.Date<br>
&nbsp;&nbsp;3.Amount
</div>

<script>
$(document).ready(function() {
    jQuery("#batchForm").validate();
    
    jQuery("#userForm").validate({
        rules: {
            file: {
                required: true,
                accept: "csv"
            }
        },
        messages: {
            file: {
                accept: "csv files only"
            }
        }
    });
    
    $('a[rel=tooltip]').mouseover(function(e) {
        //Grab the title attribute's value and assign it to a variable
        var tip =  $('#tooltip_help').html();
        
        //Append the tooltip template and its value
        $(this).append('<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">' + tip + '</div><div class="tipFooter"></div></div>');		
        
        //Show the tooltip with faceIn effect
        $('#tooltip').fadeIn('500');
        $('#tooltip').fadeTo('10',0.9);
    
    }).mousemove(function(e) {
        //Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
        $('#tooltip').css('top', e.pageY + 10 );
        $('#tooltip').css('left', e.pageX + 20 );
    }).mouseout(function() {
        //Remove the appended tooltip template
        $(this).children('div#tooltip').remove();
    });
});
</script>

<? }
include('footer.php');
?>