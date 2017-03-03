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


if(!defined("IN_CDB")) {
        die("Access Denied");
}

if($action == "export" && $exportsubmit && $type)
{
	$sqldump = "";
	$time = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);
	if($type == "all") {
		$tables = array("announcements", "attachments", "banned", "buddys", "caches", "favorites", "forumlinks",
			"forums", "members", "memo", "news", "posts", "searchindex", "sessions", "settings", "smilies",
			"stats", "subscriptions", "templates", "themes", "threads", "u2u", "usergroups", "words");
		$note = "全部备份";
	} elseif($type == "standard") {//不包含"searchindex", "sessions"
		$tables = array("announcements", "attachments", "banned", "buddys", "caches", "favorites", "forumlinks",
			"forums", "members", "memo", "news", "posts", "settings", "smilies", "stats", "subscriptions",
			"themes", "threads", "u2u", "usergroups", "words");
		$note = "标准备份";
	} elseif($type == "majority") {
		$tables = array("attachments", "buddys", "caches", "favorites", "forums", "members", "posts", "settings", "smilies",
			"stats", "threads", "usergroups");
		$note = "精简备份";
	} elseif($type == "mini") {
		$tables = array("announcements", "banned", "buddys", "caches", "forumlinks", "forums", "members", "news",
			"settings", "smilies", "stats", "themes", "usergroups", "words");
		$note = "最小备份";
	}

	$sqldump = "";
	if($multivol)
	{
		if($saveto == "server")
		{
			$volume = intval($volume) + 1;//sql 分件分为几个文件保存,哪一卷
			$tableid = $tableid ? $tableid - 1 : 0;//当从要从哪张表开始保存,如上一次要保存第10张时,sqldump 文件大小还没有超过超过限制大小,但进入 for 循环,插入一部份数据后,文件大小 超过超过限制大小,for 循环返回,表 ID这时为11,要从下一个文件开始拷贝,要从拷贝这张表的剩余部分,所以 tablieid 要-1,
			$startfrom = intval($startfrom);//对于要对表中插入数据,不是一次数插入,是先插入0-63,再依次64-127,...,起始索引
			for($i = $tableid; $i < count($tables) && strlen($sqldump) < $sizelimit * 1000; $i++)
			{
				$sqldump .= sqldumptable($tablepre.$tables[$i], $startfrom, strlen($sqldump));
				$startfrom = 0;//下一张表,重新从第0条开始插入
			}

			$tableid = $i;
		}
		else {
			cpheader();
			cpmsg("只有备份到服务器才能使用分卷备份功能。");
		}
	}

	else {
		foreach($tables as $table) {
			$sqldump .= sqldumptable($tablepre.$table);
		}
	}

	$dumpfile = substr($filename, 0, strrpos($filename, "."))."-%s".strrchr($filename, ".");
	if(trim($sqldump))
	{
		$sqldump = "# Identify: ".base64_encode("$timestamp,$version,$type,$multivol,$volume")."\n".
			"#\n".
			"# Discuz! 数据备份(Discuz! Data Dump".($multivol ? " Volume $volume" : NULL).")\n".
			"# 版本: Discuz! $version\n".
			"# 备份时间: $time\n".
			"# 备份方式: $note\n".
			"# 数据库前缀: $tablepre\n".
			"#\n".
			"# 官方网站: http://www.Discuz.net\n".
			"# 请随时访问以上地址以获得最新的软件升级信息\n".
			"# --------------------------------------------------------\n\n\n".
			$sqldump;

		if($saveto == "local") {
			header("Content-Type: application/x-sql"); 
			header("Content-Disposition: attachment; filename=cdb_".date("ymd").".sql");
			header("Content-Description: Discuz! Data Dump");
			echo $sqldump;
			cdbexit();
		} elseif($saveto == "server")
		{
			cpheader();
			if($filename != "")
			{
				@$fp = fopen(($multivol ? sprintf($dumpfile, $volume) : $filename), "w");
				@flock($fp, 3);
				if(@!fwrite($fp, $sqldump)) {
					@fclose($fp);
					cpmsg("数据文件无法保存到服务器，请检查目录属性。");
				} elseif($multivol) {
					cpmsg("分卷备份：数据文件 #$volume 成功创建，程序将自动继续。", "admincp.php?action=export&type=$type&saveto=server&filename=$filename&multivol=1&sizelimit=$sizelimit&volume=$volume&tableid=$tableid&startfrom=$startrow&exportsubmit=yes");
				} else {
					cpmsg("数据成功备份至服务器 <a href=\"$filename\">$filename</a> 中。");
				}
			} else {
				cpmsg("您没有输入备份文件名，请返回修改。");
			}
		}
	}
	else {
		if($multivol) {
			$volume--;//当前 SQL 语句为空,要减少当前分卷
			$filelist = "<ul>";
			for($i = 1; $i <= $volume; $i++) {
				$filename = sprintf($dumpfile, $i);
				$filelist .= "<li><a href=\"$filename\">$filename\n";
			}
			cpheader();
			cpmsg("恭喜您，全部 $volume 个备份文件成功创建，备份完成。\n<br>$filelist");
		} else {
			cpheader();
			cpmsg("备份出错，数据表没有内容。");
		}
	}
}

