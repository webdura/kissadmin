<form name="invoicefrm" id="invoicefrm" method="post" action="" onsubmit="return validateInvoice();">
<div class="newinvoice" style="width:70%"><div id="all_forms">
<table class="send_credits" cellpadding="7" cellspacing="0" width="100%">
    <tr><td colspan="5" class="">
        <div class="fleft">
            <b>Select Client: </b>
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

        <?php
        	$script = explode("/", $_SERVER['SCRIPT_NAME']); 
         	if ($script[count($script) - 1] != 'quotations.php')
         	{
         ?>
		       <div class="fright">
		            <b>Order Number : </b><input type="text" name="order_number" value="<?=$orderNo; ?>" maxlength="48">
		        </div>
		  <?php } ?>
    </td></tr>
    <tr>
    <td>
        <table id="invTable" cellpadding="7" cellspacing="2" width="100%" border="1">
            <tr class="sc_subhead" >
                <td class="sc_subhead">Service items</td>
                <td class="sc_subhead" width="17%" align="center">Cost</td>     
                <td class="sc_subhead" width="17%" align="center">Quantity</td>     
                <td class="sc_subhead" width="17%" align="center">Discount%</td>
                <td class="sc_subhead" width="17%" align="right">Amount</td>
            </tr>
         </table>
    </td>
    
    </tr>
<? $i = 0; ?>
<tr><td colspan="5" height="40" align="right" class="total"><span style="padding-right: 40px;">INVOICE TOTAL</span><input type="text" style="text-align:right;height:20px" class="invoicetextbox_green" name="total" id="total" value="<?=$total?>" size="10" readonly /></td></tr>
    <tr><td colspan="5" align="right"><input type="submit" class="search_bt" name="save" id="save" value="Save" /><input type="submit" class="search_bt" name="sendMail" id="sendMail" value="Save & Send"/></td></tr>
</table>
</div>
</div> 
<input type="hidden" name="vvcount" id="vvcount" value="1" />
<input type="hidden" name="cccount" id="cccount" value="1" />

<input type="button" onclick="addRow()">

</form>
<script>
var count = <?=$i?>;

function addRow(){
	
	var htmll = $('#invTableRow').text();
/*$('#invTable').append('<tr><td class="sc_subhead"><select  class="inputbox_green" style="width:300px;" name="service_id[test]" id="service_id_test" onchange="checkAmount(test)"><option value="">Select</option></select></td><td class="sc_subhead" width="17%" align="center">Cost</td><td class="sc_subhead" width="17%" align="center">Quantity</td><td class="sc_subhead" width="17%" align="center">Discount%</td><td class="sc_subhead" width="17%" align="right">Amount</td></tr>'); */
	$('#invTable').append('<?php echo generateTableRow($allGroups); ?>');
}

</script>

<?php

function generateTableRow($allGroups){

	$selectService='<tr><td class="sc_subhead"><select  class="inputbox_green" style="width:300px;" name="service_id[test]" id="service_id_test" onchange="checkAmount(test)">';
	$selectService.='<option value="">Select</option>';
	
	foreach ($allGroups as $allGroupsKey=>$allGroupsValues) {
		$selectService.='<option value="" disabled="disabled">'. $allGroups[$allGroupsKey]['name']. '</option>';
		foreach ($allGroupsValues['services'] as $service) 
			$selectService.='<option value="'. $service['id']. '_'. $service['group_id'] .'">&nbsp;&nbsp;&nbsp;&nbsp;'. $service['service_name']. '</option>';
	}
		
	$selectService.='</select></td>';
	$selectService.='<td class="row2" align="center" id="creditquantity_div">';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="cost[test]" id="cost_test" readonly onchange="changeInvoice(test)" value="0">';
	$selectService.='</td>';
	
	$selectService.='<td class="row2" align="center" id="creditquantity_div" width="17%">';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="quantity[test]" id="quantity_test" onchange="changeInvoice(test)" value="0">';
	$selectService.='</td>';
	$selectService.='<td class="row2" align="center" id="creditquantity_div" width="17%">';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="<?=$discount_style?>" name="discount[test]" id="discount_test" value=" " onchange="changeInvoice(test)" />';
	$selectService.='</td>';
	$selectService.='<td class="row2" align="right" width="17%">';
	$selectService.='<input type="text" size="10" style="text-align:right;" name="amount[test]" id="amount_test" class="invoicetextbox_green" value="0" readonly />';
                       
	$selectService.='</td>';
	
	$selectService.='</tr>';
	
	return $selectService;
	
//	print_r($allGroups);
}

?>