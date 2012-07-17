<form name="invoicefrm" id="invoicefrm" method="post" action="" onsubmit="return validateInvoice();">
<div class="newinvoice" ><div id="all_forms">
<table class="send_credits" cellpadding="7" cellspacing="0" width="100%">
    <tr><td colspan="2" class="">
        <div class="fleft">
            <b>Select Client: </b>
            <select name="userId" id="userId" class="inputbox_green" >
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
    <td colspan="2">
        <table id="invTable" cellpadding="7" cellspacing="2" width="100%" border="1">
            <tr class="sc_subhead" >
                <td class="sc_subhead" width="10%">Service items</td>
                <td class="sc_subhead" width="40%">Description</td>
                <td class="sc_subhead" align="center">Cost</td>     
                <td class="sc_subhead" align="center">Quantity</td>     
                <td class="sc_subhead" align="center">Discount%</td>
                <td class="sc_subhead" align="right">Amount</td>
            </tr>
<?php

	if(count($ordersDetails) > 0 ){
		for($i=0; $i<count($ordersDetails); $i++){
	
			if ($ordersDetails[$i]['service_id'] > 0)
				echo str_replace('test', $i + 1, generateTableRow($allGroups, $ordersDetails[$i]));
			//print_r($ordersDetails); exit;
		}
	}
    else{
		for($i=0; $i<3; $i++){
			$ordersDetails = array();
			echo str_replace('test', $i + 1, generateTableRow($allGroups, $ordersDetails));
			//print_r($ordersDetails); exit;
		}
    	
    }
    
?>
         </table>
    </td>    
    </tr>

<tr>
	<td><input type="button" onclick="addRow()" value="Add Line"></td>
	<td height="40" align="right" class="total"><span style="padding-right: 40px;">INVOICE TOTAL</span><input type="text" style="text-align:right;height:20px" class="invoicetextbox_green" name="total" id="total" value="<?= sprintf("%01.2f", $total);?>" size="10" readonly /></td></tr>
    <tr><td colspan="2" align="right"><input type="submit" class="search_bt" name="save" id="save" value="Save" /><input type="submit" class="search_bt" name="sendMail" id="sendMail" value="Save & Send"/></td></tr>
</table>
</div>
</div> 
<input type="hidden" name="vvcount" id="vvcount" value="1" />
<input type="hidden" name="cccount" id="cccount" value="1" />



</form>
<script>
var count = <?=$i + 1; ?>;

<?php

echo "function addRow(){";
	
echo "var tableRow = '". generateTableRow($allGroups) . "';";
echo "for(var i=0;i<20;i++)";
echo 'tableRow = tableRow.replace("test", count);';


echo "$('#invTable').append(tableRow);";
echo "count = eval(count) + 1;";
echo "}";
?> 
</script>



<?php
function generateTableRow($allGroups, $ordersDetail){

	$selectService='<tr><td class="sc_subhead"><select class="inputbox_green" style="width:300px;" name="service_id[test]" id="service_id_test" onChange="checkAmountandDiscount(test)">';
	$selectService.='<option value="">Select</option>';
	
	foreach ($allGroups as $allGroupsKey=>$allGroupsValues) {
		$selectService.='<option value="" disabled="disabled">'. $allGroups[$allGroupsKey]['name']. '</option>';
		foreach ($allGroupsValues['services'] as $service){

	//	reset($ordersDetails);
				$sel = '';
				//echo $service['id'] . "== " . $ordersDetail['service_id']; exit;
				if($service['id']==$ordersDetail['service_id']){
					$sel = '"selected = "selected"'; 
				}
				
			$selectService.='<option value="'. $service['id']. '_'. $service['group_id'] . $sel . '">&nbsp;&nbsp;&nbsp;&nbsp;'. $service['service_name']. '</option>';
			$sel = '';
	}
	}
	$selectService.='</select></td>';

	$selectService.='<td class="row2" id="creditquantity_div">';
	$selectService.='<div id="description_test">'. $ordersDetail['description']. '</div>';
	$selectService.='</td>';
	
	$selectService.='<td class="row2" align="center" id="creditquantity_div">';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="cost[test]" id="cost_test" readonly value="'. $ordersDetail['cost']. '">';
	$selectService.='</td>';
	
	$selectService.='<td class="row2" align="center" id="creditquantity_div" >';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="invoicetextboxtxt_green" name="quantity[test]" id="quantity_test" onChange="changeFormTotal(test)" value="'. $ordersDetail['quantity']. '">';
	$selectService.='</td>';
	$selectService.='<td class="row2" align="center" id="creditquantity_div" >';
	$selectService.='<input type="text" size="10" style="text-align:right;" class="<?=$discount_style?>" name="discount[test]" id="discount_test" onChange="changeFormTotal(test)" value="'. $ordersDetail['discount']. '" />';
	$selectService.='</td>';
	$selectService.='<td class="row2" align="right" >';
	$selectService.='<input type="text" size="10" style="text-align:right;" name="amount[test]" id="amount_test" class="invoicetextbox_green" value="'. sprintf("%01.2f", $ordersDetail['amount']). '" readonly />';
                       
	$selectService.='</td>';
	
	$selectService.='</tr>';
	
	return $selectService;
	
//	print_r($allGroups);
}

?>