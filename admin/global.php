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


require "./header.php";

if(file_exists("install.php")) {
	@unlink("install.php");
	if(file_exists("install.php")) {
		die("您的安装程序 install.php 仍存在于服务器上，请通过 FTP 删除后才能进入系统设置。<br>Please delete install.php via FTP!");
	}
}

@set_time_limit(1000);

$useraction = "修改论坛系统设置";
//记录管理员操作
if($action && $action != "main" && $action != "header" && $action != "menu" && !strpos($action, "log")) {
	$extra = $semicolon = "";
	if(is_array($_GET)) {
		foreach($_GET as $key => $val) {
			if($key != "action" && $key != "sid") {
				$extra .= "$semicolon$key=$val";
				$semicolon = "; ";
			}
		}
	}

	@$fp = fopen("./datatemp/cplog.php", "a");
	@flock($fp, 3);
	@fwrite($fp, "$cdbuser\t$onlineip\t$timestamp\t$action\t$extra\n");
	@fclose($fp);
}
//显示操作完成后的信息
function cpmsg($message, $url_forward = "", $msgtype = "message") {
	if($GLOBALS[showmsgtype]) {
		showmessage($message, $url_forward);
	} else {
		extract($GLOBALS, EXTR_OVERWRITE);
		if($msgtype == "form") {
			$message = "<form method=\"post\" action=\"$url_forward\"><br><br><br>$message<br><br><br><br>\n".
	        		"<input type=\"submit\" name=\"confirmed\" value=\"确 定\"> &nbsp; \n".
        			"<input type=\"button\" value=\"返 回\" onClick=\"history.go(-1);\"></form><br>";
		} else {
			if($url_forward) {
				$message .= "<br><br><br><a href=\"$url_forward\">如果您的浏览器没有自动跳转，请点击这里</a>";
				$url_forward = url_rewriter($url_forward);
				$message .= "<script>setTimeout(\"redirect('$url_forward');\", 1250);</script>";
			} else {
				$message .= "<br><br><br><a href=\"javascript:history.go(-1);\" class=\"mediumtxt\">[ 点这里返回上一页 ]</a>";
			}
			$message = "<br><br><br>$message<br><br>";
        	}
        }

?>
<br><br><br><br><br><br><table cellspacing="0" cellpadding="0" border="0" width="60%" align="center">
<tr><td bgcolor="<?=$bordercolor?>"><table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>Discuz! 提示</td></tr><tr><td bgcolor="<?=$altbg2?>" align="center">
<table border="0" width="90%" cellspacing="0" cellpadding="0"><tr><td width="100%" align="center">
<?=$message?><br><br>
</td></tr></table></td></tr></table></td></tr></table><br><br><br>
<?

	cpfooter();
	cdbexit();
}
//论坛编辑显示
function showforum($forum, $id, $type = "") {
	$dot = array(1 => "<li>", 2 => "<li type=\"circle\">", 3 => "<li type=\"square\">");
	$url = $type == "group" ? "./index.php?gid=$forum[fid]" : "./forumdisplay.php?fid=$forum[fid]";
	$editforum = "<a href=\"admincp.php?action=forumdetail&fid=$forum[fid]\">[编辑]</a> ";
	$hide = !$forum[status] ? " (隐藏)" : NULL;
	echo $dot[$id]."<a href=\"$url\" target=\"_blank\"><b>$forum[name]</b>$hide</a> - 顺序：<input type=\"text\" name=\"order[{$forum[fid]}]\" value=\"$forum[displayorder]\" size=\"1\">".
		"&nbsp; 版主：<input type=\"text\" name=\"moderator[{$forum[fid]}]\" value=\"$forum[moderator]\" size=\"15\"> - ".
		"$editforum<a href=\"admincp.php?action=forumdelete&fid=$forum[fid]\">".
		"[删除]</a><br></li>\n";
}
//常规选项,表格显示
function showtype($name, $type = "") {
	global $bordercolor, $borderwidth, $tablespace;
	if($type != "bottom") {
		if(!$type) {
			echo "</table></td></tr></table><br><br>\n";
		}
		if(!$type || $type == "top") {

?>
<a name="#<?=$name?>"></a>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header">
<td colspan="2"><?=$name?></td>
</tr>
<?

		}
	} else {
		echo "</table></td></tr></table>\n";
	}
}
//常规选项,表格中每一行的显示
function showsetting($setname, $varname, $value, $type = "radio", $comment = "", $width = "60%") {
	global $altbg1, $altbg2;
	$setname = "<b>$setname</b>";
	if($comment) {
		$setname .= "<br>$comment";
	}
	$aligntop = $type == "textarea" || $width != "60%" ?  "valign=\"top\"" : NULL;
	echo "<tr><td width=\"$width\" bgcolor=\"$altbg1\" $aligntop>$setname</td>\n".
		"<td bgcolor=\"$altbg2\">\n";

	if($type == "radio") {
		$value ? $checktrue = "checked" : $checkfalse = "checked";
		echo "<input type=\"radio\" name=\"$varname\" value=\"1\" $checktrue> 是 &nbsp; \n".
			"<input type=\"radio\" name=\"$varname\" value=\"0\" $checkfalse> 否\n";
	} elseif($type == "text") {
		echo "<input type=\"text\" size=\"30\" value=\"$value\" name=\"$varname\">\n";
	} elseif($type == "textarea") {
		echo "<textarea rows=\"5\" name=\"$varname\" cols=\"30\">".htmlspecialchars($value)."</textarea>";
	} else {
		echo $type;
	}
	echo "</td></tr>\n";
}
//后台左边菜单的显示
function showmenu($title, $menus = array())
 {
	global $borderwidth, $bordercolor, $tablespace, $headertext, $altbg1, $altbg2, $menucount, $expand;

?>
<tr><td bgcolor="<?=$altbg1?>"><a name="#<?=$menucount?>"></a>
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<?

	if(is_array($menus))
	{
		$menucount++;
		$expanded = preg_match("/(^|_)$menucount($|_)/is", $expand);
		echo "<tr><td width=\"100%\" class=\"header\"><img src=\"images/common/".($expanded ? "minus" : "plus").".gif\"><a href=\"admincp.php?action=menu&expand=$expand&change=$menucount#$menucount\" style=\"color: $headertext\">$title</td></tr>\n";
		if($expanded) {
			foreach($menus as $menu)
			{
				echo "<tr><td bgcolor=\"$altbg2\" onMouseOver=\"this.style.backgroundColor='$altbg1'\" onMouseOut=\"this.style.backgroundColor='$altbg2'\"><img src=\"images/common/spacer.gif\"><a href=\"".url_rewriter($menu[url])."\" target=\"main\">$menu[name]</a></td></tr>";
			}
		}
	} else {
		echo "<tr><td width=\"100%\" class=\"header\"><img src=\"images/common/plus.gif\"><a href=\"".url_rewriter($menus)."\" target=\"main\" style=\"color: $headertext\">$title</a></td></tr>\n";
	}
	echo "</table></td></tr></table></td></tr>";
}
//table 表名,startfrom,currize,数据库字符串大小
function sqldumptable($table, $startfrom = 0, $currsize = 0) {
	global $db, $multivol, $sizelimit, $startrow;

	$offset = 64;//一次插入多少条数据
	if(!$startfrom)//从第0条开始插入数据,说明还没建表
	{
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
		$tabledump .= "CREATE TABLE $table (\n";

		$firstfield = 1;

		$fields = $db->query("SHOW FIELDS FROM $table");
		while ($field = $db->fetch_array($fields)) {
			if (!$firstfield) {//非0表示1行结束,换行新1行
				$tabledump .= ",\n";
			} else {
				$firstfield = 0;//0表示下次要换行
			}
			$tabledump .= "\t$field[Field] $field[Type]";

			if ($field[Null] != "YES") {
				$tabledump .= " NOT NULL";
			}

			if (!empty($field["Default"])) {
				$tabledump .= " default '$field[Default]'";
			}
			if ($field[Extra] != "") {
				$tabledump .= " $field[Extra]";
			}
		}

		$db->free_result($fields);
        //查找所有主键,唯一键
		$keys = $db->query("SHOW KEYS FROM $table");
		while ($key = $db->fetch_array($keys)) {
			$kname = $key['Key_name'];
			if ($kname != "PRIMARY" and $key['Non_unique'] == 0) {
				$kname="UNIQUE|$kname";
			}
			if(!is_array($index[$kname]))
			{
				$index[$kname] = array();
			}
			$index[$kname][] = $key['Column_name'];
		}

		$db->free_result($keys);

		while(list($kname, $columns) = @each($index)){
			$tabledump .= ",\n";
			$colnames = implode($columns, ",");

			if($kname == "PRIMARY"){
				$tabledump .= "\tPRIMARY KEY ($colnames)";
			} else {
				if (substr($kname,0,6) == "UNIQUE") {
					$kname = substr($kname,7);
				}

				$tabledump .= "\tKEY $kname ($colnames)";

			}
		}

		$tabledump .= "\n);\n\n";
	}

	$tabledumped = 0;//非分卷模式下表是否开始复制
	$numrows = $offset;//
	while(($multivol && $currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) || (!$multivol && !$tabledumped))
	{//$numrows == $offset表示当前表中的数据还没有插完
		$tabledumped = 1;
		if($multivol) {
			$limitadd = "LIMIT $startfrom, $offset";
			$startfrom += $offset;
		}

		$rows = $db->query("SELECT * FROM $table $limitadd");
		$numfields = $db->num_fields($rows);//多少列
		$numrows = $db->num_rows($rows);//多少行
		while ($row = $db->fetch_row($rows)) //生成插入数据 SQL
		{
			$comma = "";
			$tabledump .= "INSERT INTO $table VALUES(";
			for($i = 0; $i < $numfields; $i++) {
				$tabledump .= $comma."'".mysql_escape_string($row[$i])."'";
				$comma = ",";
			}
			$tabledump .= ");\n";
		}
	}

	$startrow = $startfrom;//下一个文件从哪一条开始插入
	$tabledump .= "\n";
	return $tabledump;
}

