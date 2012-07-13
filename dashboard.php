<?php 
$summary_btn = 'selected';
include("config.php"); 
include("header.php"); 
//	print_r($_SESSION); 

$list_count = 13;
$user_sql = "SELECT count(*) as userCount, MONTH(joinDate) as joinMonth, MONTHNAME(joinDate) as joinMonthName, " . 
			" YEAR(joinDate) as joinYear FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId " .
			" AND gma_logins.companyId='" . $ses_companyId. "' GROUP BY MONTH(joinDate), YEAR(joinDate) " . 
			" ORDER BY YEAR(joinDate) DESC, MONTH(joinDate) DESC LIMIT 0,$list_count";
$user_rs     = mysql_query($user_sql);
$user_count  = 0;
$user_month_count = mysql_num_rows($user_rs);

$clientOnly = "";
if( strtolower(trim($_SESSION['ses_userType']))=='client')
	$clientOnly = " AND gma_order.userId = " . $_SESSION['ses_userId'];


$order_sql = " SELECT MONTH(orderDate) as joinMonth, MONTHNAME(orderDate) as joinMonthName, YEAR(orderDate) as joinYear " .
			" FROM gma_order,gma_logins WHERE gma_order.userId=gma_logins.userId " . $clientOnly . " AND gma_logins.companyId='" . $ses_companyId ."'" .
			" GROUP BY MONTH(orderDate), YEAR(orderDate) ORDER BY YEAR(orderDate) DESC, MONTH(orderDate) DESC LIMIT 0,$list_count";

			
$order_sql =" SELECT MONTH(orderDate) as joinMonth, MONTHNAME(orderDate) as joinMonthName, YEAR(orderDate) as joinYear, " .
			" SUM(IF(serviceName LIKE '%credits%', amount,0)) AS credit_amount, " .
			" SUM(IF(serviceName NOT LIKE '%credits%', amount,0)) AS other_amount " .
			" FROM gma_order, gma_order_details, gma_logins " .
			" WHERE gma_order.userId=gma_logins.userId AND gma_order.id = gma_order_details.orderId " . $clientOnly .
			" AND gma_logins.companyId='" . $ses_companyId ."'" .
			" GROUP BY MONTH(orderDate), YEAR(orderDate) ORDER BY YEAR(orderDate) DESC, MONTH(orderDate) DESC";   


$order_rs     = mysql_query($order_sql);
$order_month_count = mysql_num_rows($order_rs);

$page_title = 'Summary';
include('sub_header.php');
?>

<div class="client_display">
<?php
	if( strtolower(trim($_SESSION['ses_userType']))=='client'){
	//	NOTHING
	}
	else{
?>
    <div class="dashboard">
        <h2>Clients</h2>
        <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">
            <tr>
                <td class="color3" align="left"><b>Month</b></td>
                <td width="15%" class="color3" align="center"><b>Count</b></td>
            </tr>
            <?
            if($user_month_count>0) { 
                $i = 0;
                while ($user_row = mysql_fetch_assoc($user_rs)) {
                    $userCount     = $user_row['userCount'];
                    $joinYear      = $user_row['joinYear'];
                    $joinMonth     = $user_row['joinMonth'];
                    $joinMonthName = $user_row['joinMonthName'];
                    
                    $user_count += $userCount;
                    $textName    = ($joinYear==date('Y') && $joinMonth==date('M')) ? 'This month' : substr($joinMonthName,0,3).' '.$joinYear;
                    ?>
                    <tr>
                        <td bgcolor="white" class="color4" align="left"><?=$textName?></td>
                        <td bgcolor="white" class="color4" align="center"><?=$userCount?></td>
                    </tr>
                <? } 
            } else { ?>
                <tr><td colspan="4" bgcolor="white" class="color4 message">No Clients</td></tr>
            <? } ?>
            <tr>
                <td class="color1" align="right"><strong>Average</strong></td>
                <td class="color1" align="center"><?=($user_count/$user_month_count)?></td>
            </tr>
        </table>
    </div>
<?php } //END OF if( strtolower(trim($_SESSION['ses_userType']))=='client') ?>
    
    <div class="dashboard">
        <h2>Invoice</h2>
        <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">
            <tr>
                <td class="color3" align="left"><b>Month</b></td>
                <td width="15%" class="color3" align="center"><b>Credits</b></td>
                <td width="15%" class="color3" align="center"><b>Other</b></td>
                <td width="15%" class="color3" align="center"><b>Total</b></td>
            </tr>
            <?
            $i = $creditTotal = $otherTotal = $Total = 0;
            if($order_month_count>0) { 
                while ($order_row = mysql_fetch_assoc($order_rs)) {
                    $joinYear      = $order_row['joinYear'];
                    $joinMonth     = $order_row['joinMonth'];
                    $joinMonthName = $order_row['joinMonthName'];
                    
                    $credits          = $order_row['credit_amount'];
                    $others           = $order_row['other_amount'];
                    
                    $credits = ($credits>0) ? $credits : 0;
                    $others  = ($others>0) ? $others : 0;
                    
                    $totals  = $credits + $others;
                    
                    $creditTotal += $credits;
                    $otherTotal  += $others;
                    $Total       += $totals;
                    
                    $textName    = ($joinYear==date('Y') && $joinMonth==date('M')) ? 'This month' : substr($joinMonthName,0,3).' '.$joinYear;
                    ?>
                    <tr>
                        <td bgcolor="white" class="color4" align="left"><?=$textName?></td>
                        <td bgcolor="white" class="color4" align="center">R <?=formatMoney($credits)?></td>
                        <td bgcolor="white" class="color4" align="center">R <?=formatMoney($others)?></td>
                        <td bgcolor="white" class="color4" align="center">R <?=formatMoney($totals)?></td>
                    </tr>
                <? }
            } else { ?>
                <tr><td bgcolor="white" class="color4 message" colspan="4">No Invoices</td></tr>
            <? } ?>
            <tr>
                <td class="color1" align="right"><strong>Average</strong></td>
                <td class="color1" align="center">R <?=formatMoney($creditTotal/$order_month_count)?></td>
                <td class="color1" align="center">R <?=formatMoney($otherTotal/$order_month_count)?></td>
                <td class="color1" align="center">R <?=formatMoney($Total/$order_month_count)?></td>
            </tr>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>