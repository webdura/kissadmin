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
$theme_sql = "SELECT gma_company_theme.*,theme FROM gma_company_theme,gma_theme WHERE id=theme_id AND companyId='$ses_companyId'";
$theme_rs  = mysql_query($theme_sql);
if(mysql_num_rows($theme_rs)==0)
{
    $theme_flag = 0;
    $theme_sql = "SELECT * FROM gma_theme WHERE `default`='1'";
    $theme_rs  = mysql_query($theme_sql);
}
$theme_row = mysql_fetch_assoc($theme_rs);

$company_theme_id = (isset($theme_row['theme_id'])) ? $theme_row['theme_id'] : $theme_row['id'];
$company_theme    = $theme_row['theme'];

$site_logo        = (isset($theme_row['site_logo']) && $theme_row['site_logo']!='') ? 'images/company/'.$theme_row['site_logo'] : 'images/logo.png';
$invoice_logo     = (isset($theme_row['invoice_logo']) && $theme_row['invoice_logo']!='') ? 'images/company/'.$theme_row['invoice_logo'] : $site_logo;
$invoice_status   = (isset($theme_row['invoice_status'])) ? $theme_row['invoice_status'] : 0;
$theme_head_color = $theme_row['head_color'];
$invoice_logo_mail = ($invoice_status==1) ? $site_logo : $invoice_logo; 

if($ses_loginType!='user') {
    $theme_sql = "SELECT * FROM gma_admin_details,gma_theme WHERE id=theme_id AND userId='$ses_userId'";
    $theme_rs  = mysql_query($theme_sql);
    $theme_row = mysql_fetch_assoc($theme_rs);
    
    $user_theme_id = (isset($theme_row['theme_id'])) ? $theme_row['theme_id'] : 0;
    $user_theme    = (isset($theme_row['theme'])) ? $theme_row['theme'] : '';
}

$default_theme = ($user_theme!='') ? $user_theme : $company_theme;

//$theme_sql = "SELECT * FROM gma_theme WHERE 1 ORDER BY rand()";
//$theme_rs  = mysql_query($theme_sql);
//$theme_row = mysql_fetch_assoc($theme_rs);
//$default_theme = $theme_row['theme'];


