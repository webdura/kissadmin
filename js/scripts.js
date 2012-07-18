
jQuery.validator.addMethod("url_new", function(value, element) { 
  return this.optional(element) || /^(?:http:\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
}, "Please enter a valid URL.");

jQuery.validator.addMethod("subdomain", function(value, element) { return /^[a-zA-Z0-9]+$/.test(value); }, "Please enter only numbers or letters");

jQuery.validator.addMethod("file_size", function(value, element) { 
    var element_id = element.id
        
    var fileInput = $("#" + element_id)[0];
    
    //alert(fileInput.value);
    if (navigator.appName == "Microsoft Internet Explorer") {
        if (fileInput.value) {
            var oas = new ActiveXObject("Scripting.FileSystemObject");
            var e = oas.getFile(fileInput.value);
            var size = e.size;
        }
    }
    else if (fileInput.value != null && fileInput.files[0] != undefined)
    {
        var size = fileInput.files[0].fileSize;
    }
    if (size > 2097152)
        return false;
    else
        return true;      
}, "Maximum allowded size is 2 MB");




function paymentExport() {
    var ids = 0;
    jQuery.each($('input[id=delete]'), function() {
        if(this.checked)
            ids = ids + ',' + this.value;
    });
    
    if(ids!=0)
        window.location.href = "payments.php?action=export&parm="+ids;
    else
    {
        alert('Please select any payments')
        return false;
    }
}

function validateInvoice()	{
    var selclient = document.getElementById("userId");
    if(selclient.value=='')	{
        alert("Select the Client");
        return false;
    }
    return true;
}

function settingsTab()
{
    if($('#settings').css('display')=='block')
        $('#settings').css('display', 'none')
    else
        $('#settings').css('display', '')
}

function changeOder(id, task, order)
{
    $.post("ajax_check.php", { id: id, task: task, order: order } );
}

function addNewServiceRow(group_id)
{
    count = eval(count) + 1;
    
    var html = $('#service_'+group_id).html();
    
    
    for(var i=0;i<20;i++)
        html = html.replace("test", count)
        
    $('#service_list_'+group_id).append(html);
    selectClientDiscountToAll(group_id, count)

}

function removeServiceRow(div_id)
{
    $('#div_'+div_id).remove();
}

function checkAmount(div_id)
{
    service_id = $('#service_id_'+div_id).val();
    $.post("ajax_check.php", { service_id: service_id, task: 'checkAmount' }, function(data) {
        $('#cost_'+div_id).val(data);
        
        changeInvoice(div_id)
    });
}


var discounts = new Array();
function selectClientDiscount()
{
    var userId   = $('#userId').val();
    $.post("ajax_check.php", { userId: userId, task: 'checkDiscount' }, function(data) {
        var discount = data.split('~!~');
        var length   = discount.length;
        
        var i, group_id, value, data_new;
        for(var i=0;i<length;i++) {
            data_new = discount[i].split('~~~');            
            group_id = data_new[0];
            value    = data_new[1];
            
            discounts[group_id] = value;
        }
        
        jQuery.each($('.service'), function(i, val) {
            group_id = $(val).val();
            box_id   = $(val).attr('id');
            if(group_id!='' && group_id!='0') {
                group_id = group_id.split('_');
                group_id = group_id[1];
                
                discount = discounts[group_id];
                new_box  = box_id.replace("service_id_", 'discount_');
                //if($('#'+new_box).val()==0 || $('#'+new_box).val()=='')
                $('#'+new_box).val(discount);
            }
        });
        
        changeTotal();
    });
}

function checkAmountandDiscount(div_id)
{
    service_id = $('#service_id_'+div_id).val();
    $.post("ajax_check.php", { service_id: service_id, task: 'checkAmountandDiscount' }, function(data) {
     
        var discount = 0;
        if(service_id!='' && service_id!='0') {
            group_id = service_id.split('_');
            group_id = group_id[1];
            
            if(discounts[group_id])
                discount = discounts[group_id];
        }
        
        $('#cost_'+div_id).val(data.amount);
        $('#discount_'+div_id).val(discount);
        $('#quantity_'+div_id).val(data.quantity);
        $('#description_'+div_id).html(data.description);
        
        changeTotal()
    }, 'json');
}

function changeTotal()
{
    var total = 0;
    for(i=1;i<count;i++)
    {
        if($('#cost_'+i))
        {
            var cost     = $('#cost_'+i).val();
            var quantity = $('#quantity_'+i).val();
            var discount = $('#discount_'+i).val();
            var discount_val = 0;
            
            if(quantity<=0 && cost>0)
                quantity = 1;
            $('#quantity_'+i).val(quantity);
            
            var amount   = cost * quantity;
            
            if(discount>0)
                discount_val = ((amount * discount) / 100);
                
            amount =  (amount - discount_val);
            total  = total + amount;
            
            amount =  amount.toFixed(2);
            $('#amount_'+i).val(amount);
        }
    }
    total = total.toFixed(2);
    $('#total').val(total);
}

function selectClientDiscountToAll(group_id, newId)
{
	
	if(newId == undefined) {
	
	    jQuery.each($('#service_list_'+group_id+' input'), function(i, val) {
	        cost_id = val.id;
	         if(cost_id.search("discount_")>=0 && cost_id != 'discount_test')
	        {
	        	$('#'+cost_id).val($('#service_'+group_id).find('#discount_test').val());
	        }
	    });
	}
	else
	{
		$('#service_list_'+group_id).find('#discount_'+newId).val($('#service_'+group_id).find('#discount_test').val());
	}

}

function changeTheme(theme_id)
{
    $.post("ajax_check.php", { theme_id: theme_id, task: 'changeTheme' }, function(data) {
     
        $('#head_bg').css("background-color", data.head_bg);
        $('#head_color').css("background-color", data.head_color);
        $('#site_logo_div').css("background-color", data.head_bg);
//        $('#color1').css("background-color", data.color1);
//        $('#color2').css("background-color", data.color2);
//        $('#color3').css("background-color", data.color3);
//        $('#color4').css("background-color", data.color4);
        
        $('#head_bg').val(data.head_bg);
        $('#head_color').val(data.head_color);
//        $('#color1').val(data.color1);
//        $('#color2').val(data.color2);
//        $('#color3').val(data.color3);
//        $('#color4').val(data.color4);
        
    }, 'json');
}

function checkUncheck(checkbox)
{
    var checked = true;
    if(!checkbox.checked)
        checked = false;
        
    jQuery.each($('input[id=delete]'), function() {
        this.checked = checked;
    });
}

function deleteAll()
{
    var ids = 0;
    jQuery.each($('input[id=delete]'), function() {
        if(this.checked)
            ids = ids + ',' + this.value;
    });
    
    if(ids==0)
    {
        alert("Please select any records to delete ");
        return false;
    }
    $('#listForm').submit();
}


function checkAmountQuotation(div_id)
{
    service_id = $('#service_id_'+div_id).val();
    $.post("ajax_check.php", { service_id: service_id, task: 'checkAmount' }, function(data) {
        $('#cost_'+div_id).val(data);
        
        changeQuotation(div_id)
    });
}
function validateQuotation()	{
    var selclient = document.getElementById("userId");
    if(selclient.value=='')	{
        alert("Select the Client");
        return false;
    }
    return true;
}
function changeQuotation(div_id)
{
    //alert(div_id + ' == ' + count)
    var total = 0;
    for(i=1;i<=count;i++)
    {
        if($('#cost_'+i))
        {
            var cost     = $('#cost_'+i).val();
            var quantity = $('#quantity_'+i).val();
            var discount = $('#discount_'+i).val();
            var discount_val = 0;
            
            if(quantity<=0 && cost>0)
                quantity = 1;
            $('#quantity_'+i).val(quantity);
            
            var amount   = cost * quantity;
            
            if(discount>0)
                discount_val = ((amount * discount) / 100);
                
            amount = amount - discount_val;
            total  = total + amount;
            
            //alert(amount + '==' + discount + '==' + discount_val + '==' + total)
            $('#amount_'+i).val(amount);
        }
    }
    $('#total').val(total);
}

function userOrders(user_id) {   
    $('#pending_amount_div').css('display', 'none');
    $('#pending_amount').val(0);
    
    var payment_id = $('#payment_id').val(); 
    $.post("ajax_check.php", { user_id: user_id, payment_id: payment_id, task: 'userOrders' }, function(data) {
        $('#order_div').html(data.orders);
        $('#pending_amount').val(data.pending_amount);
        
        if(data.pending_amount>0) {
            $('#pending_amount_td').html(data.pending_amount_div);
            $('#pending_amount_div').css('display', 'block');
        }
    }, 'json'); 
}

function checkOrderAmount() {
    var orderId = 0;
    var user_id = $('#userId').val();
    var amount  = $('#amount').val();
    jQuery.each($('input[id=orderId]'), function() {
        if(this.checked)
            orderId = orderId + ',' + this.value;
    });
    
    amount = amount + $('#pending_amount').val();
    
    $.post("ajax_check.php", { user_id: user_id, orderId: orderId, amount: amount, task: 'checkOrderAmount' }, function(data) {
        // alert(data.result)
        if(data.result=='error') {
            alert(data.message);
            return false;
        } else {
            document.userForm.submit();
        }
    }, 'json');
    
    return false;
}