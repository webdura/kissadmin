<link rel="stylesheet" href="js/ui/themes/smoothness/ui.all.css" type="text/css" media="screen" />
<script src="js/jquery-ui.min.js" type="text/JavaScript"></script>    
<input type="checkbox" name="repeat" id="repeat" value="1" <?php echo $chked; ?>> Repeat Invoice 

<div id="repeat_invoice" style="width:520px; border:solid 1px; height:50px; padding-top:10px; padding-left:10px;<?=$display; ?> ">

	<div style="width:80px; float:left; ">
	    <input type="text" name="startdate" id="startdate" value="<?=$startdate; ?>" style="width:80px" readonly>
		Start Date
	</div>
	<div style="width:110px; float:left; padding-left:12px; height:50px; ">
	<select name="how_often">
	<?php
		$howOften = getHowOften();
		
		if(isset($how_often) && $how_often > 0)
			$selkey = $how_often;
		else 
			$selkey = 3;
		
		foreach ($howOften as $key=>$value){
			$sel = '';
			if($key==$selkey)
				$sel = 'selected="selected"';
			
			echo '<option value="'. $key .'" '. $sel .'>'. $value .'</option>';
		}
	?>
	</select>
		How Often
	</div>
	<div style="width:140px; float:left; padding-left:12px; ">
	    <input type="text" name="how_many" id="how_many" value="<?=$how_many; ?>" defaultVal="Forever"  maxlength="2">
		How Many
	</div>
	<div style="width:80px; float:left; padding-left:12px;">
	    <input type="text" name="invoices_sent" id="invoices_sent" value="0" maxlength="2" readonly>
		Invoices Sent
	</div>

</div>

<script>

$.datepicker.setDefaults({
	showOn: 'focus',
	dateFormat: date_format,
	minDate: new Date()
});
   
$(document).ready(function() {
    $('#startdate').datepicker();
	if($('#how_many').val()=='') {
    	$('#how_many').val($('#how_many').attr('defaultVal'));
    	$('#how_many').css({color:'grey'});
	}
          
});

$('#repeat').click(function(){

	if ($(this).is(":checked")) {
		jConfirm('Checking this box will result in automatic emailing of this invoice until unchecked', 'Confirmation Dialog', function(r) {
	    	if(r===true){
 		  		$("#repeat_invoice").show();
	    	}else{
				$("#repeat").attr("checked", false);

	    	}
		});
    }
    else {
        $("#repeat_invoice").hide();   	
    }
	
});

$('#how_many').focus(function(){
    if ( $(this).val() == $(this).attr('defaultVal') ){
      $(this).val('');
      $(this).css({color:'black'});
    }
    });

$('#how_many').blur(function(){
    if ( $(this).val() == '' ){
      $(this).val($(this).attr('defaultVal'));
      $(this).css({color:'grey'});
    }
    });

</script>