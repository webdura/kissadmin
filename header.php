<?php 
include("functions.php");
if(!isset($_SESSION['ses_userId']) || $_SESSION['ses_userId']=='') {
    header("Location: index.php");
    exit;
}

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
        mysql_query($company_email_sql);
    }
    
    $allModules = array();
    $module_sql = "SELECT * FROM gma_company_module WHERE companyId='$ses_companyId' ORDER BY module_id ASC";
    $module_rs  = mysql_query($module_sql);
    while($module_row = mysql_fetch_assoc($module_rs))
    {
        $allModules[] = $module_row;
    }
    
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

$filename = basename($_SERVER['SCRIPT_NAME']);
$pages    = $settings_menu = $top_menu = $main_menu = $module_ids = array();
$module_ids[] = 0;
$settings_menu_active = true;
if(isset($_SESSION['ses_userId']) && $_SESSION['ses_userId']>0) {
    $admins_id  = 5;
    $admins_sql = "SELECT * FROM gma_admins WHERE type='$ses_userType'";
    $admins_rs  = mysql_query($admins_sql);
    if(mysql_num_rows($admins_rs)==1) {
        $admins_row = mysql_fetch_assoc($admins_rs);
        $admins_id  = $admins_row['id'];
    }
    
    $module_sql = "SELECT * FROM gma_admins_permission AS AP, gma_company_module AS CM, gma_modules AS MO WHERE AP.companyId=CM.companyId AND AP.module_id=CM.module_id AND AP.module_id=MO.id AND AP.admins_id=$admins_id AND CM.companyId=$ses_companyId AND CM.status=1";
    
    $module_settings_sql = "$module_sql AND menu=1 ORDER BY `order` ASC";
    $module_settings_rs  = mysql_query($module_settings_sql);
    while ($module_settings_row = mysql_fetch_assoc($module_settings_rs)) {
        if($module_settings_row['filename']==$filename) {
            $module_settings_row['class'] = 'selected';
            $settings_menu_active = true;
        }
            
    	   $settings_menu[] = $module_settings_row;
    	   $pages[]         = $module_settings_row['filename'];
    	   $module_ids[]    = $module_settings_row['id'];
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
    
    $module_top_sql = "$module_sql AND menu=3 ORDER BY `order` ASC";
    $module_top_rs  = mysql_query($module_top_sql);
    while ($module_top_row = mysql_fetch_assoc($module_top_rs)) {
        if($module_top_row['filename']==$filename)
            $module_top_row['class'] = 'selected';
            
    	   $top_menu[]   = $module_top_row;
    	   $pages[]      = $module_top_row['filename'];
    	   $module_ids[] = $module_top_row['id'];
    }
//    echo '<pre>'; print_r($top_menu); exit;
    
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
        $module_sql = "SELECT * FROM gma_admins_permission AS AP, gma_company_module AS CM, gma_modules AS MO WHERE AP.companyId=CM.companyId AND AP.module_id=CM.module_id AND AP.module_id=MO.id AND AP.admins_id=$admins_id AND CM.companyId=$ses_companyId AND CM.status=1 ORDER BY `menu` DESC, `order` ASC";
        $module_rs  = mysql_query($module_sql);
        $module_row = mysql_fetch_assoc($module_rs);
        $filename   = $module_row['filename'];
        
        return header("Location: $filename");
        exit;
    }
}

