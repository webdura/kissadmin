<?php
$settings = false;
include("header.php");
include("config.php");

if(isset($_POST['grade_1']))
{
    $grade_1 = GetSQLValueString($_POST['grade_1'], 'text');
    $grade_2 = GetSQLValueString($_POST['grade_2'], 'text');
    $grade_3 = GetSQLValueString($_POST['grade_3'], 'text');
    
    $grade_sql = "SELECT * FROM gma_grading WHERE companyId='$ses_companyId'";
    $grade_rs  = mysql_query($grade_sql);
    $grade_sql = "grade_1=$grade_1,grade_2=$grade_2,grade_3=$grade_3";
    if(mysql_num_rows($grade_rs)==0)
        $grade_sql = "INSERT INTO gma_grading SET $grade_sql,companyId='$ses_companyId'";
    else
        $grade_sql = "UPDATE gma_grading SET $grade_sql WHERE companyId='$ses_companyId'";
    mysql_query($grade_sql);
    
    header("Location: grading.php?msg=updated");
    exit;
}

$grade_sql = "SELECT * FROM gma_grading WHERE companyId='$ses_companyId'";
$grade_rs  = mysql_query($grade_sql);
if(mysql_num_rows($grade_rs)==0)
{
    $grade_sql = "SELECT * FROM gma_grading WHERE companyId='0'";
    $grade_rs  = mysql_query($grade_sql);
}
$grade_row = mysql_fetch_assoc($grade_rs);

$page_title = "Grading Messages";
include_once('sub_header.php');
?>

<form method="POST" id="gradeForm" name='gradeForm'>
<table width="100%" border="0" cellspacing="2" cellpadding="5" class="list addedit">
    <tr>
        <th colspan="2"><?=$page_title?></th>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td width="20%" valign="top">Grading 1</td>
        <td><textarea name="grade_1" id="grade_1" class="required textarea"><?=$grade_row['grade_1']?></textarea></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td valign="top">Grading 2</td>
        <td><textarea name="grade_2" id="grade_2" class="required textarea"><?=$grade_row['grade_2']?></textarea></td>
    </tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td valign="top">Grading 3</td>
        <td><textarea name="grade_3" id="grade_3" class="required textarea"><?=$grade_row['grade_3']?></textarea></td>
    </tr>
</table>
<div class="addedit_btn fleft"><input type="submit" name="sbmt" id="sbmt" value="Submit" class="btn_style" /></div>
</form>

<script>
$(document).ready(function() {
    jQuery("#gradeForm").validate();
});
</script>

<?php include("footer.php");  ?>