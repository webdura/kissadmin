<?
include_once('config.php');
$ses_userId = $_SESSION['ses_userId'];
$login_sql  = "SELECT * FROM gma_logins WHERE userId='$ses_userId'";
$login_rs   = mysql_query($login_sql);
if(mysql_num_rows($login_rs)>0)
{
    $login_row = mysql_fetch_assoc($login_rs);
    
    $ses_userName               = $login_row['userName'];
    $_SESSION['ses_userId']     = $ses_userId     = $login_row['userId'];
    $_SESSION['ses_companyId']  = $ses_companyId  = $login_row['companyId'];
    $_SESSION['ses_userType']   = $ses_userType   = $login_row['userType'];
    $_SESSION['ses_loginType']  = $ses_loginType  = ($ses_userType=='normal' || $ses_userType=='trial' || $ses_userType=='client') ? 'user' : 'admin';
    
    $company_sql = "SELECT * FROM gma_company,gma_logins WHERE ownerId=userId AND gma_company.companyId='$ses_companyId'";
    $company_rs  = mysql_query($company_sql);
    $company_row = mysql_fetch_assoc($company_rs);
    $admin_email = $company_row['email'];
}
else if(!isset($_GET['default']))
{
    return header("Location: index.php?logoff");
    exit;
}
else if(isset($_GET['default']))
    $ses_companyId = 0;

$theme_flag = 1;
$theme_sql = "SELECT gma_company_theme.*,theme,color1,color2,color3,color4 FROM gma_company_theme,gma_theme WHERE id=theme_id AND companyId='$ses_companyId'";
$theme_rs  = mysql_query($theme_sql);
if(mysql_num_rows($theme_rs)==0)
{
    $theme_flag = 0;
    $theme_sql = "SELECT * FROM gma_theme WHERE `default`='1'";
    $theme_rs  = mysql_query($theme_sql);
}
$theme_row = mysql_fetch_assoc($theme_rs);

$theme_theme_id   = $active_theme['theme_id']       = (isset($theme_row['theme_id'])) ? $theme_row['theme_id'] : $theme_row['id'];
$theme_theme      = $active_theme['theme']          = $theme_row['theme'];
$site_logo        = $active_theme['site_logo']      = (isset($theme_row['site_logo']) && $theme_row['site_logo']!='') ? 'images/company/'.$theme_row['site_logo'] : 'images/logo.png';
$invoice_logo     = $active_theme['invoice_logo']   = (isset($theme_row['invoice_logo']) && $theme_row['invoice_logo']!='') ? 'images/company/'.$theme_row['invoice_logo'] : '';
$invoice_status   = $active_theme['invoice_status'] = (isset($theme_row['invoice_status'])) ? $theme_row['invoice_status'] : 0;
$theme_head_bg    = $active_theme['head_bg']        = $theme_row['head_bg'];
$theme_head_color = $active_theme['head_color']     = $theme_row['head_color'];
$theme_color1     = $active_theme['color1']         = $theme_row['color1'];
$theme_color2     = $active_theme['color2']         = $theme_row['color2'];
$theme_color3     = $active_theme['color3']         = $theme_row['color3'];
$theme_color4     = $active_theme['color4']         = $theme_row['color4'];

$invoice_logo_mail = ($invoice_status==1 || $invoice_logo=='') ? $site_logo : $invoice_logo;

function userTypes($user_type='', $flag=0, $array=0)
{
    $userTypes = array();
    if($flag==0)
        $userTypes = array('normal'=>'normal', 'trial'=>'trial', 'client'=>'client');
    else 
    {
        $userTypes = array();
        $admins_sql = "SELECT * FROM gma_admins WHERE id>2 AND user='A'";
        $admins_rs  = mysql_query($admins_sql);
        while ($admins_row = mysql_fetch_assoc($admins_rs))
        {
            $userTypes[$admins_row['type']] = ($array==1) ? $admins_row['type'] : $admins_row['name'];
        }
    }
    if($array==1)
        return $userTypes;
        
    $result    = '<select name="userType" id="userType" class="textbox required" style="width:260px">';
    foreach ($userTypes as $key=>$userType)
    {
        $selected  = ($key==$user_type ? "selected" : '');
        $result   .= "<option value='$key' $selected>$userType</option>";
    }
    $result   .= "</select>";
    
    return $result;
}

