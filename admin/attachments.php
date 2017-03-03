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

$app = 35;

if(!$deletesubmit && !$searchsubmit) {
	$forumselect = "<select name=\"forumprune\">\n";
	$forumselect .= "<option value=\"all\">全部论坛</option>\n";
	$querycat = $db->query("SELECT * FROM $table_forums WHERE type='forum' OR type='sub' ORDER BY displayorder");
	while($forum = $db->fetch_array($querycat)) {
		$forumselect .= "<option value=\"$forum[fid]\">$forum[name]</option>\n";
	}
	$forumselect .= "</select>";

?>
<br><form method="post" action="admincp.php?action=attachments">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">

<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr><td class="header" colspan="2">搜索附件</td></tr>

<tr><td bgcolor="<?=$altbg1?>">记录存在但文件缺失的附件：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="checkbox" name="nomatched" value="1"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">附件尺寸小于(bytes)：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="sizeless" size="20"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">附件尺寸大于(bytes)：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="sizemore" size="20"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">被下载次数小于：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="dlcountless" size="20"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">被下载次数大于：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="dlcountmore" size="20"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">发表于多少天以前：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="daysold" size="20"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">所在论坛：</td>
<td bgcolor="<?=$altbg2?>" align="right"><select name="forums"><option value="all">&nbsp;&nbsp;> 全部论坛</option>
<option value="">&nbsp;</option><?=forumselect()?></select></td></tr>

<tr><td bgcolor="<?=$altbg1?>">附件文件名包含：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="filename" size="40"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">作者：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="author" size="40"></td></tr>

</table></td></tr></table><br><center>
<input type="submit" name="searchsubmit" value="搜索附件"></center>
</form>
<?

} elseif($searchsubmit) {

	$sql = "a.pid=p.pid";

	if($forums && $forums != "all") {
		$sql .= " AND p.fid='$forums'";
	} elseif($forums != "all") {
		cpmsg("您没有选择附件所在论坛，请返回修改。");
	}
	if($daysold != "") {
		$sql .= " AND p.dateline<='".($timestamp - (86400 * $daysold))."'";
	}
	if($author != "") {
		$sql .= " AND p.author='$author'";
	}
	if($filename != "") {
		$sql .= " AND a.filename LIKE '%$filename%'";
	}
	if($sizeless != "") {
		$sql .= " AND a.filesize<'$sizeless'";
	}
	if($sizemore != "") {
		$sql .= " AND a.filesize>'$sizemore' ";
	}
	if($dlcountless != "") {
		$sql .= " AND a.downloads<'$dlcountless'";
	}
	if($dlcountmore != "") {
		$sql .= " AND a.downloads>'$dlcountmore'";
	}

	if(!$page) {
		$page = 1;
	}
	$start = ($page - 1) * $app;

	$query = $db->query("SELECT COUNT(*) FROM $table_attachments a, $table_posts p WHERE $sql");
	$num = $db->result($query, 0);
	$multipage = multi($num, $app, $page, "admincp.php?action=attachments&filename=$filename&author=$author&forums=$forums&sizeless=$sizeless&sizemore=$sizemore&dlcountless=$dlcountless&dlcountmore=$dlcountmore&daysold=$daysold&nomatched=$nomatched&searchsubmit=1");
		
	$attachments = "";//t.tid=a.tid 从附件表找到对应主题表,a.pid=p.pid从附件表找到对应贴子表,f.fid=p.fid再从贴子表找到论坛表
	$query = $db->query("SELECT a.*, p.fid, p.author, t.tid, t.tid, t.subject, f.name AS fname FROM $table_attachments a, $table_posts p, $table_threads t, $table_forums f WHERE t.tid=a.tid AND f.fid=p.fid AND $sql LIMIT $start, $app");
	while($attachment = $db->fetch_array($query))
	{
		$matched = file_exists("./$attachdir/$attachment[attachment]") ? NULL : "<b>附件文件缺失!</b><br>";
		$attachsize = sizecount($attachment[filesize]);//nocache 意味着只搜索缺失文件
		if(!$nomatched || ($nomatched && $matched))//条件1搜索所有文件  或条件2 搜索缺失文件,并且记录中的文件缺失了
		{
			$attachments .= "<tr><td bgcolor=\"$altbg1\" width=\"45\" align=\"center\" valign=\"middle\"><input type=\"checkbox\" name=\"delete[]\" value=\"$attachment[aid]\"></td>\n".
				"<td bgcolor=\"$altbg2\" align=\"center\" width=\"20%\"><b>$attachment[filename]</b><br><a href=\"viewthread.php?action=attachment&aid=$attachment[aid]\">[下载该附件]</a></td>\n".
				"<td bgcolor=\"$altbg1\" align=\"center\" width=\"20%\">$matched<a href=\"$attachurl/$attachment[attachment]\" class=\"smalltxt\">$attachment[attachment]</a></td>\n".
				"<td bgcolor=\"$altbg2\" align=\"center\" width=\"8%\">$attachment[author]</td>\n".
				"<td bgcolor=\"$altbg1\" valign=\"middle\" width=\"25%\"><a href=\"viewthread.php?tid=$attachment[tid]\"><b>".wordscut($attachment[subject], 18)."</b></a><br>论坛：<a href=\"forumdisplay.php?fid=$attachment[fid]\">$attachment[fname]</a></td>\n".
				"<td bgcolor=\"$altbg2\" valign=\"middle\" width=\"18%\" align=\"center\">$attachsize</td>\n".
				"<td bgcolor=\"$altbg1\" valign=\"middle\" width=\"7%\" align=\"center\">$attachment[downloads]</td></tr>\n";
		}
	}
?>
<br><form method="post" action="admincp.php?action=attachments">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td class="multi" colspan="7"><?=$multipage?></td></tr>
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%" style="table-layout: fixed;word-break: break-all">
<tr><td class="header" width="6%" align="center"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td class="header" width="15%" align="center">附件名</td>
<td class="header" width="25%" align="center">存储文件名</td>
<td class="header" width="14%" align="center">作者</td>
<td class="header" width="23%" align="center">所在主题</td>
<td class="header" width="8%" align="center">尺寸</td>
<td class="header" width="8%" align="center">下载</td></tr>
<?=$attachments?>
</table></td></tr>
<tr><td class="multi" colspan="7"><?=$multipage?></td></tr></table><br>
<center><input type="submit" name="deletesubmit" value="更新列表"></center></form>
<?

} elseif($deletesubmit)
{

	if(is_array($delete)) {
		$ids = $comma = "";
		foreach($delete as $aid) {
			$ids .= "$comma'$aid'";
			$comma = ", ";
		}

		$tids = $pids = $comma1 = $comma2 = "";
		$query = $db->query("SELECT tid, pid, attachment FROM $table_attachments WHERE aid IN ($ids)");
		while($attach = $db->fetch_array($query)) {
			@unlink("$attachdir/$attach[attachment]");//删除附件文件
			$tids .= "$comma1'$attach[tid]'";
			$comma1 = ", ";
			$pids .= "$comma2'$attach[pid]'";
			$comma2 = ", ";
		}
		$db->query("DELETE FROM $table_attachments WHERE aid IN ($ids)");////删除要删除的附件表记录
		$db->query("UPDATE $table_posts SET aid='0' WHERE pid IN ($pids)");//主题中附件被删除的要清空附近 ID

		$attachtids = $comma = "";
		$query = $db->query("SELECT tid, filetype FROM $table_attachments WHERE tid IN ($tids) GROUP BY tid ORDER BY pid DESC");
		while($attach = $db->fetch_array($query)) {//当前主题删除了某个贴子附件,但还有附近存在
			$db->query("UPDATE $table_threads SET attachment='$attach[filetype]' WHERE tid='$attach[tid]'");

			$attachtids .= "$comma'$attach[tid]'";//附件表中,某些主题删除了某个贴子附件,但还有附近存在,对于这些主题不要清空 attachment
			$comma = ", ";
		}
		$db->query("UPDATE $table_threads SET attachment='' WHERE tid IN ($tids)".($attachtids ? " AND tid NOT IN ($attachtids)" : NULL));//对当前主题中没有附件 的设置附件类型为空

		cpmsg("附件列表成功更新。");
	} else {
		cpmsg("您没有选择要删除的附件，请返回修改。");
	}
}

?>