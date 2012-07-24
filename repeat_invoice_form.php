<?php
$invoice_btn = 'selected';    
include("config.php");
if(isset($_REQUEST['popup']))
    include("functions.php");
else 
    include("header.php");
?>
<script src="js/jquery-ui-1.8.21.custom.min.js" type="text/JavaScript"></script>    
<!--<script src="js/jquery.ui.datepicker.min.js" type="text/JavaScript"></script>-->    
<input type="checkbox" name="repeat" value="1"> Repeat Invoice 

<div>

<div>
    <input type="text" name="startdate" id="startdate" value="" style="width:66px" readonly>
	Start Date
</div>


</div>

<script>
$(document).ready(function() {
    $('#startdate').datepicker();
});

</script>