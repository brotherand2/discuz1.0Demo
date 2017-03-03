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

cpheader();

if($action == "announcements")
{

	if(!$deletesubmit && !$addsubmit && !$edit) {

		$announcements = "";
		$query = $db->query("SELECT * FROM $table_announcements ORDER BY starttime DESC, id DESC");
		while($announce = $db->fetch_array($query)) {
			$announce[author] = "<a href=\"./member.php?action=viewpro&username=".rawurlencode($announce[author])."\" target=\"_blank\">$announce[author]</a>";
			$announce[subject] = "<a href=\"admincp.php?action=announcements&edit=$announce[id]\">$announce[subject]</a>";
			$announce[starttime] = $announce[starttime] ? gmdate("$dateformat", $announce[starttime] + $timeoffset * 3600) : "不限";
			$announce[endtime] = $announce[endtime] ? gmdate("$dateformat", $announce[endtime] + $timeoffset * 3600) : "不限";
			$announce[message] = "<a href=\"admincp.php?action=announcements&edit=$announce[id]\">".wordscut($announce[message], 20)."</a>";
			$announcements .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$announce[id]\"></td>\n".
				"<td bgcolor=\"$altbg2\">$announce[author]</td>\n".
				"<td bgcolor=\"$altbg1\">$announce[subject]</td>\n".
				"<td bgcolor=\"$altbg2\">$announce[message]</td>\n".
				"<td bgcolor=\"$altbg1\">$announce[starttime]</td>\n".
				"<td bgcolor=\"$altbg2\">$announce[endtime]</td></tr>\n";
		}
		$newstarttime = gmdate("Y-n-j", $timestamp + $timeoffset * 3600);

?>
<br><form method="post" action="admincp.php?action=announcements">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="6">论坛公告编辑</td></tr>
<tr align="center" class="header">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>发布人</td><td>标题</td><td>内容</td><td>起始时间</td><td>终止时间</td></tr>
<?=$announcements?>
</table></td></tr></table><br><center>
<input type="submit" name="deletesubmit" value="删除选定公告"></center></form>

<br><form method="post" action="admincp.php?action=announcements">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">添加论坛公告</td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>标题：</b></td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="newsubject"></td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>起始时间：</b><br>格式：yyyy-mm-dd</td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="newstarttime" value="<?=$newstarttime?>"></td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>终止时间：</b><br>格式：yyyy-mm-dd</td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="newendtime"> 留空为不限制</td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>" valign="top"><b>公告内容：</b><br>公告中可以使用 BB 代码</td>
<td width="79%" bgcolor="<?=$altbg2?>"><textarea name="newmessage" cols="60" rows="10"></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="addsubmit" value="添加论坛公告">
</form>
<?

	}
	elseif($edit)//edit 是要编辑的 ID
	{

		if(!$editsubmit)
		{
			$query = $db->query("SELECT * FROM $table_announcements WHERE id='$edit'");
			if($announce = $db->fetch_array($query)) {
				$announce[starttime] = $announce[starttime] ? gmdate("Y-n-j", $announce[starttime] + $timeoffset * 3600) : "";
				$announce[endtime] = $announce[endtime] ? gmdate("Y-n-j", $announce[endtime] + $timeoffset * 3600) : "";

?>
<br><form method="post" action="admincp.php?action=announcements&edit=<?=$edit?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">编辑论坛公告</td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>标题：</b></td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="subjectnew" value="<?=$announce[subject]?>"></td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>起始时间：</b><br>格式：yyyy-mm-dd</td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="starttimenew" value="<?=$announce[starttime]?>"></td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>"><b>终止时间：</b><br>格式：yyyy-mm-dd</td>
<td width="79%" bgcolor="<?=$altbg2?>"><input type="text" size="45" name="endtimenew" value="<?=$announce[endtime]?>"> 留空为不限制</td></tr>

<tr><td width="21%" bgcolor="<?=$altbg1?>" valign="top"><b>公告内容：</b><br>公告中可以使用 BB 代码</td>
<td width="79%" bgcolor="<?=$altbg2?>"><textarea name="messagenew" cols="60" rows="10"><?=cdbhtmlspecialchars($announce[message])?></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="editsubmit" value="编辑论坛公告">
</form>
<?
			}
			else {//明明还显示,点进去,数据库查不到,是另一个管理员删除了
				cpmsg("指定的公告不存在，请返回。");
			}
		}
		else
			{
			$newsubject = cdbhtmlspecialchars($newsubject);
			if(strpos($starttimenew, "-")) {
				$time = explode("-", $starttimenew);
				$starttimenew = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
			} else {
				$starttimenew = 0;
			}
			if(strpos($endtimenew, "-")) {
				$time = explode("-", $endtimenew);
				$endtimenew = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
			} else {
				$endtimenew = 0;
			}

			if(!$starttimenew) {
				cpmsg("您必须输入起始时间，请返回修改。");
			} elseif(!trim($subjectnew) || !trim($messagenew)) {
				cpmsg("您必须输入公告标题和内容，请返回修改。");
			} else {
				$db->query("UPDATE $table_announcements SET subject='$subjectnew', starttime='$starttimenew', endtime='$endtimenew', message='$messagenew' WHERE id='$edit'");
				updatecache("announcements");
				cpmsg("论坛公告成功编辑。");
			}
		}

	}
	elseif($deletesubmit) {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_announcements WHERE id IN ($ids)");
		}

		updatecache("announcements");
		cpmsg("指定公告成功删除");

	}
	elseif($addsubmit) {

		$newsubject = cdbhtmlspecialchars($newsubject);
		if(strpos($newstarttime, "-")) {
			$time = explode("-", $newstarttime);
			$newstarttime = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
		} else {
			$newstarttime = 0;
		}
		if(strpos($newendtime, "-")) {
			$time = explode("-", $newendtime);
			$newendtime = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
		} else {
			$newendtime = 0;
		}

		if(!$newstarttime) {
			cpmsg("您必须输入起始时间，请返回修改。");
		} elseif(!trim($newsubject) || !trim($newmessage)) {
			cpmsg("您必须输入公告标题和内容，请返回修改。");
		} else {
			$db->query("INSERT INTO $table_announcements (author, subject, starttime, endtime, message)
				VALUES ('$cdbuser', '$newsubject', '$newstarttime', '$newendtime', '$newmessage')");
			updatecache("announcements");
			cpmsg("论坛公告成功添加");
		}
	}

}
elseif($action == "news")
{

	if(!$newssubmit) {

		$news = "";
		$query = $db->query("SELECT * FROM $table_news");
		while($newsrow = $db->fetch_array($query)) {
			$news .= "<tr align=\"center\"><td bgcolor=\"$altbg1\"><input type=\"checkbox\" name=\"delete[]\" value=\"$newsrow[id]\"></td>\n".
				"<td bgcolor=\"$altbg2\"><input type=\"text\" size=\"50\" name=\"subject[$newsrow[id]]\" value=\"".htmlspecialchars($newsrow[subject])."\"></td>\n".
				"<td bgcolor=\"$altbg1\"><input type=\"text\" size=\"25\" name=\"link[$newsrow[id]]\" value=\"".htmlspecialchars($newsrow[link])."\"></td></tr>\n";
		}

?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>如果您不想显示首页滚动新闻，请把已有各项删除即可。</ul>
<ul><li>新闻标题中可使用如下动态变量：总主题数 $threads，总帖数 $posts，会员总数 $members，最新会员名字 $lastmember 等，直接把他们放在新闻内容里(或加上大括号)即可显示动态新闻内容。</ul>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=news">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="3">首页滚动新闻编辑</td></tr>
<tr align="center" class="header">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td>新闻内容</td><td>链接</td></tr>
<?=$news?>
<tr bgcolor="<?=$altbg2?>"><td colspan="3" height="1"></td></tr>
<tr align="center" bgcolor="<?=$altbg1?>">
<td>新增：</td>
<td><input type="text" size="50" name="newsubject"></td>
<td><input type="text" size="25" name="newlink"></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="newssubmit" value="更新首页新闻列表"></center></form>
<?

	} else {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_news WHERE id IN ($ids)");
		}

		if(is_array($subject)) {
			foreach($subject as $id => $val) {
				$db->query("UPDATE $table_news SET subject='$subject[$id]', link='$link[$id]' WHERE id='$id'");
			}
		}

		if($newsubject != "") {
			$db->query("INSERT INTO $table_news (subject, link) VALUES ('$newsubject', '$newlink')");
		}

		updatecache("news");
		cpmsg("首页新闻成功更新。");
	}
}

?>