$theme_rows = array();
$theme_sql = "SELECT * FROM gma_theme WHERE 1 ORDER BY name ASC";
$theme_rs  = mysql_query($theme_sql);
while ($theme_row = mysql_fetch_assoc($theme_rs)) {
	   $theme_rows[$theme_row['id']] = $theme_row;
}

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
    $email_content = stripslashes($email_row['content']);
    $email_status  = $email_row['status'];
    // if($email_row['update']==0) {
    if(!strstr($email_content, '<table')) {
        //$email_content = nl2br($email_content);
    }
    
    if(!isset($array_values['companyname'])) {
        $array_values['invoice_logo']          = $company_row['companyName'];
        $array_values['company_account_email'] = $company_row['companyAccountEmail'];
        $array_values['company_account_tel'] 	 = $company_row['companyAccountTel'];
        $array_values['company_website'] 		    = $company_row['companyWebsite'];
        $array_values['companyname']           = $company_row['companyName'];
        
        $array_values['companyAddress1'] = $company_row['companyAddress1'];
        $array_values['companyAddress2'] = $company_row['companyAddress2'];
        $array_values['companyCity']     = $company_row['companyCity'];
        $array_values['companyProvince'] = $company_row['companyProvince'];
        $array_values['companyZip'] 			  = $company_row['companyZip'];

        $array_values['companyBankName']   		= $company_row['companyBankName'];
        $array_values['companyBranchName']  	= $company_row['companyBranchName'];
        $array_values['companyBranchNo']    	= $company_row['companyBranchNo'];
        $array_values['companyAccountName'] 	= $company_row['companyAccountName'];
        $array_values['companyAccountType'] 	= $company_row['companyAccountType'];
        $array_values['companyAccountNo']   	= $company_row['companyAccountNo'];
        
        $array_values['company_address']		= $company_row['companyName'];
        $array_values['company_address']	.= (trim($array_values['companyAddress1'])!='')?",<br/>".$array_values['companyAddress1']:'';
        $array_values['company_address']	.= (trim($array_values['companyAddress2'])!='')?",<br/>".$array_values['companyAddress2']:'';
        $array_values['company_address']	.= (trim($array_values['companyCity'])!='')?",<br/>".$array_values['companyCity']:'';
        $array_values['company_address']	.= (trim($array_values['companyProvince'])!='')?",<br/>".$array_values['companyProvince']:'';
        $array_values['company_address']	.= (trim($array_values['companyZip'])!='')?",<br/>".$array_values['companyZip']:'';
        
        $array_values['company_address'] = trim($array_values['company_address'], ',<br/>');
        $array_values['bank_details'] = '<div style="width:400px;">';
        if($array_values['status']!=1) {
         			$array_values['bank_details'] .= '<div style="float:left;  width:180px; text-align:right; padding-right:20px;" > Payment method: </div>'; 
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" >EFT </div>';
         			$array_values['bank_details'] .= '<div style="clear:both" >&nbsp;</div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:180px; text-align:right; padding-right:20px;" > Bank Details: </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" > &nbsp; </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:180px; text-align:right; padding-right:20px;" > Bank: </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" >' . $company_row['companyBankName']. '</div>'; 
         			$array_values['bank_details'] .= '<div style="float:left; width:180px; text-align:right; padding-right:20px;" > Branch: </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" >' . $company_row['companyBranchName'] . '</div>'; 
         			$array_values['bank_details'] .= '<div style="float:left; width:180px; text-align:right; padding-right:20px;" > Account Name: </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" >' . $company_row['companyAccountName']. '</div>'; 
         			$array_values['bank_details'] .= '<div style="float:left; width:180px; text-align:right; padding-right:20px;" > Account Number: </div>';
         			$array_values['bank_details'] .= '<div style="float:left; width:200px;" >' . $company_row['companyAccountNo']. '</div>'; 
        }
        $array_values['bank_details'] .= '</div>';
       
    }
    $array_values['link_org']    = "{$SITE_URL}index.php";
    $array_values['link']        = "<a href='".$array_values['link_org']."'>Click here</a>";
    
    $array_values['logo'] = '';
    if($invoice_logo_mail!='' && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $invoice_logo_mail))
        $array_values['logo'] = "<img src='{$SITE_URL}$invoice_logo_mail'>";
     
    foreach ($array_values as $key => $value) {
        $email_subject = str_replace("[$key]", $value, $email_subject);
        $email_content = str_replace("[$key]", $value, $email_content);
    }
    $email_to = $array_values['to_email'];
    
    if($flag==1) {
        return $email_content;
    } else if($email_status==1 && $flag==0) {
     
        $css_content = cssRead();
        $email_content = "<style>$css_content</style>$email_content";
        
//        global $theme_head_bg, $theme_head_color, $theme_color1, $theme_color2, $theme_color3, $theme_color4;
//        
//        $email_content = str_replace('class="color1"', 'style="background-color: '.$theme_color1.'"', $email_content);
//        $email_content = str_replace('class="color2"', 'style="border-top:4px solid '.$theme_color2.'"', $email_content);
//        $email_content = str_replace('class="color3"', 'style="background-color: '.$theme_color3.'"', $email_content);
//        $email_content = str_replace('class="color4"', 'style="background-color: '.$theme_color4.'"', $email_content);
//        
//        $email_content = str_replace("class='color1'", 'style="background-color: '.$theme_color1.'"', $email_content);
//        $email_content = str_replace("class='color2'", 'style="border-top:4px solid '.$theme_color2.'"', $email_content);
//        $email_content = str_replace("class='color3'", 'style="background-color: '.$theme_color3.'"', $email_content);
//        $email_content = str_replace("class='color4'", 'style="background-color: '.$theme_color4.'"', $email_content);
        
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
        $mailToSend->send($email_to);
                
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
    $number = "R " . sprintf("%01.2f", $number); 
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
            $links	.= 	"<span class='current'>$i</span> ";
        else
            $links 	.= 	"<a href='$filename&$page=$i'>$i</a> ";
    }
    
    //	Previous Record
    if ($currentpage>1)
    {
        $i  		= 	$currentpage - 1;
        $prev  	= 	"<a href='$filename&$page=$i'>&#171; Previous</a>";
    }
    else
        $prev  	= 	"<span class='nextprev'>&#171; Previous</span>";
    
    //	Previous Page
    if($startpage>1)
    {
        $i 			= 	$startpage	-	1;
        $previous	=  "<a href='$filename&$page=$i'>&#171; Previous Page</a>";	
    }
    else
        $previous = " <span class='nextprev'>&#171; Previous Page</span> ";
    
    if ($currentpage<$totalpages)
    {
        $i 		= 	$currentpage + 1;
        $next 	= 	"<a href='$filename&$page=$i'>Next &#187;</a> ";
    }
    else
        $next 	= 	"<span class='nextprev'>Next &#187;</span> ";		
    
    if($endpage<$totalpages)
    {
        $i 		=	$endpage	+	1;
        $more 	=  	"<a href='$filename&$page=$i'>Next Page&#187;  &nbsp;</a>";
    }
    else
        $more = " <span class='nextprev'>Next Page &#187;</span> "; // we're on the last page, don't print next link
    
    $more1	=	'<font color="#ffffff"></font>';	
    $result	=	"<table align='center' border='0' width='100%' class='nostyle'><tr><td class='pages bodytag' >$previous $prev  $links  $next $more $more1</td></tr></table>";
    $result	=	"<table align='right' border='0' class='nostyle'><tr><td class='pages bodytag' ><span class='text'>Page: (Page $currentpage of $totalpages )</span> $previous $prev  $links  $next $more $more1</td></tr></table>";
    
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

