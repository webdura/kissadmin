<?php
$settings = false;
include("header.php");  
include("config.php");

$action   = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$sub_id = (isset($_REQUEST['sub_id']) && $_REQUEST['sub_id']>0) ? $_REQUEST['sub_id'] : 0;
$perPage  = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum  = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$sub_sql   = "SELECT * FROM gma_subscription WHERE 1";
switch ($action)
{
    case 'add':
        $title     = 'Add Subscription Details';
        if(isset($_POST['name']) && $_POST['name']!='')
        {
            $name     = GetSQLValueString(trim($_POST['name']), 'text');
            $amount   = GetSQLValueString($_POST['amount'], 'text');
            $duration = GetSQLValueString($_POST['duration'], 'text');
            $period   = GetSQLValueString($_POST['period'], 'text');
            $status   = isset($_POST['status']) ? 1 : 0;
            
            $sub_query = "INSERT INTO gma_subscription SET `name`=$name,`amount`=$amount,`duration`=$duration,`period`=$period,`status`='$status'";
            mysql_query($sub_query);
            
            header("Location: subscription.php?a");
            exit;
        }
        $sub_row['status'] = 1;
        break;
        
    case 'edit':
        $title     = 'Edit Subscription Details';
        
        if(isset($_POST['name']) && $_POST['name']!='')
        {
            $name     = GetSQLValueString(trim($_POST['name']), 'text');
            $amount   = GetSQLValueString($_POST['amount'], 'text');
            $duration = GetSQLValueString($_POST['duration'], 'text');
            $period   = GetSQLValueString($_POST['period'], 'text');
            $status   = isset($_POST['status']) ? 1 : 0;
            
            $sub_query = "UPDATE gma_subscription SET `name`=$name,`amount`=$amount,`duration`=$duration,`period`=$period,`status`='$status' WHERE `id`='$sub_id'";
            mysql_query($sub_query);
            
            header("Location: subscription.php?u");
            exit;
        }
        
        $sub_sql .= " AND id='$sub_id'";
        $sub_rs   = mysql_query($sub_sql);
        if(mysql_num_rows($sub_rs)!=1)
        {
            header("Location: subscription.php?i");
            exit;
        }
        $sub_row    = mysql_fetch_assoc($sub_rs);
        break;
        
    case 'delete':
        $sub_sql .= " AND id='$sub_id'";
        $sub_rs   = mysql_query($sub_sql);
        if(mysql_num_rows($sub_rs)!=1)
        {
            header("Location: subscription.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_subscription WHERE id='$sub_id'";
        mysql_query($sql);
        
        header("Location: subscription.php?d");        
        break;
        
    case 'deleteall':
        $sub_id   = implode(',', $_REQUEST['delete']);
        $sub_sql .= " AND id IN ($sub_id)";
        $sub_id   = 0;
        $sub_rs   = mysql_query($sub_sql);
        while($sub_row = mysql_fetch_assoc($sub_rs))
        {
            $sub_id .= ','.$sub_row['id'];
        }
        if($sub_id=='0')
        {
            header("Location: subscription.php?i");
            exit;
        }
        $sql = "DELETE FROM gma_subscription WHERE id IN ($sub_id)";
        mysql_query($sql);
        
        header("Location: subscription.php?d");        
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $rowsPerPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY `id` ASC ';
        
        $sub_sql  .= " $orderBy";
        $sub_rs    = mysql_query($sub_sql);
        $sub_count = mysql_num_rows($sub_rs);
        
        $pagination = '';
        if($sub_count>$perPage)
        {
            $sub_sql  .= " LIMIT $offset, $perPage";
            $sub_rs    = mysql_query($sub_sql);
            
            $maxPage     = ceil($sub_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($sub_count, $perPage, 5);
        }
                
        $add_url    = 'subscription.php?action=add';
        
        $del_url    = 'javascript:void(0);';
        $del_click  = 'deleteAll();';
        
        break;
}
$sub_periods = array('M' => 'Month', 'Y' => 'Year');
$page_title = 'Subscriptions Management';
include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr height="30">
        <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
        <th><span>Name</span></th>
        <th width="10%">Duration</th>
        <th width="10%">Amount</th>
        <th width="10%">Status</th>
        <th width="10%">Action</th>
    </tr>  
    <?php
    $j=0;
    while($sub_row = mysql_fetch_assoc($sub_rs))
    {
        $class   = ((($j++)%2)==1) ? 'altrow' : '';
        $auto_id = $sub_row['id'];
        ?>
        <tr class="<?=$class?>">
            <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
            <td><?=$sub_row['name']?></td>
            <td><?=$sub_row['duration'].' '.($sub_periods[$sub_row['period']])?></td>
            <td><?=formatMoney($sub_row['amount'])?></td>
            <td><?=($sub_row['status']==1 ? 'Active' : 'Inactive')?></td>
            <td><a href="subscription.php?action=edit&sub_id=<?=$auto_id?>" class="btn_style">Edit</a>&nbsp;<a href="subscription.php?action=delete&sub_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');" class="btn_style">Delete</a></td>
        </tr>
        <?php
    }
    if($sub_count==0) { ?>
       <tr><td class="norecords" colspan="10">No Records Found</td></tr>
    <? } ?>
</table>
</form>
<? if($pagination!='') { ?><div class="pagination"><?=$pagination?></div><? } else { echo '<br>';} ?>

<? } else if($action=='edit' || $action=='add') { ?>

<form method="POST" id="userForm" name='userForm'>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=$title?>&nbsp;<span class="backlink"><a href="subscription.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Name</td>
        <td><input type="text" name="name" id="name" class="fleft textbox required" value="<?=@$sub_row['name']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Amount</td>
        <td><input type="text" name="amount" id="amount" class="fleft textbox required number" value="<?=@$sub_row['amount']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Duration</td>
        <td><input type="text" name="duration" id="duration" class="fleft textbox required number" value="<?=@$sub_row['duration']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Period</td>
        <td>
            <select name="period" id="period" class="fleft textbox required">
                <option value="">Select any period</option>
                <? foreach ($sub_periods as $key=>$name) {
                    $selected = ($key==$sub_row['period']) ? 'selected' : '';
                    
                    echo "<option value='$key' $selected>$name</option>";
                }
                ?>
            </select>
        </td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Active</td>
        <td><input type="checkbox" name="status" id="status" value="1" <?=(isset($sub_row['status']) && $sub_row['status']==1) ? 'checked' : ''?> /></td>  
    </tr> 
</table>
<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>

<script>
$(document).ready(function() {
    jQuery("#userForm").validate();
});
</script>

<?
}
include('footer.php');
?>