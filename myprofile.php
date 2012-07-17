<?php
$profile_btn = 'selected';
include("header.php");
include("config.php");

if($ses_loginType=='user')
    $user_sql = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId AND gma_logins.userId='$ses_userId'";
else 
    $user_sql = "SELECT * FROM gma_admin_details,gma_logins WHERE gma_admin_details.userId=gma_logins.userId AND gma_admin_details.userId='$ses_userId'";
$user_rs  = mysql_query($user_sql);
$user_row = mysql_fetch_assoc($user_rs);

if($ses_userType=='gnet_admin' || $ses_userType=='super_admin')
{
    $company_sql = "SELECT * FROM gma_company WHERE companyId='$ses_companyId'";
    $company_rs  = mysql_query($company_sql);
    $company_row = mysql_fetch_assoc($company_rs);
}

if(isset($_POST['userName']))
{
    unset($_POST['sbmt']);
    unset($_POST['cpassword']);
    
    $userName  = $_POST['userName'];
    $password  = $_POST['password'];
    $useremail = $_POST['email'];
    unset($_POST['userName']);
    unset($_POST['password']);
    unset($_POST['email']);
    
    $sql = ($password!='') ? ",password=".GetSQLValueString($password,'text') : '';
    $sql = "UPDATE gma_logins SET userName=".GetSQLValueString($userName, 'text').",email=".GetSQLValueString($useremail, 'text')."$sql WHERE userId='$ses_userId'";
    mysql_query($sql);
    
    if($ses_loginType=='user')
    {
        $values = '';
        foreach ($_POST AS $name=>$value)
        {
            if($values!='') $values .= ',';
            $values .= "$name=".GetSQLValueString($value, 'text');
        }
        if($values!='')
        {
            $sql = "UPDATE gma_user_details SET $values WHERE userId='$ses_userId'";
            mysql_query($sql);
        }
    }
    else
    {
        $sql = "UPDATE gma_admin_details SET fullName=".GetSQLValueString($_POST['fullName'],'text').",theme_id=".GetSQLValueString($_POST['theme_id'],'text')." WHERE userId='$ses_userId'";
        mysql_query($sql);
        
        if($ses_userType=='gnet_admin' || $ses_userType=='super_admin')
        {
            $sql = "UPDATE gma_company SET companyName=".GetSQLValueString($_POST['companyName'], 'text').",companyVatNo=".GetSQLValueString($_POST['companyVatNo'], 'text').",companyAccountEmail=".GetSQLValueString($_POST['companyAccountEmail'], 'text').",companyAccountTel=".GetSQLValueString($_POST['companyAccountTel'], 'text').",companyAccountFax=".GetSQLValueString($_POST['companyAccountFax'], 'text').",companyAccountContact=".GetSQLValueString($_POST['companyAccountContact'], 'text').",companyBankName=".GetSQLValueString($_POST['companyBankName'], 'text').",companyBranchName=".GetSQLValueString($_POST['companyBranchName'], 'text').",companyBranchNo=".GetSQLValueString($_POST['companyBranchNo'], 'text').",companyAccountName=".GetSQLValueString($_POST['companyAccountName'], 'text').",companyAccountType=".GetSQLValueString($_POST['companyAccountType'], 'text').",companyAccountNo=".GetSQLValueString($_POST['companyAccountNo'], 'text').",companyDiscount=".GetSQLValueString($_POST['companyDiscount'], 'text')." WHERE companyId='$ses_companyId'";
            mysql_query($sql);
        }
    }
    
    header("Location: myprofile.php?msg=updated");
    exit;
}

$theme_rows = array();
$theme_sql = "SELECT * FROM gma_theme WHERE 1 ORDER BY name ASC";
$theme_rs  = mysql_query($theme_sql);
while ($theme_row = mysql_fetch_assoc($theme_rs)) {
	   $theme_rows[$theme_row['id']] = $theme_row;
}

$page_title = "Business Details";
include_once('sub_header.php');
?>

<form method="POST" id="userForm" name='userForm'>

