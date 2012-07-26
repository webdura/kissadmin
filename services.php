<?php
$settings = false;
include("header.php");  
include("config.php");

$action     = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$service_id = (isset($_REQUEST['service_id']) && $_REQUEST['service_id']>0) ? $_REQUEST['service_id'] : 0;
$group_id   = (isset($_REQUEST['group_id']) && $_REQUEST['group_id']>0) ? $_REQUEST['group_id'] : 0;
$perPage    = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 500;
$pageNum    = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$service_sql = "SELECT gma_services.*,gma_groups.name as group_name FROM gma_services,gma_groups WHERE group_id=gma_groups.id AND companyId=".GetSQLValueString($ses_companyId, 'text');
switch ($action)
{
    case 'add':
        $title     = 'Add Service Details';
        if(isset($_POST['service_name']) && $_POST['service_name']!='')
        {
            $service_name = GetSQLValueString(trim($_POST['service_name']), 'text');
            $description = GetSQLValueString(trim($_POST['description']), 'text');
            $group_id     = GetSQLValueString(trim($_POST['group_id']), 'text');
            $amount       = GetSQLValueString(trim($_POST['amount']), 'text');
            $order        = GetSQLValueString(trim($_POST['order']), 'text');
            $status       = isset($_POST['status']) ? 1 : 0;
            
            $service_query = "INSERT INTO gma_services SET service_name=$service_name, description=$description,group_id=$group_id,amount=$amount,`order`=$order,status=$status";
            mysql_query($service_query);
            
            header("Location: services.php?a");
            exit;
        }
        $service_row['status'] = 1;
        break;
        
    case 'edit':
        $title     = 'Edit Service Details';
        
        if(isset($_POST['service_name']) && $_POST['service_name']!='')
        {
            $service_name = GetSQLValueString(trim($_POST['service_name']), 'text');
            $description = GetSQLValueString(trim($_POST['description']), 'text');
            $group_id     = GetSQLValueString(trim($_POST['group_id']), 'text');
            $amount       = GetSQLValueString(trim($_POST['amount']), 'text');
            $order        = GetSQLValueString(trim($_POST['order']), 'text');
            $status       = isset($_POST['status']) ? 1 : 0;
            
            $service_query = "UPDATE gma_services SET service_name=$service_name, description=$description, group_id=$group_id,amount=$amount,`order`=$order,status=$status WHERE id=$service_id";
            mysql_query($service_query);
            
            header("Location: services.php?u");
            exit;
        }
        
        $service_sql .= " AND gma_services.id='$service_id'";
        $service_rs   = mysql_query($service_sql);
        if(mysql_num_rows($service_rs)!=1)
        {
            header("Location: services.php?i");
            exit;
        }
        $service_row    = mysql_fetch_assoc($service_rs);
        
        break;
        
    case 'delete':
        $service_sql .= " AND gma_services.id='$service_id'";
        $service_rs   = mysql_query($service_sql);
        if(mysql_num_rows($service_rs)!=1)
        {
            header("Location: services.php?i");
            exit;
        }
        $sql = "DELETE FROM gma_services WHERE id='$service_id'";
        mysql_query($sql);
        
        header("Location: services.php?d");        
        break;
        
    case 'deleteall':
        $service_id   = implode(',', $_REQUEST['delete']);
        $service_sql .= " AND gma_services.id IN ($service_id)";
        $service_id   = 0;
        $service_rs   = mysql_query($service_sql);
        while($service_row = mysql_fetch_assoc($service_rs))
        {
            $service_id .= ','.$service_row['id'];
        }
        if($service_id=='0')
        {
            header("Location: groups.php?i");
            exit;
        }
        $sql = "DELETE FROM gma_services WHERE id IN ($service_id)";
        mysql_query($sql);
        
        header("Location: services.php?d");        
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
            // echo '<pre>'; print_r($_POST); exit;
            foreach ($_POST['add'] as $key)
            {
                $group_id     = GetSQLValueString($_POST['group_id'][$key], 'int');
                $group_name   = GetSQLValueString($_POST['group_name'][$key], 'text');
                $service_name = GetSQLValueString($_POST['service_name'][$key], 'text');
                $description  = GetSQLValueString($_POST['description'][$key], 'text');
                $amount       = GetSQLValueString($_POST['amount'][$key], 'text');
                
                if($group_id==0)
                {
                    $sql = "SELECT * FROM gma_groups WHERE companyId=".GetSQLValueString($ses_companyId, 'text')." AND name=$group_name";
                    $rs  = mysql_query($sql);
                    if(mysql_num_rows($rs)==0)
                    {
                        $sql = "INSERT INTO gma_groups SET companyId=".GetSQLValueString($ses_companyId, 'text').",name=$group_name";
                        mysql_query($sql);
                        $group_id = mysql_insert_id();
                        $order    = 1;
                    }
                    else 
                    {
                        $row = mysql_fetch_assoc($rs);
                        $group_id = $row['id'];
                    }
                }
                
                if($group_id>0)
                {
                    $sql = "SELECT MAX(`order`) AS `order_new` FROM gma_services WHERE group_id='$group_id'";
                    $rs  = mysql_query($sql);
                    $row = mysql_fetch_assoc($rs);
                    $order = $row['order_new'] + 1;
                    
                    $values  = "group_id=$group_id,service_name=$service_name,description=$description,amount=$amount,`order`='$order'";     
                    
                    $sql = "INSERT INTO gma_services SET $values";
                    mysql_query($sql);
                }                
            }
            header("Location: services.php?msg=added");
            exit;
        }   
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = 'ORDER BY '.(($_REQUEST['orderby']!='') ? $_REQUEST['orderby'].' '.$_REQUEST['order'].',' : '')."gma_groups.`name` ASC, gma_services.`service_name` ASC";
        
        $service_sql  .= ($group_id>0) ? " AND group_id='$group_id'" : '';
        $service_sql  .= " $orderBy";
        $service_rs    = mysql_query($service_sql);
        $service_count = mysql_num_rows($service_rs);
        
        $pagination = '';
        
