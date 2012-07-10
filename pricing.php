<?php
include("header.php");  
include("config.php");

$group_id = 0;
$service_sql = "SELECT gma_services.*,gma_groups.name as group_name FROM gma_services,gma_groups WHERE group_id=gma_groups.id AND companyId=".GetSQLValueString($ses_companyId, 'text').' ORDER BY group_id ASC';
$service_rs = mysql_query($service_sql);
$page_title = 'Pricing';
include('sub_header.php');

?>
<div class="newinvoice" style="width:80%"><br><br>
<?
while ($service_row = mysql_fetch_assoc($service_rs)) {
	   if($service_row['group_id']!=$group_id) {
	        if($group_id!=0) echo '</tbody></table>';
	        echo '<table width="100%" border="0" cellspacing="2" cellpadding="5" class="color3 fleft" style="margin:10px;">
	            <tbody>
             <tr><td colspan="10" class="color3"><div align="left"><b>'.$service_row['group_name'].'</b></div></td></tr>';
	        
	        $group_id = $service_row['group_id'];
	   }
	   echo '<tr>
              <td class="color4 normaltext"><div align=left >'.$service_row['service_name'].'</div></td>
              <td class="color4 normaltext" width="20%" nowrap><div align=left >R '.formatMoney($service_row['amount'],true).'</div></td>
          </tr>';
}
if($group_id!=0) echo '</tbody></table><br><br>';
?>
</div>
<?
include('footer.php');
?>