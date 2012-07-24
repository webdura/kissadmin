<?php
function generateTableRow($ordersDetail) {
    global $allGroups;
    
    $service_box  = '<select class="textbox service" name="service_id[~~test~~]" id="service_id_~~test~~" onChange="checkAmountandDiscount(~~test~~)">';
    $service_box .= "<option value=''>Select</option>";
    foreach ($allGroups as $main_group) {
        $service_box .= "<optgroup label='{$main_group['name']}'>";
        foreach ($main_group['services'] as $service){
            $key   = $service['id']. '_'. $service['group_id'];
            $value = $service['service_name'];
            
            $selected = ($service['id']==$ordersDetail['service_id']) ? 'selected' : '';
            
            $service_box .= "<option value='$key' $selected>$value</option>";
        }
        $service_box .= '</optgroup>';
    }
    $service_box .= '</select>';

    $result = "<tr class='~~class~~'>
                    <td>$service_box</td>
                    <td id='creditquantity_div'><div id='description_~~test~~'>{$ordersDetail['serviceName']}</div></td>
                    <td class='row2' id='creditquantity_div'><input type='text' size='10' style='text-align:right;' class='invoicetextboxtxt_green' name='cost[~~test~~]' id='cost_~~test~~' readonly value='{$ordersDetail['cost']}'></td>
                    <td class='row2' id='creditquantity_div' ><input type='text' size='10' style='text-align:right;' class='invoicetextboxtxt_green' name='quantity[~~test~~]' id='quantity_~~test~~' onChange='changeTotal(~~test~~)' value='{$ordersDetail['quantity']}'></td>
                    <td class='row2' id='creditquantity_div' ><input type='text' size='10' style='text-align:right;' class='{$discount_style}' name='discount[~~test~~]' id='discount_~~test~~' onChange='changeTotal(~~test~~)' value='{$ordersDetail['discount']}' /></td>
                    <td class='row2' ><input type='text' size='10' style='text-align:right;' name='amount[~~test~~]' id='amount_~~test~~' class='invoicetextbox_green' value='".sprintf('%01.2f', $ordersDetail['amount'])."' readonly /></td>
                </tr>";
    
    return $result;
}
$list_org = generateTableRow(array());
?>
<form name="invoicefrm" id="invoicefrm" method="post" action="" onsubmit="return validateInvoice();">
<table cellpadding="7" cellspacing="0" width="100%">
<tr><td colspan="2" class="">
    <div class="fleft">
        <b>Select Client&nbsp;:&nbsp;</b>
        <select name="userId" id="userId" class="textbox" onchange="selectClientDiscount();" >
            <option value="" selected>Select Client</option>
            <?php
            foreach ($company_users as $user) {
                $user_id  = $user['userId'];
                $name     = $user['businessName'].' - '.$user['userName'];
                $selected = ($userId==$user_id) ? 'selected' : '';
                
                echo "<option value='$user_id' $selected>$name</option>";
            }
            ?>
        </select>
    </div>
    <? if($invoice) { ?>
    <div class="fright"><b>Order Number&nbsp;:&nbsp;</b><input type="text" name="order_number" value="<?=$orderNo; ?>" class="textbox" maxlength="48" style="width:150px"></div>
    <? } ?>
</td></tr>
<tr><td colspan="2">
    <table width="100%" class="list" id="invTable" cellpadding="0" cellspacing="0">
        <tr>
            <th width="10%">Service items</th>
            <th width="40%">Description</th>
            <th>Cost</th>     
            <th>Quantity</th>     
            <th>Discount%</th>
            <th>Amount</th>
        </tr>
        <?php
        if(count($orderDetails) > 0 ){
            for($i=1; $i<=count($orderDetails); $i++) {
                if ($orderDetails[$i-1]['service_id'] > 0) {
                    $list = generateTableRow($orderDetails[$i-1]);
                    
                    $list = str_replace('~~test~~', $i, $list);
                    $list = str_replace('~~class~~', ($i%2==0 ? 'altrow' : ''), $list);
                    
                    echo $list;
                }
            }
        } else {
            for($i=1; $i<=5; $i++){
                $list = str_replace('~~test~~', $i, $list_org);
                $list = str_replace('~~class~~', ($i%2==0 ? 'altrow' : ''), $list);
                
                echo $list;
            }
        }
        ?>
    </table>
</td></tr>
<tr>
    <td><input type="button" onclick="addRow()" value="Add Line"></td>
    <td height="40" class="total" align="right" style="padding-right: 23px;">
    	<span style="padding-right: 40px;">INVOICE TOTAL</span>
    	<input type="text" style="text-align:right;height:20px" class="invoicetextbox_green" name="total" id="total" value="<?= sprintf("%01.2f", $total);?>" size="10" readonly />
    </td>
</tr>
</table>
<div class="addedit_btn fright"><input type="submit" name="save" id="save" value="Save as Draft" class="btn_style" />&nbsp;&nbsp;<input type="submit" name="sendMail" id="sendMail" value="Send by Mail" class="btn_style" /></div>

</form>
<table id="test_div" style="display:none"><?=$list_org?></table>
<script>
var count = <?=$i?>;
function addRow(){
    var tableRow  = $('#test_div').html();
    var classname = '';
    
    for(var i=0;i<100;i++)
        tableRow = tableRow.replace("~~test~~", count);
        
    if(count%2==0) classname='altrow';
    tableRow = tableRow.replace("~~class~~", classname);
        
    $('#invTable').append(tableRow);
    count = eval(count) + 1;
}
<? if($action='add') { ?>
    selectClientDiscount()
<? } ?>
</script>