function GetSQLValueString($theValue, $theType='text', $theDefinedValue = "", $theNotDefinedValue = "") 
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
    
    $payment_sql  = $order_sql = $credit_sql = "userID='$userId'";
    if($flag==1)
    {
        $payment_sql .= " AND date(date)<'$date1'";
        $order_sql   .= " AND (date(orderDate)<'$date1')";
        $credit_sql .= " AND (date(creditnoteDate)<'$date1')";
    }
    else
    {
        $payment_sql .= " AND 1=2";
        $order_sql   .= " AND 1=2";
        $credit_sql .= " AND 1=2";
    }
    
    $payment_sql    = "SELECT SUM(amount) as amount FROM gma_payments WHERE $payment_sql";
    $payment_rs     = mysql_query($payment_sql);
    $payment_row    = mysql_fetch_assoc($payment_rs);
    $payment_amount = $payment_row['amount'];
    
    $order_sql    = "SELECT SUM(invoice_amount) as amount FROM gma_order WHERE $order_sql";
    $order_rs     = mysql_query($order_sql);
    $order_row    = mysql_fetch_assoc($order_rs);
    $order_amount = $order_row['amount'];
    
    $credit_sql    = "SELECT SUM(creditnote_amount) as amount FROM gma_creditnote WHERE $credit_sql";
    $credit_rs     = mysql_query($credit_sql);
    $credit_row    = mysql_fetch_assoc($credit_rs);
    $credit_amount = $credit_row['amount'];
    
//    echo $userId.'<br>';
//    echo "$payment_sql == $payment_amount<br>";
//    echo "$order_sql == $order_amount<br>";
//    echo "$credit_sql == $credit_amount<br>";
    
    $balance_forward = $balance_due = $order_amount - $payment_amount - $credit_amount;
//    if($userId > 0)
	   $payment_sql  = $order_sql = $credit_sql = "userID='$userId'"; 
