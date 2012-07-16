<?php
$settings = false;
include("header.php");  
include("config.php");

$action    = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$perPage   = ($_SESSION['perpageval']!='') ? $_SESSION['perpageval'] : 50;
$pageNum   = ($_REQUEST['page']!='') ? $_REQUEST['page'] : 1;

$userId     = (isset($_REQUEST['user_id']) && $_REQUEST['user_id']>0) ? $_REQUEST['user_id'] : 0;
$srchtxt    = trim(($_REQUEST['srchtxt']!='') ? $_REQUEST['srchtxt'] : '');
$search     = trim(($_REQUEST['search']!='') ? $_REQUEST['search'] : '');
$userTypes  = userTypes('', 1, 1);
$userTypes  = "'".implode("', '", $userTypes)."'";
$page_title = 'Administrator Users';

$user_sql = "SELECT * FROM gma_admin_details,gma_logins,gma_admins WHERE gma_admins.type=gma_logins.userType AND gma_admin_details.userId=gma_logins.userId AND userType IN ($userTypes) AND gma_logins.companyId='$ses_companyId'";
switch ($action)
{
    case 'add':
        if(isset($_REQUEST['sbmt']))
        {
            unset($_POST['sbmt']);
            
            $discounts = $_POST['discount'];
            unset($_POST['discount']);
            
            $userName = $_POST['userName'];
            $password = $_POST['password'];
            $userType = $_POST['userType'];
            $useremail = $_POST['email'];
            unset($_POST['userName']);
            unset($_POST['password']);
            unset($_POST['userType']);
            unset($_POST['email']);
            $sql = "INSERT INTO gma_logins SET userName=".GetSQLValueString($userName, 'text').",password=".GetSQLValueString($password, 'text').",userType=".GetSQLValueString($userType, 'text');
            $sql = "INSERT INTO gma_logins SET companyId=".GetSQLValueString($ses_companyId, 'text').",userName=".GetSQLValueString($userName, 'text').",password=".GetSQLValueString($password, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text');
            mysql_query($sql);
            $userId = mysql_insert_id();
                
            $values = "userId='$userId'";
            foreach ($_POST AS $name=>$value)
            {
                if($values!='') $values .= ',';
                $values .= "$name=".GetSQLValueString($value, 'text');
            }
            $sql = "INSERT INTO gma_admin_details SET $values";
            mysql_query($sql);
        
            $email_values = array(
                'name' => $_POST['fullName'],
                'username' => $userName,
                'password' => $password,
                'email' => $useremail,
                'to_email' => $useremail,
            );
            emailSend('new_admin', $email_values);
            
            header("Location: admins.php?msg=added");
            exit;
        }
        break;
        
    case 'edit':
        if(isset($_REQUEST['userName']))
        {
            $values = '';
            unset($_POST['sbmt']);
            
            $discounts = $_POST['discount'];
            unset($_POST['discount']);
            
            $userName = $_POST['userName'];
            $password = $_POST['password'];
            $userType = $_POST['userType'];
            $useremail = $_POST['email'];
            unset($_POST['userName']);
            unset($_POST['password']);
            unset($_POST['userType']);
            unset($_POST['email']);
            $sql = ($password!='') ? ",password=".GetSQLValueString($password,'text') : '';
            $sql = "UPDATE gma_logins SET userName=".GetSQLValueString($userName, 'text').",email=".GetSQLValueString($useremail, 'text').",userType=".GetSQLValueString($userType, 'text')."$sql WHERE userId='$userId'";
            mysql_query($sql);
            
            foreach ($_POST AS $name=>$value)
            {
                if($values!='') $values .= ',';
                $values .= "$name=".GetSQLValueString($value, 'text');
            }
            if($values!='')
            {
                $sql = "UPDATE gma_admin_details SET $values WHERE userId='$userId'";
                mysql_query($sql);
                
                header("Location: admins.php?msg=updated");
                exit;
            }
        }
        $user_sql = "SELECT * FROM gma_admin_details AS UD,gma_logins AS LO WHERE UD.userId=LO.userId AND UD.userId='$userId' AND userType IN ($userTypes)";
        $user_rs  = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: admins.php");
            exit;
        }
        $user_row = mysql_fetch_array($user_rs);	 
        
        break;
        
    case 'view':
        $user_sql = "SELECT * FROM gma_admin_details AS UD,gma_logins AS LO WHERE UD.userId=LO.userId AND UD.userId='$userId' AND userType IN ($userTypes)";
        $user_rs  = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: admins.php");
            exit;
        }
        $user_row = mysql_fetch_array($user_rs);
        
        break;
        
    case 'delete':
        $user_sql = "SELECT * FROM gma_admin_details AS UD,gma_logins AS LO WHERE UD.userId=LO.userId AND UD.userId='$userId' AND userType IN ($userTypes)";
        $user_rs  = mysql_query($user_sql);
        if(mysql_num_rows($user_rs)!=1)
        {
            header("Location: admins.php");
            exit;
        }
        $user_row = mysql_fetch_array($user_rs);
        if($user_row['userType']!='gnet_admin')
        {
            $user_sql = "DELETE FROM gma_admin_details WHERE userId='$userId'";
            mysql_query($user_sql);
            $user_sql = "DELETE FROM gma_logins WHERE userId='$userId'";
            mysql_query($user_sql);
        }
        header("Location: admins.php?msg=deleted");
        exit;
        break;
        
    case 'permission':
             
        $userTypes = array();
        $admins_sql = "SELECT * FROM gma_admins WHERE id>2";
        $admins_rs  = mysql_query($admins_sql);
        while ($admins_row = mysql_fetch_assoc($admins_rs))
        {
            $userTypes[$admins_row['id']] = $admins_row['name'] . "|" . $admins_row['user'];
        }
        
        $userModules = array();
        $modules_sql = "SELECT * FROM gma_modules WHERE id IN (SELECT module_id FROM gma_company_module WHERE status=1 AND companyId='$ses_companyId') AND display=1";
        $modules_rs  = mysql_query($modules_sql);
        while ($modules_row = mysql_fetch_assoc($modules_rs))
        {
            $userModules[$modules_row['id']] = $modules_row['name'] . "|" . $modules_row['for_users'];
        }
        
        $permissionModules = array();
        $permission_sql = "SELECT * FROM gma_admins_permission WHERE admins_id>2 AND companyId='$ses_companyId'";
        $permission_rs  = mysql_query($permission_sql);
        while ($permission_row = mysql_fetch_assoc($permission_rs))
        {
            $permissionModules[$permission_row['admins_id']][] = $permission_row['module_id'];
        }
        // echo '<pre>'; print_r($permissionModules); exit;
        
        $links = '<a href="admins.php" title="Admin users">Admin users</a>';
        $page_title = 'Administrator permission';
        
        break;
        
    default:
        $action  = 'list';
        $offset  = ($pageNum - 1) * $perPage;
        $orderBy = ($_REQUEST['orderby']!='') ? 'ORDER BY '.$_REQUEST['orderby'].' '.$_REQUEST['order'] : 'ORDER BY fullName ASC ';
        
        $user_sql  .= ($srchtxt!='') ? " AND (fullName LIKE '$srchtxt%' OR userName LIKE '$srchtxt%' OR gma_admin_details.userId='$srchtxt')" : '';
        $user_sql  .= ($search!='') ? " AND (fullName LIKE '$search%')" : '';
        
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
        
        //$links = '<a href="admins.php?action=add" title="Add new">Add new</a><a href="admins.php?action=permission" title="Admin Permission">Admin Permission</a>';
        $search_box = true;
        $add_url    = 'admins.php?action=add';
        $other_urls[] = array('text'=>'Permission', 'sign'=>'+', 'url'=>'admins.php?action=permission', 'click'=>'');
        
        $chars = '<a href="admins.php">All</a>';
        for($i=65;$i<91;$i++)
        {
            $char      = chr($i);
            $selected  = ($char==$search ? "class='selected'" : '');
            $chars    .= "&nbsp;<a href='admins.php?search=$char' $selected>$char</a>";
        }
        break;
}

