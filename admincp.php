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


if(file_exists("install.php")) {
	@unlink("install.php");
	if(file_exists("install.php")) {
		die("您的安装程序 install.php 仍存在于服务器上，请通过 FTP 删除后才能进入系统设置。<br>Please delete install.php via FTP!");
	}
}

require "./admin/global.php";

$css = <<<EOT
<style type="text/css">
a:link,a:visited	{ text-decoration: none; color: $link }
select			{ font-family: 宋体; font-size: $fontsize; font-weight: normal; background-color: $altbg1; color: $tabletext }
body			{ scrollbar-base-color: $altbg1; scrollbar-arrow-color: $bordercolor; font-size: $fontsize; $bgcode }
table			{ font-family: $font; color: $tabletext; font-size: $fontsize }
textarea,input,object	{ font-family: $font; font-size: $fontsize; font-weight: normal; background-color: $altbg1; color: $tabletext }
.bold			{ font-weight: $bold }
.subject		{ font-size: $fontsize; color: $tabletext; font-family: $font; font-weight: $bold }
.post			{ font-size: $font3; font-weight: normal; font-family: $font }
.header			{ color: $headertext; font-family: $font; font-weight: $bold; font-size: $fontsize; $headerbgcode }
.category		{ font-family: $font; color: $cattext; font-size: $fontsize; $catbgcode }
.nav			{ font-family: $font; font-weight: $bold; font-size: $fontsize }
.smalltxt		{ font-size: 12px; color: $tabletext; font-family: Tohoma, Arial, Verdana }
.mediumtxt		{ font-size: $fontsize; font-family: $font; font-weight: normal; color: $text }
.navtd			{ font-size: $fontsize; font-family: $font; color: $headertext; text-decoration: none }
.multi			{ font-family: $font; color: $link; font-size: $fontsize }
</style>
EOT;

$navigation = "&raquo; 系统设置";
$navtitle .= " - 系统设置";