function emailSend($email_template, $array_values, $companyId=null, $flag=0) {
    global $ses_companyId, $SITE_URL, $company_row, $invoice_logo_mail;
    
    $companyId = (is_null($companyId) ? $ses_companyId : $companyId);
    
    $company_sql = "SELECT * FROM gma_company, gma_logins, gma_admin_details WHERE gma_admin_details.userId=gma_logins.userId AND gma_company.companyId=gma_logins.companyId AND gma_company.ownerId=gma_logins.userId AND gma_company.companyId=".GetSQLValueString($companyId, 'text');
    $company_rs  = mysql_query($company_sql);
    $company_row = mysql_fetch_assoc($company_rs);
    $email_from  = $company_row['email'];
    
    $email_sql = "SELECT * FROM gma_emails WHERE companyId=".GetSQLValueString($companyId, 'text')." AND template=".GetSQLValueString($email_template, 'text');
    $email_rs  = mysql_query($email_sql);
    $email_row = mysql_fetch_assoc($email_rs);
    $email_subject = $email_row['subject'];
    $email_content = $email_row['content'];
    $email_status  = $email_row['status'];
    // if($email_row['update']==0) {
    if(!strstr($email_content, '<table')) {
        //$email_content = nl2br($email_content);
    }
    
    if(!isset($array_values['companyname'])) {
        $array_values['invoice_logo']          = $company_row['companyName'];
        $array_values['company_account_email'] = $company_row['companyAccountEmail'];
        $array_values['companyname']           = $company_row['companyName'];
        
        $array_values['companyBankName']    = $company_row['companyBankName'];
        $array_values['companyBranchName']  = $company_row['companyBranchName'];
        $array_values['companyBranchNo']    = $company_row['companyBranchNo'];
        $array_values['companyAccountName'] = $company_row['companyAccountName'];
        $array_values['companyAccountType'] = $company_row['companyAccountType'];
        $array_values['companyAccountNo']   = $company_row['companyAccountNo'];
    }
    
    $array_values['link_org']    = "{$SITE_URL}index.php";
    $array_values['link']        = "<a href='".$array_values['link_org']."'>Click here</a>";
    
    $array_values['logo'] = '';
    if($invoice_logo_mail!='' && file_exists("images/company/$invoice_logo_mail"))
        $array_values['logo'] = "<img src='{$SITE_URL}images/company/$invoice_logo_mail'>";
    
    foreach ($array_values as $key => $value) {
        $email_subject = str_replace("[$key]", $value, $email_subject);
        $email_content = str_replace("[$key]", $value, $email_content);
    }
    $email_to = $array_values['to_email'];
    
    if($flag==1) { 
        return $email_content;
        
    } else if($email_status==1 && $flag==0) {     
     
        global $theme_head_bg, $theme_head_color, $theme_color1, $theme_color2, $theme_color3, $theme_color4;
        
        $email_content = str_replace('class="color1"', 'style="background-color: '.$theme_color1.'"', $email_content);
        $email_content = str_replace('class="color2"', 'style="border-top:4px solid '.$theme_color2.'"', $email_content);
        $email_content = str_replace('class="color3"', 'style="background-color: '.$theme_color3.'"', $email_content);
        $email_content = str_replace('class="color4"', 'style="background-color: '.$theme_color4.'"', $email_content);
        
        $email_content = str_replace("class='color1'", 'style="background-color: '.$theme_color1.'"', $email_content);
        $email_content = str_replace("class='color2'", 'style="border-top:4px solid '.$theme_color2.'"', $email_content);
        $email_content = str_replace("class='color3'", 'style="background-color: '.$theme_color3.'"', $email_content);
        $email_content = str_replace("class='color4'", 'style="background-color: '.$theme_color4.'"', $email_content);
        
        include("includes/htmlMimeMail.inc.php");
        $mailToSend  = new htmlMimeMail();
        
        $mailToSend->setSubject($email_subject);
        $mailToSend->setFrom($email_from);
        $mailToSend->setBcc('seacrows@gmail.com');
        $mailToSend->setHtml($email_content);
        
        if(isset($array_values['outfile']) && isset($array_values['filename'])) {
            $attachment = $mailToSend->getFile($array_values['outfile']);
            $mailToSend->addAttachment($attachment, $array_values['filename']);
        }
        
        $email_to = explode(',', $email_to);
        //$mailToSend->send($email_to);
                
        $today  = date('d-m-Y H:i:s');
        $myFile = "mail_log.txt";
        $fp = fopen($myFile, 'a') or die("can't open file");
        $stringData = implode(',', $email_to).", ".$email_from.", ".$today.", ".$email_subject."\n$email_content\n\n";
        fwrite($fp, $stringData);
        fclose($fp);
        
        return true;
    }
}

function formatMoney($number, $fractional=false) { 
    if ($fractional) { 
        $number = sprintf('%.2f', $number); 
    } 
    while (true) { 
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
        if ($replaced != $number) { 
            $number = $replaced; 
        } else { 
            break; 
        } 
    } 
    return $number; 
} 