//        $links = '<a href="groups.php" title="Groups">Groups</a><a href="services.php?action=import" title="Import services">Import services</a><a href="services.php?action=add" title="Add new">Add new</a><a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
        
        $add_url    = 'services.php?action=add';
        
        $del_url    = 'javascript:void(0);';
        $del_click  = 'deleteAll();';
        
        $other_urls[] = array('text'=>'Import', 'sign'=>'+', 'url'=>'services.php?action=import', 'click'=>'');
        $other_urls[] = array('text'=>'Groups', 'sign'=>'+', 'url'=>'groups.php', 'click'=>'');
        
        $group_search = true;
        
        break;
}

$group_rows = array();
$group_sql = "SELECT * FROM gma_groups WHERE companyId=".GetSQLValueString($ses_companyId, 'text')." ORDER BY name ASC";
$group_rs  = mysql_query($group_sql);
while ($group_row = mysql_fetch_assoc($group_rs)) {
	   $group_rows[] = $group_row;
}

$page_title = 'Services';

include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<div class="client_display">
    <table width="100%" class="list" cellpadding="0" cellspacing="0">
        <tr height="30">
            <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
            <th width="30%"><span>Group</span>&nbsp;<a href="?<?=$queryString ?>&orderby=group_name&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=group_name&order=DESC" class="desc"></a></th>
            <th><span>Service Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=service_name&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=service_name&order=DESC" class="desc"></a></th>
            <th width="10%"><span>Amount</span>&nbsp;<a href="?<?=$queryString ?>&orderby=amount&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=amount&order=DESC" class="desc"></a></th>
            <th width="10%">Order</th>
            <th width="10%">Status</th>
            <th width="10%">Action</th>
        </tr>  
        <?php
        $j=0;
        while($service_row = mysql_fetch_assoc($service_rs))
        {
            $class   = ((($j++)%2)==1) ? 'altrow' : '';
            $auto_id = $service_row['id'];
            ?>
            <tr class="<?=$class?>">
                <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
                <td><?=$service_row['group_name']?></td>
                <td><?=$service_row['service_name']?></td>
                <td><?=formatMoney($service_row['amount'], true)?></td>
                <td><input type="text" value="<?=$service_row['order']?>" onkeyup="changeOder('<?=$auto_id?>', 'serviceorder', this.value)" size="5" style="text-align:center;"></td>
                <td><?=($service_row['status']==1 ? 'Active' : 'Inactive')?></td>
                <td><a href="services.php?action=edit&service_id=<?=$auto_id?>" class="btn_style">Edit</a>&nbsp;<a href="services.php?action=delete&service_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');" class="btn_style">Delete</a></td>
            </tr>
            <?php
        }
        if($service_count==0) { ?>
           <tr><td class="norecords" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
</div>
</form>

<? } else if($action=='edit' || $action=='add') { ?>

<form method="POST" id="userForm" name='userForm'>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=$title?>&nbsp;<span class="backlink"><a href="services.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Service Name</td>
        <td><input type="text" name="service_name" id="service_name" class="fleft textbox required" value="<?=@$service_row['service_name']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Service Description</td>
        <td><input type="text" name="description" id="description" class="fleft textbox" value="<?=@$service_row['description']?>" /></td> 
        <!--<td><textarea name="description" rows="5" cols="30"><?=@$service_row['description']?></textarea></td>  -->
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Group</td>
        <td>
            <select name="group_id" id="group_id" class="fleft textbox required">
                <option value="">Select any group</option>
                <? foreach ($group_rows as $group) {
                    $selected = ($group['id']==$service_row['group_id']) ? 'selected' : '';
                    
                    echo "<option value='".$group['id']."' $selected>".$group['name']."</option>";
                }
                ?>
            </select>
        </td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Amount</td>
        <td><input type="text" name="amount" id="amount" class="fleft textbox required number" value="<?=$service_row['amount']?>"/></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Order</td>
        <td><input type="text" name="order" id="order" class="fleft textbox required number" value="<?=$service_row['order']?>"/></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Active</td>
        <td><input type="checkbox" name="status" id="status" value="1" <?=(isset($service_row['status']) && $service_row['status']==1) ? 'checked' : ''?> /></td>
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
} else if($action=='import') { ?>    
    <? if($import==0) { ?>    
        <form method="POST" id="userForm" name='userForm' enctype="multipart/form-data">
        <table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
            <tr><th colspan="3">Import services&nbsp;<span class="backlink"><a href="services.php">Back</a></span></td></tr>
            <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
                <td width="20%">File (csv files only)</td>
                <td><input type="file" name="file" id="file" class="required"></td>  
            </tr> 
            <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
                <td></td>
                <td><a href="images/services.csv" target="_blank" class="links">Click here</a> for sample CSV file.</td>  
            </tr>
        </table>
        <div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
        </form>
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
                        accept: "Only csv files supported"
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
    <? } elseif($import==1) { ?>
        <form method="POST" id="batchForm" name='batchForm' enctype="multipart/form-data">
        <table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
        <tr>
            <th width="10%"><b>Add</b></th>
            <th width="30%"><b>Group</b></th>
            <th width="30%"><b>Service</b></th>
            <th width="30%"><b>Description</b></th>
            <th width="30%"><b>Amount</b></th>
        </tr>
        <?
        $i=1;
        $filename = 'admin_'.$ses_userId.'.csv';
        $fp = fopen($filename, 'r');
        while ($row = fgetcsv($fp)) {
            $group        = $row[0];
            $service_name = $row[1]; 
            $description  = $row[2]; 
            $amount       = $row[3]; 
            
            $group_details  = '';
            $group_exist    = 0;
            foreach ($group_rows as $group_row) { 
                $group_id   = $group_row['id'];
                $group_name = $group_row['name'];
                
                $selected    = ($group_name==$group) ? 'selected' : '';
                $group_exist = ($group_name==$group) ? 1 : $group_exist;
                
                $group_details .= "<option value='$group_id' $selected>$group_name</option>";
            }
            if($group_exist)
                $group_prefix = '<option value="" selected>Select group</option>';
            else
                $group_prefix = '<option value="0" selected>Add as new group "'.$group.'"</option>';
            $group_details  = '<select name="group_id['.$i.']" id="group_id_'.$i.'" class="textbox required">'.$group_prefix.$group_details.'</select>';
                        
            if($amount>0) { 
                ?>
                <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
                    <td><input type="checkbox" name="add[<?=$i?>]" id="add_<?=$i?>" value="<?=$i?>" checked /></td>  
                    <td><?=$group_details?><input type="hidden" name="group_name[<?=$i?>]" id="group_name_<?=$i?>" class="textbox required" value="<?=$group?>" /></td>  
                    <td><input type="text" name="service_name[<?=$i?>]" id="service_name_<?=$i?>" class="textbox required" value="<?=$service_name?>" /></td>  
                    <td><input type="text" name="description[<?=$i?>]" id="description_<?=$i?>" class="textbox" value="<?=$description?>" /></td>  
                    <td><input type="text" name="amount[<?=$i?>]" id="amount_<?=$i?>" class="textbox number required" value="<?=$amount?>" /></td>  
                </tr> 
                <?
                $i++; 
            }
        }
        ?>
        </table>
        <div class="addedit_btn"><input type="submit" name="upload" id="upload" value="Submit" class="btn_style" /></div>
        </form>
    <? } ?>
<? }
include('footer.php');
?>