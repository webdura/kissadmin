<?php 
$summary_btn = 'selected';
include("header.php"); 
include("config.php"); 

$newUserQuery = "SELECT count(`userId`) as userCount ,monthname(`joinDate`) as joinMonth,year(`joinDate`) as joinYear FROM `gma_user_details` group by month(`joinDate`),year(`joinDate`) order by year(`joinDate`) desc, month(`joinDate`) desc LIMIT 0, 13";
$newUserResult = mysql_query($newUserQuery);

$creditsQuery = "SELECT sum(`cost`) as amount, monthname(`orderDate`) as orderMonthName, month(`orderDate`) as orderMonth,  year(`orderDate`) as orderYear FROM `gma_order_details` where `serviceName` like '%credits%' group by month(`orderDate`),year(`orderDate`) order by year(`orderDate`) desc, month(`orderDate`) desc  LIMIT 0, 13"; 
$creditsQuery = "SELECT sum(`cost`) AS amount, monthname(`orderDate`) AS orderMonthName, month(`orderDate`) AS orderMonth,  year(`orderDate`) AS orderYear FROM `gma_order_details`, `gma_order` WHERE id=orderId AND `serviceName` like '%credits%' group by month(`orderDate`),year(`orderDate`) order by year(`orderDate`) desc, month(`orderDate`) desc  LIMIT 0, 13"; 
//echo $creditsQuery;exit;
$creditsResult = mysql_query($creditsQuery);

//$othersQuery = "SELECT sum(`cost`) as amount, monthname(`orderDate`) as orderMonthName, month(`orderDate`) as orderMonth,  year(`orderDate`) as orderYear FROM `gma_order_details` where `serviceName` not like '%credits%' group by month(`orderDate`),year(`orderDate`) order by year(`orderDate`) desc, month(`orderDate`) desc  LIMIT 0, 13"; 
$othersQuery = "SELECT sum(`cost`) AS amount, monthname(`orderDate`) AS orderMonthName, month(`orderDate`) AS orderMonth,  year(`orderDate`) AS orderYear FROM `gma_order_details`, `gma_order` WHERE id=orderId AND `serviceName` not like '%credits%' group by month(`orderDate`),year(`orderDate`) order by year(`orderDate`) desc, month(`orderDate`) desc  LIMIT 0, 13"; 
$othersResult = mysql_query($othersQuery);
//echo "$creditsQuery == $othersQuery";
$otherRes = array();
while($otherResAry = mysql_fetch_array($othersResult)) {
    $orderMonth = $otherResAry['orderMonth'];
    $orderYear = $otherResAry['orderYear'];
    $otherRes[$orderYear][$orderMonth] = $otherResAry['amount'];
}
$sendSizeQuery = "SELECT sum(`sendsize`) as sendsize, monthname(FROM_UNIXTIME(`starttime`)) as startMonth, year(FROM_UNIXTIME(`starttime`)) as startYear FROM `gm_stats_newsletters` GROUP BY month(FROM_UNIXTIME(`starttime`)), year(FROM_UNIXTIME(`starttime`)) ORDER BY year(FROM_UNIXTIME(`starttime`)) DESC, month(FROM_UNIXTIME(`starttime`)) DESC LIMIT 0, 13"; 
$sendSizeResult = mysql_query($sendSizeQuery);

$page_title = 'Summary';
include('sub_header.php');
?>

