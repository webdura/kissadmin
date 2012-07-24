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
<div align="center">
<div style="width:60%;">
<form method="POST" id="accountForm" name='accountForm'>
<table class="list" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <th>Terms Details</th>
        <th>Days</th>
        <th>% Discount</th>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Payment due in (days)</td>
        <td width="20%"><input type="text" name="paymentDue[1]" class="required textbox number" value="<?=$account_array['paymentDue'][1]?>" style="width:100px"></td>
        <td width="20%"><input type="text" name="paymentDue[2]" class="required textbox number" value="<?=$account_array['paymentDue'][2]?>" style="width:100px"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Overdue notice in (days)</td>
        <td><input type="text" name="overdueNotice[1]" class="required textbox number" value="<?=$account_array['overdueNotice'][1]?>" style="width:100px"></td>
        <td><input type="text" name="overdueNotice[2]" class="required textbox number" value="<?=$account_array['overdueNotice'][2]?>" style="width:100px"></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td>Suspension warning in (days)</td>
        <td><input type="text" name="suspensionWarning[1]" class="required textbox number" value="<?=$account_array['suspensionWarning'][1]?>" style="width:100px"></td>
        <td><input type="text" name="suspensionWarning[2]" class="required textbox number" value="<?=$account_array['suspensionWarning'][2]?>" style="width:100px"></td>
    </tr>
</table>
<div class="addedit_btn fright"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>
</div>
</div>

<script>
$(document).ready(function() {
    jQuery("#accountForm").validate();
});
</script>

<?php include("footer.php");  ?>