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

if($ses_loginType=='user') {
    $user_billing_sql = "SELECT * FROM gma_user_address WHERE type='B' AND userId='$ses_userId'";
    $user_billing_rs  = mysql_query($user_billing_sql);
    $user_billing     = mysql_fetch_assoc($user_billing_rs);
    
    $user_delivery_sql = "SELECT * FROM gma_user_address WHERE type='D' AND userId='$ses_userId'";
    $user_delivery_rs  = mysql_query($user_delivery_sql);
    $user_delivery     = mysql_fetch_assoc($user_delivery_rs);
}

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
    unset($_POST['delivery_same']);
    
    $sql = ($password!='') ? ",password=".GetSQLValueString($password,'text') : '';
    $sql = "UPDATE gma_logins SET userName=".GetSQLValueString($userName, 'text').",email=".GetSQLValueString($useremail, 'text')."$sql WHERE userId='$ses_userId'";
    mysql_query($sql);
    
    if($ses_loginType=='user')
    {
        $gma_user_details = $gma_user_billing = $gma_user_delivery = '';
        foreach ($_POST AS $name=>$value)
        {
            if(strstr($name, 'billing_')) {
                if($gma_user_billing!='') $gma_user_billing .= ',';
                $name = str_replace('billing_', '', $name);
                $gma_user_billing .= "$name=".GetSQLValueString($value, 'text');
            } else if(strstr($name, 'delivery_')) {
                if($gma_user_delivery!='') $gma_user_delivery .= ',';
                $name = str_replace('delivery_', '', $name);
                $gma_user_delivery .= "$name=".GetSQLValueString($value, 'text');
            } else {
                if($gma_user_details!='') $gma_user_details .= ',';
                $gma_user_details .= "$name=".GetSQLValueString($value, 'text');
            }
        }
        if($gma_user_details!='')
        {
            $sql = "UPDATE gma_user_details SET $gma_user_details WHERE userId='$ses_userId'";
            mysql_query($sql);
        }
        if($gma_user_billing!='')
        {
            if(mysql_num_rows($user_billing_rs)>0)
                $sql = "UPDATE gma_user_address SET $gma_user_billing WHERE userId='$ses_userId' AND type='B'";
            else
                $sql = "INSERT INTO gma_user_address SET userId='$ses_userId',type='B',$gma_user_billing";
            mysql_query($sql);
        }
        if($gma_user_delivery!='')
        {
            if(mysql_num_rows($user_delivery_rs)>0)
                $sql = "UPDATE gma_user_address SET $gma_user_delivery WHERE userId='$ses_userId' AND type='D'";
            else
                $sql = "INSERT INTO gma_user_address SET userId='$ses_userId',type='D',$gma_user_delivery";
            mysql_query($sql);
        }
    }
    else
    {
        $sql = "UPDATE gma_admin_details SET fullName=".GetSQLValueString($_POST['fullName'],'text').",theme_id=".GetSQLValueString($_POST['theme_id'],'text')." WHERE userId='$ses_userId'";
        mysql_query($sql);
        
        if($ses_userType=='gnet_admin' || $ses_userType=='super_admin')
        {
            $sql = "UPDATE gma_company SET companyName=".GetSQLValueString($_POST['companyName'], 'text').",companyVatNo=".GetSQLValueString($_POST['companyVatNo'], 'text').",companyAccountEmail=".GetSQLValueString($_POST['companyAccountEmail'], 'text').",companyAccountTel=".GetSQLValueString($_POST['companyAccountTel'], 'text').",companyAccountFax=".GetSQLValueString($_POST['companyAccountFax'], 'text').",companyAccountContact=".GetSQLValueString($_POST['companyAccountContact'], 'text').",companyBankName=".GetSQLValueString($_POST['companyBankName'], 'text').",companyBranchName=".GetSQLValueString($_POST['companyBranchName'], 'text').",companyBranchNo=".GetSQLValueString($_POST['companyBranchNo'], 'text').",companyAccountName=".GetSQLValueString($_POST['companyAccountName'], 'text').",companyAccountType=".GetSQLValueString($_POST['companyAccountType'], 'text').",companyAccountNo=".GetSQLValueString($_POST['companyAccountNo'], 'text')." WHERE companyId='$ses_companyId'";
            mysql_query($sql);
        }
    }
    
    header("Location: myprofile.php?msg=updated");
    exit;
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
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
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
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="display:none; margin-top:10px;">
        <tr><th colspan="3">Details of owner / manager</th></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%">First name</td>
            <td><input type="text" name="ownerFirstName" id="ownerFirstName" class="textbox" value="<?=$user_row['ownerFirstName']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Last name</td>
            <td><input type="text" name="ownerLastName" id="ownerLastName" class="textbox" value="<?=$user_row['ownerLastName']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Telephone</td>
            <td><input type="text" name="ownerPhone" id="ownerPhone" class="textbox" value="<?=$user_row['ownerPhone']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Email address</td>
            <td><input type="text" name="ownerEmail" id="ownerEmail" class="textbox email" value="<?=$user_row['ownerEmail']?>"></td>
        </tr>
    </table>
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
        <tr><th colspan="3">Billing Address</th></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%">Address</td>
            <td><input type="text" name="billing_address" id="billing_address" class="billing textbox required" value="<?=$user_billing['address']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>City</td>
            <td><input type="text" name="billing_city" id="billing_city" class="billing textbox required" value="<?=$user_billing['city']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Province</td>
            <td><input type="text" name="billing_province" id="billing_province" class="billing textbox required" value="<?=$user_billing['province']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Zip code</td>
            <td><input type="text" name="billing_zip" id="billing_zip" class="billing textbox required" value="<?=$user_billing['zip']?>"></td>
        </tr>
    </table>
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
        <tr><th colspan="3">Delivery Address</th></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%"></td>
            <td><input type="checkbox" name="delivery_same" id="delivery_same" value="1" onclick="sameBilling();">&nbsp;Same As Billing Address</td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Address</td>
            <td><input type="text" name="delivery_address" id="delivery_address" class="delivery textbox required" value="<?=$user_delivery['address']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>City</td>
            <td><input type="text" name="delivery_city" id="delivery_city" class="delivery textbox required" value="<?=$user_delivery['city']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Province</td>
            <td><input type="text" name="delivery_province" id="delivery_province" class="delivery textbox required" value="<?=$user_delivery['province']?>"></td>
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Zip code</td>
            <td><input type="text" name="delivery_zip" id="delivery_zip" class="delivery textbox required" value="<?=$user_delivery['zip']?>"></td>
        </tr>
    </table>
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="display:none; margin-top:10px;">
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
        $('.billing').keyup(function() {
            var same = $("input[name='delivery_same']:checked").val();
            if(same==1) {
                var value = $(this).val()
                var id    = $(this).attr('id');
                
                id = id.replace("billing_","delivery_")
                $('#'+id).val(value);
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
    <? $row_flag = 1; ?>
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
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
        <? $row_flag = 1; ?>
        <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
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
            <!--<tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
                <td>Overall Discount</td>
                <td><input type="text" name="companyDiscount" id="companyDiscount" class="textbox number" value="<?=$company_row['companyDiscount']?>" /></td>  
            </tr>-->
        </table>
        <? $row_flag = 1; ?>
        <table width="100%" class="list addedit" cellpadding="0" cellspacing="0" style="margin-top:10px;">
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
<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>

<?php include("footer.php");  ?>