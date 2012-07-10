<?php 
include("functions.php");
if(!isset($_SESSION['ses_userId']) || $_SESSION['ses_userId']=='') {
    header("Location: index.php");
    exit;
}

//$email_sql = "SELECT * FROM gma_emails WHERE companyId='0'";
//$email_rs  = mysql_query($email_sql);
//while ($email_row = mysql_fetch_assoc($email_rs)) {
//	   $template  = $email_row['template'];
//	   $variables = ($email_row['variables']);	   
//	   $variables = explode("\r\n", $variables);
//	   
//	   $values = '';
//	   foreach ($variables as $variable) {
//	       if($values!='') $values .= "\r\n";
//	       
//	       $value = explode('=>', $variable);
//	       $values .= $value[1].' => '.$value[0];
//	   }
//	   
//	   $email_sql = "UPDATE gma_emails SET variables='$values' WHERE template='$template' AND companyId='0'";
//	   echo $email_sql.';<br>';
//}
//exit;

if($ses_loginType=='admin')
{
    $account_sql = "SELECT * FROM gma_accounts WHERE companyId='0'";
    $account_rs  = mysql_query($account_sql);
    while ($account_row = mysql_fetch_assoc($account_rs)) {
        $grade = GetSQLValueString($account_row['grade'], 'int');
            
        $account_sql = "SELECT * FROM gma_accounts WHERE companyId='$ses_companyId' AND grade='$grade'";
        if(mysql_num_rows(mysql_query($account_sql))==0)
        {       
            $paymentDue        = GetSQLValueString($account_row['paymentDue'], 'int');
            $overdueNotice     = GetSQLValueString($account_row['overdueNotice'], 'int');
            $suspensionWarning = GetSQLValueString($account_row['suspensionWarning'], 'int');
    
            $account_sql = "INSERT INTO gma_accounts SET grade='$grade', paymentDue='$paymentDue', overdueNotice='$overdueNotice',suspensionWarning='$suspensionWarning',companyId='$ses_companyId'";
            mysql_query($account_sql);
        }
    }
    
    $grade_sql = "SELECT * FROM gma_grading WHERE companyId='$ses_companyId'";
    $grade_rs  = mysql_query($grade_sql);
    if(mysql_num_rows($grade_rs)==0)
    {
        $grade_sql = "SELECT * FROM gma_grading WHERE companyId='0'";
        $grade_rs  = mysql_query($grade_sql);
        $grade_row = mysql_fetch_assoc($grade_rs);
        $grade_1   = GetSQLValueString($grade_row['grade_1'], 'text');
        $grade_2   = GetSQLValueString($grade_row['grade_2'], 'text');
        $grade_3   = GetSQLValueString($grade_row['grade_3'], 'text');

        $grade_sql = "INSERT INTO gma_grading SET grade_1=$grade_1,grade_2=$grade_2,grade_3=$grade_3,companyId='$ses_companyId'";
        mysql_query($grade_sql);
    }
    
    $email_sql = "SELECT * FROM gma_emails WHERE companyId='0'";
    $email_rs  = mysql_query($email_sql);
    while ($email_row = mysql_fetch_assoc($email_rs))
    {
        $content = $email_row['content'];
        if(!strstr($content, '<table')) {
            $content = nl2br($content);
        }
        $template  = GetSQLValueString($email_row['template'], 'text');
        $subject   = GetSQLValueString($email_row['subject'], 'text');
        $content   = GetSQLValueString(addslashes($content), 'text');
        $variables = GetSQLValueString(nl2br($email_row['variables']), 'text');
        $upload    = GetSQLValueString($email_row['upload'], 'int');
        $module_id = GetSQLValueString($email_row['module_id'], 'int');
        
        $company_email_sql = "SELECT * FROM gma_emails WHERE companyId='$ses_companyId' AND template=$template";
        $company_email_rs  = mysql_query($company_email_sql);
        if(mysql_num_rows($company_email_rs)==0)
            $company_email_sql = "INSERT INTO gma_emails SET companyId='$ses_companyId',template=$template,subject=$subject,content=$content,variables=$variables,upload=$upload,module_id=$module_id";
        else {
            $company_email_row = mysql_fetch_assoc($company_email_rs);
            if($company_email_row['update']==1)
                $company_email_sql = "UPDATE gma_emails SET variables=$variables,upload=$upload,module_id=$module_id WHERE companyId='$ses_companyId' AND template=$template";
            else
                $company_email_sql = "UPDATE gma_emails SET subject=$subject,content=$content,variables=$variables,upload=$upload,module_id=$module_id WHERE companyId='$ses_companyId' AND template=$template";
        }
//        echo "$template == $company_email_sql<hr>";
        mysql_query($company_email_sql);
    }
//    exit;
    
    $allModules = array();
    $module_sql = "SELECT * FROM gma_company_module WHERE companyId='$ses_companyId' ORDER BY module_id ASC";
    $module_rs  = mysql_query($module_sql);
    while($module_row = mysql_fetch_assoc($module_rs))
    {
        $allModules[] = $module_row;
    }

    $company_permission_sql = "SELECT * FROM gma_admins_permission WHERE companyId='$ses_companyId'";        
    $company_permission_rs  = mysql_query($company_permission_sql);
    if(mysql_num_rows($company_permission_rs)==0)
    {    
        $permission_sql = "SELECT * FROM gma_admins_permission WHERE companyId='0'";
        $permission_rs  = mysql_query($permission_sql);
        while ($permission_row = mysql_fetch_assoc($permission_rs))
        {
            $admins_id = GetSQLValueString($permission_row['admins_id'], 'int');
            $module_id = GetSQLValueString($permission_row['module_id'], 'int');
            
            $company_permission_sql = "SELECT * FROM gma_admins_permission WHERE companyId='$ses_companyId' AND admins_id='$admins_id' AND module_id='$module_id'";
            $company_permission_rs  = mysql_query($company_permission_sql);
            if(mysql_num_rows($company_permission_rs)==0)
            {
                $sql = "INSERT INTO gma_admins_permission SET companyId='$ses_companyId', admins_id='$admins_id', module_id='$module_id';";
                mysql_query($sql);
            }
        }
    }
}

