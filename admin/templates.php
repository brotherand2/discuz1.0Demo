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

if($action == "tpldownload") {

	$tplfile = "";
	$templates = $db->query("SELECT name, template FROM $table_templates WHERE name<>'cdb_credits'");
	while ($template=$db->fetch_array($templates)) {
		$template[template] = stripslashes($template[template]);
		$tplfile.= "$template[name]|#*CDB TEMPLATE*#|$template[template]|#*CDB TEMPLATE FILE*#|";
	}
	header("Content-Disposition: filename=templates.cdb");
	header("Content-Length: $filesize");
	header("Content-Type: application/x-Discuz!");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $tplfile;
	cdbexit();

}

cpheader();

if($action == "templates")
{

	if(!$tplsubmit)
	{

		$query = $db->query("SELECT name, modified FROM $table_templates WHERE name<>'cdb_version' AND name<>'cdb_credits' ORDER BY name");
		while($tpl = $db->fetch_array($query))
		{
			$pos = strpos($tpl[name], "_");
			if(!$pos) {
				$template[$tpl[name]][] = array("name" => $tpl[name], "modified" => $tpl[modified]);
			} else {
				$template[substr($tpl[name], 0, $pos)][] = array("name" => $tpl[name], "modified" => $tpl[modified]);
			}
		}

?>
<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="3">模板维护</td></tr>

<form method="post" action="admincp.php?action=tpladd">
<tr bgcolor="<?=$altbg2?>"><td width="30%">添加模板</td><td width="30%"><input type="text" name="name" size="20" maxlength="40"></td>
<td width="40%"><input type="submit" name="addsubmit" value="添 加"></td></tr></form>

<form method="post" action="admincp.php">
<tr bgcolor="<?=$altbg1?>"><td width="30%">备份与恢复</td><td width="30%"><select name="action"><option value="tplresetall">将模板全部恢复默认</option>
<option value="tpldownload">将当前模板打包下载</option></select></td>
<td width="40%"><input type="submit" name="addsubmit" value="提 交"></td></tr></form>

</table></td></tr></table></td></tr></table><br><br>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>选择模板 - 斜体显示的为修改过的模板</td></tr>
<tr bgcolor="<?=$altbg1?>"><td><br>
<?

		$query = $db->query("SELECT template FROM $table_templates WHERE name='cdb_version'");
		$tplversion = $db->result($query, 0);
		if(trim($tplversion) != trim($version)) {
			echo "<br> &nbsp; &nbsp; <b>警告！</b>使用中的模板($tplversion)和当前 Discuz! 版本($version)不一致，这可能导致一些无法预测的问题，请立即将模板恢复默认。<br><br>";
		}

		foreach($template as $tpl => $subtpls)
		{
			echo "<ul><li><b>$tpl 模板组</b><ul>";
			foreach($subtpls as $subtpl)
			{
				if($subtpl[modified]) {
					echo "<li><u><i>$subtpl[name]</i></u> &nbsp; <a href=\"admincp.php?action=tpledit&name=$subtpl[name]\">[编辑]</a> <a href=\"admincp.php?action=tpldelete&name=$subtpl[name]\">[删除]</a> <a href=\"admincp.php?action=tplreset&name=$subtpl[name]\">[恢复默认]</a>";
				} else {
					echo "<li>$subtpl[name] &nbsp; <a href=\"admincp.php?action=tpledit&name=$subtpl[name]\">[编辑]</a> <a href=\"admincp.php?action=tpldelete&name=$subtpl[name]\">[删除]</a>";
				}
			}
			echo "</ul></ul>";
		}

		echo "</td><tr></table></td></tr></table>\n";

	}
	else {
		
	}

}
elseif($action == "tpledit")
{

	if(!$editsubmit && !$deletesubmit) {

		$query = $db->query("SELECT name, template FROM $table_templates WHERE name='$name' AND name<>'cdb_version' AND name<>'cdb_credits'");
		$template = $db->fetch_array($query);
		$template[template] = str_replace("\\'", "'", htmlspecialchars(stripslashes($template[template])));

?>
<script language="JavaScript">
var n = 0;
function displayHTML(obj) {
	win = window.open(" ", 'popup', 'toolbar = no, status = no, scrollbars=yes');
	win.document.write("" + obj.value + "");
}
function HighlightAll(obj) {
	obj.focus();
	obj.select();
	if (document.all) {
		obj.createTextRange().execCommand("Copy");
		window.status = "将模板内容复制到剪贴板";
		setTimeout("window.status=''", 1800);
	}
}
function findInPage(obj, str)
{
	var txt, i, found;
	if (str == "") {
		return false;
	}
	if (document.layers)
	{
		if (!obj.find(str)) {
			while(obj.find(str, false, true)) {
				n++;
			}
		} else {
			n++;
		}
		if (n == 0) {
			alert('未找到指定字串。');
		}
	}
	if (document.all) {
		txt = obj.createTextRange();
		for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
			txt.moveStart('character', 1);
			txt.moveEnd('textedit');
		}
		if (found) {
			txt.moveStart('character', -1);
			txt.findText(str);
			txt.select();
			txt.scrollIntoView();
			n++;
		} else {
			if (n > 0) {
				n = 0;
				findInPage(str);
			} else {
				alert("未找到指定字串。");
			}
		}
	}
	return false;
}
</script>
<form method="post" action="admincp.php?action=tpledit&name=<?=$name?>">
<table cellspacing="0" cellpadding="0" border="0" width="60%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr><td class="header">编辑模板 - <?=$template[name]?></td></tr>
<tr><td bgcolor="<?=$altbg1?>" align="center">
<textarea cols="85" rows="20" name="templatenew"><?=$template[template]?></textarea><br><br>
<input name="search" type="text" accesskey="t" size="20" onChange="n=0;">
<input type="button" value="查找" accesskey="f" onClick="findInPage(this.form.templatenew, this.form.search.value)">&nbsp;&nbsp;&nbsp;
<input type="button" value="预览" accesskey="p" onClick="displayHTML(this.form.templatenew)">
<input type="button" value="复制" accesskey="c" onClick="HighlightAll(this.form.templatenew)">&nbsp;&nbsp;&nbsp;
<input type="submit" name="editsubmit" value="确认修改">
</td></tr></table></td></tr></table>
</form>

<?

	} else {

		$templatenew = addslashes($templatenew);
		$db->query("UPDATE $table_templates SET template='$templatenew' WHERE name='$name'");
		if($db->affected_rows()) {
			$db->query("UPDATE $table_templates SET modified='1' WHERE name='$name'");
		}

		cpmsg("模板成功修改。");

	}

} elseif($action == "tpladd") {

	$name = trim($name);
	if($name) {
		$query = $db->query("SELECT id FROM $table_templates WHERE name='$name'");
		if($db->result($query, 0)) {
			cpmsg("指定模板已经存在，请返回修改。");
		}
		$db->query("INSERT INTO $table_templates (name) VALUES ('$name')");

		cpmsg("指定模板成功添加。");
	} else {
		cpmsg("您没有指定新的模板名称，请返回修改。");
	}

} elseif($action == "tplresetall") {

	if(!$confirmed) {
		cpmsg("本操作不可恢复，您确定清空全部现有模板，并恢复为默认吗？", "admincp.php?action=tplresetall", "form");
	} else {
		$tplfile = "./templates.cdb";
		if(is_readable($tplfile))
		{
			$db->query("DELETE FROM $table_templates");
			$fp = fopen($tplfile, "r");
			$templates = explode("|#*CDB TEMPLATE FILE*#|", fread($fp, filesize($tplfile)));
			fclose($fp);

			ksort($templates);
			foreach($templates as $template) {
				$template = explode("|#*CDB TEMPLATE*#|", $template);
				if($template[0] && $template[1]) {
					$db->query("INSERT INTO $table_templates (name, template)
						VALUES ('".addslashes($template[0])."', '".addslashes(addslashes(trim($template[1])))."')");
				}
			}
			$db->query("DELETE FROM $table_templates WHERE name='cdb_credits'");
			$db->query("INSERT INTO $table_templates (name, template)
				VALUES ('cdb_credits', 'PGNlbnRlcj48c3BhbiBjbGFzcz1cIm1lZGl1bXR4dFwiIHN0eWxlPVwiZm9udC1zaXplOiAyMHB4OyBmb250LXdlaWdodDogYm9sZFwiPkRpc2N1eiEgQ3JlZGl0czwvc3Bhbj48YnI+PGJyPjx0YWJsZSBjZWxsc3BhY2luZz1cIjBcIiBjZWxscGFkZGluZz1cIjBcIiBib3JkZXI9XCIwXCIgd2lkdGg9XCI0MDBcIiBhbGlnbj1cImNlbnRlclwiPjx0cj48dGQgYmdjb2xvcj1cIiRib3JkZXJjb2xvclwiPjx0YWJsZSBib3JkZXI9XCIwXCIgY2VsbHNwYWNpbmc9XCIkYm9yZGVyd2lkdGhcIiBjZWxscGFkZGluZz1cIiR0YWJsZXNwYWNlXCIgd2lkdGg9XCIxMDAlXCI+PHRyIGNsYXNzPVwiaGVhZGVyXCI+PHRkIGNvbHNwYW49XCIyXCIgYWxpZ249XCJjZW50ZXJcIj5EaXNjdXohIERldmVsb3BlcjwvdGQ+PC90cj48dHI+PHRkIGJnY29sb3I9XCIkYWx0YmcyXCIgYWxpZ249XCJjZW50ZXJcIiBjb2xzcGFuPVwiMlwiIGNsYXNzPVwiYm9sZFwiPkRpc2N1eiEgaXMgZGV2ZWxvcGVkIGJ5IENyb3NzZGF5IFN0dWRpbywgQWxsIFJpZ2h0cyBSZXNlcnZlZC48L3RkPjwvdHI+PHRyPjx0ZCBiZ2NvbG9yPVwiJGFsdGJnMVwiIHdpZHRoPVwiNDAlXCIgY2xhc3M9XCJib2xkXCI+UHJvZ3JhbWluZzo8L3RkPjx0ZCBiZ2NvbG9yPVwiJGFsdGJnMlwiPjxhIGhyZWY9XCJodHRwOi8vd3d3LmNyb3NzZGF5LmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPkNyb3NzZGF5PC9hPjwvdGQ+PC90cj48dHI+PHRkIGJnY29sb3I9XCIkYWx0YmcxXCIgY2xhc3M9XCJib2xkXCI+VGhlbWUgRGVzaWduOjwvdGQ+PHRkIGJnY29sb3I9XCIkYWx0YmcyXCI+PGEgaHJlZj1cImh0dHA6Ly93d3cuY3Jvc3NkYXkuY29tXCIgdGFyZ2V0PVwiX2JsYW5rXCI+Q3Jvc3NkYXk8L2E+PC90ZD48L3RyPjwvdGFibGU+PC90ZD48L3RyPjwvdGFibGU+PGJyPjxicj48dGFibGUgY2VsbHNwYWNpbmc9XCIwXCIgY2VsbHBhZGRpbmc9XCIwXCIgYm9yZGVyPVwiMFwiIHdpZHRoPVwiNDAwXCIgYWxpZ249XCJjZW50ZXJcIj48dHI+PHRkIGJnY29sb3I9XCIkYm9yZGVyY29sb3JcIj48dGFibGUgYm9yZGVyPVwiMFwiIGNlbGxzcGFjaW5nPVwiJGJvcmRlcndpZHRoXCIgY2VsbHBhZGRpbmc9XCIkdGFibGVzcGFjZVwiIHdpZHRoPVwiMTAwJVwiIHN0eWxlPVwid29yZC1icmVhazoga2VlcC1hbGxcIj48dHIgY2xhc3M9XCJoZWFkZXJcIj48dGQgY29sc3Bhbj1cIjJcIiBhbGlnbj1cImNlbnRlclwiPkRpc2N1eiEgU3VwcG9ydCBUZWFtPC90ZD48L3RyPjx0cj48dGQgYmdjb2xvcj1cIiRhbHRiZzFcIiB3aWR0aD1cIjQwJVwiIHZhbGlnbj1cInRvcFwiIGNsYXNzPVwiYm9sZFwiPkFydCBTdXBwb3J0OjwvdGQ+PHRkIGJnY29sb3I9XCIkYWx0YmcyXCI+PGEgaHJlZj1cImh0dHA6Ly90eWMudWRpLmNvbS50dy9jZGJcIiB0YXJnZXQ9XCJfYmxhbmtcIj50eWM8L2E+LCA8YSBocmVmPVwiaHR0cDovL3NtaWNlLm5ldC9+eW91cmFuL2NkYi9pbmRleC5waHBcIiB0YXJnZXQ9XCJfYmxhbmtcIj7Qx8q0PC9hPiwgPGEgaHJlZj1cImh0dHA6Ly93d3cuY25tYXlhLm9yZ1wiIHRhcmdldD1cIl9ibGFua1wiPrr8wOq6/c2/PC9hPjwvdGQ+PC90cj48dHI+PHRkIGJnY29sb3I9XCIkYWx0YmcxXCIgdmFsaWduPVwidG9wXCIgY2xhc3M9XCJib2xkXCI+UGx1Z2luczo8L3RkPjx0ZCBiZ2NvbG9yPVwiJGFsdGJnMlwiPjxhIGhyZWY9XCJodHRwOi8vd3d3Lm51Y3BwLmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPktuaWdodEU8L2E+LCA8YSBocmVmPVwiaHR0cDovL3d3dy56YzE4LmNvbS9cIiB0YXJnZXQ9XCJfYmxhbmtcIj5mZWl4aW48L2E+LCA8YSBocmVmPVwiaHR0cDovL3NtaWNlLm5ldC9+eW91cmFuL2NkYi9pbmRleC5waHBcIiB0YXJnZXQ9XCJfYmxhbmtcIj7Qx8q0PC9hPiwgPGEgaHJlZj1cImh0dHA6Ly90cnVlaG9tZS5uZXRcIiB0YXJnZXQ9XCJfYmxhbmtcIj7Az7H4vsawyTwvYT48L3RkPjwvdHI+PHRyPjx0ZCBiZ2NvbG9yPVwiJGFsdGJnMVwiIHZhbGlnbj1cInRvcFwiIGNsYXNzPVwiYm9sZFwiPk9mZmljYWwgVGVzdGVyczo8L3RkPjx0ZCBiZ2NvbG9yPVwiJGFsdGJnMlwiPjxhIGhyZWY9XCJodHRwOi8vdHJ1ZWhvbWUubmV0XCIgdGFyZ2V0PVwiX2JsYW5rXCI+wM+x+L7GsMk8L2E+LCBhYnUsIDxhIGhyZWY9XCJodHRwOi8vd3d3Lm51Y3BwLmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPktuaWdodEU8L2E+LCA8YSBocmVmPVwiaHR0cDovL3d3dy56YzE4LmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPmZlaXhpbjwvYT4sIDxhIGhyZWY9XCJodHRwOi8vc21pY2UubmV0L355b3VyYW4vY2RiL2luZGV4LnBocFwiIHRhcmdldD1cIl9ibGFua1wiPtDHyrQ8L2E+LCA8YSBocmVmPVwiaHR0cDovL3R5Yy51ZGkuY29tLnR3L2NkYlwiIHRhcmdldD1cIl9ibGFua1wiPnR5YzwvYT4sIDxhIGhyZWY9XCJodHRwOi8vd3d3LnR4eXgubmV0XCIgdGFyZ2V0PVwiX2JsYW5rXCI+8Km2+TwvYT4sIDxhIGhyZWY9XCJodHRwOi8vcy10bS5uZXRcIiB0YXJnZXQ9XCJfYmxhbmtcIj7Evrb6PC9hPiwgPGEgaHJlZj1cImh0dHA6Ly93d3cub3VycGhwLmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPlNoYXJteTwvYT4sIDxhIGhyZWY9XCJodHRwOi8vd3d3LmVhY2h1LmNvbVwiIHRhcmdldD1cIl9ibGFua1wiPlIuQzwvYT4sIDxhIGhyZWY9XCJodHRwOi8vd3d3Lmp1bm9tYXkuY29tXCIgdGFyZ2V0PVwiX2JsYW5rXCI+QVNVUkE8L2E+LCA8YSBocmVmPVwiaHR0cDovL3d3dy5IYWtrYU9ubGluZS5jb21cIiB0YXJnZXQ9XCJfYmxhbmtcIj7OtMP7seLW2zwvYT4sIDxhIGhyZWY9XCJodHRwOi8vM3B1bmsuY29tXCIgdGFyZ2V0PVwiX2JsYW5rXCI+M3B1bms8L2E+LCA8YSBocmVmPVwiaHR0cDovL3d3dy5wdWZmZXIuaWR2LnR3L2NkYlwiIHRhcmdldD1cIl9ibGFua1wiPnB1ZmZlcjwvYT48L3RkPjwvdHI+PC90YWJsZT48L3RkPjwvdHI+PC90YWJsZT48YnI+PGJyPg==')");
			cpmsg("全部模板成功恢复为默认。");
		} else {
			cpmsg("模板数据文件 templates.cdb 不存在，无法恢复。");
		}
	}

} elseif($action == "tpldelete") {

	if(!$confirmed) {
		cpmsg("本操作不可恢复，您确定要删除模板 <b>$name</b> 吗？", "admincp.php?action=tpldelete&name=$name", "form");
	} else {
		$db->query("DELETE FROM $table_templates WHERE name='$name'");
		cpmsg("指定模板成功删除。");
	}

} elseif($action == "tplreset") {

	if(!$confirmed) {
		cpmsg("本操作不可恢复，您确定要将模板 <b>$name</b> 恢复到默认吗？", "admincp.php?action=tplreset&name=$name", "form");
	} else {
		$tplfile = "./templates.cdb";
		if(is_readable($tplfile)) {
			$fp = fopen($tplfile, "r");
			$templates = explode("|#*CDB TEMPLATE FILE*#|", fread($fp, filesize($tplfile)));
			fclose($fp);

			foreach($templates as $template) {
				$template = explode("|#*CDB TEMPLATE*#|", $template);
				if($template[0] == $name) {
					break;
				}
			}

			$db->query("UPDATE $table_templates SET template='".addslashes(addslashes(trim($template[1])))."', modified='0' WHERE name='$name'");
			cpmsg("指定模板成功恢复为默认。");
		} else {
			cpmsg("模板数据文件 templates.cdb 不存在，无法恢复。");
		}
	}

}

?>