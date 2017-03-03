<?php
//-----------------------------------------------------------------------------
//    Discuz! Board 1.0 Standard - Discuz! 中文论坛 (PHP & MySQL) 1.0 标准版
//-----------------------------------------------------------------------------
//    Copyright(C) Dai Zhikang, Crossday Studio, 2002. All rights reserved
//
//    Crossday 工作室 www.crossday.com    *Discuz! 技术支持 www.Discuz.net
//-----------------------------------------------------------------------------
//  请详细阅读 Discuz! 授权协议,查看或使用 Discuz! 的任何部分意味着完全同意
//  协议中的全部条款,请举手之劳支持国内软件事业,严禁一切违反协议的侵权行为.
//-----------------------------------------------------------------------------
// Discuz! 专注于提供高效强大的论坛解决方案,如用于商业用途,您必须购买使用授权!
//-----------------------------------------------------------------------------

// CDB 论坛首页新帖调用程序 whatsnew.php 2.0.0 GOLD

// 本程序使用方法见 utilities/whatsnew.txt

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$num = 10;				// 显示多少条论坛新贴
$forumurl = "http://localhost/cdb";	// 论坛 URL 地址
$length = 50;				// 标题显示最大长度(字符数)
$smdir = "images/smilies";		// Smilies 相对论坛路径
$pre = "□-";				// 标题前字符,如显示为贴子图标,请设置为 "icon"

require "./config.php";
require "./functions.php";

mysql_connect($dbhost, $dbuser, $dbpw);
mysql_select_db($dbname);

$fidin = $_GET["fidin"];
$fidout = $_GET["fidout"];

$pre = addslashes($pre);

$forumadd = "";
$and = "";
$or = "";

if(strtolower($fidin) != "all") {
	$fidind = explode("_", $fidin);
	$fidoutd = explode("_", $fidout);
	foreach($fidind as $fid) {
		if(trim($fid)) {
			$forumadd .= "$or fid='$fid'";
			$or = " OR ";
			$and = " AND ";
		}
	}

	if($forumadd) {
		$forumadd = "( $forumadd )";
	}

	foreach($fidoutd as $fid) {
		if(trim($fid)) {
			$forumadd .= "$and fid<>'$fid'";
			$and = " AND ";
		}
	}
}

if($forumadd) {
	$forumadd = "AND $forumadd";
}	


$query = mysql_query("SELECT subject, tid, icon FROM $tablepre"."threads WHERE closed NOT LIKE 'moved|%' $forumadd ORDER BY lastpost DESC LIMIT 0, $num") or die(mysql_error());
while($threads = mysql_fetch_array($query)) {
	$threads[subject] = wordscut(cdbhtmlspecialchars($threads[subject]), $length - 2);
	if($pre == "icon") {
		if($threads[icon]) {
			$icon = "<img src='$forumurl/$smdir/$threads[icon]' valign='absmiddle' border='0'>";
		} else {
			$icon = "";
		}
		echo"document.write(\"$icon <a href=$forumurl/viewthread.php?tid=$threads[tid] target=_blank>$threads[subject]</a><br>\");\n";
	} else {
		echo"document.write(\"<a href=$forumurl/viewthread.php?tid=$threads[tid] target=_blank>$pre$threads[subject]</a><br>\");\n";
	}
}

?>