function splitsql($sql){
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return($ret);
}

function cpheader() {
	if($GLOBALS[showmsgtype] == "cdb_with_header") {
		loadtemplates("css,header,footer,showmessage");
		extract($GLOBALS, EXTR_OVERWRITE);
		eval("\$css = \"".template("css")."\";");
		eval("\$header = \"".template("header")."\";");
		echo $header;
	} elseif(!$GLOBALS[showmsgtype]) {
		global $css, $bgcode, $text, $charset;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<?=$css?>

<script language="JavaScript">
function checkall(form) {
	for(var i = 0;i < form.elements.length; i++) {
		var e = form.elements[i];
		if (e.name != 'chkall' && e.disabled != true) {
			e.checked = form.chkall.checked;
		}
	}
}
function Popup(url, window_name, window_width, window_height) {
	var settings = "toolbar=no,location=no,directories=no,"+"status=no,menubar=no,scrollbars=yes,"+"resizable=yes,width="+window_width+",height="+window_height;
	NewWindow = window.open(url, window_name, settings);
}

function redirect(url) {
	window.location.replace(url);
}

function redirect(url) {
	window.location.replace(url);
}
</script>
</head>

<body <?=$bgcode?> text="<?=$text?>" leftmargin="10" topmargin="10">
<br>
<?
	}

}

