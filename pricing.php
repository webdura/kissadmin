<?php
include("header.php");  
include("config.php");

$group_id = 0;
$service_sql = "SELECT gma_services.*,gma_groups.name as group_name FROM gma_services,gma_groups WHERE group_id=gma_groups.id AND companyId=".GetSQLValueString($ses_companyId, 'text').' ORDER BY group_id ASC';
$service_rs = mysql_query($service_sql);
$page_title = 'Pricing';
include('sub_header.php');

while ($service_row = mysql_fetch_assoc($service_rs)) {
	   if($service_row['group_id']!=$group_id) {
	        if($group_id!=0) echo '</table>';
         echo '<table class="list addedit fleft" cellpadding="0" cellspacing="0" style="margin:5px" width="100%">';
         echo '<tr><th colspan="3">'.$service_row['group_name'].'</td></tr>';
//	        echo '<table width="100%" border="0" cellspacing="2" cellpadding="5" class="color3 fleft" style="margin:10px;">
//	            <tbody>
//             <tr><td colspan="10" class="color3"><div><b>'.$service_row['group_name'].'</b></div></td></tr>';
	        
	        $group_id = $service_row['group_id'];
	        $row_flag = 1;
	   }
	   echo '<tr class="'.(($row_flag++)%2==1 ? '' : 'altrow').'">
              <td width="300">'.$service_row['service_name'].'</td>
              <td>R '.formatMoney($service_row['amount'],true).'</td>
          </tr>';
}
if($group_id!=0) echo '</table>';
echo '<div class="clear"></div>';

include('footer.php');
?>