$group_sql = "SELECT * FROM gma_groups WHERE 1 ORDER BY name ASC";
$group_rs  = mysql_query($group_sql);
while ($group_row = mysql_fetch_assoc($group_rs)) {
	   $group_rows[$group_row['id']] = $group_row;
}

$userType = userTypes($user_row['userType'], 1);

include('sub_header.php');
if($action=='list') { ?>

<table width="100%" class="list" cellpadding="0" cellspacing="0">
    <tr valign="middle">
        <th width="25%"><span>Full Name</span>&nbsp;<a href="?<?=$queryString ?>&orderby=fullName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=fullName&order=DESC" class="desc"></a></th>
        <th width="20%"><span>Username</span>&nbsp;<a href="?<?=$queryString ?>&orderby=userName&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=userName&order=DESC" class="desc"></a></th>
        <th width="25%"><span>Email</span>&nbsp;<a href="?<?=$queryString ?>&orderby=email&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=email&order=DESC" class="desc"></a></th>
        <th width="20%"><span>Admin Type</span>&nbsp;<a href="?<?=$queryString ?>&orderby=userType&order=ASC" class="asc"></a><a href="?<?=$queryString ?>&orderby=userType&order=DESC" class="desc"></a></th>
        <th width="10%">Action</th>
    </tr>
    <?php
    $j=0;
    while($user_row = mysql_fetch_assoc($user_rs))
    {
        $class   = ((($j++)%2)==1) ? 'altrow' : '';
        $auto_id = $user_row['userId'];
        ?>
        <tr class="<?=$class?>">
            <td><?=$user_row['fullName']?></td>
            <td><?=$user_row['userName']?></td>
            <td><?=$user_row['email']?></td>
            <td><?=$user_row['name']?></td>
            <td><a href="admins.php?action=view&user_id=<?=$auto_id?>" alt="View Details" title="View Details" class="btn_style">View</a>&nbsp;<a href="admins.php?action=edit&user_id=<?=$auto_id?>" alt="Edit Details" title="Edit Details" class="btn_style">Edit</a>&nbsp;<a href="admins.php?action=delete&user_id=<?=$auto_id?>" alt="Delete Details" title="Delete Details" class="btn_style">Delete</a></td>
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
<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3"><?=($action=='add' ? 'ADD' : 'EDIT')?> ADMIN DETAILS&nbsp;<span class="backlink"><a href="admins.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%">Username</td>
        <td><input type="text" name="userName" id="userName" class="textbox required" value="<?=$user_row['userName']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Password</td>
        <td><input type="text" name="password" id="password" class="textbox required" value="<?=$user_row['password']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Full Name</td>
        <td><input type="text" name="fullName" id="fullName" class="textbox required" value="<?=$user_row['fullName']?>" /></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email</td>
        <td><input type="text" name="email" id="email" class="textbox required email" value="<?=$user_row['email']?>"/></td>  
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>User Type</td>
        <td><?=$userType?></td>  
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
});
</script>

