<?php 
include("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GNet Mail</title>
<link href="style.php?default" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
		<div id="head">
           <div id="head_left"></div>
           <div id="head_right"></div>
        </div>
        <div class="clear"></div>
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
//        if($user_row['userType']!='normal' && $user_row['userType']!='trial' && $user_row['userType']!='client')
//            header("Location: users.php");
//        else
        header("Location: dashboard.php");
        exit;
    }
    else
        $msg1 = "Incorrect Username/Password";
}
?>

<form name="loginfrm" method="post" action="">
<table width="100%" height="100%" cellpadding="0" align="center" cellspacing="3">
<tr><td width="100%" height="100%" align="center" valign="middle">

<table cellpadding="0" cellspacing="3" style="border:2px solid #FA7E00; width:380px; height:190px;padding:5px 5px 5px 40px; margin-top:50px;">
    <tr><td colspan="2" align="center" style="color:#336600;"><?php echo $msg; ?></td></tr>
    <tr><td colspan="2" align="center" style="color:#FF0000;"><?php echo $msg1; ?></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td><strong>Username</strong></td>
        <td><input type="text" name="username" id="username" class="search_bt" /></td>
    </tr>
    <tr>
        <td><strong>Password</strong></td>
        <td><input type="password" name="password" id="password" class="search_bt" /></td>
    </tr>
    <tr>
        <td><!--<a href="forgotpassword.php" style="text-decoration:underline;">Forgot Password</a>--></td>
        <td><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td>
    </tr>
</table>

</td></tr>
</table>
</form>
<?php include("footer.php"); ?>