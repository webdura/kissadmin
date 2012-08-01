<?php 
include('functions.php');


//$css_content .= "\n\n\n\n\n";
//$css_content .= "/* Company Theme */\n\n\n";
//if($site_logo!='') { 
//    $css_content .= "#head_left { background:url(images/company/$site_logo) no-repeat left top;	}\n";
//}
//$css_content .= "#head { background-color: $theme_head_bg; }\n";
//$css_content .= ".logout a, .settings a, .logout, .settings { color: $theme_head_color; }\n";
//$css_content .= ".sub_head { background-color: $theme_color1; }\n";
//$css_content .= ".sub_head { border-top:4px solid $theme_color2; }\n";
//$css_content .= ".thead { background-color: $theme_color3; }\n";
//$css_content .= ".row2 td, .row2 th { background-color: $theme_color4; }\n";
//
//$css_content .= ".sc_head { background-color: $theme_head_bg; color: $theme_head_color; }\n";
//$css_content .= ".sc_subhead { background-color: $theme_color1; }";
//$css_content .= ".total { background-color: $theme_color3; }\n";
//
//$css_content .= ".head_bg { background-color: $theme_head_bg; }\n";
//$css_content .= ".head_color { color: $theme_head_color; }\n";
//$css_content .= ".color1 { background-color: $theme_color1; }\n";
//$css_content .= ".color2 { border-top:4px solid $theme_color2; }\n";
//$css_content .= ".color3 { background-color: $theme_color3; }\n";
//$css_content .= ".color4 { background-color: $theme_color4; }\n";

if(!isset($_REQUEST['flag'])) {
    $templateModified = time();
    header("Content-Type: text/css");
    echo cssRead();
}