cpheader();

if($action == "export")
{
	if(!$exportsubmit)
	{

?>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>数据备份功能根据您的选择备份全部论坛贴子和设置数据，导出的数据文件可用“数据恢复”功能或 phpMyAdmin 导入。</ul>
<ul><li>附件的备份只需手工转移 attachments 目录和文件即可，Discuz! 不提供单独备份。</ul>
<ul><li>强烈建议：备份到服务器请使用 .sql 作为扩展名，这将给日后的维护带来很大方便。</ul>
</td></tr></table></td></tr></table>

<br><br><form name="backup" method="post" action="admincp.php?action=export">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">数据备份方式</td></tr>
<tr>
<td bgcolor="<?=$altbg1?>" width="40%"><input type="radio" value="all" name="type"> 全部备份</td>
<td bgcolor="<?=$altbg2?>" width="60%">包括论坛全部数据表数据</td></tr>

<tr>
<td bgcolor="<?=$altbg1?>"><input type="radio" value="standard" checked name="type"> 标准备份(推荐)</td>
<td bgcolor="<?=$altbg2?>">除模板以外的全部数据</td></tr>

<tr>
<td bgcolor="<?=$altbg1?>"><input type="radio" value="majority" name="type"> 精简备份</td>
<td bgcolor="<?=$altbg2?>">仅包括用户、板块设置及贴子数据</td></tr>

<tr>
<td bgcolor="<?=$altbg1?>"><input type="radio" value="mini" name="type" > 最小备份</td>
<td bgcolor="<?=$altbg2?>">仅包括用户、板块设置及系统设置数据</td></tr>

<tr bgcolor="<?=$altbg2?>" class="header"><td colspan="2">选择目标位置</td></tr>

<tr bgcolor="<?=$altbg2?>">
<td colspan="2"><input type="radio" value="local" name="saveto" onclick="this.form.filename.disabled=this.checked;if(this.form.multivol.checked) {alert('注意：\n\n备份到本地无法使用分卷备份功能。');this.form.multivol.checked=false;this.form.sizelimit.disabled=true;}"> 备份到本地</td></tr>
<tr bgcolor="<?=$altbg2?>"><td><input type="radio" value="server" checked name="saveto" onclick="this.form.filename.disabled=!this.checked"> 备份到服务器</td>
<td><input type="text" size="40" name="filename" value="./datatemp/cdb_<?=date("md")."_".random(5)?>.sql" onclick="alert('注意：\n\n数据文件保存在服务器的可见目录下，其他人有    \n可能下载得到这些文件，这是不安全的。因此请    \n在使用随机文件名的同时，及时删除备份文件。');"></td>
</tr>


<tr class="header"><td colspan="2">使用分卷备份</td></tr>
<tr bgcolor="<?=$altbg2?>">
<td><input type="checkbox" name="multivol" value="1" onclick="this.form.sizelimit.disabled=!this.checked;if(this.checked && this.form.saveto[1].checked!=true) {alert('注意：\n\n只有选择备份到服务器才能使用分卷备份功能。');this.form.saveto[1].checked=true;this.form.filename.disabled=false;}"> 文件长度限制(KB)</td>
<td><input type="text" size="40" name="sizelimit" value="1024" disabled></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="exportsubmit" value="备份数据"></center></form>
<?

	}

}
elseif($action == "import")
{
	 if(!$importsubmit && !$deletesubmit)
	 {
	 	$exportlog = array();
	 	$dir = dir("./datatemp");
		while($entry = $dir->read()) {
			$entry = "./datatemp/$entry";
			if (is_file($entry) && strtolower(strrchr($entry, ".")) == ".sql")
			{
				$filesize = filesize($entry);
				$fp = fopen($entry, "r");
				$identify = explode(",", base64_decode(preg_replace("/^# Identify:\s*([ \-\w]+)\s*/", "\\1", fgets($fp, 128))));
				fclose ($fp);
 
				$exportlog[$identify[0]] = array(	"version" => $identify[1],
									"type" => $identify[2],
									"multivol" => $identify[3],
									"volume" => $identify[4],
									"filename" => $entry,
									"size" => $filesize);
			}
		}
		$dir->close();
		krsort($exportlog);
		reset($exportlog);

		$exportinfo = "";
		foreach($exportlog as $dateline => $info)
		{
			$info[dateline] = is_int($dateline) ? gmdate("$dateformat $timeformat", $dateline + $timeoffset * 3600) : "未知";
			switch($info[type]) {
				case all: $info[type] = "全部"; break;
				case standard: $info[type] = "标准"; break;
				case majority: $info[type] = "精简"; break;
				case mini: $info[type] = "最小"; break;
			}
			$info[size] = sizecount($info[size]);
			$info[multivol] = $info[multivol] ? "是" : "否";
			$info[volume] = $info[multivol] ? $info[volume] : "";
			$exportinfo .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$info[filename]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><a href=\"$info[filename]\">".substr(strrchr($info[filename], "/"), 1)."</a></td>\n".
				"<td bgcolor=\"$altbg1\">$info[version]</td>\n".
				"<td bgcolor=\"$altbg2\">$info[dateline]</td>\n".
				"<td bgcolor=\"$altbg1\">$info[type]</td>\n".
				"<td bgcolor=\"$altbg2\">$info[size]</td>\n".
				"<td bgcolor=\"$altbg1\">$info[multivol]</td>\n".
				"<td bgcolor=\"$altbg2\">$info[volume]</td>\n".
				"<td bgcolor=\"$altbg1\"><a href=\"admincp.php?action=import&from=server&datafile1=$info[filename]&importsubmit=yes\">[导入]</a></td>\n";
		}

?>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>本功能在恢复备份数据的同时，将全部覆盖原有数据，请确定是否需要恢复，以免造成数据损失。</ul>
<ul><li>数据恢复功能只能恢复由当前版本 Discuz! 导出的数据文件，其他软件导出格式可能无法识别。</ul>
<ul><li>从本地恢复数据需要服务器支持文件上传并保证数据尺寸小于允许上传的上限，否则只能使用从服务器恢复。</ul>
<ul><li>如果您使用了分卷备份，只需按顺序恢复各分卷文件即可。</ul>
</td></tr></table></td></tr></table>

<br><form name="restore" method="post" action="admincp.php?action=import" enctype="multipart/form-data">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header">
<td colspan="2">数据恢复</td>
</tr>

<tr>
<td bgcolor="<?=$altbg1?>" width="40%"><input type="radio" name="from" value="server" checked onclick="this.form.datafile1.disabled=!this.checked;this.form.datafile.disabled=this.checked">从服务器(填写文件名或 URL)：</td>
<td bgcolor="<?=$altbg2?>" width="60%"><input type="text" size="40" name="datafile1" value="./datatemp/"></td></tr>

<tr>
<td bgcolor="<?=$altbg1?>" width="40%"><input type="radio" name="from" value="local" onclick="this.form.datafile1.disabled=this.checked;this.form.datafile.disabled=!this.checked">从本地文件：</td>
<td bgcolor="<?=$altbg2?>" width="60%"><input type="file" size="29" name="datafile" disabled></td></tr>

</table></td></tr></table><br><center>
<input type="submit" name="importsubmit" value="恢复数据"></center>
</form>

<br><form method="post" action="admincp.php?action=import">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="9">数据备份记录</td></tr>
<tr align="center" class="header"><td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>文件名</td><td>版本</td>
<td>备份时间</td><td>类型</td>
<td>尺寸</td><td>多卷</td>
<td>卷号</td><td>操作</td></tr>
<?=$exportinfo?>
</table></td></tr></table><br><center>
<input type="submit" name="deletesubmit" value="删除选定备份"></center></form>
<?

	 }
	 elseif($importsubmit)
	 {

		$readerror = 0;
		if($from == "server") {
			$datafile = $datafile1;
			$datafile_size = @filesize($datafile1);
		}
		@$fp = fopen($datafile, "r");
		if($datafile_size) {
			@flock($fp, 3);
			$sqldump = @fread($fp, $datafile_size);
		} else {
			$sqldump = @fread($fp, 99999999);
		}
		@fclose($fp);
		if(!$sqldump) {
			cpmsg("数据文件不存在：可能服务器不允许上传文件或尺寸超过限制。");
		} elseif(!strpos($sqldump, "CDB Forum Database Dump") && !strpos($sqldump, "Discuz! Data Dump")) {
			cpmsg("数据文件非 Discuz! 格式，无法导入。");
		} else {
			$sqlquery = splitsql($sqldump);
			unset($sqldump);
			foreach($sqlquery as $sql) {
				if(trim($sql) != "") {
					$db->query($sql);
				}
			}
			cpmsg("数据成功导入论坛数据库。");
		}

	}
	 elseif($deletesubmit)
	 {

		if(is_array($delete)) {
			foreach($delete as $filename) {
				@unlink($filename);
			}
			cpmsg("指定备份文件成功删除。");
		} else {
			cpmsg("您没有选择要删除的备份文件，请返回。");
		}

	}

}
elseif($action == "runquery")
{

	if(!$sqlsubmit) {

?>
<br><br><form method="post" action="admincp.php?action=runquery">
<table cellspacing="0" cellpadding="0" border="0" width="550" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan=2>Discuz! 数据库升级</td></tr>
<tr bgcolor="<?=$altbg1?>" align="center">
<td valign="top">请将数据库升级语句粘贴在下面：<br><textarea cols="85" rows="10" name="queries"></textarea><br>
<br><center>注意：为确保升级成功，请不要修改 SQL 语句的任何部分。<br><br>
<input type="submit" name="sqlsubmit" value="数据库升级"></center>
</td>
</tr>
</table>
</td></tr></table>
</form></td></tr>
<?

	} else {

		$sqlquery = splitsql(str_replace(" cdb_", " $tablepre", $queries));
		foreach($sqlquery as $sql) {
			if(trim($sql) != "") {
				$db->query(stripslashes($sql), 1);
				$sqlerror = $db->error();
				if($sqlerror) {
					break;
				}
			}
		}

		cpmsg($sqlerror ? "升级错误，MySQL 提示：$sqlerror" : "Discuz! 数据结构成功升级。");
	}	

}
elseif($action == "optimize")
{

	$query = $db->query("SELECT VERSION()");
	$dbversion = $db->result($query, 0);
	if($dbversion < "3.23") {
		cpmsg("MySQL 版本低于 3.23，不支持优化功能。");
	} else {
?>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>数据表优化的功能如同磁盘整理程序，推荐您定期优化数据库以减少数据碎片，保持良好的存取和检索性能。</ul>
<ul><li>本功能需 MySQL 3.23 以上版本支持，当前服务器 MySQL 版本：<?=$dbversion?>。</ul>
</td></tr></table></td></tr></table>

<br><br><form name="optimize" method="post" action="admincp.php?action=optimize">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr align="center" class="header">
<td>优化</td><td>数据表名</td><td>类型</td><td>记录数</td>
<td>数据</td><td>索引</td><td>碎片</td></tr>
<?
		$optimizetable = "";
		$totalsize = 0;
		if(!$optimizesubmit) {
			$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
			while($table = $db->fetch_array($query)) {
				echo "<tr>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\"><input type=\"checkbox\" name=\"$table[Name]\" value=\"1\" checked></td>\n".
					"<td td onClick=\"document.optimize.$table[Name].checked = !document.optimize.$table[Name].checked;\" style=\"cursor: hand\" onmouseover=\"this.style.backgroundColor='$altbg1';\" onmouseout=\"this.style.backgroundColor='$altbg2';\" bgcolor=\"$altbg2\" align=\"center\">$table[Name]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$table[Type]</td>\n".
					"<td bgcolor=\"$altbg2\" align=\"center\">$table[Rows]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$table[Data_length]</td>\n".
					"<td bgcolor=\"$altbg2\" align=\"center\">$table[Index_length]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$table[Data_free]</td>\n".
					"</tr>\n";
				$totalsize += $table[Data_length] + $table[Index_length];
			}
			echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">共占用数据库：".sizecount($totalsize)."</td></tr></table><tr><td align=\"center\"><br><input type=\"submit\" name=\"optimizesubmit\" value=\"优化数据表\"></td></tr>\n";
		} else {
			$db->query("ALTER TABLE $table_threads ORDER BY lastpost");
			$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
			while($table = $db->fetch_array($query)) {
				$tablename = ${$table[Name]};
				if(!$tablename) {
					$tablename = "未优化";
				} else {
					$tablename = "优化";
					$db->query("OPTIMIZE TABLE $table[Name]");
				}
				echo "<tr>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$tablename</td>\n".
					"<td bgcolor=\"$altbg2\" align=\"center\">$table[Name]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$table[Type]</td>\n".
					"<td bgcolor=\"$altbg2\" align=\"center\">$table[Rows]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">$table[Data_length]</td>\n".
					"<td bgcolor=\"$altbg2\" align=\"center\">$table[Index_length]</td>\n".
					"<td bgcolor=\"$altbg1\" align=\"center\">0</td>\n".
					"</tr>\n";
				$totalsize += $table[Data_length] + $table[Index_length];
			}
			echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">共占用数据库：".sizecount($totalsize)."</td></tr></table>";
		}
	}

	echo "</table></form>";
}

?>