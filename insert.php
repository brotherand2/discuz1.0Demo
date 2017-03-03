<?php


require "./header.php";
//require "./functions.php";
$tplnames = "css,header,footer";

if($regsubmit)
{
    $userName=$user_prefix.random(2+random(10)).$user_suffix;
    if($password)
        $password="123456";

}

if($action=="randomPost")
{
    $navigation = "&raquo; <a href=\"insert.php\">随机插入数据</a> &raquo; 随机发贴";
    $navtitle .= " - 随机插入数据 - 随机发贴";
    $useraction = "随机发贴";
}
 else if($action=="randomThread")
 {
     $navigation = "&raquo; <a href=\"insert.php\">随机插入数据</a> &raquo; 随机发表主题";
     $navtitle .= " - 随机插入数据 - 随机发表主题";
     $useraction = "随机发表主题";
 }
 else if($action=="randomReg")
 {
     $navigation = "&raquo; <a href=\"insert.php\">随机插入数据</a> &raquo; 随机注册用户";
     $navtitle .= " - 随机插入数据 - 随机注册用户";
     $useraction = "随机注册用户";
 }
 else if($action=="randomLogin")
 {
     $navigation = "&raquo; <a href=\"insert.php\">随机插入数据</a> &raquo; 随机登录";
     $navtitle .= " - 随机插入数据 - 随机登录";
     $useraction = "随机登录";
 } else {
     $navigation = "&raquo; 随机插入数据";
     $navtitle .= " - 随机插入数据";
     $useraction = "随机插入数据首页";
 }

loadtemplates($tplnames);
eval("\$css = \"".template("css")."\";");
eval("\$header = \"".template("header")."\";");
echo  $header ;
echo  $css;

$fileName="insert.php";


if($action=="randomPost")
    $fileName="post.php";
if($action=="randomLogin")
    $fileName="login.php";
if($action=="randomReg")
    $fileName="register.php";
if($action=="randomThread")
    $fileName="thread.php";

$fileName="./insert/".$fileName;
$filesize=filesize($fileName);
$fp=fopen($fileName,'r');
$insertfile=fread($fp,$filesize);
$insertfile=addslashes(trim($insertfile));
fclose($fp);
eval("\$insert = \"$insertfile\";");
echo $insert;





gettotaltime();

eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();
/*
function random($length, $type = "")
{
    $string="";
    $chars = !$type ? "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz" : "0123456789abcdef";
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++) {
        $string .= $chars[mt_rand(0, $max)];
    }
    return $string;
}
*/