<? if($ses_loginType=='user') { ?>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Login Details</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account Grading</td>
        <td><?=$user_row['grade']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Username</td>
        <td>
            <input type="text" name="userName" id="userName" class="required textbox" value="<?=$user_row['userName']?>">
            <!--<i>used for this Admin Panel only - does not affect Members login</i>-->
        </td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Password</td>
        <td>
            <input type="password" name="password" id="password" class="textbox" value="" autocomplete='off'>
            <i>Enter only if you need to update password</i>
        </td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Reenter Password</td>
        <td><input type="password" name="cpassword" id="cpassword" class="textbox" value="" autocomplete='off'></td>
    </tr>
</table>
<? $row_flag = 1; ?><br>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Details of person responsable for account</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Business name</td>
        <td><input type="text" name="businessName" id="businessName" class="required textbox" value="<?=$user_row['businessName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>First name</td>
        <td><input type="text" name="firstName" id="firstName" class="required textbox" value="<?=$user_row['firstName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Last name</td>
        <td><input type="text" name="lastName" id="lastName" class="required textbox" value="<?=$user_row['lastName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Telephone</td>
        <td><input type="text" name="phone" id="phone" class="required textbox" value="<?=$user_row['phone']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email address</td>
        <td><input type="text" name="email" id="email" class="required email textbox" value="<?=$user_row['email']?>"></td>
    </tr>
</table>
<? $row_flag = 1; ?><br>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Details of owner / manager</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">First name</td>
        <td><input type="text" name="ownerFirstName" id="ownerFirstName" class="required textbox" value="<?=$user_row['ownerFirstName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Last name</td>
        <td><input type="text" name="ownerLastName" id="ownerLastName" class="required textbox" value="<?=$user_row['ownerLastName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Telephone</td>
        <td><input type="text" name="ownerPhone" id="ownerPhone" class="required textbox" value="<?=$user_row['ownerPhone']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email address</td>
        <td><input type="text" name="ownerEmail" id="ownerEmail" class="required textbox email" value="<?=$user_row['ownerEmail']?>"></td>
    </tr>
</table>
<? $row_flag = 1; ?><br>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Bank account details: (for debits once off or monthly)</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Bank name</td>
        <td><input type="text" name="bankName" id="bankName" class="textbox" value="<?=$user_row['bankName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account name</td>
        <td><input type="text" name="accountName" id="accountName" class="textbox" value="<?=$user_row['accountName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account number</td>
        <td><input type="text" name="accountNo" id="accountNo" class="textbox" value="<?=$user_row['accountNo']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Branch code</td>
        <td><input type="text" name="branchCode" id="branchCode" class="textbox" value="<?=$user_row['branchCode']?>"></td>
    </tr>
</table>

<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>

<script>
$(document).ready(function() {
    $('#userName').focus();
    jQuery("#userForm").validate({
        rules: {
            userName: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkUserName',
                        user_id: '<?=$ses_userId?>'
                    }
                }
            },
            password: {
                minlength: 5
            },
            cpassword: {
                minlength: 5,
                equalTo: "#password"
            },
            email: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkEmail',
                        user_id: '<?=$ses_userId?>'
                    }
                }
            }
        },
        messages: {
            userName: {
                remote: jQuery.format("Username is already in use.")
            },
            email: {
                remote: jQuery.format("Email is already in use.")
            },
            cpassword: {
                equalTo: jQuery.format("Whoops: Your passwords don't match. Please enter them again.")
            }
        }
    });
});
</script>
<? } else { ?>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Login Details</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Username</td>
        <td>
            <input type="text" name="userName" id="userName" class="required textbox" value="<?=$user_row['userName']?>">
            <!--<i>used for this Admin Panel only - does not affect Members login</i>-->
        </td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Password</td>
        <td>
            <input type="password" name="password" id="password" class="textbox" value="" autocomplete='off'>
            <i>Enter only if you need to update password</i>
        </td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Reenter Password</td>
        <td><input type="password" name="cpassword" id="cpassword" class="textbox" value="" autocomplete='off'></td>
    </tr>
</table>
<? $row_flag = 1; ?><br>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">Details</th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Full Name</td>
        <td><input type="text" name="fullName" id="fullName" class="required textbox" value="<?=$user_row['fullName']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email address</td>
        <td><input type="text" name="email" id="email" class="required textbox email" value="<?=$user_row['email']?>"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Theme</td>
        <td>
            <select class="textbox" name="theme_id" id="theme_id">
                <option value="0">Company Theme</option>
                <? foreach ($theme_rows as $theme_id=>$theme_row) { ?>
                    <option value="<?=$theme_id?>" <?=($user_theme_id==$theme_id ? 'selected' : '')?>><?=$theme_row['name']?></option>
                <? } ?>
            </select>        
        </td>
    </tr>
</table>
<? if($ses_userType=='gnet_admin' || $ses_userType=='super_admin') { ?>
    <? $row_flag = 1; ?><br>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
        <tr><th colspan="3">Company Details</th></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%">Company Name</td>
            <td><input type="text" name="companyName" id="companyName" class="textbox required" value="<?=$company_row['companyName']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Vat No</td>
            <td><input type="text" name="companyVatNo" id="companyVatNo" class="textbox" value="<?=$company_row['companyVatNo']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Email</td>
            <td><input type="text" name="companyAccountEmail" id="companyAccountEmail" class="textbox email" value="<?=$company_row['companyAccountEmail']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Tel</td>
            <td><input type="text" name="companyAccountTel" id="companyAccountTel" class="textbox" value="<?=$company_row['companyAccountTel']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Fax</td>
            <td><input type="text" name="companyAccountFax" id="companyAccountFax" class="textbox" value="<?=$company_row['companyAccountFax']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Contact</td>
            <td><input type="text" name="companyAccountContact" id="companyAccountContact" class="textbox" value="<?=$company_row['companyAccountContact']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Overall Discount</td>
            <td><input type="text" name="companyDiscount" id="companyDiscount" class="textbox number" value="<?=$company_row['companyDiscount']?>" /></td>  
        </tr>
    </table>
    <? $row_flag = 1; ?><br>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
        <tr><th colspan="3">Bank Details</th></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%">Bank Name</td>
            <td><input type="text" name="companyBankName" id="companyBankName" class="textbox" value="<?=$company_row['companyBankName']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Branch Name</td>
            <td><input type="text" name="companyBranchName" id="companyBranchName" class="textbox" value="<?=$company_row['companyBranchName']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Branch No</td>
            <td><input type="text" name="companyBranchNo" id="companyBranchNo" class="textbox" value="<?=$company_row['companyBranchNo']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Name</td>
            <td><input type="text" name="companyAccountName" id="companyAccountName" class="textbox" value="<?=$company_row['companyAccountName']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account Type</td>
            <td><input type="text" name="companyAccountType" id="companyAccountType" class="textbox" value="<?=$company_row['companyAccountType']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Account No</td>
            <td><input type="text" name="companyAccountNo" id="companyAccountNo" class="textbox" value="<?=$company_row['companyAccountNo']?>" /></td>  
        </tr>
    </table>
<? } ?>

<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>

<script>
$(document).ready(function() {
    $('#userName').focus();
    jQuery("#userForm").validate({
        rules: {
            userName: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkUserName',
                        user_id: '<?=$ses_userId?>'
                    }
                }
            },
            password: {
                minlength: 5
            },
            cpassword: {
                minlength: 5,
                equalTo: "#password"
            },
            companyDiscount: {
                maxlength: 2
            },
            email: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkEmail',
                        user_id: '<?=$ses_userId?>'
                    }
                }
            }
        },
        messages: {
            userName: {
                remote: jQuery.format("Username is already in use.")
            },
            email: {
                remote: jQuery.format("Email is already in use.")
            },
            cpassword: {
                equalTo: jQuery.format("Whoops: Your passwords don't match. Please enter them again.")
            },
            companyDiscount: {
                maxlength: jQuery.format("Whoops: Maxmimum discount allowded is 99.")
            }
        }
    });
});
</script>

<? } ?>
</form>

</div>

<?php include("footer.php");  ?>