//    else 
//     	   $payment_sql  = $order_sql = $credit_sql = "1"; 
   
    if($flag==1)
    {
        $payment_sql .= " AND (date(date)>='$date1' AND date(date)<='$date2')";
        $order_sql   .= " AND (date(orderDate)>='$date1' AND date(orderDate)<='$date2')";
        $credit_sql  .= " AND (date(creditnoteDate)>='$date1' AND date(creditnoteDate)<='$date2')";
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
    
    $credit_sql = "SELECT *,DATE_FORMAT(creditnoteDate,'%Y%m%d') AS date_new,DATE_FORMAT(creditnoteDate,'%d/%m/%Y') AS creditnoteDate FROM gma_creditnote WHERE $credit_sql GROUP BY creditId ORDER BY creditnoteDate ASC";
    $credit_rs  = mysql_query($credit_sql);
    while ($credit_row = mysql_fetch_assoc($credit_rs))
    {
        $date_new  = $credit_row['date_new'];
        $creditId  = $credit_row['creditId'];
        $user_id   = $credit_row['userId'];
        $amount    = $credit_row['creditnote_amount'];
        
        $details_row['type']    = 'creditnote';
        $details_row['creditId'] =  $credit_row['id'];
        $details_row['desc']    = 'Credit note '.$credit_row['creditId'];
        $details_row['date']    = $credit_row['creditnoteDate'];
        $details_row['amount']  = $amount;
        
        $balance_due = $balance_due - $amount;
        
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
                    
                    $balance_forward = ($type!='order') ? $balance_forward-$amount : $balance_forward + $amount;
                }
            }
        }
        ksort($details);
    }
    $cellpadding = ($date_flag==0) ? 3 : 0;
    $cellpadding = 0;
//    $result = "<table width='100%' class='client_display_table' cellpadding='$cellpadding' cellspacing='$cellpadding'>
    $result = "<table width='100%' class='list' cellpadding='$cellpadding' cellspacing='$cellpadding'>
    <tr>
        <th width='10%'><span>Date</span></th>
        <th><span>Description</span></th>
        <th width='15%'><span>Payment</span></th>
        <th width='15%'><span>Amount</span></th>
        <th width='15%'><span>Balance</span></th>
    </tr>";
    $j=0; $balance = 0;
    if($balance_forward!=0)
    {
        $j++;
        if($date_flag==0)
            $class = (($j%2)==0) ? 'altrow' : '';
        else 
            $class = "border_bottom";
            
        $result .= "<tr class='$class'>
            <td>$date3</td>
            <td>Balance Forward</td>
            <td></td>
            <td></td>
            <td>".formatMoney($balance_forward, true)."</td>
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
            
            if($date_flag==0 && $type=='order')
                $desc = "<a href='invoices.php?action=view&orderId=".$row['orderId']."&popup' class='thickbox links'>$desc</a>";
            if($date_flag==0 && $type=='creditnote')
                $desc = "<a href='creditnote.php?action=view&creditnoteId=".$row['creditId']."&popup' class='thickbox links'>$desc</a>";
            
            $balance = ($type!='order') ? $balance-$amount : $balance + $amount;
            $result .= "<tr class='$class'>
                <td>$date</td>
                <td>$desc</td>
                <td>".(($type=='payment') ? formatMoney($amount, true) : '')."</td>
                <td>".(($type!='payment') ? formatMoney(($type=='order' ? $amount : "-$amount"), true) : '')."</td>
                <td>".formatMoney($balance, true)."</td>
            </tr>";
        }
    }
    if($j!=0) {
        $class = 'footer';
        $result .= "<tr class='$class'>
            <td colspan='4' style='text-align:right'><b>Current Balance&nbsp;:&nbsp;</b></td>
            <td align='right'><b>".formatMoney($balance_due, true)."</b></td>
        </tr>";
    } else { 
        $result .= "<tr><td class='norecords' colspan='10'>No Records Found</td></tr>";
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

function invoiceDetails($orderId, $flag=0)
{
    global $default_theme, $admin_email, $ses_companyId, $company_row;
    
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
        $serviceName = (trim($order_detail_row['serviceName'])=='')?$order_detail_row['service_name']:$order_detail_row['serviceName'];
        $description = $order_detail_row['description'];
        $cost        = $order_detail_row['cost'];
        $quantity    = $order_detail_row['quantity'];
        $discount    = $order_detail_row['discount'];
        $amount      = $order_detail_row['order_amount'];
        
        $class    = (($i%2)==0) ? 'altrow' : '';
        
        $details .= "<tr class='$class'>
                <td>".$i++."</td>
                <td>$serviceName</td>
                <td>$description</td>
                <td>$cost</td>
                <td>$quantity</td>";
            
        if($discount_flag!=0)
            $details .= "<td align='right'>$discount%</td>";
            
        $details .= "<td align='right'>".formatMoney($amount, true)."</td>
        </tr>";
    }
    $result .= "<tr>
                    <th>#</th>
                    <th>ITEM</th>
                    <th>DESCRIPTION</th>
                    <th>COST</th>
                    <th>QUANTITY</th>";
    
    if($discount_flag!=0)
        $result .= "<th>DISCOUNT</th>";
        
    $result .= "   <th>AMOUNT</th>
                </tr>$details";
    
    
    $result .= "<tr class='footer'>
                    <td colspan='".($discount_flag!=0 ? 6 : 5)."'><div align='right'><b>TOTAL DUE</b></div></td>
                    <td><div align='left'>".formatMoney($order_row['invoice_amount'], true)."</div></td>
                </tr>";
    $result = '<table width="100%" class="list" cellpadding="0" cellspacing="0">'.$result.'</table>';
        
    $order_details['invoiceId']    = $order_row['invoiceId'];
    $order_details['comments']     = $order_row['comments'];
    $order_details['order_date']   = date("j F Y", strtotime($order_row['orderDate']));
    
    $order_details['status']	      = $order_row['orderStatus'];    
    $order_details['status_text']  = ($order_row['orderStatus']==1)?'PAID':'Pending';    
    $order_details['firstname']    = $order_row['firstName'];    
    $order_details['lastname']     = $order_row['lastName'];    
    $order_details['clientname']   = $order_row['businessName'];    
    $order_details['phone']        = $order_row['phone'];    
    $order_details['address']      = $order_row['address'];    
    $order_details['vatno']        = $order_row['vatNo'];    
    $order_details['order_number'] = $order_row['order_number'];    
    $order_details['email']        = $order_details['to_email'] = $order_row['email'];
    
    $user_detail_sql = "SELECT * FROM gma_user_address WHERE userId=" . $order_row['userId'];
    $user_detail_rs  = mysql_query($user_detail_sql);
    $i = 1;
    while ($user_detail_row = mysql_fetch_assoc($user_detail_rs))
    {
        if($user_detail_row['type']=='B'){
            $order_details['billing_address']  = $user_detail_row['address'];
            $order_details['billing_city']     = $user_detail_row['city'];
            $order_details['billing_province'] = $user_detail_row['province'];
            $order_details['billing_zip']      = $user_detail_row['zip'];
        } else {
            $order_details['delivery_address']  = $user_detail_row['address'];
            $order_details['delivery_city']     = $user_detail_row['city'];
            $order_details['delivery_province'] = $user_detail_row['province'];
            $order_details['delivery_zip']      = $user_detail_row['zip'];			
        }
    }
    
    $order_details['invoiceDetails'] = $result; 
    
    return $order_details;
}