<? } else if($action=='view') { ?>

<table width="100%" class="list addedit" cellpadding="0" cellspacing="0">
    <tr><th colspan="3">VIEW ADMIN DETAILS&nbsp;<span class="backlink"><a href="admins.php">Back</a></span></td></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">    
        <td>Username</td>
        <td><?=$user_row['userName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Full Name</td>
        <td><?=$user_row['fullName']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Email</td>
        <td><?=$user_row['email']?></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>User Type</td>
        <td><?=$user_row['userType']?></td>
    </tr>
</table>
</div>

<? } 
else if($action=='permission') { 
 
    foreach ($userTypes as $admins_id=>$name) { 
        $admn = explode("|", $name);
        $name = $admn[0];
        $adminType = $admn[1];
        
        echo '<div class="moduleContainer">';
        echo '<div class="adminHead">' . $name . '</div>';
        echo showPermissionGrid($adminType,$admins_id, $userModules, $permissionModules[$admins_id]);
        echo '</div>';
        echo '<div style="height:20px;"></div>';
    }
    ?>
    <script>
    $(document).ready(function() {
        $('input').bind('click', function() {
            var id      = $(this).attr("id");
            var checked = 0;
            if($(this).attr("checked"))
                checked = 1;
            
            $.post("ajax_check.php", { task: 'permission', id: id, checked: checked } );
        });
    });
    </script>

<? }
include('footer.php');

function showPermissionGrid($adminType,$admins_id, $arrModuleNames, $arrPermissions){
    foreach ($arrModuleNames as $module_id=>$module) {
        $name    = $admins_id.'_'.$module_id;
        $checked = (in_array($module_id, $arrPermissions)) ? 'checked' : ''; 
        
        $modool = explode("|", $module);
        $module = $modool[0];
        $moduleUsers = $modool[1];
        
        if(strpos($moduleUsers,$adminType)===false) { } else { 
            $moduleTable .= '<div class="moduleName"><input type="checkbox" id="'. $name. '" value="1" ' . $checkbox . $checked . '>' . $module. '</div>';
        }
   } 
   return $moduleTable;
}	
?>