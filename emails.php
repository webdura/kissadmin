<?php
$settings = false;
include("header.php");  
include("config.php");

$action   = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$template = (isset($_REQUEST['template']) && $_REQUEST['template']!='') ? $_REQUEST['template'] : '';
$perPage  = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum  = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$email_sql   = "SELECT * FROM gma_emails WHERE companyId=".GetSQLValueString($ses_companyId, 'text')." AND module_id IN ($module_ids)";
switch ($action)
{
    case 'edit':
        $title     = 'Edit Message';
        $template  = GetSQLValueString($template, 'text');
        
        if(isset($_POST['subject']) && $_POST['subject']!='')
        {
            $subject = GetSQLValueString(trim($_POST['subject']), 'text');
            $content = $_POST['content'];
            $status  = isset($_POST['status']) ? 1 : 0;
            
            if(isset($_FILES['upload']) && $_FILES['upload']['size']>0)
            {
                $tmp_file = $_FILES['upload']['tmp_name'];
                $filename = $_FILES['upload']['name'];
                
                $exts = explode('.', $filename);
                $ext  = $exts[count($exts)-1];
                
                $filename = 'email_'.$ses_companyId.'.'.$ext;
                copy($tmp_file, $filename);
                
                $fp = fopen($filename, 'r');
                $content = fread($fp, filesize($filename));
                fclose($fp);
                
                @unlink($filename);
            }
            $content = GetSQLValueString(trim($content), 'text');
            
            $email_query = "UPDATE gma_emails SET `subject`=$subject,`content`=$content,`status`='$status',`update`='1' WHERE companyId='$ses_companyId' AND template=$template";
            mysql_query($email_query);
            
            header("Location: emails.php?u");
            exit;
        }
        
        $email_sql .= " AND template=$template";
        $email_rs   = mysql_query($email_sql);
        if(mysql_num_rows($email_rs)!=1)
        {
            header("Location: emails.php?i");
            exit;
        }
        $email_row = mysql_fetch_assoc($email_rs);
        $title     = $email_row['subject'].' Message';
        break;
        
    default:
        $action      = 'list';
        $offset      = ($pageNum - 1) * $rowsPerPage;
        $orderBy     = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY `upload` DESC,`template` ASC ';
        $email_sql  .= " $orderBy";
        $email_rs    = mysql_query($email_sql);
        $email_count = mysql_num_rows($email_rs);
        
        $pagination = '';
        if($email_count>$perPage)
        {
            $email_sql  .= " LIMIT $offset, $perPage";
            $email_rs    = mysql_query($email_sql);
            
            $maxPage     = ceil($email_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($email_count, $perPage, 5);
        }
        $links = '';
        // echo "$email_sql"; exit;
        
        break;
}

$page_title = 'Messages';

include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<div class="client_display">
    <table width="100%" class="list" cellpadding="0" cellspacing="0">
        <tr height="30">
            <th>Template</th>
            <th width="10%">Status</th>
            <th width="5%">Action</th>
        </tr>  
        <?php
        $j=0;
        while($email_row = mysql_fetch_assoc($email_rs))
        {
            $class   = ((($j++)%2)==1) ? 'altrow' : '';
            $auto_id = $email_row['template'];
            ?>
            <tr class="<?=$class?>">
                <td><?=$email_row['subject']?></td>
                <td><?=($email_row['status']==1 ? 'Active' : 'Inactive')?></td>
                <td><a href="emails.php?action=edit&template=<?=$auto_id?>" class="btn_style">Edit</a></td>
            </tr>
            <?php
        }
        if($email_count==0) { ?>
           <tr><td class="norecords" colspan="10">No Records Found</td></tr>
        <? } ?>
    </table>
</div>
</form>

<? } else if($action=='edit' || $action=='add') { ?>

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
    <? if($email_row['upload']==1) { ?>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="30%">Upload content</td>
            <td><input type="file" name="upload" id="upload" class="fleft" /></td>  
        </tr> 
    <? } ?>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Replaceable variables</td>
        <td><?=$email_row['variables']?></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Status</td>
        <td><input type="checkbox" name="status" id="status" value="1" <?=(isset($email_row['status']) && $email_row['status']==1) ? 'checked' : ''?> /></td>  
    </tr> 
</table>
</form>
<div class="addedit_btn"><input type="submit" name="upload" id="upload" value="Submit" class="btn_style" /></div>

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
}
include('footer.php');
?>