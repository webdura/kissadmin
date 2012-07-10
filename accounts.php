<?php
$settings = false;
include("header.php");
include("config.php");

if(isset($_POST['paymentDue']))
{
    foreach ($_POST['paymentDue'] as $grade=>$paymentDue) {
        $paymentDue        = GetSQLValueString($_POST['paymentDue'][$grade], 'int');
        $overdueNotice     = GetSQLValueString($_POST['overdueNotice'][$grade], 'int');
        $suspensionWarning = GetSQLValueString($_POST['suspensionWarning'][$grade], 'int');
        $grade             = GetSQLValueString($grade, 'int');
        $paymentDue        = ($paymentDue!=0) ? $paymentDue : 1;
        $overdueNotice     = ($overdueNotice!=0) ? $overdueNotice : 1;
        $suspensionWarning = ($suspensionWarning!=0) ? $suspensionWarning : 1;
        
        $account_sql = "UPDATE gma_accounts SET paymentDue='$paymentDue',overdueNotice='$overdueNotice',suspensionWarning='$suspensionWarning' WHERE grade='$grade' AND companyId='$ses_companyId'";
        mysql_query($account_sql);
    }
    
    header("Location: accounts.php?msg=updated");
    exit;
}

$account_sql = "SELECT * FROM gma_accounts WHERE companyId='$ses_companyId'";
$account_rs  = mysql_query($account_sql);
//if(mysql_num_rows($account_rs)==0)
//{
//    $account_sql = "SELECT * FROM gma_accounts WHERE companyId='0'";
//    $account_rs  = mysql_query($account_sql);
//}
$account_array = array();
while ($account_row = mysql_fetch_assoc($account_rs)) {
    $account_array['paymentDue'][$account_row['grade']]        = $account_row['paymentDue'];
    $account_array['overdueNotice'][$account_row['grade']]     = $account_row['overdueNotice'];
    $account_array['suspensionWarning'][$account_row['grade']] = $account_row['suspensionWarning'];
}
//echo '<pre>'; print_r($account_array); exit;

$page_title = "Terms Details";
include_once('sub_header.php');
?>

<div class="client_display" style="padding-top:10px">
<form method="POST" id="accountForm" name='accountForm'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e2e2e2">
<tr><td>

    <table width="100%" border="0" cellspacing="2" cellpadding="5">
        <tr>
            <td style="padding:10px;"><strong>Terms Details</td>
            <td style="padding:10px;"><strong>Grade 1</td>
            <td style="padding:10px;"><strong>Grade 2</td>
            <td style="padding:10px;"><strong>Grade 3</td>
        </tr>
        <tr class="row1">
            <td>Payment due in (days)</td>
            <td width="20%"><input type="text" name="paymentDue[1]" class="required textbox number" value="<?=$account_array['paymentDue'][1]?>" style="width:100px"></td>
            <td width="20%"><input type="text" name="paymentDue[2]" class="required textbox number" value="<?=$account_array['paymentDue'][2]?>" style="width:100px"></td>
            <td width="20%"><input type="text" name="paymentDue[3]" class="required textbox number" value="<?=$account_array['paymentDue'][3]?>" style="width:100px"></td>
        </tr>
        <tr class="row2">
            <td>Overdue notice in (days)</td>
            <td><input type="text" name="overdueNotice[1]" class="required textbox number" value="<?=$account_array['overdueNotice'][1]?>" style="width:100px"></td>
            <td><input type="text" name="overdueNotice[2]" class="required textbox number" value="<?=$account_array['overdueNotice'][2]?>" style="width:100px"></td>
            <td><input type="text" name="overdueNotice[3]" class="required textbox number" value="<?=$account_array['overdueNotice'][3]?>" style="width:100px"></td>
        </tr>
        <tr class="row1">
            <td>Suspension warning in (days)</td>
            <td><input type="text" name="suspensionWarning[1]" class="required textbox number" value="<?=$account_array['suspensionWarning'][1]?>" style="width:100px"></td>
            <td><input type="text" name="suspensionWarning[2]" class="required textbox number" value="<?=$account_array['suspensionWarning'][2]?>" style="width:100px"></td>
            <td><input type="text" name="suspensionWarning[3]" class="required textbox number" value="<?=$account_array['suspensionWarning'][3]?>" style="width:100px"></td>
        </tr>
    </table>
    
</td></tr>
<tr><td style="padding:10px;padding-left:280px;" align="right"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="search_bt" /></td></tr>
</table>

<script>
$(document).ready(function() {
    jQuery("#accountForm").validate();
});
</script>
</form>

</div>

<?php include("footer.php");  ?>