function paginations($totalrecord,$pagerecord,$rows,$page='page')
{
    $linkpage    = $_SERVER['PHP_SELF'];
    $QueryString = $_SERVER["QUERY_STRING"];
    $queryString = explode("&page=",$QueryString);
    $queryString = $queryString[0];
    $linkpage    = "$linkpage?$queryString";
    
    $currentpage	=	1;
    if(isset($_GET[$page]))
        $currentpage	=	$_GET[$page];
    
    $totalpages =	ceil($totalrecord/$pagerecord);
    $filename		 =	(!strstr($linkpage, '?')) ? $linkpage.'?' : $linkpage;
    foreach ($_GET AS $key=>$value)
    {
        if($key<>$page)
        {
            $filename	.=	"&$key=$value";
        }
    }
    
    $startpage	=	(ceil($currentpage/$rows)-1) * $rows + 1;
    if($startpage<0)
        $startpage	=	1;
    if($startpage>$totalpages)
        $startpage	=	(ceil($totalpages/$rows)-1) * $rows + 1;
    
    $endpage	=	$startpage	+	$rows	-	1;
    
    if($endpage>$totalpages)	
        $endpage	=	$totalpages;
    
    for($i=$startpage,$links='';$i<=$endpage;$i++)
    {	
        if ($i==$currentpage)
            $links	.= 	"<span class='current btn_style'>$i</span> ";
        else
            $links 	.= 	"<a href='$filename&$page=$i' class='btn_style'>$i</a> ";
    }
    
    //	Previous Record
    if ($currentpage>1)
    {
        $i  		= 	$currentpage - 1;
        $prev  	= 	"<a href='$filename&$page=$i' class='btn_style'>&#171; Previous</a>";
    }
    else
        $prev  	= 	"<span class='inactive btn_style'>&#171; Previous</span>";
    
    //	Previous Page
    if($startpage>1)
    {
        $i 			= 	$startpage	-	1;
        $previous	=  "<a href='$filename&$page=$i' class='btn_style'>&#171; Previous Page</a>";	
    }
    else
        $previous = " <span class='inactive btn_style'>&#171; Previous Page</span> ";
    
    if ($currentpage<$totalpages)
    {
        $i 		= 	$currentpage + 1;
        $next 	= 	"<a href='$filename&$page=$i' class='btn_style'>Next &#187;</a> ";
    }
    else
        $next 	= 	"<span class='inactive btn_style'>Next &#187;</span> ";		
    
    if($endpage<$totalpages)
    {
        $i 		=	$endpage	+	1;
        $more 	=  	"<a href='$filename&$page=$i' class='btn_style'>Next Page&#187;  &nbsp;</a>";
    }
    else
        $more = " <span class='inactive btn_style'>Next Page &#187;</span> "; // we're on the last page, don't print next link
    
//    $more1	=	'<font color="#ffffff"></font>';	
//    $result	=	"<table align='center' border='0' width='100%' class='nostyle'><tr><td class='pages bodytag' >$previous $prev  $links  $next $more $more1</td></tr></table>";
//    $result	=	"<table align='right' border='0' class='nostyle'><tr><td class='pages bodytag' ><span class='text'>Page: (Page $currentpage of $totalpages )</span> $previous $prev  $links  $next $more $more1</td></tr></table>";
    $result	=	"$previous $prev  $links  $next $more $more1";
    $result	=	"$prev  $links  $next";
    
//    if($totalpages<=1)
//        $result	=	'';
    
    return $result;
}

function pagination($maxPage, $pageNum=1)
{
    if($maxPage<=1)
        return '';
        
    // print the link to access each page
    $self = $_SERVER['PHP_SELF'];
    $nav  = '';
    
    $actualQueryString = $_SERVER["QUERY_STRING"];
    $queryString = explode("&page=",$actualQueryString);
    $queryString = $queryString[0];
    for($page = 1; $page <= $maxPage; $page++) {
        if ($page == $pageNum)   {
            $nav .= " $page | "; // no need to create a link to current page
        } else {
            $nav .= " <a href=\"$self?".$queryString."&page=$page\" style='text-decoration:underline;'>$page</a> |  ";
        }
    }
    
    if ($pageNum > 1) {
        $page  = $pageNum - 1;
        $prev  = " | <a href=\"$self?".$queryString."&page=$page\" style='text-decoration:underline;'>Back |</a> ";
        
        $first = " <a href=\"$self?".$queryString."&page=1\" style='text-decoration:underline;'><< </a> ";
    } else {
        $prev  = '|'; // we're on page one, don't print previous link
        $first = ''; // nor the first page link
    }
    
    if ($pageNum < $maxPage) {
        $page = $pageNum + 1;
        $next = " <a href=\"$self?".$queryString."&page=$page\" style='text-decoration:underline;'> Next</a> |";
        
        $last = " <a href=\"$self?".$queryString."&page=$maxPage\" style='text-decoration:underline;'> >></a> ";
    } else {
        $next = ''; // we're on the last page, don't print next link
        $last = ''; // nor the last page link
    }
    
    // print the navigation link
    return 'Page: (Page '.$pageNum.' of '.$maxPage.') &nbsp; '.$first. $prev . $nav . $next . $last;
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
    $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
    
    switch ($theType) 
    {
        case "text":
                    $theValue = ($theValue != "") ? "'" . $theValue . "'" : "''";
                    break;    
        case "textnew":
                    $theValue = ($theValue != "") ? $theValue : "";
                    break;    
        case "long":
        case "int":
                    $theValue = ($theValue != "") ? intval($theValue) : "''";
                    break;
        case "double":
                    $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "''";
                    break;
        case "date":
                    $theValue = ($theValue != "") ? "'" . $theValue . "'" : "''";
                    break;
        case "defined":
                    $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                    break;
    }
    
    return $theValue;
}

