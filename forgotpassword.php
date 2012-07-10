<?php include("config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GNet Mail</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!--[if IE6]> <link href="css/styleie6.css" rel="stylesheet" type="text/css" /><![endif]-->
</head>
<body>
<div id="wrapper">
		<div id="head">
           <div id="head_left"></div>
           <div id="head_right"></div>
        </div>
        <div class="clear"></div>
<?php
	if(isset($_REQUEST['sbmt']))
	{
	$email = $_REQUEST['email'];
	$slct = "SELECT * FROM `gma_user_details` WHERE `email`='$email'";
	$run_slct = mysql_query($slct);
	$numrows=mysql_num_rows($run_slct);
	
	if($numrows=='1')
	{
	$ftch = mysql_fetch_array($run_slct);
	$password = $ftch['password'];
	
	$email_to = $email;
	$subject="Your password here";
	$headers = 'From: admin@gnetmail.com' . "\r\n";
	$message= "Your password for login to our website is ".$password;
	//echo $message;
	$ok = mail($email_to,$subject,$message,$headers);
	if($ok)
	{
	$msg = "Password sent";
	}
	else
	{
	$msg1 = "Mail could not be sent";
	}
	
	}
	else
	{
	$msg1 = "Email not found";
	}
	
	}
?>

<form name="form" id="form" method="post" action="">
<table width="100%" cellpadding="0" align="center" cellspacing="0">
	<tr><td width="100%" align="center" valign="middle">
		<table cellpadding="0" cellspacing="3" style="border:2px solid #7688A8; width:380px; height:190px;padding:5px 5px 5px 10px; background-color:#E7EAF9;margin-top:50px; ">
		<tr><td colspan="2" align="center" class="msg"><?php echo $msg; ?></td></tr>
		<tr><td colspan="2" align="center" style="color:#FF0000;"><?php echo $msg1; ?></td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td><strong>Email</strong></td>
		<td><input type="text" name="email" id="email" <?php if($msg1) { ?>value="<?php echo $_REQUEST['email']; ?>"<?php } ?> /></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td>
		</tr>
		
		</table>
	</td></tr>
</table>
</form> 
<?php include("footer.php"); ?>