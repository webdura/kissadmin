
<form method="POST" id="emailForm" name='emailForm' enctype="multipart/form-data">
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="2"><b>STEP 4 :- Enter Your Message</b></th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Subject</td>
        <td><input type="text" name="subject" id="subject" class="fleft textbox required" value="<?=@$email_row['subject']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>" valign="top">
        <td>Content</td>
        <td><textarea name="content" id="content" class="fleft textarea"><?=@($email_row['content'])?></textarea></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>" valign="top">
        <td>Replaceable variables</td>
        <td><?=$email_row['variables']?></td>  
    </tr> 
</table>
<div class="addedit_btn"><input type="submit" name="upload" id="upload" value="Submit" class="btn_style" /></div>
</form>

<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script> 
<script type="text/javascript">
CKEDITOR.replace('content', { });

$(document).ready(function() {
    jQuery("#emailForm").validate({
        rules: {
            upload: {
                accept: "html|htm|txt"
            }
        },
        messages: {
            upload: {
                accept: jQuery.format("Only html OR txt file types allowed")
            }
        }
    });
});
</script>