function quotationDetails($quotationId, $flag=0)
{
    global $default_theme, $admin_email, $ses_companyId, $company_row;
    
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
        
        $class    = (($i%2)==0) ? 'altrow' : '';
        
        $details .= "<tr class='$class'>
                <td>".$i++."</td>
                <td>$serviceName</td>
                <td>$description</td>
                <td>$cost</td>
                <td>$quantity</td>";
            
        if($discount_flag!=0)
            $details .= "<td align='right'>$discount%</td>";
            
        $details .= "<td align='right'>".formatMoney($amount, true)."</td>
        </tr>";
    }
    $result .= "<tr>
                    <th>#</th>
                    <th>ITEM</th>
                    <th>DESCRIPTION</th>
                    <th>COST</th>
                    <th>QUANTITY</th>";
    
    if($discount_flag!=0)
        $result .= "<th>DISCOUNT</th>";
        
    $result .= "   <th>AMOUNT</th>
                </tr>$details";
    
    
    $result .= "<tr class='footer'>
                    <td colspan='".($discount_flag!=0 ? 6 : 5)."'><div align='right'><b>TOTAL DUE</b></div></td>
                    <td><div align='left'>".formatMoney($quotation_row['invoice_amount'], true)."</div></td>
                </tr>";
    $result = '<table width="100%" class="list" cellpadding="0" cellspacing="0">'.$result.'</table>';
        
    $quotation_details['invoiceId']    = $quotation_row['invoiceId'];    
    $quotation_details['comments']     = $quotation_row['comments'];
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
     
    $quotation_details['quotationDetails'] = $result; 
    //echo '<pre>'; print_r($quotation_details); exit;  
    
    return $quotation_details;
}

