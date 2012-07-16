<?php
$settings = false;
include("header.php");  
include("config.php");

$action   = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$group_id = (isset($_REQUEST['group_id']) && $_REQUEST['group_id']>0) ? $_REQUEST['group_id'] : 0;
$perPage  = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum  = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$group_sql   = "SELECT *,(SELECT COUNT(*) FROM gma_services WHERE group_id=gma_groups.id) AS counts FROM gma_groups WHERE companyId=".GetSQLValueString($ses_companyId, 'text');
switch ($action)
{
    case 'add':
        $title     = 'Add Group Details';
        if(isset($_POST['name']) && $_POST['name']!='')
        {
            $name     = GetSQLValueString(trim($_POST['name']), 'text');
            $order    = GetSQLValueString($_POST['order'], 'int');
            $discount = isset($_POST['discount']) ? 1 : 0;
            $status   = isset($_POST['status']) ? 1 : 0;
            
            $group_query = "INSERT INTO gma_groups SET companyId=".GetSQLValueString($ses_companyId, 'text').",`name`=$name,`order`=$order,`discount`='$discount',`status`='$status'";
            mysql_query($group_query);
            
            header("Location: groups.php?a");
            exit;
        }
        $group_row['status'] = 1;
        break;
        
    case 'edit':
        $title     = 'Edit Group Details';
        
        if(isset($_POST['name']) && $_POST['name']!='')
        {
            $name     = GetSQLValueString(trim($_POST['name']), 'text');
            $order    = GetSQLValueString(trim($_POST['order']), 'text');
            $discount = isset($_POST['discount']) ? 1 : 0;
            $status   = isset($_POST['status']) ? 1 : 0;
            
            $group_query = "UPDATE gma_groups SET `name`=$name,`order`=$order,`discount`='$discount',`status`='$status' WHERE `id`='$group_id'";
            mysql_query($group_query);
            
            header("Location: groups.php?u");
            exit;
        }
        
        $group_sql .= " AND id='$group_id'";
        $group_rs   = mysql_query($group_sql);
        if(mysql_num_rows($group_rs)!=1)
        {
            header("Location: groups.php?i");
            exit;
        }
        $group_row    = mysql_fetch_assoc($group_rs);
        break;
        
    case 'delete':
        $group_sql .= " AND id='$group_id'";
        $group_rs   = mysql_query($group_sql);
        if(mysql_num_rows($group_rs)!=1)
        {
            header("Location: groups.php?i");
            exit;
        }
        
        $sql = "DELETE FROM gma_groups WHERE id='$group_id'";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_services WHERE group_id='$group_id'";
        mysql_query($sql);
        
        header("Location: groups.php?d");        
        break;
        
    case 'deleteall':
        $group_id   = implode(',', $_REQUEST['delete']);
        $group_sql .= " AND id IN ($group_id)";
        $group_id   = 0;
        $group_rs   = mysql_query($group_sql);
        while($group_row = mysql_fetch_assoc($group_rs))
        {
            $group_id .= ','.$group_row['id'];
        }
        if($group_id=='0')
        {
            header("Location: groups.php?i");
            exit;
        }
        $sql = "DELETE FROM gma_groups WHERE id IN ($group_id)";
        mysql_query($sql);
        
        $sql = "DELETE FROM gma_services WHERE group_id IN ($group_id)";
        mysql_query($sql);
        
        header("Location: groups.php?d");        
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $rowsPerPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY `order` ASC ';
        
        $group_sql  .= ($serviceId>0) ? " AND service_id='$serviceId'" : '';
        $group_sql  .= " $orderBy";
        $group_rs    = mysql_query($group_sql);
        $group_count = mysql_num_rows($group_rs);
        
        $pagination = '';
        if($group_count>$perPage)
        {
            $group_sql  .= " LIMIT $offset, $perPage";
            $group_rs    = mysql_query($group_sql);
            
            $maxPage     = ceil($group_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($group_count, $perPage, 5);
        }
                
        $add_url    = 'groups.php?action=add';
        
        $del_url    = 'javascript:void(0);';
        $del_click  = 'deleteAll();';
        
        break;
}

$page_title = 'Groups';

include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr height="30">
        <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
        <th><span>Group Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=name&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=name&order=DESC" class="desc"></a></th>
        <th width="10%">Status</th>
        <th width="10%">Discount</th>
        <th width="10%">Services</th>
        <th width="10%">Order</th>
        <th width="10%">Action</th>
    </tr>  
    <?php
    $j=0;
    while($group_row = mysql_fetch_assoc($group_rs))
    {
        $class   = ((($j++)%2)==1) ? 'altrow' : '';
        $auto_id = $group_row['id'];
        ?>
        <tr class="<?=$class?>">
            <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
            <td><?=$group_row['name']?></td>
            <td><?=($group_row['status']==1 ? 'Active' : 'Inactive')?></td>
            <td><?=($group_row['discount']==1 ? 'Active' : 'Inactive')?></td>
            <td><?=$group_row['counts']?></td>
            <td><input type="text" value="<?=$group_row['order']?>" onkeyup="changeOder('<?=$auto_id?>', 'grouporder', this.value)" size="5" style="text-align:center;"></td>
            <td><a href="groups.php?action=edit&group_id=<?=$auto_id?>" class="btn_style">Edit</a>&nbsp;<a href="groups.php?action=delete&group_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');" class="btn_style">Delete</a></td>
        </tr>
        <?php
    }
    if($group_count==0) { ?>
       <tr><td class="norecords" colspan="10">No Records Found</td></tr>
    <? } ?>
</table>
</form>
<? if($pagination!='') { ?><div class="pagination"><?=$pagination?></div><? } else { echo '<br>';} ?>

<? } else if($action=='edit' || $action=='add') { ?>

<form method="POST" id="userForm" name='userForm'>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=$title?>&nbsp;<span class="backlink"><a href="groups.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Group Name</td>
        <td><input type="text" name="name" id="name" class="fleft textbox required" value="<?=@$group_row['name']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Order</td>
        <td><input type="text" name="order" id="order" class="fleft textbox required number" value="<?=@$group_row['order']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Discount</td>
        <td><input type="checkbox" name="discount" id="discount" value="1" <?=(isset($group_row['discount']) && $group_row['discount']==1) ? 'checked' : ''?> /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Active</td>
        <td><input type="checkbox" name="status" id="status" value="1" <?=(isset($group_row['status']) && $group_row['status']==1) ? 'checked' : ''?> /></td>  
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