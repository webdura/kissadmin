<? 
session_start();
if(isset($_REQUEST['theme'])) {
    $_SESSION['theme'] = $_REQUEST['theme'];
    header("Location: index.php");
}
if(!isset($_SESSION['theme']))
    $_SESSION['theme'] = 'blue';

switch ($_SESSION['theme']) {
    case 'blue':
            $theme = 'blue';
            break;
    case 'cammo':
            $theme = 'cammo';
            break;
    case 'green':
            $theme = 'green';
            break;
    case 'orange':
            $theme = 'orange';
            break;
    case 'paisley_royal':
            $theme = 'paisley_royal';
            break;
    case 'pink_hearts':
            $theme = 'pink_hearts';
            break;
    case 'pink_passion':
            $theme = 'pink_passion';
            break;
    case 'retro_70':
            $theme = 'retro_70';
            break;
    default:
            $_SESSION['theme'] = $theme = 'blue';
            break;
}
//echo $theme;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>GNet Mail</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/<?=$theme?>_theme.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
    <div id="head">
        <div id="head_left">
            <div class="head_left"></div>
            <div class="logo"><img src="images/logo.png" align="center"></div>
            <div class="head_right"></div>
        </div>
        <div id="head_right">
            <img src="images/KISSAdmin_logo.png" align="right">
            <div class="right_links">
                <div class="login">
                    Logged in as "Vinayak"&nbsp;&nbsp;&nbsp;|&nbsp;
                    <a href="javascript:void(0);" onclick="return false;settingsTab();">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="index.php">Logout</a>
                </div>
                <div class="submenu">
                    <a href="index.php?theme=blue" title="Admin">Blue</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=cammo" title="Admin">Cammo</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=green" title="Admin">Green</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=orange" title="Admin">Orange</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=paisley_royal" title="Admin">Paisley</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=pink_hearts" title="Admin">Hearts</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=pink_passion" title="Admin">Passion</a>
                    &nbsp;|&nbsp;
                    <a href="index.php?theme=retro_70" title="Admin">Retro</a>
                </div>
            </div>
        </div>
    </div>
    <div id="top_buttons">
        <ul>
            <li><a href="index.php" class="summary_btn"></a></li>
            <li><a href="index.php" class="clients_btn"></a></li>
            <li><a href="index.php" class="quotation_btn"></a></li>
            <li><a href="index.php" class="invoices_btn"></a></li>
            <li><a href="index.php" class="statement_btn"></a></li>
            <li><a href="index.php" class="payment_btn"></a></li>
            <li><a href="index.php" class="pricing_btn"></a></li>
        </ul> 
    </div>
    <div id="maincontent">
        
        <div id="subcontent">
            <div class="page_header">
                <div class="fleft padding_top">
                    <h2 class="">Clients</h2>
                    <a class="addnew fleft" href="index.php"><div class="fleft btn_style">+</div><span>&nbsp;&nbsp;Add New</span></a>
                    <div class="search">
                        <input type="text" class="inputbox">&nbsp;&nbsp;<input type="button" class="btn_style" value="Search">
                    </div>
                </div>
                <div class="padding_top fcenter text_links">
                    <a href="index.php">All</a>
                    <? for($i=65;$i<=90;$i++) { ?> <a href="index.php"><?=chr($i)?></a> <? } ?>
                </div>
                <div class="fright pagination">
                    <a href="#" class="btn_style">first</a>&nbsp;<a href="#" class="btn_style">1</a>&nbsp;<a href="#" class="btn_style">2</a>&nbsp;<a href="#" class="btn_style">3</a>&nbsp;<a href="#" class="btn_style">last</a>
                </div>
            </div>
            <div class="contents">
                
                <table cellpadding="0" cellspacing="0" width="100%" class="list">
                    <tr>
                       <th>Col 1<a href="index.php" class="asc"></a><a href="index.php" class="desc"></a></th>
                       <th width="30%">Col 2<a href="index.php" class="asc"></a><a href="index.php" class="desc"></a></th>
                       <th width="30%">Col 3<a href="index.php" class="asc"></a><a href="index.php" class="desc"></a></th>
                    </tr>
                    <tr>
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr class="altrow">
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr>
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr class="altrow">
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr>
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr class="altrow">
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr>
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr class="altrow">
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr>
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                    <tr class="altrow">
                        <td>Col 1</td>
                        <td>Col 2</td>
                        <td><input type="button" class="btn_style" value="view">&nbsp;&nbsp;<input type="button" class="btn_style" value="edit">&nbsp;&nbsp;<input type="button" class="btn_style" value="delete"></td>
                    </tr>
                </table>
                
            </div>
        </div>
        
    </div>
    <div class="clear"></div>
    
</div>
</body>
</html>