function cpfooter() {
	if($GLOBALS[showmsgtype] == "cdb_with_header") {
		extract($GLOBALS, EXTR_OVERWRITE);
		gettotaltime();
		$debuginfo = $GLOBALS["debuginfo"];
		eval("\$footer = \"".template("footer")."\";");
		echo $footer;
		cdb_output();
	} elseif(!$GLOBALS[showmsgtype]) {
		global $bordercolor, $text, $version;

?>
<br><br><br><br><hr size="0" noshade color="<?=$bordercolor?>" width="80%"><center><font style="font-size: 11px; font-family: Tahoma, Verdana, Arial">
Powered by <a href="http://forum.crossday.com" style="color: <?=$text?>"><b>Discuz!</b> <?=$version?></a> &nbsp;&copy; 2002, <b>
<a href="http://www.crossday.com" target="_blank" style="color: <?=$text?>">Crossday Studio</a></b></font>

</body>
</html>
<?
	}
}

function dirsize($dir) {
	@$dh = opendir($dir);
	$size = 0;
	while ($file = @readdir($dh)) {
		if ($file != "." and $file != "..") {
			$path = $dir."/".$file;
			if (is_dir($path)) {
				$size += dirsize($path);
			} elseif (is_file($path)) {
				$size += filesize($path);
			}
		}
	}
	@closedir($dh);
	return $size;
}


?>