<?php 
include('functions.php');

$templateModified = time();
header("Content-Type: text/css");

$files   = array('style', 'pagination', 'thickbox', $theme_theme);
$content = '';
foreach ($files as $file)
{
    if($content!='') $content .= "\n\n\n\n\n";
    
    $content .= "/* $file */\n\n\n";
    $file     = "css/$file.css";
    $fp       = fopen($file, 'r');
    $content .= fread($fp, filesize($file));
    fclose($fp);
}

$content .= "\n\n\n\n\n";
$content .= "/* Company Theme */\n\n\n";
if($site_logo!='') { 
    $content .= "#head_left { background:url(images/company/$site_logo) no-repeat left top;	}\n";
}
$content .= "#head { background-color: $theme_head_bg; }\n";
$content .= ".logout a, .settings a, .logout, .settings { color: $theme_head_color; }\n";
$content .= ".sub_head { background-color: $theme_color1; }\n";
$content .= ".sub_head { border-top:4px solid $theme_color2; }\n";
$content .= ".thead { background-color: $theme_color3; }\n";
$content .= ".row2 td, .row2 th { background-color: $theme_color4; }\n";

$content .= ".sc_head { background-color: $theme_head_bg; color: $theme_head_color; }\n";
$content .= ".sc_subhead { background-color: $theme_color1; }";
$content .= ".total { background-color: $theme_color3; }\n";

$content .= ".head_bg { background-color: $theme_head_bg; }\n";
$content .= ".head_color { color: $theme_head_color; }\n";
$content .= ".color1 { background-color: $theme_color1; }\n";
$content .= ".color2 { border-top:4px solid $theme_color2; }\n";
$content .= ".color3 { background-color: $theme_color3; }\n";
$content .= ".color4 { background-color: $theme_color4; }\n";

echo $content;exit;