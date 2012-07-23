<?php
$settings = false;
include("header.php");  
include("config.php");


$email_template = 'send_custom_invoices';
$email_sql   = "SELECT * FROM gma_emails WHERE companyId=0 AND template=".GetSQLValueString($email_template, 'text');

		$title     = 'Edit Message';
        $template  = GetSQLValueString($template, 'text');
        
        if(isset($_POST['subject']) && $_POST['subject']!='')
        {
        	if(isset($_SESSION['sendInvoiceId']) && $_SESSION['sendInvoiceId'] > 0 ){
	            $subject = GetSQLValueString(trim($_POST['subject']), 'text');
	            $content = GetSQLValueString(trim($_POST['content'], 'text'));
	            
	            $email_query = "UPDATE gma_send_invoices SET `subject`=$subject,`content`=$content " .
	            	" WHERE id=" . $_SESSION['sendInvoiceId'];
	            mysql_query($email_query);
	            
	            $notSend = invoiceEmailSend($_SESSION['sendInvoiceId']);
	            
	            if(trim($notSend) != '') {
	            	echo '<div class="norecords"> Invoice was not sent to the following emails <br></div>';
	            	echo $notSend;	
	            	}
	            else {
	            	echo " Invoice was successfully sent <br>";
	            }
	            
	           include('footer.php');
	           exit;
        	}
        }
        
        
        $email_sql  .= " $orderBy";
        $email_rs    = mysql_query($email_sql);
        $email_count = mysql_num_rows($email_rs);
		$email_row = mysql_fetch_assoc($email_rs);        
        



$page_title = 'Messages';

include('sub_header.php');
?>

<form method="POST" id="emailForm" name='emailForm' enctype="multipart/form-data">
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=$title?>&nbsp;<span class="backlink"><a href="emails.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Subject</td>
        <td><input type="text" name="subject" id="subject" class="fleft textbox required" value="<?=@$email_row['subject']?>" /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Content</td>
        <td><textarea name="content" id="content" class="fleft textarea"><?=@($email_row['content'])?></textarea></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
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

<?

include('footer.php');
?>