if(!$action || $action == "header" || $action == "menu")
{
	if(!$action)
	{

?>
<html>
<head>
<title>Discuz! 系统设置面板</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
</head>

<frameset cols="160,*" frameborder="no" border="0" framespacing="0" rows="*">
<frame name="menu" noresize scrolling="yes" src="admincp.php?action=menu&sid=<?=$sid?>">
<frameset rows="20,*" frameborder="no" border="0" framespacing="0" cols="*">
<frame name="header" noresize scrolling="no" src="admincp.php?action=header&sid=<?=$sid?>">
<frame name="main" noresize scrolling="yes" src="admincp.php?action=main&sid=<?=$sid?>">
</frameset></frameset></html>
<?

	} elseif($action == "header")
	{

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<?=$css?>
</head>

<body leftmargin="0" topmargin="0">
<table cellspacing="0" cellpadding="2" border="0" width="100%" height="100%" bgcolor="<?=$altbg2?>">
<tr valign="middle">
<td width="33%"><a href="http://www.crossday.com" target="_blank">Discuz! <?=$version?> 系统设置面板</a></td>
<td width="33%" align="center"><a href="http://www.Discuz.net" target="_blank">Discuz! 用户交流</a></td>
<td width="34%" align="right"><a href="index.php" target="_blank">论坛首页</a></TD>
</tr>
</table>
</body></html>
<?

	} elseif($action == "menu") {

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<?=$css?>
</head>

<body leftmargin="3" topmargin="3">

<br><table cellspacing="0" cellpadding="0" border="0" width="100%" align="center" style="table-layout: fixed">
<tr><td bgcolor="<?=$bordercolor?>">
<table width="100%" border="0" cellspacing="2" cellpadding="0">
<tr><td style="<?=$bgcode?>">
<table width="100%" border="0" cellspacing="3" cellpadding="<?=$tablespace?>">
<tr><td bgcolor="<?=$altbg1?>" align="center"><a href="admincp.php?action=menu&expand=1_2_3_4_5_6_7_8_9_10">[展开]</a> &nbsp; <a href="admincp.php?action=menu">[收缩]</a></td></tr>
<?
//change 是菜单的标记,只有有子菜单的才有标记
		if(preg_match("/(^|_)$change($|_)/is", $expand))
		{
			$expandlist = explode("_", $expand);
			$expand = $underline = "";//去掉标记,这样就不用显示子菜单
			foreach($expandlist as $count) {
				if($count != $change) {
					$expand .= "$underline$count";
					$underline = "_";
				}
			}
		} else {
			$expand .= $expand ? "_$change" : $change;
		}

		$pluginsarray = array();
		if(is_array($plugins))
		{
			foreach($plugins as $plugin)
			{
				if($plugin[name] && $plugarray[cpurl]) {
					$pluginsarray[] = array("name" => $plugin[name], "url" => $plugin[cpurl]);
				}
			}
		}

		$menucount = 0;
		showmenu("面板首页", "admincp.php?action=main");
		showmenu("常规选项", "admincp.php?action=settings");
		showmenu("论坛设置", array(	array("name" => "添加论坛", "url" => "admincp.php?action=forumadd"),
						array("name" => "论坛编辑", "url" => "admincp.php?action=forumsedit"),
						array("name" => "论坛合并", "url" => "admincp.php?action=forumsmerge")));
		showmenu("用户设置", array(	array("name" => "用户编辑", "url" => "admincp.php?action=members"),
						array("name" => "用户组编辑", "url" => "admincp.php?action=usergroups"),
						array("name" => "IP 禁止", "url" => "admincp.php?action=ipban")));
		showmenu("新闻公告", array(	array("name" => "论坛公告", "url" => "admincp.php?action=announcements"),
						array("name" => "首页新闻", "url" => "admincp.php?action=news")));
		showmenu("其他设置", array(	array("name" => "界面方案", "url" => "admincp.php?action=themes"),
						array("name" => "联盟论坛", "url" => "admincp.php?action=forumlinks"),
						array("name" => "词语过滤", "url" => "admincp.php?action=censor"),
						array("name" => "Smilies 编辑", "url" => "admincp.php?action=smilies")));
		showmenu("数据管理", array(	array("name" => "数据备份", "url" => "admincp.php?action=export"),
						array("name" => "数据恢复", "url" => "admincp.php?action=import"),
						array("name" => "数据库升级", "url" => "admincp.php?action=runquery"),
						array("name" => "数据表优化", "url" => "admincp.php?action=optimize")));
		showmenu("论坛维护", array(	array("name" => "附件编辑", "url" => "admincp.php?action=attachments"),
						array("name" => "批量删贴", "url" => "admincp.php?action=prune"),
						array("name" => "短消息清理", "url" => "admincp.php?action=u2uprune")));
		showmenu("系统工具", array(	array("name" => "论坛通知", "url" => "admincp.php?action=newsletter"),
						array("name" => "模板编辑", "url" => "admincp.php?action=templates"),
						array("name" => "更新缓存", "url" => "admincp.php?action=flush"),
						array("name" => "重建统计数据", "url" => "admincp.php?action=chooser")));
		showmenu("管理记录", array(	array("name" => "密码错误记录", "url" => "admincp.php?action=illegallog"),
						array("name" => "版主管理记录", "url" => "admincp.php?action=modslog"),
						array("name" => "系统管理记录", "url" => "admincp.php?action=cplog")));
		showmenu("插件配置", $pluginsarray);
		showmenu("退出面板", "./member.php?action=logout&referer=admincp.php%3Faction%3Dmain");

?>
</table></td></tr></table></td></tr></table>

</body>
</html>
<?

	}

}
else
{

	if(!$isadmin) {
		cpheader();
		cpmsg("只有管理员才能进入系统设置！");
	}

	if ($action=="main")
	{
		$serverinfo = PHP_OS." / PHP v".PHP_VERSION;
		$serverinfo .= @ini_get("safe_mode") ? " 安全模式" : NULL;
		$dbversion = $db->result($db->query("SELECT VERSION()"), 0);

		if(@ini_get("file_uploads")) {
			$fileupload = "允许 - 文件 ".ini_get("upload_max_filesize")." - 表单：".ini_get("post_max_size");
		} else {
			$fileupload = "<font color=\"red\">禁止</font>";
		}

		$forumselect = $groupselect = "";
		$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups ORDER BY status, creditslower");
		while($group = $db->fetch_array($query)) {
			$groupselect .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
		}
		$query = $db->query("SELECT fid, name FROM $table_forums WHERE type='forum' OR type='sub'");
		while($forum = $db->fetch_array($query)) {
			$forumselect .= "<option value=\"$forum[fid]\">$forum[name]</option>\n";
		}

		$dbsize = 0;
		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'", 1);
		while($table = $db->fetch_array($query)) {
			$dbsize += $table[Data_length] + $table[Index_length];
		}
		$dbsize = $dbsize ? sizecount($dbsize) : "未知";

		$attachsize = dirsize("./$attachdir");
		$attachsize = $attachsize ? sizecount($attachsize) : "未知";

		cpheader();

?>
<font class="mediumtxt">
<b>欢迎光临 <a href="http://www.Discuz.net" target="_blank">Discuz! <?=$version?></a> 系统设置面板</b><br>
版权所有&copy; <a href="http://www.crossday.com" target="_blank">Crossday Studio</a>, 2002.

<br><br><br><table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="3">快 捷 方 式</td></tr>

<form method="post" action="admincp.php?action=forumdetail"><tr bgcolor="<?=$altbg2?>"><td>编辑论坛</td>
<td><select name="fid"><?=$forumselect?></select></td><td><input type="submit" value="提 交"></td></tr></form>

<form method="post" action="admincp.php?action=usergroups&type=detail"><tr bgcolor="<?=$altbg1?>"><td>编辑用户组权限</td>
<td><select name="id"><?=$groupselect?></td><td><input type="submit" value="提 交"></td></tr></form>

<form method="post" action="admincp.php?action=members"><tr bgcolor="<?=$altbg2?>"><td>编辑用户</td>
<td><input type="text" size="25" name="username"></td><td><input type="submit" name="searchsubmit" value="提 交"></td></tr></form>

<form method="post" action="admincp.php?action=export&type=standard&saveto=server"><tr bgcolor="<?=$altbg1?>"><td>标准备份到服务器</td>
<td><input type="text" size="25" name="filename" value="./datatemp/cdb_<?=date("md")."_".random(5)?>.sql"></td><td><input type="submit" name="exportsubmit" value="提 交"></td></tr></form>

</table></td></tr></table></td></tr></table><br><br>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">系 统 信 息</td></tr>
<tr bgcolor="<?=$altbg2?>"><td width="50%">服务器环境</td><td><?=$serverinfo?></td></tr>
<tr bgcolor="<?=$altbg1?>"><td>MySQL 版本</td><td><?=$dbversion?></td></tr>
<tr bgcolor="<?=$altbg2?>"><td>附件上传许可</td><td><?=$fileupload?></td></tr>
<tr bgcolor="<?=$altbg1?>"><td>数据库占用</td><td><?=$dbsize?></td></tr>
<tr bgcolor="<?=$altbg2?>"><td>附件文件占用</td><td><?=$attachsize?></td></tr>
</table></td></tr></table></td></tr></table><br><br>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">开 发 团 队</td></tr>
<tr bgcolor="<?=$altbg2?>"><td width="50%">程序设计</td><td><a href="http://www.crossdasy.com" target="_blank">Crossday</a></td></tr>
<tr bgcolor="<?=$altbg1?>"><td>插件开发</td><td><a href="http://www.nucpp.com">KnightE</a>, <a href="http://www.zc18.com/" target="_blank">feixin</a>, <a href="http://truehome.net" target="_blank">老兵酒吧</a></td></tr>
<tr bgcolor="<?=$altbg2?>"><td>美工支持</td><td><a href="http://www.crossdasy.com" target="_blank">Crossday</a>, <a href="http://tyc.udi.com.tw/cdb" target="_blank">Tyc</a>, <a href="http://smice.net/~youran/cdb/index.php" target="_blank">星蚀</a>, <a href="http://www.cnmaya.org" target="_blank">狐狸糊涂</a></td></tr>
<tr bgcolor="<?=$altbg1?>"><td>技术支持</td><td><a href="mailto:cdb@crossday.com">cdb@crossday.com</a></td></tr>
</table></td></tr></table></td></tr></table>
<?

	} elseif($action == "settings") {
		include "./admin/settings.php";
	} elseif($action == "forumadd" || $action == "forumsedit" || $action == "forumsmerge" || $action == "forumdetail" || $action == "forumdelete") {
		include "./admin/forums.php";
	} elseif($action == "members" || $action == "memberprofile" || $action == "usergroups" || $action == "ipban") {
		include "./admin/members.php";
	} elseif($action == "announcements" || $action == "news") {
		include "./admin/announcements.php";
	} elseif($action == "themes" || $action == "forumlinks" || $action == "censor" || $action == "smilies" || $action == "flush") {
		include "./admin/misc.php";
	} elseif($action == "export" || $action == "import" || $action == "runquery" || $action == "optimize") {
		include "./admin/database.php";
	} elseif($action == "attachments") {
		include "./admin/attachments.php";
	} elseif($action == "chooser") {
		include "./admin/chooser.php";
	} elseif($action == "prune" || $action == "u2uprune") {
		include "./admin/prune.php";
	} elseif($action == "newsletter") {
		include "./admin/newsletter.php";
	} elseif($action == "templates" || $action == "tpladd" || $action == "tplresetall" || $action == "tpldownload" || $action == "tpledit" || $action == "tpldelete" || $action == "tplreset") {
		include "./admin/templates.php";
	} elseif($action == "illegallog" || $action == "modslog" || $action == "cplog") {
		include "./admin/logs.php";
	}else if($action=="plugin")
	{
		include "./admin/randomInsert.php";
	}
	if(!$nofooter) {
		cpfooter();
	}

}

cdb_output();

?>