function creditNoteDetails($creditnoteId, $flag=0) {
    global $default_theme, $admin_email, $ses_companyId, $company_row;
    
    $details = $result = '';
    
    $creditnote_sql = "SELECT * FROM gma_creditnote,gma_user_details,gma_logins,gma_company WHERE gma_logins.userId=gma_creditnote.userId AND gma_creditnote.userId=gma_user_details.userId AND gma_company.companyId=gma_logins.companyId AND id='$creditnoteId'";
    $creditnote_rs  = mysql_query($creditnote_sql);
    $creditnote_row = mysql_fetch_assoc($creditnote_rs);
    
    $creditnote_detail_sql = "SELECT * FROM gma_creditnote_details LEFT JOIN gma_services ON id=service_id WHERE creditnoteId='$creditnoteId' AND discount>0";
    $creditnote_detail_rs  = mysql_query($creditnote_detail_sql);
    $discount_flag    = mysql_num_rows($creditnote_detail_rs);
    
    $creditnote_detail_sql = "SELECT *,gma_creditnote_details.amount AS creditnote_amount FROM gma_creditnote_details LEFT JOIN gma_services ON id=service_id WHERE creditnoteId='$creditnoteId'";
    $creditnote_detail_rs  = mysql_query($creditnote_detail_sql);
    $i = 1;
    while ($creditnote_detail_row = mysql_fetch_assoc($creditnote_detail_rs)) {
        $service_id  = $creditnote_detail_row['id'];
        $serviceName = (trim($creditnote_detail_row['serviceName'])=='') ? $creditnote_detail_row['service_name'] : $creditnote_detail_row['serviceName'];
        $description = $creditnote_detail_row['description'];
        $cost        = $creditnote_detail_row['cost'];
        $quantity    = $creditnote_detail_row['quantity'];
        $discount    = $creditnote_detail_row['discount'];
        $amount      = $creditnote_detail_row['creditnote_amount'];
        
        $class    = (($i%2)==0) ? 'altrow' : '';
        
        $details .= "<tr class='$class'>
                <td>".$i++."</td>
                <td>$serviceName</td>
                <td>$description</td>
                <td>$cost</td>
                <td>$quantity</td>";
            
        if($discount_flag!=0)
            $details .= "<td align='right'>$discount%</td>";
            
        $details .= "<td align='right'>".formatMoney($amount, true)."</td>
        </tr>";
    }
    $result .= "<tr>
                    <th>#</th>
                    <th>ITEM</th>
                    <th>DESCRIPTION</th>
                    <th>COST</th>
                    <th>QUANTITY</th>";
    
    if($discount_flag!=0)
        $result .= "<th>DISCOUNT</th>";
        
    $result .= "   <th>AMOUNT</th>
                </tr>$details";
    
    
    $result .= "<tr class='footer'>
                    <td colspan='".($discount_flag!=0 ? 6 : 5)."'><div align='right'><b>TOTAL DUE</b></div></td>
                    <td><div align='left'>".formatMoney($creditnote_row['creditnote_amount'], true)."</div></td>
                </tr>";
    $result = '<table width="100%" class="list" cellpadding="0" cellspacing="0">'.$result.'</table>';
        
    $creditnote_details['creditId']        = $creditnote_row['creditId'];   
    $creditnote_details['comments']        = $creditnote_row['comments']; 
    $creditnote_details['creditnote_date'] = date("j F Y", strtotime($creditnote_row['creditnoteDate']));
     
    $creditnote_details['firstname']    = $creditnote_row['firstName'];    
    $creditnote_details['lastname']     = $creditnote_row['lastName'];    
    $creditnote_details['clientname']   = $creditnote_row['businessName'];    
    $creditnote_details['phone']        = $creditnote_row['phone'];    
    $creditnote_details['address']      = $creditnote_row['address'];    
    $creditnote_details['vatno']        = $creditnote_row['vatNo'];
    $creditnote_details['email']        = $creditnote_row['email'];        
    $creditnote_details['to_email']     = $creditnote_row['email'];
    
    $user_detail_sql = "SELECT * FROM gma_user_address WHERE userId=" . $creditnote_row['userId'];
    $user_detail_rs  = mysql_query($user_detail_sql);
    $i = 1;
    while ($user_detail_row = mysql_fetch_assoc($user_detail_rs))
    {
        if($user_detail_row['type']=='B'){
            $creditnote_details['billing_address'] = $user_detail_row['address'];
            $creditnote_details['billing_city'] = $user_detail_row['city'];
            $creditnote_details['billing_province'] = $user_detail_row['province'];
            $creditnote_details['billing_zip'] = $user_detail_row['zip'];
        } else {
            $creditnote_details['delivery_address'] = $user_detail_row['address'];
            $creditnote_details['delivery_city'] = $user_detail_row['city'];
            $creditnote_details['delivery_province'] = $user_detail_row['province'];
            $creditnote_details['delivery_zip'] = $user_detail_row['zip'];			
        }
    }
    $creditnote_details['creditnoteDetails'] = $result; 
    
    return $creditnote_details;
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

function dateFormat($date, $showTime='N')
{
    if($date=="0000-00-00 00:00:00" || $date=="0000-00-00")
        $date = '';
    else if(strtolower($showTime)=='y')
        $date = date("d/m/Y H:i", strtotime($date));
    else
        $date = date("d/m/Y", strtotime($date));
    
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
        return 'PAID';
    else if($status==2) 
        return 'Partial';    
}

function userCheck($userId) {
   global $ses_companyId;
   
   $login_sql  = "SELECT * FROM gma_logins WHERE userId='$userId' AND companyId='$ses_companyId'";
   $login_rs   = mysql_query($login_sql);
   
   return (mysql_num_rows($login_rs)==0) ? 0 : $userId;
}


function invoiceEmailSend($sendInvoiceId) {
    global $ses_companyId, $SITE_URL, $company_row, $invoice_logo_mail;
    
    $notSend = '';
    
    $email_sql = "SELECT companyName, fullName, invoicePath, subject, message, email " .
    			   " FROM gma_send_invoices, gma_company, gma_admin_details, gma_logins " .
    			   " WHERE gma_send_invoices.companyId = gma_company.companyId AND ".
    			   " gma_send_invoices.userId = gma_admin_details.userId AND " .
    			   " gma_send_invoices.companyId = gma_logins.companyId AND " .
    			   " gma_send_invoices.userId = gma_logins.userId AND " .
    			   " gma_send_invoices.id = ". $sendInvoiceId;
    			   
    $email_rs  = mysql_query($email_sql);
    $email_row = mysql_fetch_assoc($email_rs);
    $email_from = $email_row['email'];
	$path = $email_row['invoicePath'];
    
    if(!isset($array_values['companyname'])) {
        $array_values['invoice_logo']          = $company_row['companyName'];
        $array_values['companyname']           = $company_row['companyName'];
        
    }
    
    $email_users_sql = "SELECT * " .
    			   " FROM gma_send_invoice_details " .
    			   " WHERE sendInvoiceId = ". $sendInvoiceId;
    			   
   		$email_users_rs  = mysql_query($email_users_sql);
/*        $today  = date('d-m-Y H:i:s');
        $myFile = "mail_log1.txt";
        $fp = fopen($myFile, 'a') or die("can't open file");
*/
        include("includes/htmlMimeMail.inc.php");
        $mailToSend  = new htmlMimeMail();        
        
    while($email_users_row = mysql_fetch_assoc($email_users_rs)){
    	$array_values['firstname'] = $email_users_row['firstName'];
    	$array_values['lastname'] = $email_users_row['lastName'];
//    	print_r($array_values);
		foreach ($array_values as $key => $value) {
			$email_subject = str_replace("[$key]", $value, $email_users_row['subject']);
			$email_content = str_replace("[$key]", $value, $email_users_row['content']);
		}
    	$email_to = $email_users_row['email'];
        
        $mailToSend->setSubject($email_subject);
        $mailToSend->setFrom($email_from);
        $mailToSend->setBcc('seacrows@gmail.com');
        $mailToSend->setHtml($email_content);
        
        $invoiceFile = dirname($_SERVER['SCRIPT_FILENAME']).'/file_upload/invoices/'.$path.'/'.$email_users_row['fileName'];
        if(file_exists($invoiceFile)){
            $attachment = $mailToSend->getFile($invoiceFile);
            $mailToSend->addAttachment($attachment, $array_values['filename']);
	        $email_to = explode(',', $email_to);
	        //$mailToSend->send($email_to);
       }
        else {
        	$notSend .= $array_values['firstname'] ." ". $array_values['lastname'] .", ".  $email_to ."<br>";
//	       fwrite($fp, $stringData);
        	
        }
                
        
	}
//        fclose($fp);
        return $notSend; 
    

} 

function saveRepeatedInvoice($orderId, $data, $allServices){

    if($orderId>0)
    {
        $order_sql = "SELECT * FROM gma_order WHERE id='$orderId'";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)>0)
        {
            $order_row = mysql_fetch_array($order_rs);
            $invoiceId = $order_row['invoiceId'];
        }
        else 
            $orderId = 0;
    }

    $orderDate    = date('Y-m-d H:i:s');
    $userId       = $data['userId'];
    $order_number = $data['order_number'];

    mysql_query("DELETE FROM gma_order_repeat_details WHERE orderRepeatId='$orderId'");    
    if($orderId==0)
    {
        $order_sql = "INSERT INTO gma_order_repeat SET userId='$userId',order_number='$order_number',orderDate='$orderDate'";
        mysql_query($order_sql);
        $orderId = mysql_insert_id();
    }
    
    $invoice_amount = $total = 0;
    foreach ($data['service_id'] as $key=>$service_group_id)
    {
        if($service_group_id!='' && $service_group_id!='0')
        {
            $request     = explode('_', $service_group_id);
            $service_id  = $request[0];
            $group_id    = $request[1];
            
            $serviceName = ($service_id>0) ? $allServices[$service_id]['service_name'] : $_REQUEST['service_name'][$key];
            $service_id  = ($service_id>0) ? $service_id : 0;
            $cost        = $data['cost'][$key];
            $quantity    = $data['quantity'][$key];
            $discount    = $data['discount'][$key];
            $amount      = $data['amount'][$key];
            
            
            $order_sql = "INSERT INTO gma_order_repeat_details SET orderRepeatId='$orderId',group_id='$group_id',service_id='$service_id',serviceName='$serviceName',cost='$cost',quantity='$quantity',discount='$discount',amount='$amount'";
            mysql_query($order_sql);
            
            $invoice_amount = $invoice_amount + $amount;
        }
    }
    
    if(trim(strtolower($data['how_many']))=='forever'){
    	$howmany = '1001';
    }else 
     	$howmany = $data['how_many'];   

    if(isset($_REQUEST['sendMail']))	
     	$send_mail = 'Y';
     else 
     	$send_mail = 'N';

    $order_sql = "UPDATE gma_order_repeat SET userId='$userId',order_number='$order_number', " .
    			" invoice_amount='$invoice_amount', startDate = '" . convertToMysqlDate($data['startdate']) ."'," .
    			" how_often = " . $data['how_often'] .", how_many = " . $howmany . "," .
    			" sendMail = '" . $send_mail ."' WHERE id='$orderId'";
    mysql_query($order_sql); 
    
   return true;
	
}

function getHowOften($key=0) {
	
	$howOften = array("1"=>"Weekly", 
		"2"=>"Twice a month",
		"3"=>"Monthly",
		"4"=>"2 Months",
		"5"=>"3 Months",
		"6"=>"6 Months",
		"7"=>"Yearly",
		"8"=>"2 Years"
	);
	
	if ($key > 0)
		return $howOften[$key];
	else	
		return $howOften;
	
}

function readFiles($folder) {
    $files = array();
    if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
            if($file!='.' && $file!='..')
                $files[] = $file;
        }
        closedir($handle);
    }
    
    return $files;
}

function cssRead() {
    global $default_theme;
    
    //$theme_theme = 'blue_theme';
    $files   = array('style', 'pagination', 'thickbox', $default_theme);
    $css_content = '';
    foreach ($files as $file)
    {
        if($css_content!='') $css_content .= "\n\n\n\n\n";
        
        $css_content .= "/* $file */\n\n\n";
        $file     = "css/$file.css";
        $fp       = fopen($file, 'r');
        $css_content .= fread($fp, filesize($file));
        fclose($fp);
    }
    
    return $css_content;
}
?>