$filename = basename($_SERVER['SCRIPT_NAME']);
$pages    = $top_menu = $main_menu = $module_ids = array();
$module_ids[] = 0;
$top_menu_active = true;
if(isset($_SESSION['ses_userId']) && $_SESSION['ses_userId']>0) {
    $admins_id  = 5;
    $admins_sql = "SELECT * FROM gma_admins WHERE type='$ses_userType'";
    $admins_rs  = mysql_query($admins_sql);
    if(mysql_num_rows($admins_rs)==1) {
        $admins_row = mysql_fetch_assoc($admins_rs);
        $admins_id  = $admins_row['id'];
    }
    
    $module_sql = "SELECT * FROM gma_admins_permission AS AP, gma_company_module AS CM, gma_modules AS MO WHERE AP.companyId=CM.companyId AND AP.module_id=CM.module_id AND AP.module_id=MO.id AND AP.admins_id=$admins_id AND CM.companyId=$ses_companyId AND CM.status=1";
    
    $module_top_sql = "$module_sql AND menu=1 ORDER BY `order` ASC";
    $module_top_rs  = mysql_query($module_top_sql);
    while ($module_top_row = mysql_fetch_assoc($module_top_rs)) {
        if($module_top_row['filename']==$filename)
            $top_menu_active = true;
            
    	   $top_menu[]   = $module_top_row;
    	   $pages[]      = $module_top_row['filename'];
    	   $module_ids[] = $module_top_row['id'];
    }
    
    $module_main_sql = "$module_sql AND menu=2 ORDER BY `order` ASC";
    $module_main_rs  = mysql_query($module_main_sql);
    while ($module_main_row = mysql_fetch_assoc($module_main_rs)) {
        if($module_main_row['filename']==$filename)
            $module_main_row['class'] .= ' selected';
     
    	   $main_menu[]  = $module_main_row;
    	   $pages[]      = $module_main_row['filename'];
    	   $module_ids[] = $module_main_row['id'];
    }
    
    $company_sql = "SELECT * FROM gma_company, gma_logins, gma_admin_details WHERE gma_admin_details.userId=gma_logins.userId AND gma_company.companyId=gma_logins.companyId AND gma_company.ownerId=gma_logins.userId AND gma_company.companyId=".GetSQLValueString($ses_companyId, 'text');
    $company_rs  = mysql_query($company_sql);
    $company_row = mysql_fetch_assoc($company_rs);
}
$module_ids = implode(',', $module_ids);

