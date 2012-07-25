<?php
$user_btn = 'selected';
include("header.php");  
include("config.php");

$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$perPage   = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum   = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$userId     = (isset($_REQUEST['user_id']) && $_REQUEST['user_id']>0) ? $_REQUEST['user_id'] : 0;
$srchtxt    = trim(($_REQUEST['srchtxt']!='') ? $_REQUEST['srchtxt'] : '');
$search     = trim(($_REQUEST['search']!='') ? $_REQUEST['search'] : '');
$userTypes  = userTypes('', 0, 1);
$userTypes  = "'".implode("', '", $userTypes)."'";
$page_title = 'Clients';

if($ses_loginType=='user' && $action!="view") {
    $action = "list";
}


$user_sql  = "SELECT * FROM gma_user_details,gma_logins,gma_company WHERE gma_user_details.userId=gma_logins.userId AND gma_company.companyId=gma_logins.companyId AND userType IN ($userTypes) AND gma_company.companyId='$ses_companyId' ";
switch ($action)
{
    case 'add':
        if(isset($_REQUEST['sbmt']))
        {
            unset($_POST['sbmt']);
            
            $discounts = $_POST['discount'];
            unset($_POST['discount']);
            
            $_POST['joinDate'] = convertToMysqlDate($_POST['joinDate']);
            
            $userName  = $_POST['userName'];
            $password  = $_POST['password'];
            $userType  = $_POST['userType'];
            $useremail = $_POST['email'];
            unset($_POST['userName']);
            unset($_POST['password']);
            unset($_POST['userType']);
            unset($_POST['email']);
            unset($_POST['delivery_same']);
            $sql = "INSERT INTO gma_logins SET companyId=".GetSQLValueString($ses_companyId, 'text').",userName=".GetSQLValueString($userName, 'text').",password=".GetSQLValueString($password, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text');
            mysql_query($sql);
            $userId = mysql_insert_id();
                
            $gma_user_details = $gma_user_billing = $gma_user_delivery = "userId='$userId'"; //,joinDate=NOW()";
            $_POST['discount_type'] = (isset($_POST['discount_type'])) ? 1 : 0;
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
            foreach ($discounts as $group_id=>$discount)
            {
                if($discount>0)
                {
                    $sql = "INSERT INTO gma_user_discount SET userId='$userId',group_id='$group_id',discount='$discount'";
                    mysql_query($sql);                
                }
            }
            if($gma_user_details!='')
            {
                $sql = "INSERT INTO gma_user_details SET $gma_user_details";
                mysql_query($sql);
            }
            if($gma_user_billing!='')
            {
                $sql = "INSERT INTO gma_user_address SET type='B',$gma_user_billing";
                mysql_query($sql);
            }
            if($gma_user_delivery!='')
            {
                $sql = "INSERT INTO gma_user_address SET type='D',$gma_user_delivery";
                mysql_query($sql);
            }
        
            $email_values = array(
                'firstname' => $_POST['firstName'],
                'lastname' => $_POST['lastName'],
                'clientname' => $_POST['businessName'],
                'username' => $userName,
                'password' => $password,
                'email' => $useremail,
                'to_email' => $useremail,
            );
            emailSend('new_client', $email_values);
                
            header("Location: users.php?msg=added");
            exit;
        }
        $user_row['joinDate'] = date('d/m/Y');
        break;
        
    case 'edit':
        if(isset($_REQUEST['userName']))
        {
            $values = '';
            unset($_POST['sbmt']);
            
            $discounts = $_POST['discount'];
            unset($_POST['discount']);
            
            $_POST['joinDate'] = convertToMysqlDate($_POST['joinDate']);
            
            $userName  = $_POST['userName'];
            $password  = $_POST['password'];
            $userType  = $_POST['userType'];
            $useremail = $_POST['email'];
            unset($_POST['userName']);
            unset($_POST['password']);
            unset($_POST['userType']);
            unset($_POST['email']);
            $sql = ($password!='') ? ",password=".GetSQLValueString($password,'text') : '';
            $sql = "UPDATE gma_logins SET userName=".GetSQLValueString($userName, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text')."$sql WHERE userId='$userId'";
            mysql_query($sql);
            
            $gma_user_details = $gma_user_billing = $gma_user_delivery = ""; //,joinDate=NOW()";
            $_POST['discount_type'] = (isset($_POST['discount_type'])) ? 1 : 0;
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
            $sql = "DELETE FROM gma_user_discount WHERE userId='$userId'";
            mysql_query($sql);                
            foreach ($discounts as $group_id=>$discount)
            {
                if($discount>0)
                {
                    $sql = "INSERT INTO gma_user_discount SET userId='$userId',group_id='$group_id',discount='$discount'";
                    mysql_query($sql);                
                }
            }
            if($gma_user_details!='')
            {
                $sql = "UPDATE gma_user_details SET $gma_user_details WHERE userId='$userId'";
                mysql_query($sql);
            }
            if($gma_user_billing!='')
            {
                $sql = "UPDATE gma_user_address SET $gma_user_billing WHERE userId='$userId' AND type='B'";
                mysql_query($sql);
            }
            if($gma_user_delivery!='')
            {
                $sql = "UPDATE gma_user_address SET $gma_user_delivery WHERE userId='$userId' AND type='D'";
                mysql_query($sql);
            }
                
            header("Location: users.php?msg=updated");
            exit;
        }
        $user_sql .= " AND gma_logins.userId='$userId'";
        $user_rs   = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: users.php");
            exit;
        }
        $user_row = mysql_fetch_array($user_rs);	 
        $user_row['joinDate'] = dateFormat($user_row['joinDate']);
            
        $discount_sql = "SELECT * FROM gma_user_discount WHERE userId='$userId'";
        $discount_rs  = mysql_query($discount_sql);
        while ($discount_row = mysql_fetch_array($discount_rs))
        {
            $user_row['group_ids'][$discount_row['group_id']] = $discount_row['discount'];
        }     
        
        $user_billing_sql = "SELECT * FROM gma_user_address WHERE type='B' AND userId='$userId'";
        $user_billing_rs  = mysql_query($user_billing_sql);
        $user_billing     = mysql_fetch_assoc($user_billing_rs);
        
        $user_delivery_sql = "SELECT * FROM gma_user_address WHERE type='D' AND userId='$userId'";
        $user_delivery_rs  = mysql_query($user_delivery_sql);
        $user_delivery     = mysql_fetch_assoc($user_delivery_rs);
        
        break;
        
    case 'view':
        $user_sql .= " AND gma_logins.userId='$userId'";
        $user_rs   = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: users.php");
            exit;
        }
        $user_row     = mysql_fetch_array($user_rs);
        $discount_sql = "SELECT * FROM gma_user_discount WHERE userId='$userId'";
        $discount_rs  = mysql_query($discount_sql);
        while ($discount_row = mysql_fetch_array($discount_rs))
        {
            $user_row['group_ids'][$discount_row['group_id']] = $discount_row['discount'];
        }
        break;
        
    case 'delete':
        $user_sql .= " AND gma_logins.userId='$userId'";
        $user_rs   = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: users.php");
            exit;
        }
                
