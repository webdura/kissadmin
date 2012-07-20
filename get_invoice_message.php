<?php
$action      = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$account_btn = 'selected';
include("config.php");
if($action=='save' || $action=='view' || $action=='email')
    include("functions.php");
else
    include("header.php");  
    
$page_title = 'Send Invoices';
include('sub_header.php');
?>

<form method="POST" id="userForm" name='userForm'>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Subject</td>
        <td><input type="text" name="service_name" id="service_name" class="fleft textbox required" value="<?=@$service_row['service_name']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Service Description</td>
        <td><textarea name="description" rows="5" cols="30"><?=@$service_row['description']?></textarea>
       </td>  
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

<?php include("footer.php");  ?>