$systemModules = array();
$module_sql = "SELECT * FROM gma_modules WHERE 1 ORDER BY id ASC";
$module_rs  = mysql_query($module_sql);
while($module_row = mysql_fetch_assoc($module_rs))
{
    $systemModules[] = $module_row['filename'];
}
if(in_array($filename, $systemModules)) {
    if(!in_array($filename, $pages)) {
        return header("Location: index.php");
        exit;
    }
}

$message = '';
if(isset($_GET['msg']) && $_GET['msg']=='updated')
    $message = 'Details successfully updated !.';
else if(isset($_GET['msg']) && $_GET['msg']=='added')
    $message = 'Details successfully added !.';
else if(isset($_GET['msg']) && $_GET['msg']=='deleted')
    $message = 'Details successfully deleted !.';
else if(isset($_GET['msg']) && $_GET['msg']=='invalid')
    $message = 'Invalid Access !.';
else if(isset($_GET['msg']) && $_GET['msg']=='import')
    $message = 'Details successfully imported !.';
else if(isset($_GET['i']))
    $message = 'Invalid details !';
else if(isset($_GET['a']))
    $message = 'Details successfully added !';
else if(isset($_GET['u']))
    $message = 'Details successfully updated !';
else if(isset($_GET['d']))
    $message = 'Details successfully deleted !';
else if(isset($_GET['imp']))
    $message = 'Details successfully imported !';
else if(isset($_GET['i']))    
    $message = 'Invalid Access !.';
else if(isset($_GET['msg']))    
    $message = $_GET['msg'];
    
if(isset($_GET['msg']))
{
    $query_string = $_SERVER['QUERY_STRING'];
    $query_string = str_replace('msg='.$_GET['msg'], '', $query_string);
    $_SERVER['QUERY_STRING'] = $query_string;
}
$actualQueryString = $_SERVER["QUERY_STRING"];

$queryString = explode("&orderby=", $actualQueryString); 
$queryString = $queryString[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>GNet Mail</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="style.php" rel="stylesheet" type="text/css" />
    <script src="js/jquery-1.4.4.min.js" type="text/JavaScript"></script>
    <script src="js/jquery.validate.js" type="text/JavaScript"></script>
    <script src="js/thickbox-compressed.js" type="text/JavaScript"></script>
    <script src="js/scripts.js" type="text/JavaScript"></script>
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
    <script src="js/jquery.alerts.js" type="text/JavaScript"></script>
    <link rel="stylesheet" href="css/jquery.alerts.css" type="text/css" media="screen" />
    <script>
    var date_format = 'dd/mm/yyyy';
//    var date_format = 'yyyy-mm-dd';
    var start_date  = '01/01/1940';
    </script>
</head>
<body>
<div id="wrapper">
    <div id="head">
        <div id="head_left">
        <img src="images/company/admin_logo.png" align="right"></div>
        <div id="head_right">
            <span class="logout">
                Logged in as "<?=$ses_userName?>"&nbsp;&nbsp;&nbsp;|&nbsp;
                <? if(count($top_menu)>0) { ?>
                    <a href="javascript:void(0);" onclick="settingsTab();">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <? } ?>
                <a href="index.php?logoff=signout">Logout</a>
            </span>
            <? if(count($top_menu)>0) { ?>
                <span class="settings" id="settings" <?=($top_menu_active ? 'style="display:block;"' : 'style="display:none;"')?>>
                    <? foreach ($top_menu as $key=>$menu) { ?>
                        <? if($key!=0) { ?> &nbsp;|&nbsp; <? } ?>
                        <a href="<?=$menu['filename']?>" title="<?=$menu['name']?>"><?=$menu['name']?></a>
                    <? } ?>
                </span>
            <? } ?>
        </div>
    </div>
    <div class="clear"></div>
    <div id="top_buttons">
        <ul class="buttons">
            <? foreach ($main_menu as $key=>$menu) { ?>
                <li class="button"><a href="<?=$menu['filename']?>" class="<?=$menu['class']?>"></a></li>
            <? } ?>
        </ul> 
    </div>
    <div class="clear"></div>
    <div class="message" <?=($message=='' ? 'style="display:none"' : '')?>><?=$message?></div>