//        $sql = "DELETE FROM gma_logins WHERE userId='$userId'";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_user_details WHERE userId='$userId'";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_user_discount WHERE userId='$userId'";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_order_details WHERE orderId IN (SELECT id FROM gma_order WHERE userId='$userId')";
//        mysql_query($sql);
//
//        $sql = "DELETE FROM gma_order WHERE userId='$userId'";
//        mysql_query($sql);
//
//        $sql = "DELETE FROM gma_payments WHERE userId='$userId'";
//        mysql_query($sql);
        
        header("Location: users.php?d");        
        break;
        
    case 'deleteall':
        $user_id   = implode(',', $_REQUEST['delete']);
        $user_sql .= " AND gma_logins.userId IN ($user_id)";
        $user_id   = 0;
        $user_rs   = mysql_query($user_sql);
        while($user_row = mysql_fetch_assoc($user_rs))
        {
            $user_id .= ','.$user_row['userId'];
        }
        if($user_id=='0')
        {
            header("Location: users.php?i");
            exit;
        }
                
//        $sql = "DELETE FROM gma_logins WHERE userId IN ($user_id)";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_user_details WHERE userId IN ($user_id)";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_user_discount WHERE userId IN ($user_id)";
//        mysql_query($sql);
//        
//        $sql = "DELETE FROM gma_order_details WHERE orderId IN (SELECT id FROM gma_order WHERE userId IN ($user_id))";
//        mysql_query($sql);
//
//        $sql = "DELETE FROM gma_order WHERE userId IN ($user_id)";
//        mysql_query($sql);
//
//        $sql = "DELETE FROM gma_payments WHERE userId IN ($user_id)";
//        mysql_query($sql);
        
        header("Location: users.php?d");        
        break;
        
    case 'login':	
    	   $userId   = $_REQUEST['userId'];
        $user_sql = "SELECT * FROM `gma_logins` WHERE `userId`='$userId' AND `companyId`='$ses_companyId'";
        $user_rs  = mysql_query($user_sql);
        
        if(mysql_num_rows($user_rs)==1)
        {
            $user_row = mysql_fetch_assoc($user_rs);
            
            $_SESSION['usr_userId'] = $_SESSION['ses_userId'];
            
            $_SESSION['ses_userId']    = $ses_userId    = $user_row['userId'];
            $_SESSION['ses_companyId'] = $ses_companyId = $user_row['companyId'];
            $_SESSION['ses_userType']  = $ses_userType  = $user_row['userType'];
            $_SESSION['ses_loginType'] = $ses_loginType = ($ses_loginType=='normal' || $ses_loginType=='trial' || $ses_loginType=='client') ? 'user' : 'admin';
        }
        
        header("Location: index.php");        
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY businessName ASC ';
        
        $user_sql  .= ($srchtxt!='') ? " AND (firstName LIKE '$srchtxt%' OR lastName LIKE '$srchtxt%' OR businessName LIKE '$srchtxt%' OR userName LIKE '$srchtxt%' OR gma_user_details.userId='$srchtxt')" : '';
        $user_sql  .= ($search!='') ? " AND (businessName LIKE '$search%')" : '';
                
        $user_sql  .= " $orderBy";
        $user_rs    = mysql_query($user_sql);
        $user_count = mysql_num_rows($user_rs);
        
        $pagination = '';
        if($user_count>$perPage)
        {
            $user_sql  .= " LIMIT $offset, $perPage";
            $user_rs    = mysql_query($user_sql);
            
            $maxPage     = ceil($user_count/$perPage);
            $pagination  = pagination($maxPage, $pageNum);
            $pagination  = paginations($user_count, $perPage, 5);
        }
        
        if($ses_loginType!='user') {
            //$links = '<a href="users.php?action=add" title="Add new">Add new</a><a href="javascript:void(0);" onclick="deleteAll();" title="Delete">Delete</a>';
            $add_url   = 'users.php?action=add';
            $del_url   = 'javascript:void(0);';
            $del_click = 'deleteAll();';
        }
	        
        $chars = '<a href="users.php">All</a>';
        for($i=65;$i<91;$i++)
        {
            $char      = chr($i);
            $selected  = ($char==$search ? "class='selected'" : '');
            $chars    .= "&nbsp;<a href='users.php?search=$char' $selected>$char</a>";
        }
        $search_box = true;
        break;
}
$group_sql = "companyId='$ses_companyId' AND status=1";
$group_sql = "SELECT * FROM gma_groups WHERE $group_sql ORDER BY name ASC";
$group_rs  = mysql_query($group_sql);
while ($group_row = mysql_fetch_assoc($group_rs)) {
	   $group_rows[$group_row['id']] = $group_row;
}

