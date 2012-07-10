<?php
$action      = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$account_btn = 'selected';
include("config.php");
if($action=='save' || $action=='view' || $action=='email')
    include("functions.php");
else
    include("header.php");  

$action      = (isset($_REQUEST['action']) && $_REQUEST['action']!='') ? $_REQUEST['action'] : 'list';
$userId      = (isset($_REQUEST['userId']) && $_REQUEST['userId']>0 && ($ses_loginType!='user')) ? $_REQUEST['userId'] : $ses_userId;
$date_range  = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : '3months';
$startdate   = (isset($_REQUEST['startdate'])) ? $_REQUEST['startdate'] : '';
$enddate     = (isset($_REQUEST['enddate'])) ? $_REQUEST['enddate'] : '';

if($date_range!='thismonth' && $date_range!='lastmonth' && $date_range!='all' && $date_range!='3months' && $date_range!='daterange')
    $date_range = 'thismonth';
else if($date_range=='daterange' && ($startdate=='' || $enddate==''))
{
    if($startdate=='' && $enddate=='')
        $date_range = 'thismonth';
    else if($enddate=='')
        $enddate = date('Y-m-d');
}

if($action=='save' || $action=='view' || $action=='email')
{
    $date_range = 'all';
    $payment_details = myAccount($userId, $date_range, $startdate, $enddate, 1);
    $details     = $payment_details['result'];
    $balance_due = $payment_details['balance_due'];
    $title       = $payment_details['title'];
    $title       = "GNetMail Statement ".date('d/m/Y');
    
    $user_sql = "SELECT * FROM gma_logins,gma_user_details WHERE gma_logins.userId=gma_user_details.userId AND gma_logins.userId='$userId'";
    $user_rs  = mysql_query($user_sql);
    $user_row = mysql_fetch_assoc($user_rs);
    
    $array_values['userId']     = $user_row['userId'];    
    $array_values['firstname']  = $user_row['firstName'];    
    $array_values['lastname']   = $user_row['lastName'];    
    $array_values['clientname'] = $user_row['businessName'];    
    $array_values['phone']      = $user_row['phone'];    
    $array_values['address']    = $user_row['address'];    
    $array_values['vatno']      = $user_row['vatNo'];   
    $array_values['grade']      = $user_row['grade'];        
    $array_values['email']      = $user_row['email'];        
    $array_values['to_email']   = $user_row['email'];  
    
    $array_values['statement']      = $payment_details['result'];
    $array_values['statement_date'] = date('d/m/Y');
    $array_values['amount_due']     = 'R '.formatMoney($balance_due, true);
    
    $result = emailSend('statement', $array_values, null, 1);
        
    $result = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title>GNet Mail</title>
    <link href='css/style.css' rel='stylesheet' type='text/css' />
    </head>
    <body style='padding:5px;'>$result</body>
    </html>";
    
    $save_file = false;
    if($action=='save' || $action=='email')
        $save_file = true;
    $outfile   = 'test.pdf';
    if ( $save_file ) 
    {
        require_once("dompdf/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($result);
        $dompdf->set_paper('letter', 'portrait');
        $dompdf->render();
        
        if ( strtolower(DOMPDF_PDF_BACKEND) === "gd" )
            $outfile = str_replace(".pdf", ".png", $outfile);
        
        list($proto, $host, $path, $file) = explode_url($outfile);
        if ( $proto != "" )
            $outfile = $file; 
        
        $outfile = realpath(dirname($outfile)) . DIRECTORY_SEPARATOR . basename($outfile);
        
        file_put_contents($outfile, $dompdf->output( array("compress" => 0) ));
        
        unset($dompdf);
    }
    
    $invoicefile = "GNetMail Statement ".date('d/m/Y').".pdf";
    if($action=='save') {
        // We'll be outputting a PDF
        header('Content-type: application/pdf');
        // It will be called downloaded.pdf
        //header('Content-Disposition: attachment; filename="'.$invoicefile.'"');
        header('Content-Disposition: inline; filename="'.$invoicefile.'"');
        // The PDF source is in original.pdf
        readfile($outfile);
        exit;
    }
    else if($action=='email') {    
        $array_values['outfile']    = $outfile; 
        $array_values['filename']   = $invoicefile; 
        //echo '<pre>'; print_r($array_values);
        
        emailSend('statement_email', $array_values);
    }
    else if($action=='view') {
        echo $result;
    }
    exit;
}
    
$payment_details = myAccount($userId, $date_range, $startdate, $enddate);
$details = $payment_details['result'];
$title   = $payment_details['title'];

$user_sql  = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId AND gma_user_details.userId='$userId' GROUP BY userName ORDER BY businessName ASC"; 
$user_rs   = mysql_query($user_sql);
$user_row  = mysql_fetch_assoc($user_rs);
$userName     = $user_row['firstName'].' '.$user_row['lastName'];
$userName     = $user_row['userName'];
$businessName = $user_row['businessName'];

$user_sql  = "SELECT * FROM gma_user_details,gma_logins WHERE gma_user_details.userId=gma_logins.userId GROUP BY userName ORDER BY businessName ASC"; 
$user_rs   = mysql_query($user_sql);
?>

<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<form name="frm" id="frm" method="get" action="">
<div class="sub_head">
    
    <div style="float:right;padding-top:2px;padding-left:20px;">
        <span class="head_links" style="padding-right:20px;"><a href="javascript:void(0);" onclick="mailStatement('myaccount.php?userId=<?=$userId?>&date=<?=$date_range?>&startdate=<?=$startdate?>&enddate=<?=$enddate?>&action=email');" title="Email Statement">Email Statement</a></span>
        <div class="head_links" style="padding-left:5px;padding-right:5px;">|</div>
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=<?=$date_range?>&startdate=<?=$startdate?>&enddate=<?=$enddate?>&action=save" title="Save Statement" target="_blank">Save Statement</a></span>
        <div class="head_links" style="padding-left:5px;padding-right:5px;">|</div>
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=<?=$date_range?>&startdate=<?=$startdate?>&enddate=<?=$enddate?>&action=view" title="View Statement" target="_blank" class='thickbox'>View Statement</a></span>
    </div>    
    <span id="sub_head_left" style="float:right;"><div class="sub_sub_head">Statement : </div></span>
    
    <span id="sub_head_left" style="float:left;"><div class="sub_sub_head">Account Activity</div></span>
    <div style="float:left;padding-top:2px;padding-left:20px;">
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=all" title="All">All</a></span>
        <div class="head_links" style="padding-left:5px;padding-right:5px;">|</div>
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=3months" title="Last 3 months">Last 3 months</a></span>
        <div class="head_links" style="padding-left:5px;padding-right:5px;">|</div>
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=lastmonth" title="Last month">Last month</a></span>
        <div class="head_links" style="padding-left:5px;padding-right:5px;">|</div>
        <span class="head_links"><a href="myaccount.php?userId=<?=$userId?>&date=thismonth" title="This month">This month</a></span>
    </div>
    
</div>

<div class="pagination" align="right">
    <table border="0" width="100%" cellpadding="5">
    <tr valign="middle">
       <? if($ses_loginType!='user') { ?>
        <td align="left" width="400">
            <b>Select Client : </b>
            <select name="userId" id="userId" class="inputbox_green" style="width:300px;">
                <option value="">Select All</option>
                <?php
                while($user_row = mysql_fetch_array($user_rs))
                {
                    $user_Id  = $user_row['userId'];
                    $name     = $user_row['businessName'].' - '.$user_row['userName'];
                    $selected = ($userId==$user_Id && $userId!=$ses_userId) ? 'selected' : '';
                    
                    echo "<option value='$user_Id' $selected>$name</option>";
                }
                ?>
            </select>
        </td>
        <? } else { ?> <input type="hidden" name="userId" id="userId" value="<?=$userId?>"> <?} ?>
        <td width="280">
        <b>Date Range : </b>
        <input type="hidden" name="date" id="date" value="daterange">
        <input type="text" name="startdate" id="startdate" value="<?=($startdate)?>" style="width:80px" readonly>&nbsp;-&nbsp;
        <input type="text" name="enddate" id="enddate" value="<?=($enddate)?>" style="width:80px" readonly>
        </td>
        <td align="left" width="50"><input type="submit" value="Search" class="search_bt" /></td>
        <td align="right"><?=$pagination?>&nbsp;</td>
    </tr>
    </table>
</div>
<br>
<div class="sub_subhead" ><?=$title?></div>
<div class="sub_subhead_new" ><?=($businessName!='' ? $businessName.' - '.$userName : '')?></div>
<?php
$queryString = explode("&orderby=",$actualQueryString); 
$queryString = $queryString[0];
?>

<div class="client_display clear"><?=$details?></div>
</form>

<script>
$(document).ready(function() {
    $('#startdate').datePicker({startDate: start_date, dateFormat: date_format});
    $('#enddate').datePicker({startDate: start_date, dateFormat: date_format});
});
function mailStatement(url)
{
    $.post(url, function(data) {
        alert('Email successfully send')
    });
}
</script>

<?php include("footer.php");  ?>