<div class="summary">
    <div class="sum_title">
        <span class="sum_lg_title" >New Clients for:</span>
        <span class="sum_mg_title" >Total invoiced for:</span>
        <span class="sum_rg_title" >Total Send:</span><br/>
    </div>
    
    <div class="sum_left">
        <table width="100%" style="margin-top:18px;">
        <tr><td>
            <table width="80%" style="margin:0 auto;" cellpadding="5" cellspacing="0">
                <?php
                $i = 0;
                $val = 0;
                while($newUserResAry = mysql_fetch_array($newUserResult)) {
                    $text = ($i == 0) ? 'This month' : substr($newUserResAry['joinMonth'],0,3).' '.$newUserResAry['joinYear'];
                    $class     = ($i % 2) ? "row1" : "row2";
                    $val       = $newUserResAry['userCount']+$val;
                    $userCount = ($newUserResAry['userCount']=='')?0:$newUserResAry['userCount'];
                    echo '<tr>
                        <td class="'.$class.'"><strong>'.$text.':</strong></td>
                        <td class="'.$class.'" style="padding-left:15px;">'.$userCount.'</td>
                    </tr>';
                    $i++;
                }						
                ?>					
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td width="45%"><strong>Average:</strong></td>
                    <td style="padding-left:15px;"><?php printf ("%01.2f", $val/13);?></td>						
                </tr>
                <tr><td>&nbsp;</td></tr>
            </table>
        </td></tr>
        </table>
    </div>

    <div class="sum_mid">
        <table width="88%" style="margin:0 auto;" cellpadding="5" cellspacing="0">
            <tr>
                <td></td>
                <td class="green_text" style="padding-left:15px;">Credits</td>
                <td class="green_text">Other</td>
                <td class="green_text">Total</td>
            </tr>
            <?php
            $i = 0;
            $creditsTotal = $OverAlltotal = $othersTotal = 0;			 
            while($creditsResAry = mysql_fetch_array($creditsResult)) {
                $class = ($i % 2) ? "row1" : "row2";
                
                $orderMonth   = $creditsResAry['orderMonth'];
                $orderYear    = $creditsResAry['orderYear'];
                $text         =  ($i == 0) ? 'This month' : substr($creditsResAry['orderMonthName'],0,3).' '.$orderYear;
                $creditsTotal = $creditsResAry['amount'] + $creditsTotal;
                $othersTotal  = $otherRes[$orderYear][$orderMonth] + $othersTotal;
                $total        = $creditsResAry['amount'] + $otherRes[$orderYear][$orderMonth];
                $OverAlltotal = $total + $OverAlltotal;
                $creditTotal  = ($creditsResAry['amount']=='') ? 0 : $creditsResAry['amount'];
                $otherTotal   = ($otherRes[$orderYear][$orderMonth]=='') ? 0 : $otherRes[$orderYear][$orderMonth];
                $totalCount   = ($total=='') ? 0 : $total;
                
                echo '<tr>
                    <td class="'.$class.'"><strong>'.$text.':</strong></td>
                    <td class="'.$class.'" style="padding-left:15px;">R '.str_replace(".00","",formatMoney($creditTotal,true)).'</td>									
                    <td class="'.$class.'">R '.str_replace(".00","",formatMoney($otherTotal,true)).'</td>
                    <td class="'.$class.'">R '.str_replace(".00","",formatMoney($totalCount,true)).'</td>
                </tr>';
                $i++;
            }						
            ?>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <td width="28%"><strong>Average:</td>           
                <td class="even" style="padding-left:15px;">R <?php $creditsTotal1 = $creditsTotal/13; echo formatMoney($creditsTotal1,true); ?></td>
                <td>R <?php $othersTotal1 = $othersTotal/13; echo formatMoney($othersTotal1,true); ?></td>
                <td>R <?php $OverAlltotal1 = $OverAlltotal/13; echo formatMoney($OverAlltotal1,true); ?></td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
        </table>
    </div>

    <div class="sum_right">
        <table width="100%" style="margin-top:18px;">
        <tr><td>
            <table width="80%" style="margin:0 auto;" cellpadding="5" cellspacing="0">
                <?php
                $i = $val = 0;
                while($sendSizeResAry = mysql_fetch_array($sendSizeResult)) {
                    $text     = ($i == 0) ? 'This month' : substr($sendSizeResAry['startMonth'],0,3).' '.$sendSizeResAry['startYear'];
                    $class    = ($i % 2) ? "row1" : "row2";
                    $val      = $sendSizeResAry['sendsize']+$val;
                    $sendsize = ($sendSizeResAry['sendsize']=='')?0:$sendSizeResAry['sendsize'];
                    echo '<tr>
                        <td class="'.$class.'"><strong>'.$text.':</strong></td>
                        <td class="'.$class.'" style="padding-left:15px;">'.str_replace(".00","",formatMoney($sendsize,true)).'</td>	
                    </tr>';
                    $i++;
                }						
                ?>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td width="45%"><strong>Average:</strong></td>
                    <td style="padding-left:15px;"><?php $val1 = $val/13; echo str_replace(".00","",formatMoney($val1,true));?></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            </table>
        </td></tr>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>