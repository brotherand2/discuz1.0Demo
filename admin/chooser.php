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

if(!$forumsubmit && !$membersubmit && !$threadsubmit) {

?>
<br><br><form method="post" action="admincp.php?action=chooser">
<table cellspacing="0" cellpadding="0" border="0" width="500" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">重建主题贴数</td></tr>
<tr bgcolor="<?=$altbg2?>">
<td align="center">每个循环更新的主题数： &nbsp; &nbsp; <input type="text" name="pertask" value="300"></td></tr>
</table></td></tr></table><br><center>
<input type="submit" name="threadsubmit" value="更 新"> &nbsp;
<input type="reset" value="重 置"></center></form><br>

<form method="post" action="admincp.php?action=chooser">
<table cellspacing="0" cellpadding="0" border="0" width="500" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">重建用户发贴数</td></tr>
<tr bgcolor="<?=$altbg2?>">
<td align="center">每个循环更新的用户数： &nbsp; &nbsp; <input type="text" name="pertask" value="30"></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="membersubmit" value="更 新"> &nbsp;
<input type="reset" value="重 置"></center></form><br>

<form method="post" action="admincp.php?action=chooser">
<table cellspacing="0" cellpadding="0" border="0" width="500" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">重建论坛贴数</td></tr>
<tr bgcolor="<?=$altbg2?>">
<td align="center">每个循环更新的论坛数： &nbsp; &nbsp; <input type="text" name="pertask" value="10"></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="forumsubmit" value="更 新"> &nbsp;
<input type="reset" value="重 置"></center></form><br>
<?

} elseif($forumsubmit)
{

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=chooser&current=$next&pertask=$pertask&forumsubmit=1";
	$processed = 0;

	$queryf = $db->query("SELECT fid, type FROM $table_forums WHERE type<>'group' LIMIT $current, $pertask");
	while($forum = $db->fetch_array($queryf))

    {
		$processed = 1;

		$fids = "'$forum[fid]'";
		$query = $db->query("SELECT fid FROM $table_forums WHERE fup='$forum[fid]'");
		while($sub = $db->fetch_array($query)) {
			$fids .= ", '$sub[fid]'";
		}

		$query = $db->query("SELECT COUNT(*) FROM $table_threads WHERE fid IN ($fids)");
		$threadnum = $db->result($query, 0);
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE fid IN ($fids)");
		$postnum = $db->result($query, 0);

		$query = $db->query("SELECT subject, lastpost, lastposter FROM $table_threads WHERE fid IN ($fids) ORDER BY lastpost DESC LIMIT 0, 1");
		$thread = $db->fetch_array($query);
		$lastpost = addslashes("$thread[subject]\t$thread[lastpost]\t$thread[lastposter]");

		$db->query("UPDATE $table_forums SET threads='$threadnum', posts='$postnum', lastpost='$lastpost' WHERE fid='$forum[fid]'");
	}

	if($processed) {
		cpmsg("重建论坛贴数：正在处理论坛从 $current 到 $next", $nextlink);
	} else {
		$db->query("UPDATE $table_forums SET threads='0', posts='0' WHERE type='group'");
		cpmsg("论坛贴数重建完成。");
	}

} elseif($threadsubmit) {

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=chooser&current=$next&pertask=$pertask&threadsubmit=1";
	$processed = 0;

	$queryt = $db->query("SELECT tid FROM $table_threads LIMIT $current, $pertask");
	while($threads = $db->fetch_array($queryt)) {
		$processed = 1;
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$threads[tid]'");
		$replynum = $db->result($query, 0);
		$replynum--;
		$db->query("UPDATE $table_threads SET replies='$replynum' WHERE tid='$threads[tid]'");
	}

	if($processed) {
		cpmsg("重建主题贴数：正在处理主题从 $current 到 $next", $nextlink);
	} else {
		cpmsg("主题贴数重建完成。");
	}

} elseif($membersubmit) {

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=chooser&current=$next&pertask=$pertask&membersubmit=1";
	$processed = 0;

	$queryt = $db->query("SELECT username FROM $table_members LIMIT $current, $pertask");
	while($mem = $db->fetch_array($queryt)) {
		$processed = 1;
		$username = addslashes($mem[username]);
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE author='$username'");
		$postsnum = $db->result($query, 0);
		$postsnum += $postsnum2;
		$db->query("UPDATE $table_members SET postnum='$postsnum' WHERE username='$username'");
	}

	if($processed) {
		cpmsg("重建用户发贴数：正在处理用户从 $current 到 $next", $nextlink);
	} else {
		cpmsg("用户发帖数重建完成。");
	}
}

?>