$userType = userTypes($user_row['userType']);

include('sub_header.php');
if($action=='list') { ?>

<form method="POST" id="listForm" name='listForm'>
<input type="hidden" name="action" value="deleteall">
<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr height="30">
        <th width="3%"><input type="checkbox" name="selectall" id="selectall" onclick="checkUncheck(this);"></th>
        <th width="20%" valign="middle"><span>Client Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=businessName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=businessName&order=DESC" class="desc"></a></th>
        <th width="20%"><span>Contact Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=firstName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=firstName&order=DESC" class="desc"></a></th>
        <th width="15%"><span>Username</span>&nbsp;<a href="?<?=$queryString ?>&orderby=userName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=userName&order=DESC" class="desc"></a></th>
        <th width="15%"><span>Email</span>&nbsp;<a href="?<?=$queryString ?>&orderby=email&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=email&order=DESC" class="desc"></a></th>
        <th width="10%">Tel No.</th>
        <th width="17%">Action</th>
    </tr>
    <?php
    $j=0;
    while($user_row = mysql_fetch_assoc($user_rs))
    {
        $class   = ((($j++)%2)==1) ? 'altrow' : '';
        $auto_id = $user_row['userId'];
        ?>
        <tr class="<?=$class?>">
            <td><input type="checkbox" id="delete" name="delete[]" value="<?=$auto_id?>"></td>
            <? if($companyId==0 && 1==2) { ?>
                <td><?=$user_row['companyName']?></td>
            <? } ?>
            <td><?=$user_row['businessName']?></td>
            <td><?=$user_row['firstName'].' '.$user_row['lastName']?></td>
            <td><?=$user_row['userName']?></td>
            <td><?=$user_row['email']?></td>
            <td><?=$user_row['phone']?></td>
            <td class="buttons">
                <!--<a href="users.php?action=view&user_id=<?=$auto_id?>" alt="View Details" title="View Details" class="btn_style">View</a>-->
                <?php if( strtolower(trim($_SESSION['ses_userType']))!='client') { ?>
                    <a href="users.php?action=edit&user_id=<?=$auto_id?>" alt="Edit Details" title="Edit Details" class="btn_style">Edit</a>&nbsp;<a href="users.php?action=delete&user_id=<?=$auto_id?>" onclick="return window.confirm('Are you sure to delete this ?');" class="btn_style">Delete</a>&nbsp;<a href="invoices.php?action=add&userId=<?=$auto_id?>" class="btn_style">Invoice</a>&nbsp;<a href="users.php?action=login&userId=<?=$auto_id?>" class="btn_style">Login</a>
                <? } ?>
            </td>
        </tr>
        <?php
    }
    if($user_count==0) { ?>
       <tr><td class="norecords" colspan="10">No Records Found</td></tr>
    <? } ?>
</table>
</form>
    
<? } else if($action=='edit' || $action=='add') { ?>

<form name="userForm" id="userForm" method="post" action="">
<input type="hidden" name="userType" value="normal">
    <table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
        <tr><th colspan="3">Basic Details</td></tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td width="20%">Account Grading</th>
            <td><input type="text" name="grade" id="grade" class="textbox number" value="<?=$user_row['grade']?>" /></td>  
        </tr>  
        <tr valign="top" class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Discount Percentage</th>
            <td>
            <div style="padding-bottom:5px;"><b>Overall Discount : </b><input type="checkbox" name="discount_type" id="discount_type" value="1" <?=($user_row['discount_type']==1 ? 'checked' : '')?> />&nbsp;<input type="text" name="discount_value" id="discount_value" class="textbox number" style="width:200px;" value="<?=@$user_row['discount_value']?>" /></div>
            <? foreach ($group_rows as $group_id=>$group_row) { ?>
                <b><?=$group_row['name']?>&nbsp;:&nbsp;</b><input type="text" name="discount[<?=$group_id?>]" id="discount_<?=$group_id?>" class="textbox number discount" style="width:200px;" value="<?=@$user_row['group_ids'][$group_id]?>" /><br>
            <? } ?>
            
            </td>  
        </tr>  
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Username</th>
            <td><input type="text" name="userName" id="userName" class="textbox required" value="<?=$user_row['userName']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Password</th>
            <td><input type="text" name="password" id="password" class="textbox required" value="<?=$user_row['password']?>" /></td>  
        </tr>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Vat No</th>
            <td><input type="text" name="vatNo" id="vatNo" class="textbox required" value="<?=$user_row['vatNo']?>" /></td>  
        </tr>    
        <!--<tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>User Status</th>
            <td><input type="text" name="userStatus" id="userStatus" class="textbox" value="<?=$user_row['userStatus']?>" /></td>  
        </tr>-->    
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Join Date</th>
            <td><input type="text" name="joinDate" id="joinDate" class="textbox required" value="<?=$user_row['joinDate']?>" readonly /></td>  
        </tr>
        <!--<tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>User Type</th>
            <td><?=$userType?></td>  
        </tr>--> 
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td>Lead</th>
            <td><input type="text" name="lead" id="lead" class="textbox" value="<?=$user_row['lead']?>" /></td>  
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
    <div class="addedit_btn"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>

<script>
$(document).ready(function() {
    $('#joinDate').datepicker({dateFormat: date_format, showOtherMonths: true, selectOtherMonths: true});
    //$('#joinDate').datePicker({startDate: start_date, dateFormat: date_format});
    
    var discount_type = $('#discount_type').attr('checked');
    if(discount_type) {
        $('.discount').css('background', '#F4F4F4');
        $('.discount').attr('readonly', 'readonly');
        
        $('#discount_value').css('background', '#FFF');
        $('#discount_value').removeAttr('readonly')
    } else {
        $('#discount_value').css('background', '#F4F4F4');
        $('#discount_value').attr('readonly', 'readonly');
    }
    $('#discount_type').click(function() {
        var discount_type = $('#discount_type').attr('checked');
        if(discount_type) {
            $('#discount_value').css('background', '#FFF');
            $('#discount_value').removeAttr('readonly')
            
            $('.discount').css('background', '#F4F4F4');
            $('.discount').attr('readonly', 'readonly');
        } else {
            $('#discount_value').css('background', '#F4F4F4');
            $('#discount_value').attr('readonly', 'readonly');
            
            $('.discount').css('background', '#FFF');
            $('.discount').removeAttr('readonly');
        }
    });
    //alert(discount_type)
   
    jQuery("#userForm").validate({
        rules: {
            userName: {
                required: true,
                remote: {
                    url: "ajax_check.php",
                    type: "post",
                    data: {
                        task: 'checkUserName',
                        user_id: '<?=$userId?>'
                    }
                }
            },
            email: {
                required: true,
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

<? } else if($action=='view') { ?>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">VIEW CLIENT DETAILS&nbsp;<span class="backlink"><a href="users.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account Grading</th>
        <td><?=$user_row['grade']?></td>
    </tr>
    <tr valign="top" class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Discount Percentage</th>
        <td>
        
        <? foreach ($group_rows as $group_id=>$group_row) { ?>
            <?=$group_row['name']?>&nbsp;:&nbsp;<?=@$user_row['group_ids'][$group_id]?><br>
        <? } ?>
        
        </td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Username</th>
        <td><?=$user_row['userName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="25%">Client Name</th>
        <td><?=$user_row['businessName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>First Name</th>
        <td><?=$user_row['firstName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Last Name</th>
        <td><?=$user_row['lastName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email</th>
        <td><?=$user_row['email']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Tel No.</th>
        <td><?=$user_row['phone']?></td>
    </tr> 
    
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Owner/Manager First Name</th>
        <td><?=$user_row['ownerFirstName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Owner/Manager Last Name</th>
        <td><?=$user_row['ownerLastName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Owner/Manager Email</th>
        <td><?=$user_row['ownerEmail']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Owner/Manager Tel No.</th>
        <td><?=$user_row['ownerPhone']?></td>
    </tr> 
    
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Address</th>
        <td><?=$user_row['address']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Payment Method</th>
        <td><?=$user_row['paymentMethod']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Payment Details Method</th>
        <td><?=$user_row['paymentDetailsMethod']?></td>
    </tr>
    
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Bank Name</th>
        <td><?=$user_row['bankName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account Name</th>
        <td><?=$user_row['accountName']?></td>
    </tr>  
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account Type</th>
        <td><?=$user_row['accountType']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Account No</th>
        <td><?=$user_row['accountNo']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Branch Code</th>
        <td><?=$user_row['branchCode']?></td>
    </tr>
    
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Vat No</th>
        <td><?=$user_row['vatNo']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>User Status</th>
        <td><?=$user_row['userStatus']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Join Date</th>
        <td><?=$user_row['joinDate']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>User Type</th>
        <td><?=$user_row['userType']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Lead</th>
        <td><?=$user_row['lead']?></td>
    </tr>
</table>

<? }
include('footer.php');
?>