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

if($action == "themes" && $export)
{
	$query = $db->query("SELECT * FROM $table_themes WHERE themeid='$export'");
	if($theme = $db->fetch_array($query)) {
		$export = "";
		foreach($theme as $key => $val) {
			if($key != "themeid") {
				$export .= "$key = $val;\n";
			}
		}
		header("Content-Disposition: filename=cdb_$theme[themename].theme");
		header("Content-Type: application/octet-stream");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $export;

		cdbexit();
	} else {
		cpheader();
		cpmsg("指定的风格不存在，无法导出。");
	}
}

cpheader();

if($action == "themes" && !$export)
{

	if(!$themesubmit && !$importsubmit && !$edit && !$export) {

		$themeselect = "";
		$query = $db->query("SELECT themeid, themename, imgdir, boardimg FROM $table_themes");
		while($themeinfo = $db->fetch_array($query))
		{
			$themeselect .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$themeinfo[themeid]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" name=\"namenew[$themeinfo[themeid]]\" value=\"$themeinfo[themename]\" size=\"15\"></td>\n".
				"<td bgcolor=\"$altbg1\"><input type=\"text\" name=\"imgdirnew[$themeinfo[themeid]]\" value=\"$themeinfo[imgdir]\" size=\"18\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" name=\"boardimgnew[$themeinfo[themeid]]\" value=\"$themeinfo[boardimg]\" size=\"15\"></td>\n".
				"<td bgcolor=\"$altbg1\"><a href=\"admincp.php?action=themes&export=$themeinfo[themeid]\">[下载]</a></td>\n".
				"<td bgcolor=\"$altbg2\"><a href=\"admincp.php?action=themes&edit=$themeinfo[themeid]\">[详情]</a></td></tr>\n";
		}

?>
<form method="post" action="admincp.php?action=themes">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header" align="center">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>方案名称</td><td>图片目录</td><td>论坛 logo</td><td>导出</td><td>编辑</td></tr>
<?=$themeselect?>
<tr bgcolor="<?=$altbg2?>"><td height="1" colspan="6"></td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>">新增：</td>
<td bgcolor="<?=$altbg2?>"><input type="text" name="newname" size="15"></td>
<td bgcolor="<?=$altbg1?>"><input type="text" name="newimgdir" size="18"></td>
<td bgcolor="<?=$altbg2?>"><input type="text" name="newboardimg" size="15"></td>
<td bgcolor="<?=$altbg1?>" colspan="2">&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="themesubmit" value="更新界面设置"></center></form>

<br><form method="post" action="admincp.php?action=themes">
<table cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="90%" bgcolor="<?=$bordercolor?>" align="center">
<tr class="header"><td>导入界面方案 - 请将导出的文件内容粘贴如下</td></tr>
<tr><td bgcolor="<?=$altbg1?>" align="center"><textarea name="themedata" cols="80" rows="8"></textarea></td></tr>
</table><br><center><input type="submit" name="importsubmit" value="将风格文件导入 Discuz!"></center></form>
<?

	} elseif($themesubmit)
	{

		if(is_array($namenew))
		{
			foreach($namenew as $id => $val)
			{
				$db->query("UPDATE $table_themes SET themename='$namenew[$id]', imgdir='$imgdirnew[$id]', boardimg='$boardimgnew[$id]' WHERE themeid='$id'");
			}
		}

		if(is_array($delete))
		{
			$ids = $names = $comma1 = $comma2 = "";
			foreach($delete as $id) {
				$ids .= "$comma1'$id'";
				$comma1 = ", ";
				$names .= "$comma2'$namenew[$id]'";
				$comma2 = ", ";
			}
			$db->query("DELETE FROM $table_themes WHERE themeid IN ($ids)");
			$db->query("UPDATE $table_members SET theme='' WHERE theme IN ($names)");
		}

		if($newname) {
			$db->query("INSERT INTO $table_themes (themename, imgdir, boardimg)
				VALUES ('$newname', '$newimgdir', '$newboardimg')");
		}

		cpmsg("界面方案成功更新。");

	} elseif($importsubmit)
	{

		foreach(explode(";", $themedata) as $detail) {
			list($key, $val) = explode("=", trim($detail));
			$newtheme[trim($key)] = trim($val);
		}

		if($newtheme[themename]) {
			$query = $db->query("SELECT COUNT(*) FROM $table_themes WHERE themename='$newtheme[themename]'");
			if($db->result($query, 0)) {
				$newtheme[themename] .= "_".random(4);
				$renameinfo = "导入名称与现有界面重复，新界面被重命名为<br><b>$newtheme[themename]</b>。";
			}

			$fields = $comma = "";
			$query = $db->query("SHOW FIELDS FROM $table_themes");
			while($table = $db->fetch_row($query)) {
				$fields .= "$comma'".$newtheme[$table[0]]."'";
				$comma = ", ";
			}
			$db->query("INSERT INTO $table_themes VALUES ($fields)");

			cpmsg("{$renameinfo}界面方案成功导入。");
		} else {
			cpmsg("界面数据缺少必要元素，无法导入。");
		}

	} elseif($edit) {

		if(!$editsubmit) {

			$query = $db->query("SELECT * FROM $table_themes WHERE themeid='$edit'");
			if($themestuff = $db->fetch_array($query))  {

				echo "<form method=\"post\" action=\"admincp.php?action=themes&edit=$edit\">\n";

				showtype("编辑界面方案 - $themestuff[themename]", "top");
				showsetting("界面方案名称：", "namenew", $themestuff[themename], "text", "识别界面风格的标志，请勿使用空格或特殊符号");
				showsetting("论坛 logo：", "boardlogonew", $themestuff[boardimg], "text", "如使用 Flash 动画，请用逗号隔开 URL，宽度和高度，如“logo.swf,80,40”");
				showsetting("界面图片目录：", "imgdirnew", $themestuff[imgdir], "text", "");
				showsetting("Smilies 图片目录：", "smdirnew", $themestuff[smdir], "text", "");

				showtype("文字颜色设置");
				showsetting("禁用粗体字显示：", "noboldnew", $themestuff[nobold], "radio", "选择“是”前台将不显示任何粗体字内容");
				showsetting("字体设置：", "fontnew", $themestuff[font], "text", "多个候选字体间请用半角逗号 \",\" 分割");
				showsetting("字号设置：", "fontsizenew", $themestuff[fontsize], "text", "和使用字号、pt、px(推荐) 等单位");
				showsetting("超级链接文字颜色：", "linknew", $themestuff[link], "text", "");
				showsetting("表头文字颜色：", "headertextnew", $themestuff[headertext], "text", "");
				showsetting("栏目文字颜色：", "cattextnew", "$themestuff[cattext]", "text", "");
				showsetting("表格中文字颜色：", "tabletextnew", $themestuff[tabletext], "text", "");
				showsetting("页面中 (表格除外) 文字颜色：", "textnew", $themestuff[text], "text", "");

				showtype("表格与背景颜色设置");
				showsetting("表格边框颜色：", "bordercolornew", $themestuff[bordercolor], "text", "");
				showsetting("表格边框宽度：", "borderwidthnew", $themestuff[borderwidth], "text", "");
				showsetting("表格边缘空隙：", "tablespacenew", $themestuff[tablespace], "text", "");
				showsetting("页面背景：", "bgcolornew", $themestuff[bgcolor], "text", "输入 16 进制颜色或图片链接");
				showsetting("表头背景颜色：", "headercolornew", $themestuff[headercolor], "text", "输入 16 进制颜色或图片链接");
				showsetting("栏目背景颜色：", "catcolornew", $themestuff[catcolor], "text", "输入 16 进制颜色或图片链接");
				showsetting("表格背景配色 1：", "altbg1new", $themestuff[altbg1], "text", "建议设置为相对表格背景色 2 较深的颜色");
				showsetting("表格背景配色 2：", "altbg2new", $themestuff[altbg2], "text", "建议设置为相对表格背景色 1 较浅的颜色");
				showsetting("表格宽度：", "tablewidthnew", $themestuff[tablewidth], "text", "可设置为像素或百分比");

				showtype("", "bottom");

				echo "<br><center><input type=\"submit\" name=\"editsubmit\" value=\"更新方案设置\"></center></form>";
			} else {
				cpmsg("指定论坛界面不存在，请返回。");
			}

		} else {
			
			$db->query("UPDATE $table_themes SET themename='$namenew', bgcolor='$bgcolornew', altbg1='$altbg1new', altbg2='$altbg2new', link='$linknew', bordercolor='$bordercolornew', headercolor='$headercolornew', headertext='$headertextnew', catcolor='$catcolornew', tabletext='$tabletextnew', text='$textnew', borderwidth='$borderwidthnew', tablewidth='$tablewidthnew', tablespace='$tablespacenew', fontsize='$fontsizenew', font='$fontnew', nobold='$noboldnew', boardimg='$boardlogonew', imgdir='$imgdirnew', smdir='$smdirnew', cattext='$cattextnew' WHERE themeid='$edit'");
			cpmsg("界面方案成功更新。如果您正使用此风格，新的设置可能不会马上更新。");

		}

	}

}
elseif($action == "forumlinks") {

	if(!$forumlinksubmit) {

		$forumlinks = "";
		$query = $db->query("SELECT * FROM $table_forumlinks ORDER BY displayorder");
		while($forumlink = $db->fetch_array($query)) {
			$forumlinks .= "<tr bgcolor=\"$altbg2\" align=\"center\">\n".
				"<td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$forumlink[id]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"3\" name=\"displayorder[$forumlink[id]]\" value=\"$forumlink[displayorder]\"></td>\n".
				"<td bgcolor=\"$altbg1\"><input type=\"text\" size=\"15\" name=\"name[$forumlink[id]]\" value=\"$forumlink[name]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"15\" name=\"url[$forumlink[id]]\" value=\"$forumlink[url]\"></td>\n".
				"<td bgcolor=\"$altbg1\"><input type=\"text\" size=\"15\" name=\"note[$forumlink[id]]\" value=\"$forumlink[note]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"15\" name=\"logo[$forumlink[id]]\" value=\"$forumlink[logo]\"></td></tr>\n";
		}

?>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>如果您不想在首页显示联盟论坛，请把已有各项删除即可。</ul>
<ul><li>未填写文字说明的项目将以紧凑型显示。</ul>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=forumlinks">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="6">联盟论坛编辑</td></tr>
<tr align="center" class="header">
<td><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>顺序号</td><td>论坛名称</td><td>论坛 URL</td><td>文字说明</td>
<td>logo 地址(可选)</td></tr>
<?=$forumlinks?>
<tr bgcolor="<?=$altbg2?>"><td colspan="6" height="1"></td></tr>
<tr bgcolor="<?=$altbg1?>" align="center">
<td>新增：</td>            
<td><input type="text" size="3" name="newdisplayorder"></td>
<td><input type="text" size="15" name="newname"></td>
<td><input type="text" size="15" name="newurl"></td>
<td><input type="text" size="15" name="newnote"></td>
<td><input type="text" size="15" name="newlogo"></td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="forumlinksubmit" value="更新列表"></center></form></td></tr>
<?

	} else {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_forumlinks WHERE id IN ($ids)");
		}

		if(is_array($name)) {
			foreach($name as $id => $val) {
				$db->query("UPDATE $table_forumlinks SET displayorder='$displayorder[$id]', name='$name[$id]', url='$url[$id]', note='$note[$id]', logo='$logo[$id]' WHERE id='$id'");
			}
		}

		if($newname != "") {
			$db->query("INSERT INTO $table_forumlinks (displayorder, name, url, note, logo) VALUES ('$newdisplayorder', '$newname', '$newurl', '$newnote', '$newlogo')");
		}

		updatecache("forumlinks");
		cpmsg("联盟论坛成功更新。");
	}

}
elseif($action == "censor")
{

	if(!$censorsubmit) {

		$censorwords = "";
		$query = $db->query("SELECT * FROM $table_words");
		while($censor = $db->fetch_array($query)) {
			$censorwords .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$censor[id]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"30\" name=\"find[$censor[id]]\" value=\"$censor[find]\"></td>\n".
				"<td bgcolor=\"$altbg1\"><input type=\"text\" size=\"30\" name=\"replace[$censor[id]]\" value=\"$censor[replacement]\"></td></tr>\n";
		}

?>
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>替换为的内容中可以使用 html 代码。</ul>
<ul><li>为不影响程序效率，请不要设置过多不需要的过滤内容。</ul>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=censor">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr align="center" class="header"><td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>不良词语</td><td>替换为</td></tr>
<?=$censorwords?>
<tr bgcolor="<?=$altbg2?>"><td colspan="3" height="1"></td></tr>
<tr bgcolor="<?=$altbg1?>">
<td align="center">新增：</td>
<td align="center"><input type="text" size="30" name="newfind"></td>
<td align="center"><input type="text" size="30" name="newreplace"></td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="censorsubmit" value="更新列表"></center>
</form>
<?

	} else {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_words WHERE id IN ($ids)");
		}

		if(is_array($find)) {
			foreach($find as $id => $val) {
				$db->query("UPDATE $table_words SET find='$find[$id]', replacement='$replace[$id]' WHERE id='$id'");
			}
		}

		if($newfind != "") {
			$db->query("INSERT INTO $table_words (find, replacement) VALUES ('$newfind', '$newreplace')");
		}

		updatecache("censor");
		cpmsg("词语过滤成功更新。");

	}

}
elseif($action == "smilies")
{

	if(!$smiliesubmit) {

		$smilies = $picons = "";
		$query = $db->query("SELECT * FROM $table_smilies");
		while($smiley = $db->fetch_array($query)) {
			if($smiley[type] == "smiley") {
				$smilies .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$smiley[id]\"></td>\n".
					"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"25\" name=\"code[$smiley[id]]\" value=\"$smiley[code]\"></td>\n".
					"<td bgcolor=\"$altbg1\"><input type=\"text\" size=\"25\" name=\"url[$smiley[id]]\" value=\"$smiley[url]\"></td>\n".
					"<td bgcolor=\"$altbg2\"><input type=\"hidden\" name=\"type[$smiley[id]]\" value=\"$smiley[type]\"><img src=\"./$smdir/$smiley[url]\"></td></tr>\n";
			} elseif($smiley[type] == "picon") {
				$picons .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$smiley[id]\"></td>\n".
					"<td colspan=\"2\" bgcolor=\"$altbg2\"><input type=\"text\" size=\"35\" name=\"url[$smiley[id]]\" value=\"$smiley[url]\"></td>\n".
					"<td bgcolor=\"$altbg1\"><input type=\"hidden\" name=\"type[$smiley[id]]\" value=\"$smiley[type]\"><img src=\"./$smdir/$smiley[url]\"></td></tr>\n";
			}
		}

?>
<form method="post" action="admincp.php?action=smilies">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="4" align="left">Smilies 编辑</td></tr>
<tr align="center" class="header">
<td width="45">删?</td>
<td>Smilies 代码</td><td>Smilies 名称</td><td>Smilies 图片</td></tr>
<?=$smilies?>
<tr><td bgcolor="<?=$altbg2?>" colspan="4" height="1"></td></tr>
<tr bgcolor="<?=$altbg1?>" align="center"><td>添加：</td>
<td><input type="text" size="25" name="newcode"></td>
<td><input type="text" size="25" name="newurl1"></td>
<td></td></tr><tr>
<td bgcolor="<?=$altbg2?>" colspan="4" height="1"></td></tr>
<tr><td colspan="4" class="header">主题图标</td></tr>
<tr align="center" class="header">
<td width="45">删?</td>
<td colspan="2">图标名称</td><td>主题图标</td></tr>
<?=$picons?>
<tr><td bgcolor="<?=$altbg2?>" colspan="4" height="1"></td></tr>
<tr bgcolor="<?=$altbg1?>" align="center">
<td>添加：</td><td colspan="2"><input type="text" name="newurl2" size="35"></td><td>&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="smiliesubmit" value="编辑 Smilies 设置"></center></form>
<?

	} else {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_smilies WHERE id IN ($ids)");
		}

		if(is_array($url)) {
			foreach($url as $id => $val) {
				$db->query("UPDATE $table_smilies SET type='$type[$id]', code='$code[$id]', url='$url[$id]' WHERE id='$id'");
			}
		}

		if($newcode != "") {
			$query = $db->query("INSERT INTO $table_smilies (type, code, url)
				VALUES ('smiley', '$newcode', '$newurl1')");
		}
		if($newurl2 != "") {
			$query = $db->query("INSERT INTO $table_smilies (type, code, url)
				VALUES ('picon', '', '$newurl2')");
		}

		updatecache("smilies");
		updatecache("picons");
		cpmsg("Smilies 列表成功更新。");

	}
	
}
elseif($action == "flush") {

	updatecache(settings);
	updatecache(usergroups);
	updatecache(announcements);
	updatecache(forums);
	updatecache(forumlinks);
	updatecache(smilies);
	updatecache(picons);
	updatecache(news);
	updatecache(censor);

	cpmsg("全部缓存更新完毕。");

}

?>