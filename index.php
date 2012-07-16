<?php 
include("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>KissAdmin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="style.php?default" rel="stylesheet" type="text/css" />
    <script src="js/jquery-1.4.4.min.js" type="text/JavaScript"></script>
    <script src="js/jquery.validate.js" type="text/JavaScript"></script>
    <script src="js/animatedcollapse.js" type="text/JavaScript"></script>
    <script src="js/scripts.js" type="text/JavaScript"></script>
    <script>
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
            <div class="logo"><img src="images/logo.png" align="center"></div>
            <div class="head_right"></div>
        </div>
        <div id="head_right">
            <img src="images/KISSAdmin_logo.png" align="right">
            <div class="right_links">
                <? if(isset($_SESSION['ses_userId']) && $_SESSION['ses_userId']>0) { ?>
                <div class="login">
                    Logged in as "<?=$_SESSION['displayName']?>"&nbsp;&nbsp;&nbsp;|&nbsp;
                    <? if(count($top_menu)>0) { ?>
                        <a href="javascript:void(0);" onclick="settingsTab();">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <? } ?>
                    <a href="index.php?logoff=signout">Logout</a>
                </div>
                <? } ?>
                <? if(count($top_menu)>0) { ?>
                    <div class="submenu">
                    <? foreach ($top_menu as $key=>$menu) { ?>
                        <? if($key!=0) { ?> &nbsp;|&nbsp; <? } ?>
                        <a href="<?=$menu['filename']?>" title="<?=$menu['name']?>"><?=$menu['name']?></a>
                    <? } ?>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
    <div id="top_buttons">
        <ul>
            <? foreach ($main_menu as $key=>$menu) { ?>
                <li><a href="<?=$menu['filename']?>" class="<?=$menu['class']?>"></a></li>
            <? } ?>
        </ul> 
    </div>
    <div id="maincontent">
    
<div class="block" <?=($message=='' ? 'style="display:none"' : '')?>>
    <div class="message successmsg" style="display: block;" id="successmsg"><span id=""><?=$message?></span><span title="Dismiss" class="close" onclick="animatedcollapse.hide('successmsg');"></span></div>
</div>

<?php 
if(isset($_REQUEST['logoff']))
{
    unset($_SESSION['ses_userId']);
    unset($_SESSION['ses_userType']);
    unset($_SESSION['ses_loginType']);
    
    if(isset($_SESSION['usr_userId']) && $_SESSION['usr_userId']>0)
    {
        $_SESSION['ses_userId'] = $_SESSION['usr_userId'];
        unset($_SESSION['usr_userId']);
    }
    else if(isset($_SESSION['adm_userId']) && $_SESSION['adm_userId']>0)
    {
        $_SESSION['ses_userId'] = $_SESSION['adm_userId'];
        unset($_SESSION['adm_userId']);
    }
    
    header("Location: index.php");
    exit;
}
else if(isset($_SESSION['ses_userId']) && $_SESSION['ses_userId']>0)
{ 
	
    $ses_companyId = $_SESSION['ses_companyId'];
    $ses_userType  = $_SESSION['ses_userType'];
    $admins_id  = 5;
    $admins_sql = "SELECT * FROM gma_admins WHERE type='$ses_userType'";
    $admins_rs  = mysql_query($admins_sql);
    if(mysql_num_rows($admins_rs)==1) {
        $admins_row = mysql_fetch_assoc($admins_rs);
        $admins_id  = $admins_row['id'];
    }
    
    $module_sql = "SELECT * FROM gma_admins_permission AS AP, gma_company_module AS CM, gma_modules AS MO WHERE AP.companyId=CM.companyId AND AP.module_id=CM.module_id AND AP.module_id=MO.id AND AP.admins_id=$admins_id AND CM.companyId=$ses_companyId AND CM.status=1 ORDER BY `menu` DESC, `order` ASC";
    $module_rs  = mysql_query($module_sql);
    $module_row = mysql_fetch_assoc($module_rs);
    $filename   = $module_row['filename'];
    
    return header("Location: $filename");
    exit;
}
if(isset($_REQUEST['sbmt']))
{
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $user_sql = "SELECT * FROM `gma_logins` WHERE `userName`='$username' AND `password`='$password'";
    $user_rs  = mysql_query($user_sql);
    
    if(mysql_num_rows($user_rs)==1)
    {
        $user_row = mysql_fetch_assoc($user_rs);
        
        $_SESSION['ses_userId']    = $ses_userId    = $user_row['userId'];
        $_SESSION['ses_companyId'] = $ses_companyId = $user_row['companyId'];
        $_SESSION['ses_userType']  = $ses_userType  = $user_row['userType'];
        $_SESSION['ses_loginType'] = $ses_loginType = ($ses_loginType=='normal' || $ses_loginType=='trial' || $ses_loginType=='client') ? 'user' : 'admin';
        
        $msg = "Login successful";
        header("Location: dashboard.php");
        exit;
    }
    else
        $msg1 = "Incorrect Username/Password";
}
$page_title = "Login";
?>
<div id="subcontent">
    <div class="page_header">
        <div class="fleft padding_top">
            <h2 class=""><?=$page_title?></h2>
        </div>
    </div>
    <div class="contents">
            
<form name="loginfrm" method="post" action="">
<table class="list addedit login" cellpadding="0" cellspacing="0" border="0" align="center" width="30%">
    <tr>
        <td><strong>Username</strong></td>
        <td><input type="text" name="username" id="username" class="textbox required" autocomplete='off' /></td>
    </tr>
    <tr class="altrow">
        <td><strong>Password</strong></td>
        <td><input type="password" name="password" id="password" class="textbox required" /></td>
    </tr>
    <tr>
        <td><!--<a href="forgotpassword.php" style="text-decoration:underline;">Forgot Password</a>--></td>
        <td><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></td>
    </tr>
</table>
</form>

<?php include("footer.php"); ?>