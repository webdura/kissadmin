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
        
        $links = '<a href="groups.php?action=add" title="Add new">Add new</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
        
        break;
}

$page_title = 'Groups';

include('sub_header.php');
if($action=='list') { ?>

<? if($pagination!='') { ?><div class="pagination"><?=$pagination?></div><? } else { echo '<br>';} ?>
<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<div class="client_display">
    <table width="100%" class="client_display_table" cellpadding="3" cellspacing="3">
        <tr height="30">
            <th class="thead"width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
            <th class="thead"><span>Group Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=name&order=ASC"><img src="images/arrowAsc.png"  border="0"/></a>&nbsp;<a href="?<?=$queryString ?>&orderby=name&order=DESC"><img src="images/arrowDec.png"  border="0"/></a></th>
            <th width="10%" class="thead" align="center">Status</th>
            <th width="10%" class="thead" align="center">Discount</th>
            <th width="10%" class="thead" align="center">Services</th>
            <th width="10%" class="thead" align="center">Order</th>
            <th width="10%" class="thead" align="center">Action</th>
        </tr>  
        <?php
        $j=0;
        while($group_row = mysql_fetch_assoc($group_rs))
        {
            $class   = ((($j++)%2)==1) ? 'row2' : 'row1';
            $auto_id = $group_row['id'];
            ?>
            <tr class="<?=$class?>">
                <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
                <td><?=$group_row['name']?></td>
                <td align="center"><?=($group_row['status']==1 ? 'Active' : 'Inactive')?></td>
                <td align="center"><?=($group_row['discount']==1 ? 'Active' : 'Inactive')?></td>
                <td align="center"><?=$group_row['counts']?></td>
                <td align="center"><input type="text" value="<?=$group_row['order']?>" onkeyup="changeOder('<?=$auto_id?>', 'grouporder', this.value)" size="5" style="text-align:center;"></td>
                <td align="center"><a href="groups.php?action=edit&group_id=<?=$auto_id?>">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="groups.php?action=delete&group_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');">Delete</a></td>
            </tr>
            <?php
        }
        if($group_count==0) { ?>
           <tr><td class="message" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
</div>
</form>
<? if($pagination!='') { ?><div class="pagination"><?=$pagination?></div><? } else { echo '<br>';} ?>

<? } else if($action=='edit' || $action=='add') { ?>

<div class="newinvoice">
    <form method="POST" id="userForm" name='userForm'>
        <table width="100%" class="send_credits" cellpadding="13" cellspacing="3">
            <tr><td colspan="12" align="center" class="msg"><?php echo $msg; ?></td></tr>
            <tr><td colspan="13" class="sc_head"><?=$title?><span class="back fright"><a href="groups.php">Back</a></span></td></tr>
        </table>
        <table width="100%" class="send_credits" cellpadding="3" cellspacing="3">
        <tr height="25" valign="middle">
            <th class="row1" align="left" width="30%">Group Name</th>
            <td class="row1"><input type="text" name="name" id="name" class="fleft textbox required" value="<?=@$group_row['name']?>" /></td>  
        </tr> 
        <tr height="25" valign="middle">
            <th class="row1" align="left">Order</th>
            <td class="row1"><input type="text" name="order" id="order" class="fleft textbox required number" value="<?=@$group_row['order']?>" /></td>  
        </tr> 
        <tr height="25">
            <th class="row2" align="left">Discount</th>
            <td class="row2"><input type="checkbox" name="discount" id="discount" value="1" <?=(isset($group_row['discount']) && $group_row['discount']==1) ? 'checked' : ''?> /></td>  
        </tr> 
        <tr height="25">
            <th class="row1" align="left">Active</th>
            <td class="row1"><input type="checkbox" name="status" id="status" value="1" <?=(isset($group_row['status']) && $group_row['status']==1) ? 'checked' : ''?> /></td>  
        </tr> 
        <tr height="25">
            <th class="row2">&nbsp;</th>
            <td class="row2"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td>  
        </tr>
        </table>
    </form>
</div>
<script>
$(document).ready(function() {
    jQuery("#userForm").validate();
});
</script>

<?
}
include('footer.php');
?>