function myAccount($userId, $date_range, $startdate, $enddate, $date_flag=0)
{
    $details     = array();
    $balance_due = $flag = 0;
    $date1 = $date2 = $result = '';
    
    if($date_range=='thismonth')
    {
        $date  = date('Ym');
        $year  = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $date1 = ("$year-$month-01");
        $date2 = ("$year-$month-31");
        
        $flag  = 1;
        $date3 = date('d/m/Y', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $title = "Account Statement - This Month";
    }
    else if($date_range=='lastmonth')
    {
        $date  = date('Ym', mktime(0, 0, 0, date('m')-1, 1, date('Y')));
        $year  = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $date1 = ("$year-$month-01");
        $date2 = ("$year-$month-31");
        
        $flag  = 1;
        $date3 = date('d/m/Y', mktime(0, 0, 0, date('m')-1, 1, date('Y')));
        $title = "Account Statement - Last Month";
    }
    else if($date_range=='3months')
    {
        $date  = date('Ym', mktime(0, 0, 0, date('m')-3, 1, date('Y')));
        $year  = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $date1 = ("$year-$month-01");
        $date2 = date('Y')."-".date('m')."-31";
        
        $flag  = 1;
        $date3 = date('d/m/Y', mktime(0, 0, 0, date('m')-3, 1, date('Y')));
        $title = "Account Statement - Last 3 months";
    }
    else if($date_range=='daterange')
    {
        $date1 = $_REQUEST['startdate'];
        $date2 = $_REQUEST['enddate'];
        
        $flag  = 1;
//        $dates = explode('-', $date1);
//        $date1 = $date3 = $dates[2].'/'.$dates[1].'/'.$dates[0];
//        $dates = explode('-', $date2);
//        $date2 = $dates[2].'/'.$dates[1].'/'.$dates[0];
        $title = "Account Statement - $date1 - $date2";
        
        $date1 = convertToMysqlDate($_REQUEST['startdate']);
        $date2 = convertToMysqlDate($_REQUEST['enddate']);
    }
    else 
        $title = "Account Statement - All History";
    
    $payment_sql  = $order_sql = "userID='$userId'";
    if($flag==1)
    {
        $payment_sql .= " AND date<'$date1'";
        $order_sql   .= " AND (orderDate<'$date1')";
    }
    else
    {
        $payment_sql .= " AND 1=2";
        $order_sql   .= " AND 1=2";
    }
    
    $payment_sql = "SELECT SUM(amount) as amount FROM gma_payments WHERE $payment_sql";
    $payment_rs  = mysql_query($payment_sql);
    $payment_row = mysql_fetch_assoc($payment_rs);
    $payment_amount = $payment_row['amount'];
    
    $order_sql = "SELECT SUM(invoice_amount) as amount FROM gma_order WHERE $order_sql";
    $order_rs  = mysql_query($order_sql);
    $order_row = mysql_fetch_assoc($order_rs);
    $order_amount = $order_row['amount'];
    
    $balance_forward = $balance_due = $order_amount - $payment_amount;
    
    $payment_sql  = $order_sql = "userID='$userId'";
    if($flag==1)
    {
        $payment_sql .= " AND (date>='$date1' AND date<='$date2')";
        $order_sql   .= " AND (orderDate>='$date1' AND orderDate<='$date2')";
    }
    
    $payment_sql = "SELECT *,DATE_FORMAT(date,'%Y%m%d') AS date_new,DATE_FORMAT(date,'%d/%m/%Y') AS date FROM gma_payments WHERE $payment_sql ORDER BY date ASC";
    $payment_rs  = mysql_query($payment_sql);
    while ($payment_row = mysql_fetch_assoc($payment_rs))
    {
        $date_new              = $payment_row['date_new'];
        $details_row['type']   = 'payment';
        $details_row['desc']   = 'Payment - Thank you';
        $details_row['date']   = $payment_row['date'];
        $details_row['amount'] = $payment_row['amount'];
        
        $balance_due = $balance_due - $payment_row['amount'];
        
        $details[$date_new][] = $details_row;	
    }
    
    $order_sql = "SELECT *,DATE_FORMAT(orderDate,'%Y%m%d') AS date_new,DATE_FORMAT(orderDate,'%d/%m/%Y') AS orderDate FROM gma_order WHERE $order_sql GROUP BY invoiceId ORDER BY orderDate ASC";
    $order_rs  = mysql_query($order_sql);
    while ($order_row = mysql_fetch_assoc($order_rs))
    {
        $date_new  = $order_row['date_new'];
        $invoiceId = $order_row['invoiceId'];
        $user_id   = $order_row['userId'];
        $amount    = $order_row['invoice_amount'];
        
        $details_row['type']    = 'order';
        $details_row['orderId'] =  $order_row['id'];
        $details_row['desc']    = 'Invoice '.$order_row['invoiceId'];
        $details_row['date']    = $order_row['orderDate'];
        $details_row['amount']  = $amount;
        
        $balance_due = $balance_due + $amount;
        
        $details[$date_new][] = $details_row;	
    }
    ksort($details);
    
    if($date_flag==1)
    {
        $counts = 20;
        $details_new = $details;
        krsort($details_new);
        unset($details);
        
        foreach ($details_new as $date=>$detail)
        {
            foreach ($detail as $row)
            {
                if($counts>0)
                {
                    $details[$date][] = $row;
                    $counts = $counts - 1;
                } else {                         
                    $type   = $row['type'];
                    $desc   = $row['desc'];
                    $date   = $row['date'];
                    $amount = $row['amount'];
                    
                    $balance_forward = ($type=='payment') ? $balance_forward-$amount : $balance_forward + $amount;
                }
            }
        }
        ksort($details);
    }
    $cellpadding = ($date_flag==0) ? 3 : 0;
    $result = "<table width='100%' class='client_display_table' cellpadding='$cellpadding' cellspacing='$cellpadding'>
    <tr>
        <th width='10%' class='thead_new'><span>Date</span></th>
        <th class='thead_new'><span>Description</span></th>
        <th width='15%' class='thead_new' align='center'><span>Payment</span></th>
        <th width='15%' class='thead_new' align='center'><span>Amount</span></th>
        <th width='15%' class='thead_new' align='center'><span>Balance</span></th>
    </tr>";
    $j=0; $balance = 0;
    if($balance_forward!=0)
    {
        $j++;
        if($date_flag==0)
            $class = (($j%2)==0) ? 'row2' : 'row1';
        else 
            $class = "border_bottom";
            
        $result .= "<tr class='$class'>
            <td>$date3</td>
            <td>Balance Forward</td>
            <td align='right'></td>
            <td align='right'></td>
            <td align='right'>".formatMoney($balance_forward, true)."</td>
        </tr>";
    }
    $balance = $balance_forward;
    foreach ($details as $detail)
    {
        foreach ($detail as $row)
        {
            $j++;
            if($date_flag==0)
                $class = (($j%2)==0) ? 'row2' : 'row1';
            else 
                $class = "border_bottom";
                
            $type   = $row['type'];
            $desc   = $row['desc'];
            $date   = $row['date'];
            $amount = $row['amount'];
            
            if($date_flag==0 && $type!='payment')
                $desc = "<a href='invoices.php?action=view&orderId=".$row['orderId']."&popup' class='thickbox'>$desc</a>";
            
            $balance = ($type=='payment') ? $balance-$amount : $balance + $amount;
            $result .= "<tr class='$class'>
                <td>$date</td>
                <td>$desc</td>
                <td align='right'>".(($type=='payment') ? formatMoney($amount, true) : '')."</td>
                <td align='right'>".(($type!='payment') ? formatMoney($amount, true) : '')."</td>
                <td align='right'>".formatMoney($balance, true)."</td>
            </tr>";
        }
    }
    if($j!=0) {
        $j++;
        if($date_flag==0)
            $class  = (($j%2)==0) ? 'row2' : 'row1';
        else 
            $class = "row2";
        
        $result .= "<tr class='$class' style='height:30px;'>
            <td colspan='4' align='right'><b>Current Balance&nbsp;:&nbsp;</b></td>
            <td align='right'><b>".formatMoney($balance_due, true)."</b></td>
        </tr>";
    } else { 
        $result .= "<tr><td class='message' colspan='10'>No Records Found</td></tr>";
    } 
    $result .= "</table>";
    
    return array('result'=>$result, 'title'=>$title, 'balance_due'=>$balance_due);
}

function getFile($filename){
    $return = '';
    if ($fp = fopen($filename, 'rb')){
        while (!feof($fp)){
            $return .= fread($fp, 1024);
        }
        fclose($fp);
        return $return;
    } else {
        return false;
    }
}

function invoiceDetails_old($orderId, $flag=0)
{
    global $active_theme, $admin_email, $ses_companyId;
    
    $details   = $result = '';
    $email_sql = "SELECT * FROM gma_emails WHERE companyId='$ses_companyId' AND template='invoice'";
    $email_rs  = mysql_query($email_sql);
    $email_row = mysql_fetch_assoc($email_rs);
    $email_content  = $email_row['content'];
    
    $order_sql = "SELECT * FROM gma_order,gma_user_details,gma_logins,gma_company WHERE gma_logins.userId=gma_order.userId AND gma_order.userId=gma_user_details.userId AND gma_company.companyId=gma_logins.companyId AND id='$orderId'";
    // echo $order_sql;exit;
    $order_rs  = mysql_query($order_sql);
    $order_row = mysql_fetch_assoc($order_rs);
    
    $order_detail_sql = "SELECT * FROM gma_order_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId' AND discount>0";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $discount_flag    = mysql_num_rows($order_detail_rs);
    
    $order_detail_sql = "SELECT *,gma_order_details.amount AS order_amount FROM gma_order_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId'";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $i = 1;
    while ($order_detail_row = mysql_fetch_assoc($order_detail_rs))
    {
        $service_id  = $order_detail_row['id'];
        //$serviceName = ($service_id==0) ? $order_detail_row['serviceName'] : $order_detail_row['service_name'];
        $serviceName = $order_detail_row['serviceName'];
        $cost        = $order_detail_row['cost'];
        $quantity    = $order_detail_row['quantity'];
        $discount    = $order_detail_row['discount'];
        $amount      = $order_detail_row['order_amount'];
        if($cost>0)
        {
            $details .= "<tr>
                <td bgcolor=white class='color4'><div align=left >".$i++."</div></td>
                <td bgcolor=white class='color4'><div align=left >".$serviceName."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$cost."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$quantity."</div></td>";
            
            if($discount_flag!=0)
                $details .= "<td bgcolor=white class='color4'><div align=center >".$discount."%</div></td>";
                
            $details .= "<td bgcolor=white class='color4'><div align=right>" .formatMoney($amount,true)."</div></td>
            </tr>";
        }
    }
    $result .= "<tr>
                    <td width='35' class='color3'><div align='left'><b>ITEM</b></div></td>
                    <td width='400' class='color3'><div align='left'><b>DESCRIPTION</b></div></td>
                    <td width='87' class='color3'><div align='left'><b>COST</b></div></td>
                    <td width='50' class='color3'><div align='left'><b>QUANTITY</b></div></td>";
    
    if($discount_flag!=0)
        $result .= "<td width='87' class='color3'><div align='left'><b>DISCOUNT</b></div></td>";
        
    $result .= "   <td width='98' class='color3'><div align='right'><b>AMOUNT</b></div></td>
                </tr>$details";
    
    
    $result .= "<tr>
                    <td colspan='".($discount_flag!=0 ? 5 : 4)."' class='color1'><div align='right'><span><strong>TOTAL DUE </strong></span></div></td>
                    <td class='color1'><div align='right' class='style9'>".formatMoney($order_row['invoice_amount'], true)."</div></td>
                </tr>";
    
    $order_row['date']           = date("j F Y", strtotime($order_row['orderDate']));
    $order_row['invoiceDetails'] = $result;
    $order_row['admin_email']    = $admin_email;
    $order_row['email_content']  = nl2br($email_content);
    $order_row['popup_display']  = ($flag==1) ? 'none' : 'block';
    
    $details = getFile('includes/invoice_mail.html');
    foreach ($active_theme as $key => $value)
    {
        $details = str_replace("[$key]", $value, $details);
    }
    foreach ($order_row as $key => $value)
    {
        $details = str_replace("[$key]", $value, $details);
    }
    
    return $details;
}

function invoiceDetails($orderId, $flag=0)
{
    global $active_theme, $admin_email, $ses_companyId, $company_row;
    
    $details   = $result = '';
    
    $order_sql = "SELECT * FROM gma_order,gma_user_details,gma_logins,gma_company WHERE gma_logins.userId=gma_order.userId AND gma_order.userId=gma_user_details.userId AND gma_company.companyId=gma_logins.companyId AND id='$orderId'";
    $order_rs  = mysql_query($order_sql);
    $order_row = mysql_fetch_assoc($order_rs);
    
    $order_detail_sql = "SELECT * FROM gma_order_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId' AND discount>0";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $discount_flag    = mysql_num_rows($order_detail_rs);
    
    $order_detail_sql = "SELECT *,gma_order_details.amount AS order_amount FROM gma_order_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId'";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $i = 1;
    while ($order_detail_row = mysql_fetch_assoc($order_detail_rs))
    {
        $service_id  = $order_detail_row['id'];
        //$serviceName = ($service_id==0) ? $order_detail_row['serviceName'] : $order_detail_row['service_name'];
        $serviceName = $order_detail_row['serviceName'];
        $cost        = $order_detail_row['cost'];
        $quantity    = $order_detail_row['quantity'];
        $discount    = $order_detail_row['discount'];
        $amount      = $order_detail_row['order_amount'];
        if($cost>0)
        {
            $details .= "<tr>
                <td bgcolor=white class='color4'><div align=left >".$i++."</div></td>
                <td bgcolor=white class='color4'><div align=left >".$serviceName."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$cost."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$quantity."</div></td>";
            
            if($discount_flag!=0)
                $details .= "<td bgcolor=white class='color4'><div align=center >".$discount."%</div></td>";
                
            $details .= "<td bgcolor=white class='color4'><div align=right>" .formatMoney($amount,true)."</div></td>
            </tr>";
        }
    }
    $result .= "<tr>
                    <td width='35' class='color3'><div align='left'><b>ITEM</b></div></td>
                    <td width='400' class='color3'><div align='left'><b>DESCRIPTION</b></div></td>
                    <td width='87' class='color3'><div align='left'><b>COST</b></div></td>
                    <td width='50' class='color3'><div align='left'><b>QUANTITY</b></div></td>";
    
    if($discount_flag!=0)
        $result .= "<td width='87' class='color3'><div align='left'><b>DISCOUNT</b></div></td>";
        
    $result .= "   <td width='98' class='color3'><div align='right'><b>AMOUNT</b></div></td>
                </tr>$details";
    
    
    $result .= "<tr>
                    <td colspan='".($discount_flag!=0 ? 5 : 4)."' class='color1'><div align='right'><span><strong>TOTAL DUE </strong></span></div></td>
                    <td class='color1'><div align='right' class='style9'>".formatMoney($order_row['invoice_amount'], true)."</div></td>
                </tr>";
        
    $order_details['invoiceId']    = $order_row['invoiceId'];    
    $order_details['order_date']   = date("j F Y", strtotime($order_row['orderDate']));
    
    $order_details['firstname']    = $order_row['firstName'];    
    $order_details['lastname']     = $order_row['lastName'];    
    $order_details['clientname']   = $order_row['businessName'];    
    $order_details['phone']        = $order_row['phone'];    
    $order_details['address']      = $order_row['address'];    
    $order_details['vatno']        = $order_row['vatNo'];    
    $order_details['order_number'] = $order_row['order_number'];    
    $order_details['email']        = $order_row['email'];        
    $order_details['to_email']     = $order_row['email'];  
    // echo '<pre>'; print_r($order_details); print_r($order_row); exit;  
    
    $order_details['invoiceDetails'] = $result;
    
    return $order_details;
}

function quotationDetails_old($orderId, $flag=0)
{
    global $active_theme, $admin_email, $ses_companyId;
    
    $details   = $result = '';
    $email_sql = "SELECT * FROM gma_emails WHERE companyId='$ses_companyId' AND template='invoice'";
    $email_rs  = mysql_query($email_sql);
    $email_row = mysql_fetch_assoc($email_rs);
    $email_content = $email_row['content'];
    $email_content = ($email_row['update']==1) ? $email_content : nl2br($email_content);
     
    $order_sql = "SELECT * FROM gma_quotation,gma_user_details,gma_logins,gma_company WHERE gma_logins.userId=gma_quotation.userId AND gma_quotation.userId=gma_user_details.userId AND gma_company.companyId=gma_logins.companyId AND id='$orderId'";
    // echo $order_sql;exit;
    $order_rs  = mysql_query($order_sql);
    $order_row = mysql_fetch_assoc($order_rs);
    
    $order_detail_sql = "SELECT * FROM gma_quotation_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId' AND discount>0";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $discount_flag    = mysql_num_rows($order_detail_rs);
    
    $order_detail_sql = "SELECT *,gma_quotation_details.amount AS order_amount FROM gma_quotation_details LEFT JOIN gma_services ON id=service_id WHERE orderId='$orderId'";
    $order_detail_rs  = mysql_query($order_detail_sql);
    $i = 1;
    while ($order_detail_row = mysql_fetch_assoc($order_detail_rs))
    {
        $service_id  = $order_detail_row['id'];
        //$serviceName = ($service_id==0) ? $order_detail_row['serviceName'] : $order_detail_row['service_name'];
        $serviceName = $order_detail_row['serviceName'];
        $cost        = $order_detail_row['cost'];
        $quantity    = $order_detail_row['quantity'];
        $discount    = $order_detail_row['discount'];
        $amount      = $order_detail_row['order_amount'];
        if($cost>0)
        {
            $details .= "<tr>
                <td bgcolor=white class='color4'><div align=left >".$i++."</div></td>
                <td bgcolor=white class='color4'><div align=left >".$serviceName."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$cost."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$quantity."</div></td>";
            
            if($discount_flag!=0)
                $details .= "<td bgcolor=white class='color4'><div align=center >".$discount."%</div></td>";
                
            $details .= "<td bgcolor=white class='color4'><div align=right>" .formatMoney($amount,true)."</div></td>
            </tr>";
        }
    }
    $result .= "<tr>
                    <td width='35' class='color3'><div align='left'><b>ITEM</b></div></td>
                    <td width='400' class='color3'><div align='left'><b>DESCRIPTION</b></div></td>
                    <td width='87' class='color3'><div align='left'><b>COST</b></div></td>
                    <td width='50' class='color3'><div align='left'><b>QUANTITY</b></div></td>";
    
    if($discount_flag!=0)
        $result .= "<td width='87' class='color3'><div align='left'><b>DISCOUNT</b></div></td>";
        
    $result .= "   <td width='98' class='color3'><div align='right'><b>AMOUNT</b></div></td>
                </tr>$details";
    
    
    $result .= "<tr>
                    <td colspan='".($discount_flag!=0 ? 5 : 4)."' class='color1'><div align='right'><span><strong>TOTAL DUE </strong></span></div></td>
                    <td class='color1'><div align='right' class='style9'>".formatMoney($order_row['invoice_amount'], true)."</div></td>
                </tr>";
    
    $order_row['date']           = date("j F Y", strtotime($order_row['orderDate']));
    $order_row['invoiceDetails'] = $result;
    $order_row['admin_email']    = $admin_email;
    $order_row['email_content']  = nl2br($email_content);
    $order_row['popup_display']  = ($flag==1) ? 'none' : 'block';
    
    $details = getFile('includes/quotation_mail.html');
    foreach ($active_theme as $key => $value)
    {
        $details = str_replace("[$key]", $value, $details);
    }
    foreach ($order_row as $key => $value)
    {
        $details = str_replace("[$key]", $value, $details);
    }
    
    return $details;
}

function quotationDetails($quotationId, $flag=0)
{
    global $active_theme, $admin_email, $ses_companyId, $company_row;
    
    $details   = $result = '';
    
    $quotation_sql = "SELECT * FROM gma_quotation,gma_user_details,gma_logins,gma_company WHERE gma_logins.userId=gma_quotation.userId AND gma_quotation.userId=gma_user_details.userId AND gma_company.companyId=gma_logins.companyId AND id='$quotationId'";
    $quotation_rs  = mysql_query($quotation_sql);
    $quotation_row = mysql_fetch_assoc($quotation_rs);
    
    $quotation_detail_sql = "SELECT * FROM gma_quotation_details LEFT JOIN gma_services ON id=service_id WHERE quotationId='$quotationId' AND discount>0";
    $quotation_detail_rs  = mysql_query($quotation_detail_sql);
    $discount_flag    = mysql_num_rows($quotation_detail_rs);
    
    $quotation_detail_sql = "SELECT *,gma_quotation_details.amount AS quotation_amount FROM gma_quotation_details LEFT JOIN gma_services ON id=service_id WHERE quotationId='$quotationId'";
    $quotation_detail_rs  = mysql_query($quotation_detail_sql);
    $i = 1;
    while ($quotation_detail_row = mysql_fetch_assoc($quotation_detail_rs))
    {
        $service_id  = $quotation_detail_row['id'];
        //$serviceName = ($service_id==0) ? $quotation_detail_row['serviceName'] : $quotation_detail_row['service_name'];
        $serviceName = $quotation_detail_row['serviceName'];
        $cost        = $quotation_detail_row['cost'];
        $quantity    = $quotation_detail_row['quantity'];
        $discount    = $quotation_detail_row['discount'];
        $amount      = $quotation_detail_row['quotation_amount'];
        if($cost>0)
        {
            $details .= "<tr>
                <td bgcolor=white class='color4'><div align=left >".$i++."</div></td>
                <td bgcolor=white class='color4'><div align=left >".$serviceName."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$cost."</div></td>
                <td bgcolor=white class='color4'><div align=center >".$quantity."</div></td>";
            
            if($discount_flag!=0)
                $details .= "<td bgcolor=white class='color4'><div align=center >".$discount."%</div></td>";
                
            $details .= "<td bgcolor=white class='color4'><div align=right>" .formatMoney($amount,true)."</div></td>
            </tr>";
        }
    }
    $result .= "<tr>
                    <td width='35' class='color3'><div align='left'><b>ITEM</b></div></td>
                    <td width='400' class='color3'><div align='left'><b>DESCRIPTION</b></div></td>
                    <td width='87' class='color3'><div align='left'><b>COST</b></div></td>
                    <td width='50' class='color3'><div align='left'><b>QUANTITY</b></div></td>";
    
    if($discount_flag!=0)
        $result .= "<td width='87' class='color3'><div align='left'><b>DISCOUNT</b></div></td>";
        
    $result .= "   <td width='98' class='color3'><div align='right'><b>AMOUNT</b></div></td>
                </tr>$details";
    
    
    $result .= "<tr>
                    <td colspan='".($discount_flag!=0 ? 5 : 4)."' class='color1'><div align='right'><span><strong>TOTAL DUE </strong></span></div></td>
                    <td class='color1'><div align='right' class='style9'>".formatMoney($quotation_row['invoice_amount'], true)."</div></td>
                </tr>";
        
    $quotation_details['invoiceId']    = $quotation_row['invoiceId'];    
    $quotation_details['order_date']   = date("j F Y", strtotime($quotation_row['orderDate']));
    
    $quotation_details['firstname']    = $quotation_row['firstName'];    
    $quotation_details['lastname']     = $quotation_row['lastName'];    
    $quotation_details['clientname']   = $quotation_row['businessName'];    
    $quotation_details['phone']        = $quotation_row['phone'];    
    $quotation_details['address']      = $quotation_row['address'];    
    $quotation_details['vatno']        = $quotation_row['vatNo'];    
    $quotation_details['order_number'] = $quotation_row['order_number'];    
    $quotation_details['email']        = $quotation_row['email'];        
    $quotation_details['to_email']     = $quotation_row['email'];  
//    echo '<pre>'; print_r($quotation_row); exit;  
    
    $quotation_details['quotationDetails'] = $result;
    
    return $quotation_details;
}

function upload_photo($photo_dest, $file_tempname, $file_maxwidth = "", $file_maxheight = "")
{
    $file_ext        = strtolower(str_replace(".", "", strrchr($photo_dest, ".")));
    $file_dimensions = @getimagesize($file_tempname);
    $file_width      = $file_dimensions[0];
    $file_height     = $file_dimensions[1];
    
    // CHECK IF DIMENSIONS ARE LARGER THAN ADMIN SPECIFIED SETTINGS
    // AND SET DESIRED WIDTH AND HEIGHT
    $width  = $file_width ;
    $height = $file_height;
    if( $height > $file_maxheight && $file_maxheight>0) {
        $width = floor($width * $file_maxheight / $height);
        $height = $file_maxheight;
    }
    if( $width > $file_maxwidth && $file_maxwidth>0 ) {
        $height = floor($height * $file_maxwidth / $width);
        $width = $file_maxwidth;
    }

    // RESIZE IMAGE AND PUT IN USER DIRECTORY
    switch($file_ext) {
        case "gif":
            $file = imagecreatetruecolor($width, $height);
            $new = imagecreatefromgif($file_tempname);
            $kek=imagecolorallocate($file, 255, 255, 255);
            imagefill($file,0,0,$kek);
            imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $file_width, $file_height);
            imagejpeg($file, $photo_dest, 100);
            ImageDestroy($new);
            ImageDestroy($file);
            break;

        case "bmp":
            $file = imagecreatetruecolor($width, $height);
            $new = $imagecreatefrombmp($file_tempname);
            for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
            imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $file_width, $file_height);
            imagejpeg($file, $photo_dest, 100);
            ImageDestroy($new);
            ImageDestroy($file);
            break;

        case "jpeg":
        case "jpg":
            $file = imagecreatetruecolor($width, $height);
            $new = imagecreatefromjpeg($file_tempname);
            for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
            imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $file_width, $file_height);
            imagejpeg($file, $photo_dest, 100);
            ImageDestroy($new);
            ImageDestroy($file);
            break;

        case "png":
            $file = imagecreatetruecolor($width, $height);
            $new = imagecreatefrompng($file_tempname);
            for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
            imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $file_width, $file_height);
            imagejpeg($file, $photo_dest, 100);
            ImageDestroy($new);
            ImageDestroy($file);
            break;
    }

    chmod($photo_dest, 0777);

    return true;
}

function dateFormat($date)
{
    $dates = explode(' ', $date);
    $dates = explode('-', $dates[0]);
    
    // 'DD/MM/YYYY'
    $date = $dates[2].'/'.$dates[1].'/'.$dates[0];
    
    return $date;
}

function convertToMysqlDate($date)
{
    $dates = explode(' ', $date);
    $split = (strstr($dates[0], '.')) ? '.' : '/';
    $dates = explode($split, $dates[0]);
    
    // 'DD/MM/YYYY'
    $date = $dates[2].'-'.$dates[1].'-'.$dates[0];
    
    return $date;
}

function paymentStatus($status) {
    if($status==0) 
        return 'Pending';
    else if($status==1) 
        return 'Paid';
    else if($status==2) 
        return 'Partial';    
}

function userCheck($userId) {
   global $ses_companyId;
   
   $login_sql  = "SELECT * FROM gma_logins WHERE userId='$userId' AND companyId='$ses_companyId'";
   $login_rs   = mysql_query($login_sql);
   
   return (mysql_num_rows($login_rs)==0) ? 0 : $userId;
}
?>