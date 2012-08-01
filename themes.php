<?php
$user_flag = true;
$settings  = false;
include("header.php");
include("config.php");

$company_theme_sql = "SELECT * FROM gma_company_theme WHERE `companyId`='$ses_companyId'";
$company_theme_rs  = mysql_query($company_theme_sql);
$company_theme_row = mysql_fetch_assoc($company_theme_rs);
    
if(isset($_POST['theme_id']) && $_POST['theme_id']>0)
{
    if(isset($_FILES['site_logo']) && $_FILES['site_logo']['size']>0)
    {
        $filename = $_FILES['site_logo']['name'];
        $tmp_name = $_FILES['site_logo']['tmp_name'];
        
        $exts = explode('.', $filename);
        $ext  = $exts[count($exts)-1];
        
        $filename = 'site_logo_'.$ses_companyId.'.'.$ext;
        $new_name = "images/company/$filename";
        $old_name = "images/company/$site_logo";
        
        copy($tmp_name, $new_name);
        upload_photo($new_name, $new_name, 300, 80);
        if($filename != $site_logo)
            @unlink($old_name);
        $site_logo = $filename;
    } else {
        $site_logo = $company_theme_row['site_logo'];
    }
    if(isset($_FILES['invoice_logo']) && $_FILES['invoice_logo']['size']>0)
    {
        $filename = $_FILES['invoice_logo']['name'];
        $tmp_name = $_FILES['invoice_logo']['tmp_name'];
        
        $exts = explode('.', $filename);
        $ext  = $exts[count($exts)-1];
        
        $filename = 'invoice_logo_'.$ses_companyId.'.'.$ext;
        $new_name = "images/company/$filename";
        $old_name = "images/company/$invoice_logo";
        
        copy($tmp_name, $new_name);
        upload_photo($new_name, $new_name, 300, 80);
        if($filename != $invoice_logo)
            @unlink($old_name);
        $invoice_logo = $filename;
    } else {
        $invoice_logo = $company_theme_row['invoice_logo'];
    }
    
    $site_logo      = GetSQLValueString($site_logo, 'text');
    $invoice_logo   = GetSQLValueString($invoice_logo, 'text');
    $invoice_status = isset($_POST['invoice_status']) ? 1 : 0;
    $theme_id       = GetSQLValueString($_POST['theme_id'], 'text');
    
    $head_bg    = strstr($_POST['head_bg'], '#') ? $_POST['head_bg'] : '#'.$_POST['head_bg'];
    $head_bg    = GetSQLValueString($head_bg, 'text');
    $head_color = strstr($_POST['head_color'], '#') ? $_POST['head_color'] : '#'.$_POST['head_color'];
    $head_color = GetSQLValueString($head_color, 'text');

    $company_theme_sql = "companyId='$ses_companyId',	theme_id=$theme_id,	site_logo=$site_logo,	invoice_logo=$invoice_logo,	invoice_status=$invoice_status,	head_bg=$head_bg,	head_color=$head_color"; //,	color1=$color1,	color2=$color2,	color3=$color3,	color4=$color4";
    if(mysql_num_rows(mysql_query("SELECT * FROM gma_company_theme WHERE companyId='$ses_companyId'"))==0)
        $company_theme_sql = "INSERT INTO gma_company_theme SET $company_theme_sql";
    else
        $company_theme_sql = "UPDATE gma_company_theme SET $company_theme_sql WHERE companyId='$ses_companyId'";
    mysql_query($company_theme_sql);
    
    return header("Location: themes.php?msg=updated");
}


$page_title = 'Company Theme';
include('sub_header.php');
?>

<script type="text/javascript" src="js/jscolor.js"></script>

<form name="userForm" id="userForm" method="post" action="" enctype="multipart/form-data">

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Theme Details</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Theme&nbsp;:&nbsp;</td>
        <td>
            <select class="inputbox_green" style="width:300px;" name="theme_id" id="theme_id">
                <? foreach ($theme_rows as $theme_id=>$theme_row) { ?>
                    <option value="<?=$theme_id?>" <?=($company_theme_id==$theme_id ? 'selected' : '')?>><?=$theme_row['name']?></option>
                <? } ?>
            </select>
        </td>  
    </tr>  
    <? if($company_theme_row['site_logo']!='') { ?>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Current site logo</td>
        <td><img src="<?=$site_logo?>"></td>
    </tr>
    <? } ?>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td><? if($site_logo!='') { ?>New <? } ?>site logo</td>
        <td><input type="file" name="site_logo" id="site_logo" class="fleft file_size"></td>
    </tr>
    <? if($company_theme_row['invoice_logo']!='') { ?>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Current invoice logo</td>
        <td><img src="<?=$invoice_logo?>"></td>
    </tr>
    <? } ?>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td><? if($invoice_logo!='') { ?>New <? } ?>invoice logo</td>
        <td>
            <input type="file" name="invoice_logo" id="invoice_logo" class="fleft file_size">
            &nbsp;&nbsp;
            <div class="fleft">Use site logo&nbsp;<input type="checkbox" name="invoice_status" id="invoice_status" value="1" <?=($invoice_status==1 ? 'checked' : '')?>></div>        
        </td>
    </tr>
    <!--<tr class="<?//=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Header background color</td>
        <td><input type="text" name="head_bg" id="head_bg" class="required textbox color" value="<?=$theme_head_bg?>" readonly onchange="$('#site_logo_div').css('background-color', '#'+this.value);"></td>
    </tr>-->
    <!--<tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Header link color</td>
        <td><input type="text" name="head_color" id="head_color" class="required textbox color" value="<?=$theme_head_color?>" readonly></td>
    </tr>-->
</table>
<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Apply" class="btn_style" /></div>
</form>

<script>
$(document).ready(function() {
    $("#userForm").validate({
        rules: {
            site_logo: {
                accept: "jpg|jpeg|gif|png"
            },
            invoice_logo: {
                accept: "jpg|jpeg|gif|png"
            }
        },
        messages: {
            site_logo: {
                accept: jQuery.format("Only JPG, GIF or PNG file types allowed")
            },
            invoice_logo: {
                accept: jQuery.format("Only JPG, GIF or PNG file types allowed")
            }
        }
    });
});
</script>

<?php include("footer.php");  ?>