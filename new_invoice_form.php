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
            <b>Order Number : </b><input type="text" name="order_number" value="<?=$orderNo; ?>" maxlength="48">
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