$company_users = array();
$user_sql  = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId AND companyId='$ses_companyId' GROUP BY userName ORDER BY businessName ASC"; 
$user_rs   = mysql_query($user_sql);
while ($user_row = mysql_fetch_assoc($user_rs)) {
	   $company_users[] = $user_row;
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

$displayName = $company_row['companyName'];
if($ses_loginType=='user') {
    $logged_user_sql = "SELECT * FROM gma_user_details WHERE userID='$ses_userId'";
    $logged_user_rs  = mysql_query($logged_user_sql);
    $logged_user_row = mysql_fetch_assoc($logged_user_rs);
    $displayName     = $logged_user_row['businessName'];
}
//$table_name  = ($ses_loginType=='user') ? 'gma_user_details' : 'gma_admin_details';
//$logged_user_sql = "SELECT * FROM $table_name WHERE userID='$ses_userId'";
//$logged_user_rs  = mysql_query($logged_user_sql);
//$logged_user_row = mysql_fetch_assoc($logged_user_rs);
//$displayName    .= '/'.(($ses_loginType=='user') ? $logged_user_row['businessName'] : $logged_user_row['fullName']);

$menu_types = array(1=>'Settings Menu', 2=>'Main Menu', 3=>'Top Menu');

$row_flag = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>KissAdmin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link id="style_link" href="style.php" rel="stylesheet" type="text/css" />
    <script src="js/jquery-1.7.2.min.js" type="text/JavaScript"></script>
    <script src="js/jquery.validate.js" type="text/JavaScript"></script>
    <script src="js/animatedcollapse.js" type="text/JavaScript"></script>
    <script src="js/thickbox-compressed.js" type="text/JavaScript"></script>
    <script src="js/scripts.js" type="text/JavaScript"></script>
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
    
    <link rel="stylesheet" href="js/ui/themes/smoothness/ui.all.css" type="text/css" media="screen" />
    <script src="js/ui/ui.datepicker.js" type="text/JavaScript"></script>
    
    <script src="js/jquery.alerts.js" type="text/JavaScript"></script>
    <link rel="stylesheet" href="css/jquery.alerts.css" type="text/css" media="screen" />
    <script>
        var date_format = 'dd/mm/yy';
        var start_date  = '01/01/1940';
        
        animatedcollapse.addDiv('successmsg', 'fade=1');
        animatedcollapse.init();
        setTimeout("animatedcollapse.hide('successmsg');", 6000);
    </script>
</head>
<body>
<div id="wrapper">

    <div id="head">
        <div id="head_left">
            <div class="head_left"></div>
            <div class="logo"><img src="<?=$site_logo?>" align="center"></div>
            <div class="head_right"></div>
        </div>
        <div id="head_right">
            <img src="images/KISSAdmin_logo.png" align="right">
            <div class="right_links">
                <div class="login">
                    Logged in as "<?=$displayName?>"&nbsp;&nbsp;&nbsp;|&nbsp;
                    <? if(count($settings_menu)>0) { ?>
                        <a href="javascript:void(0);" onclick="settingsTab();">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <? } ?>
                    <? if(count($top_menu)>0) { ?>
                        <? foreach ($top_menu as $key=>$menu) { ?>
                            <a href="<?=$menu['filename']?>" title="<?=$menu['name']?>" class="<?=$menu['class']?>"><?=$menu['name']?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <? } ?>
                    <? } ?>
                    <a href="index.php?logoff=signout">Logout</a>
                </div>
                <? if(count($settings_menu)>0) { ?>
                    <div class="submenu" id="settings" <?=($settings_menu_active ? "style='display:block'" : '')?>>
                    <? foreach ($settings_menu as $key=>$menu) { ?>
                        <? if($key!=0) { ?> &nbsp;|&nbsp; <? } ?>
                        <a href="<?=$menu['filename']?>" title="<?=$menu['name']?>" class="<?=$menu['class']?>"><?=$menu['name']?></a>
                    <? } ?>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
    <div id="top_buttons">
        <ul id="menu">
            <? foreach ($main_menu as $key=>$menu) { ?>
                <li><a href="<?=$menu['filename']?>" class="<?=$menu['class']?>"></a>
                    <? if((strstr($menu['class'], 'invoices_btn') || strstr($menu['class'], 'quotation_btn') || strstr($menu['class'], 'clients_btn') || strstr($menu['class'], 'payment_btn')) && $ses_loginType!='user') { ?>
                        <ul>
                            <li><a href="<?=$menu['filename']?>?action=list">View All <?=str_replace(' Module', '', $menu['name'])?></a></li>
                            <li><a href="<?=$menu['filename']?>?action=add">Create New <?=str_replace(' Module', '', $menu['name'])?></a></li>
                            <? if(strstr($menu['class'], 'invoices_btn')) { ?>
                                <li><a href="repeated_invoices.php">View Repeat Invoices</a></li>
                            <? } ?>
                            <? if(strstr($menu['class'], 'invoices_btn') || strstr($menu['class'], 'payment_btn')) { ?>
                                <li><a href="creditnote.php?action=list">View Credit Notes</a></li>
                                <li><a href="creditnote.php?action=add">Create Credit Note</a></li>
                            <? } ?>
                        </ul>
                    <? } ?>
                </li>
            <? } ?>
            <? if($ses_loginType!='user') { ?>
                <li class="theme_sel">
                    Theme : 
                    <select class="selectbox" name="user_theme_id" id="user_theme_id" style="width:150px" onchange="changeTheme(this.value)">
                        <option value="0">Company Theme</option>
                        <? foreach ($theme_rows as $theme_id=>$theme_row) { ?>
                            <option value="<?=$theme_id?>" <?=($user_theme_id==$theme_id ? 'selected' : '')?>><?=$theme_row['name']?></option>
                        <? } ?>
                    </select>
                </li>
            <? } ?>
        </ul> 
    </div>
    <div id="maincontent">
    
<div class="block" <?=($message=='' ? 'style="display:none"' : '')?>>
    <div class="message successmsg" style="display: block;" id="successmsg"><span id=""><?=$message?></span><span title="Dismiss" class="close" onclick="animatedcollapse.hide('successmsg');"></span></div>
</div>