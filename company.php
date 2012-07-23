<?php
$settings = false;
include("header.php");  
include("config.php");

$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$perPage   = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum   = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$companyId  = (isset($_REQUEST['company_id']) && $_REQUEST['company_id']>0) ? $_REQUEST['company_id'] : -1;
$srchtxt    = trim(($_REQUEST['srchtxt']!='') ? $_REQUEST['srchtxt'] : '');
$search     = trim(($_REQUEST['search']!='') ? $_REQUEST['search'] : '');
$userTypes  = userTypes('', 0, 1);
$userTypes  = "'".implode("', '", $userTypes)."'";
$page_title = 'Company';

$allModules = array();
$module_sql = "SELECT * FROM gma_modules WHERE display=1 AND status=1 ORDER BY menu, `name` ASC"; 
$module_rs  = mysql_query($module_sql);
while($module_row = mysql_fetch_assoc($module_rs))
{
    $allModules[$module_row['id']] = $module_row;
}

$module_sql = "SELECT * FROM gma_modules WHERE status=1 ORDER BY menu, `name` ASC"; 
$module_rs  = mysql_query($module_sql);
while($module_row = mysql_fetch_assoc($module_rs))
{
    $allModulesInsert[$module_row['id']] = $module_row;
}

$company_sql  = "SELECT *,gma_company.status AS company_status FROM gma_logins,gma_company,gma_admin_details WHERE gma_admin_details.userId=gma_logins.userId AND gma_company.companyId=gma_logins.companyId AND gma_logins.userType!='gnet_admin' AND gma_logins.userType='super_admin'";
switch ($action)
{
    case 'add':
        if(isset($_REQUEST['companyName']))
        {
            $fullName  = $_POST['fullName'];
            $userName  = $_POST['userName'];
            $password  = $_POST['password'];
            $userType  = 'super_admin';
            $useremail = $_POST['email'];
            $status    = isset($_POST['status']) ? 1 : 0;
            
            $sql = "INSERT INTO gma_company SET companyName=".GetSQLValueString($_POST['companyName'], 'text').",companyVatNo=".GetSQLValueString($_POST['companyVatNo'], 'text').",companyAccountEmail=".GetSQLValueString($_POST['companyAccountEmail'], 'text').",companyAccountTel=".GetSQLValueString($_POST['companyAccountTel'], 'text').",companyAccountFax=".GetSQLValueString($_POST['companyAccountFax'], 'text').",companyAccountContact=".GetSQLValueString($_POST['companyAccountContact'], 'text').",companyBankName=".GetSQLValueString($_POST['companyBankName'], 'text').",companyBranchName=".GetSQLValueString($_POST['companyBranchName'], 'text').",companyBranchNo=".GetSQLValueString($_POST['companyBranchNo'], 'text').",companyAccountName=".GetSQLValueString($_POST['companyAccountName'], 'text').",companyAccountType=".GetSQLValueString($_POST['companyAccountType'], 'text').",companyAccountNo=".GetSQLValueString($_POST['companyAccountNo'], 'text').",status=".GetSQLValueString($status, 'int');
            mysql_query($sql);
            $companyId = mysql_insert_id();
            
            $sql = "INSERT INTO gma_logins SET companyId=".GetSQLValueString($companyId, 'text').",userName=".GetSQLValueString($userName, 'text').",password=".GetSQLValueString($password, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text');
            mysql_query($sql);
            $userId = mysql_insert_id();
            
            $sql = "INSERT INTO gma_admin_details SET userId='$userId',fullName=".GetSQLValueString($fullName, 'text');
            mysql_query($sql);
            
            $sql = "UPDATE gma_company SET ownerId=".GetSQLValueString($userId, 'text')." WHERE companyId=".GetSQLValueString($companyId, 'text');
            mysql_query($sql);
            
            $sql = "DELETE FROM gma_company_module WHERE companyId='$companyId'";
            mysql_query($sql);
            foreach ($allModulesInsert as $module_id=>$module)
            {
                $status = (isset($_POST['module'][$module_id])) ? 1 : (($module['display']==0 && $module['default']==1) ? 1 : 0);
                $sql = "INSERT INTO gma_company_module SET companyId='$companyId',module_id='$module_id',status='$status'";
                mysql_query($sql);
            }
            
            $email_values = array(
                'companyname' => $_POST['companyName'],
                'ownername' => $_POST['fullName'],
                'username' => $_POST['userName'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'to_email' => $_POST['email'],
            );
            emailSend('company', $email_values);
            
            header("Location: company.php?msg=added");
            exit;
        }
        $company_row = array();
        break;
        
    case 'edit':
        if(isset($_REQUEST['companyName']))
        {
            $userId    = $_POST['ownerId'];
            
            $fullName  = $_POST['fullName'];
            $userName  = $_POST['userName'];
            $password  = $_POST['password'];
            $userType  = 'super_admin';
            $useremail = $_POST['email'];
            $status    = isset($_POST['status']) ? 1 : 0;
            
            $sql = "UPDATE gma_company SET companyName=".GetSQLValueString($_POST['companyName'], 'text').",companyVatNo=".GetSQLValueString($_POST['companyVatNo'], 'text').",companyAccountEmail=".GetSQLValueString($_POST['companyAccountEmail'], 'text').",companyAccountTel=".GetSQLValueString($_POST['companyAccountTel'], 'text').",companyAccountFax=".GetSQLValueString($_POST['companyAccountFax'], 'text').",companyAccountContact=".GetSQLValueString($_POST['companyAccountContact'], 'text').",companyBankName=".GetSQLValueString($_POST['companyBankName'], 'text').",companyBranchName=".GetSQLValueString($_POST['companyBranchName'], 'text').",companyBranchNo=".GetSQLValueString($_POST['companyBranchNo'], 'text').",companyAccountName=".GetSQLValueString($_POST['companyAccountName'], 'text').",companyAccountType=".GetSQLValueString($_POST['companyAccountType'], 'text').",companyAccountNo=".GetSQLValueString($_POST['companyAccountNo'], 'text').",status=".GetSQLValueString($status, 'int')." WHERE companyId='$companyId'";
            mysql_query($sql);
            
            $sql = "UPDATE gma_logins SET companyId=".GetSQLValueString($companyId, 'text').",userName=".GetSQLValueString($userName, 'text').",password=".GetSQLValueString($password, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text')." WHERE userId='$userId'";
            mysql_query($sql);
            
            $sql = "UPDATE gma_admin_details SET fullName=".GetSQLValueString($fullName, 'text')." WHERE userId='$userId'";
            mysql_query($sql);
            
            $sql = "DELETE FROM gma_company_module WHERE companyId='$companyId'";
            mysql_query($sql);
            foreach ($allModulesInsert as $module_id=>$module)
            {
                $status = (isset($_POST['module'][$module_id])) ? 1 : (($module['display']==0 && $module['default']==1) ? 1 : 0);
                $sql = "INSERT INTO gma_company_module SET companyId='$companyId',module_id='$module_id',status='$status'";
                mysql_query($sql);
            }
            
            header("Location: company.php?msg=updated");
            exit;
        }
        $company_sql .= " AND gma_logins.companyId='$companyId'";
        $company_rs   = mysql_query($company_sql);
        if(mysql_num_rows($company_rs)!=1)
        {
            header("Location: company.php");
            exit;
        }
        $company_row = mysql_fetch_array($company_rs);
        
        $module_ids = array();
        $module_sql = "SELECT * FROM gma_company_module WHERE companyId='$companyId' AND status=1";
        $module_rs  = mysql_query($module_sql);
        while ($module_row = mysql_fetch_assoc($module_rs)) {
        	   $module_ids[] = $module_row['module_id'];
        }
        
        break;
        
    case 'delete':
        $company_sql .= " AND gma_logins.companyId='$companyId'";
        $company_rs   = mysql_query($company_sql);
        if(mysql_num_rows($company_rs)!=1)
        {
            header("Location: company.php");
            exit;
        }
        
        header("Location: company.php?d");        
        break;
        
    case 'deleteall':
//        $group_id   = implode(',', $_REQUEST['delete']);
//        echo $group_id;exit;
        
        header("Location: company.php?d");        
        break;
        
    case 'login':
        $_SESSION['adm_userId'] = $_SESSION['ses_userId'];
        $_SESSION['ses_userId'] = $_REQUEST['userId'];
        
        header("Location: index.php");        
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY companyName ASC ';
        
        $company_sql  .= ($srchtxt!='') ? " AND (companyName LIKE '$srchtxt%' OR companyAccountEmail LIKE '$srchtxt%' OR fullName LIKE '$srchtxt%' OR userName LIKE '$srchtxt%' OR gma_logins.userId='$srchtxt')" : '';
        $company_sql  .= ($search!='') ? " AND (companyName LIKE '$search%' OR fullName LIKE '$search%')" : '';
                
        $company_sql  .= " $orderBy";
        $company_rs    = mysql_query($company_sql);
        $company_count = mysql_num_rows($company_rs);
        
        $pagination = '';
        if($company_count>$perPage)
        {
            $company_sql  .= " LIMIT $offset, $perPage";
            $company_rs    = mysql_query($company_sql);
            
            $maxPage     = ceil($company_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($company_count, $perPage, 5);
        }
        
        //$links = '<a href="company.php?action=add" title="Add new">Add new</a><a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
        $add_url   = 'company.php?action=add';
        $del_url   = 'javascript:void(0);';
        $del_click = 'deleteAll();';
        $search_box = true;
            
        $chars = '<a href="company.php">All</a>';
        for($i=65;$i<91;$i++)
        {
            $char      = chr($i);
            $selected  = ($char==$search ? "class='selected'" : '');
            $chars    .= "&nbsp;<a href='company.php?search=$char' $selected>$char</a>";
        }
        break;
}

include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr height="30">
        <th width="2%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
        <th valign="middle"><span>Company Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=companyName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=companyName&order=DESC" class="desc"></a></th>
        <th valign="middle"><span>Owner Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=fullName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=fullName&order=DESC" class="desc"></a></th>
        <th width="5%">Status</th>
        <th width="10%">Action</th>
    </tr>
    <?php
    $j=0;
    while($company_row = mysql_fetch_assoc($company_rs))
    {
        $class   = ((($j++)%2)==1) ? 'altrow' : '';
        $auto_id = $company_row['companyId'];
        $ownerId = $company_row['ownerId'];
        ?>
        <tr class="<?=$class?>">
            <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
            <td><?=$company_row['companyName']?></td>
            <td><?=$company_row['fullName']?></td>
            <td><?=($company_row['company_status']==1 ? 'Active' : 'Inactive')?></td>
            <td><a href="company.php?action=edit&company_id=<?=$auto_id?>" alt="Edit Details" title="Edit Details" class="btn_style">Edit</a>&nbsp;<a href="company.php?action=delete&user_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');" class="btn_style">Delete</a>&nbsp;<a href="company.php?action=login&userId=<?=$ownerId?>" title="Login" class="btn_style">Login</a></td>
        </tr>
        <?php
    }
    if($company_count==0) { ?>
       <tr><td class="norecords" colspan="10">No Records Found</td></tr>
    <? } ?>
</table>
</form>

<? } else if($action=='edit' || $action=='add') { ?>

<form name="userForm" id="userForm" method="post" action="">
<input type="hidden" name="ownerId" id="ownerId" value="<?=$company_row['ownerId']?>"/>
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=($action=='add' ? 'ADD' : 'EDIT')?> COMPANY DETAILS&nbsp;<span class="backlink"><a href="company.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="30%">Company Name</td>
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
        <td>Bank Name</td>
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
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Status</td>
        <td><input type="checkbox" name="status" id="status" value="1" <?=((isset($company_row['company_status']) && $company_row['company_status']==1) || $action=='add') ? 'checked' : ''?> /></td>  
    </tr> 
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Owner name</td>
        <td><input type="text" name="fullName" id="fullName" class="textbox required" value="<?=$company_row['fullName']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Username</td>
        <td><input type="text" name="userName" id="userName" class="textbox required" value="<?=$company_row['userName']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Password</td>
        <td><input type="text" name="password" id="password" class="textbox required" value="<?=$company_row['password']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email</td>
        <td><input type="text" name="email" id="email" class="textbox required email" value="<?=$company_row['email']?>"/></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>" valign="top">
        <td>Modules</td>
        <td>
        
        <? $menu = 0; foreach ($allModules as $module) { ?>
            <? if($menu!=$module['menu']) { if($menu!=0) { echo '<div class="clear">&nbsp;</div>'; } $menu = $module['menu']; ?>
                <div class="clear"><b><?=$menu_types[$menu]?></b></div>
            <? } ?>
            <span class="fleft" style="width:30%"><input type="checkbox" name="module[<?=$module['id']?>]" id="module_<?=$module['id']?>" class="textbox number" style="width:20px;" value="<?=$module['id']?>" <?=((in_array($module['id'], $module_ids) || ($action=='add' && $module['default']==1)) ? 'checked' : '')?> /><?=$module['name']?></span>
        <? } ?>
        
    
        </td>  
    </tr>
</table>
<div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>

<script>
$(document).ready(function() {
    jQuery("#userForm").validate({
        rules: {
            userName: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkUserName',
                        user_id: '<?=$company_row['userId']?>'
                    }
                }
            },
            email: {
                required: true
            }
        },
        messages: {
            userName: {
                remote: jQuery.format("Username is already in use.")
            },
            email: {
                remote: jQuery.format("Email is already in use.")
            }
        }
    });
});
</script>

<? }

include('footer.php');
?>