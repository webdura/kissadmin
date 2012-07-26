<form method="POST" id="uploadCSV" name='uploadCSV' enctype="multipart/form-data">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list addedit">
    <tr>
        <th colspan="2"><b>Upload csv File</b></th>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="15%">File (csv files only)</td>
        <td><input type="file" name="csvfile" id="csvfile" class="required"></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td></td>
        <td><a href="images/invoices.csv" target="_blank" class="links">Click here</a> for sample CSV file.</td>  
    </tr>
</table>
<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Proceed To Next Step" class="btn_style" /></div>
</form>


<script>
$(document).ready(function() {
    jQuery("#uploadCSV").validate({
        rules: {
            csvfile: {
                required: true,
                accept: "csv"
            }
        },
        messages: {
            csvfile: {
                accept: "Only csv files supported"
            }
        }
    });
});
</script>