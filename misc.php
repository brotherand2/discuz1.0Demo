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
$tplnames = "css,header,footer";

if($action == "search") {
	$navigation = "&raquo; 论坛搜索";
	$navtitle = " - 论坛搜索";
	$useraction = "搜索论坛贴子";
} elseif($action == "announcements") {
	$navigation = "&raquo; 论坛公告";
	$navtitle = " - 论坛公告";
	$useraction = "查看论坛公告";
} elseif($action == "markread") {
	$navigation = "&raquo; 标记已读";
	$navtitle = " - 标记已读";
	$useraction = "将所有贴子标记已读";
} elseif($action == "stats") {
	$navigation = "&raquo; 论坛统计";
	$navtitle = " - 论坛统计";
	$useraction = "查看论坛统计数据";
} elseif($action == "lostpw" || $action == "getpasswd") {
	$navigation = "&raquo; 取回密码";
	$navtitle = " - 取回密码";
	$useraction = "取回用户密码";
} elseif($action == "active") {
	$navigation = "&raquo; 论坛新贴";
	$navtitle = " - 论坛新帖";
	$useraction = "查看论坛新帖";
}

if($action == "markread") {

	if($cdbuser) {
		$db->query("UPDATE $table_members SET lastvisit='$timestamp' WHERE username='$cdbuser'");
	}
	$CDB_SESSION_VARS[lastvisit] = $timestamp;
	setcookie("lastvisit", $timestamp, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
	showmessage("所有论坛已被标记已读，现在将转入论坛首页。", "index.php");

} elseif($action == "search") {

	if(!$allowsearch) {
		showmessage("对不起，您的级别〔{$grouptitle}〕无法使用搜索功能。");
	}

	if(!$searchsubmit && !$page) {

		preloader("misc_search");
		$posts = 0;

		$forumselect = "<select name=\"srchfid\">\n";
		$forumselect .= "<option value=\"all\">&nbsp;&nbsp;> 全部论坛</option>\n".
			"<option value=\"\">&nbsp;</option>\n".
			forumselect()."</select>";
		intval($onlinerecord) <= 500 ? $navcheck = "checked" : $gradcheck = "checked";

		eval("\$search = \"".template("misc_search")."\";");
		echo $search;

	} else {

		if(!isset($srchfid)) {
			$srchfid = "all";
		}

		$srchtxt = trim($srchtxt);
		$srchuname = trim($srchuname);

		$fids = $comma = "";
		foreach($CDB_CACHE_VARS[forums] as $fid => $forum) {
			if((!$forum[viewperm] && $allowview) || ($forum[viewperm] && strstr($forum[viewperm], "\t$groupid\t"))) {
				$fids .= "$comma'$fid'";
				$comma = ", ";
			}
		}

		if(!$srchtxt && !$srchuname && !$srchfrom) {
			showmessage("您没有指定要搜索的关键字或用户名，请返回重新填写。");
		} elseif($srchfid == "") {
			showmessage("您没有指定搜索论坛的范围，请返回重新填写。");
		} elseif(!$fids) {
			showmessage("您的级别〔{$grouptitle}〕没有权力访问任何论坛。");
		}

		preloader("forumdisplay_thread_lastpost,misc_search_result_row,misc_search_result_none,misc_search_results");

		if($srchfrom && !$srchtxt && !$srchuname) {

			$searchfrom = $timestamp - $srchfrom;
			$sqlsrch = "SELECT * FROM $table_threads t WHERE t.fid IN ($fids) AND lastpost>='$searchfrom' AND substring_index(closed,'|',1)<>'moved'";
			if($srchfid != "all" && $srchfid) {
				$sqlsrch .= " AND fid='$srchfid'";
			}
		} else {
			$sqlsrch = "SELECT t.* FROM $table_posts p, $table_threads t WHERE t.fid IN ($fids) AND p.tid=t.tid";
			if(!$srchfrom) {
				$srchfrom = $timestamp;
			}
			$searchfrom = $timestamp - $srchfrom;
			if($srchtxt) {
				if(preg_match("(AND|\+|&|\s)", $srchtxt) && !preg_match("(OR|\|)", $srchtxt)) {
					$andor = "AND";
					$srchtxt = preg_replace("/( AND |&| )/is", "+", $srchtxt);
				} else {
					$andor = "OR";
					$srchtxt = preg_replace("/( OR |\|)/is", "+", $srchtxt);
				}
				$srchtxt = str_replace("*", "%", $srchtxt);
				$srchtxts = explode("+", $srchtxt);
				$sqlsrch .= " AND ((p.message LIKE '%".trim($srchtxts[0])."%' OR p.subject LIKE '%".trim($srchtxts[0])."%')";
				for($i = 1; $i < count($srchtxts); $i++) {
					$srchtxts[$i] = trim($srchtxts[$i]);
					if($srchtxts[$i]) {
						$sqlsrch .= " $andor (p.message LIKE '%".$srchtxts[$i]."%' OR p.subject LIKE '%".$srchtxts[$i]."%')";
					}
				}
				$sqlsrch .= ")";
			}
			if($srchuname) {
			$srchuname = str_replace("*", "%", $srchuname);
				if($srchuname != htmlentities($srchuname)) {
					$srchuname = "%".$srchuname."%";
				}
				$sqlsrch .= " AND p.author LIKE '$srchuname'";
			}
			if($srchfid != "all" && $srchfid) {
				$sqlsrch .= " AND p.fid='$srchfid'";
			}
			if($searchfrom) {
				$sqlsrch .= " AND p.dateline >= '$searchfrom'";
			}

			$sqlsrch .= " GROUP BY tid";
		}

		$highlight = "&highlight=".rawurlencode(str_replace("%", "+", $srchtxt)."+".str_replace("%", "+", $srchuname));
		$pagenum = $page + 1;
		if(!$page) {
			$page = 1;
		}
		$offset = ($page - 1) * $tpp;

		if($dispmode == "gradual") {
			$multipage = "<a href=\"misc.php?action=search&srchtxt=".rawurlencode($srchtxt)."&srchuname=".rawurlencode($srchuname)."&srchfid=$srchfid&srchfrom=$srchfrom&page=".($page + 1)."&dispmode=gradual\"><< &nbsp; &nbsp; &nbsp;下 $tpp 条匹配结果 &nbsp; &nbsp; &nbsp;>></a>";
		} else {
			$keywords = $srchtxt."|".$srchuname."|".$srchfid."|".$srchfrom;
			$query = $db->query("SELECT num FROM $table_searchindex WHERE keywords='$keywords' AND ($timestamp-1800)<dateline");
			$result = $db->fetch_array($query);
			if(isset($result[num])) {
				$total = $result[num];
			} else {
				$querysrch = $db->query($sqlsrch);
				$total = $db->num_rows($querysrch);
				$db->query("INSERT INTO $table_searchindex VALUES ('$keywords', '$total', '$timestamp')");
			}
						
			$multipage = multi($total, $tpp, $page, "misc.php?action=search&srchtxt=".rawurlencode($srchtxt)."&srchuname=".rawurlencode($srchuname)."&srchfid=$srchfid&srchfrom=$srchfrom&dispmode=nav");
			if($total) {
				$multipage .= " &nbsp; &nbsp; 共 $total 个结果";
			}
		}

		$querysrch = $db->query($sqlsrch." ORDER BY lastpost DESC LIMIT $offset, $tpp");

		if (!isset($page)) {
			$page = 1;
			$start = 0;
		} else {
			$start = ($page - 1) * $tpp;
		}

		while($thread = $db->fetch_array($querysrch)) {
			$author = rawurlencode($thread[author]);
			$forumname = $CDB_CACHE_VARS[forums][$thread[fid]][name];
			if($thread[lastposter] != "游客") {
				$lastposter = "<a href=\"member.php?action=viewpro&username=".rawurlencode($thread[lastposter])."\">$thread[lastposter]</a>";
			}
			$lastreplytime = gmdate("$dateformat $timeformat", $thread[lastpost] + ($timeoffset * 3600));
			$lastpost = "$lastreplytime<br>by $lastposter";

			$postsnum = $thread[replies] + 1;
			if($postsnum  > $ppp) {
				$posts = $postsnum;
				$topicpages = $posts / $ppp;
				$topicpages = ceil($topicpages);
				for ($i = 1; $i <= $topicpages; $i++) {
					$pagelinks .= "<a href=\"viewthread.php?tid=$thread[tid]&page=$i$highlight\">$i</a> ";
					if($i == 6) {
						$i = $topicpages + 1;
					}
				}
				if($topicpages > 6) {
					$pagelinks .= " .. <a href=\"viewthread.php?tid=$thread[tid]&page=$topicpages$highlight\">$topicpages</a> ";
				}
				$multipage2 = "&nbsp;&nbsp;&nbsp;( <img src=\"$imgdir/multipage.gif\" align=\"absmiddle\" boader=0> $pagelinks)";
				$pagelinks = "";
			} else {
				$multipage2 = "";
			}

			if($thread[digist]) {
				switch($thread[digist]) {
					case 1: $level = "Ⅰ"; break;
					case 2: $level = "Ⅱ"; break;
					case 3: $level = "Ⅲ"; break;
				}
				$prefix = "<img src=\"$imgdir/digist.gif\" align=\"absmiddle\">&nbsp;精华{$level}：";
			} elseif($thread[topped]) {
				switch($thread[topped]) {
					case 1: $level = "Ⅰ"; break;
					case 2: $level = "Ⅱ"; break;
					case 3: $level = "Ⅲ"; break;
				}
				$prefix = "<img src=\"$imgdir/pin.gif\" align=\"absmiddle\">&nbsp;置顶{$level}：";
			} elseif($thread[pollopts]) {
				$prefix = "<img src=\"$imgdir/pollsmall.gif\" align=\"absmiddle\">&nbsp;投票：";
			}
			if($thread[attachment]) {
				$prefix .= attachicon($thread[attachment])." ";
			}

			eval("\$lastpost = \"".template("forumdisplay_thread_lastpost")."\";");
			eval("\$searchresults .= \"".template("misc_search_result_row")."\";");
			$prefix = "";
			$found = 1;
		}
		if(!$found) {
			eval("\$searchresults = \"".template("misc_search_result_none")."\";");
		}

		eval("\$search = \"".template("misc_search_results")."\";");
		echo $search;
		$db->query("DELETE FROM $table_searchindex WHERE ($timestamp-1800)>dateline");
	}

} elseif($action == "announcements") {

	$total = 0;
	$query = $db->query("SELECT id, endtime FROM $table_announcements");
	while($announcement = $db->fetch_array($query)) {
		if($timestamp >= $announcement[starttime] && ($timestamp <= $announcement[endtime] || !$announcement[endtime])) {
			$total++;
			if($announcement[id] == $id) {
				$page = ceil($total / $ppp);
			}
		}
	}
	if($total != $db->num_rows($query)) {
		$db->query("DELETE FROM $table_announcements WHERE endtime<>'0' AND endtime<'$timestamp'");
		updatecache("announcements");
	}

	if(!$total) {
		showmessage("目前没有公告供查看，请返回。");
	}

	preloader("misc_announcements,misc_announcement_row");

	if (!$page && !$id) {
		$page = 1;
		$start = 0;
	} else {
		$start = ($page - 1) * $ppp;
	}
	$multipage = multi($total, $ppp, $page, "misc.php?action=announcements");

	$announcement_row = "";
	$query = $db->query("SELECT * FROM $table_announcements WHERE endtime='0' OR endtime>'$timestamp' ORDER BY starttime DESC, id DESC LIMIT $start, $ppp");
	while($announcement = $db->fetch_array($query)) {
		$encodeauthor = rawurlencode($announcement[author]);
		$announcestart = gmdate("$dateformat", $announcement[starttime] + $timeoffset * 3600);
		$announceend = $announcement[endtime] ? gmdate("$dateformat", $announcement[endtime] + $timeoffset * 3600) : "不限";
		$announcement[message] = postify($announcement[message], 0, 0, 0, 1, 0, 1, 1);
		eval("\$announcement_row .= \"".template("misc_announcement_row")."\";");
	}

	eval("\$announcements = \"".template("misc_announcements")."\";");
	echo $announcements;

} elseif($action == "lostpw") {

	if(!$lostpwsubmit) {
		preloader("misc_lostpw");
		eval("\$lostpw = \"".template("misc_lostpw")."\";");
		echo $lostpw;
	} else {
		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE username='$username' AND email='$email'");
		if(!$db->result($query, 0)) {
			showmessage("用户名或 Email 地址不匹配，请返回修改。");
		}

		$newpass = random(20);
		$rcvtime = $timestamp;
		$member[username] = addslashes($member[username]);
		$db->query("UPDATE $table_members SET pwdrecover='$newpass', pwdrcvtime='$rcvtime' WHERE username='$member[username]' AND email='$member[email]'");

		sendmail($member[email], "[Discuz!] 取回密码邮件",
				"您好 $member[username]，这是 $bbname 系统发送的取回密码邮件，请访问下面的链接修改您的密码。\n\n".
				"{$boardurl}misc.php?action=getpasswd&id=$newpass\n\n".
				"欢迎您光临 $bbname\n".
				"$boardurl",
			"From: $bbname <$adminemail>");

		showmessage("取回密码的方法已经通过 Email 发送到您的信箱中，请在 10 天之内到论坛修改您的密码。", "index.php");
	}

} elseif($action == "getpasswd") {

	if(!$id) {
		showmessage("无法取回密码，请访问论坛发送的邮件上提供的取回密码的链接。");
	}
	$query = $db->query("SELECT username, pwdrecover, pwdrcvtime FROM $table_members WHERE pwdrecover='$id'");
	$recover = $db->fetch_array($query);
	if(!$recover[pwdrecover] || ($timestamp - $recover[pwdrcvtime]) > 864000) {
		$query = $db->query("UPDATE $table_members SET pwdrecover='', pwdrcvtime='' WHERE pwdrecover='$id'");
		showmessage("您所用的 ID 不存在或已经超过 10 天的有效期，无法取回密码。");
	}
	if(!$getpasswdsubmit) {
		$message = "您好 $recover[username]。请在下面输入您的新密码并牢记：<br><br>";
		$message .= "<form method=\"post\" action=\"misc.php?action=getpasswd&id=$id\">";
		$message .= "<input type=\"text\" name=\"newpasswd\" size=\"25\">&nbsp;&nbsp;<input type=\"submit\" name=\"getpasswdsubmit\" value=\"提  交\">";
		$nessage .= "</form><br>";
		showmessage($message);
	} else {
		$password = encrypt($newpasswd);
		$query = $db->query("UPDATE $table_members SET password='$password', pwdrecover='', pwdrcvtime='' WHERE pwdrecover='$id'");
		showmessage("新密码已设置为：$newpasswd &nbsp;&nbsp;&nbsp;&nbsp;请妥善保管！");
	}	

} elseif($action == "stats") {

	if(!$allowviewstats) {
		showmessage("对不起，您的级别〔{$grouptitle}〕无法浏览统计数据。");
	}

	$tplnames .= ",misc_stats";
	if(!$type) {
		$vartype = "'total', 'month', 'hour'";
	} elseif($type == "agent") {
		$vartype = "'os', 'browser'";
	} else {
		$vartype = "'$type'";
	}

	$query = $db->query("SELECT * FROM $table_stats WHERE type IN ($vartype) ORDER BY type");
	while($stats = $db->fetch_array($query)) {
		switch($stats[type]) {
			case total:
				$stats_total[$stats["var"]] = $stats[count];
				break;
			case os:
				$stats_os[$stats["var"]] = $stats[count];
				if($stats[count] > $maxos) {
					$maxos = $stats[count];
				}
				break;
			case browser:
				$stats_browser[$stats["var"]] = $stats[count];
				if($stats[count] > $maxbrowser) {
					$maxbrowser = $stats[count];
				}
				break;
			case month:
				$stats_month[$stats["var"]] = $stats[count];
				if($stats[count] > $maxmonth) {
					$maxmonth = $stats[count];
					$maxmonth_year = intval($stats["var"] / 100);
					$maxmonth_month = $stats["var"] - $maxmonth_year * 100;
				}
				break;
			case week:
				$stats_week[$stats["var"]] = $stats[count];
				if($stats[count] > $maxweek) {
					$maxweek = $stats[count];
					$maxweek_day = $stats["var"];
				}
				break;
			case hour:
				$stats_hour[$stats["var"]] = $stats[count];
				if($stats[count] > $maxhour) {
					$maxhour = $stats[count];
					$maxhourfrom = $stats["var"];
					$maxhourto = $maxhourfrom + 1;
				}
				break;
		}
	}

	if(!$type) {

		preloader("misc_stats_main");

		$query = $db->query("SELECT COUNT(*) FROM $table_forums WHERE type='forum' OR type='sub'");
		$forums = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_threads");
		$threads = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*), (MAX(dateline) - MIN(dateline)) / 86400 FROM $table_posts");
		list($posts, $runtime) = $db->fetch_row($query);

		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE dateline >= '".($timestamp - 86400)."'");
		$postsaddtoday = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE regdate >= '".($timestamp - 86400)."'");
		$membersaddtoday = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members");
		$members = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE status='论坛管理员' OR status='超级版主' OR status='版主'");
		$admins = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE postnum='0'");
		$memnonpost = $db->result($query, 0);
		$mempost = $members - $memnonpost;

		@$mempostavg = sprintf ("%01.2f", $posts / $members);
		@$threadreplyavg = sprintf ("%01.2f", ($posts - $threads) / $threads);
		@$mempostpercent = sprintf ("%01.2f", 100 * $mempost / $members);
		@$postsaddavg = round($posts / $runtime);
		@$membersaddavg = round($members / $runtime);

		$query = $db->query("SELECT author, COUNT(*) AS postnum FROM $table_posts WHERE dateline >= '".($timestamp - 86400)."' GROUP BY author ORDER BY postnum DESC LIMIT 0, 1");
		list($bestmem, $bestmempost) = $db->fetch_row($query);
		if($bestmem) {
			$bestmem = "<a href=\"member.php?action=viewpro&username=".rawurlencode($bestmem)."\"><span class=\"bold\">$bestmem</span></a>";
		} else {
			$bestmem = "无";
			$bestmempost = 0;
		}

		$query = $db->query("SELECT posts, threads, fid, name FROM $table_forums ORDER BY posts DESC LIMIT 0, 1");
		$hotforum = $db->fetch_array($query);

		$stats_total[visitors] = $stats_total[members] + $stats_total[guests];
		@$pageviewavg = sprintf ("%01.2f", $stats_total[hits] / $stats_total[visitors]);
		@$activeindex = round(($membersaddavg / $members + $postsaddavg / $posts) * 1500 + $threadreplyavg * 10 + $mempostavg * 1 + $mempostpercent / 10 + $pageviewavg);

		$statsbar_month = statsdata("month", $maxmonth);
		eval("\$statsdata = \"".template("misc_stats_main")."\";");

	} elseif($type == "week") {

		preloader("misc_stats_week");
		$statsbar_week = statsdata("week", $maxweek);
		eval("\$statsdata = \"".template("misc_stats_week")."\";");

	} elseif($type == "hour") {

		preloader("misc_stats_hour");
		$statsbar_hour = statsdata("hour", $maxhour);
		eval("\$statsdata = \"".template("misc_stats_hour")."\";");

	} elseif($type == "agent") {

		preloader("misc_stats_agent");
		$statsbar_os = statsdata("os", $maxos, "no");
		$statsbar_browser = statsdata("browser", $maxbrowser, "no");
		eval("\$statsdata = \"".template("misc_stats_agent")."\";");

	} elseif($type == "threads") {

		preloader("misc_stats_threads");

		$threads = "";
		$threadview = $threadreply = array();
		$query = $db->query("SELECT views, tid, subject FROM $table_threads ORDER BY views DESC LIMIT 0, 20");
		while($thread = $db->fetch_array($query)) {
			$thread[subject] = wordscut($thread[subject], 45);
			$threadview[] = $thread;
		}
		$query = $db->query("SELECT replies, tid, subject FROM $table_threads ORDER BY replies DESC LIMIT 0, 20");
		while($thread = $db->fetch_array($query)) {
			$thread[subject] = wordscut($thread[subject], 50);
			$threadreply[] = $thread;
		}
		for($i = 0; $i < 20; $i++) {
			$bgcolor = $bgcolor ? "" : "bgcolor=\"$altbg2\"";
			$threads .= "<tr $bgcolor><td><a href=\"viewthread.php?tid=".$threadview[$i][tid]."\">".$threadview[$i][subject]."</a></td><td align=\"right\">".$threadview[$i][views]."</td><td bgcolor=\"$altbg1\"></td>\n".
				"<td><a href=\"viewthread.php?tid=".$threadreply[$i][tid]."\">".$threadreply[$i][subject]."</a><td align=\"right\">".$threadreply[$i][replies]."</td></tr>\n";
		}
		eval("\$statsdata = \"".template("misc_stats_threads")."\";");

	} elseif($type == "member") {

		preloader("misc_stats_member");

		$members = "";
		$total = $thismonth = $today = array();
		$query = $db->query("SELECT username, uid, credit FROM $table_members ORDER BY credit DESC LIMIT 0, 20");
		while($credits[] = $db->fetch_array($query)) {}

		$query = $db->query("SELECT username, uid, postnum FROM $table_members ORDER BY postnum DESC LIMIT 0, 20");
		while($total[] = $db->fetch_array($query)) {}

		$query = $db->query("SELECT DISTINCT(author) AS username, COUNT(pid) AS postnum FROM $table_posts WHERE dateline >= ".($timestamp - 86400 * 30)." GROUP BY author ORDER BY postnum DESC LIMIT 0, 20");
		while($thismonth[] = $db->fetch_array($query)) {}

		$query = $db->query("SELECT DISTINCT(author) AS username, COUNT(pid) AS postnum FROM $table_posts WHERE dateline >= ".($timestamp - 86400)." GROUP BY author ORDER BY postnum DESC LIMIT 0, 20");
		while($today[] = $db->fetch_array($query)) {}

		for($i = 0; $i < 20; $i++) {
			$bgcolor = $bgcolor ? "" : "bgcolor=\"$altbg2\"";
			$members .= "<tr $bgcolor><td>★ - <a href=\"member.php?action=viewpro&username=".rawurlencode($credits[$i][username])."\">".$credits[$i][username]."</a></td><td align=\"right\">".$credits[$i][credit]." $creditunit</td><td bgcolor=\"$altbg1\"></td>\n".
				"<td>◆ - <a href=\"member.php?action=viewpro&username=".rawurlencode($total[$i][username])."\">".$total[$i][username]."</a></td><td align=\"right\">".$total[$i][postnum]." 篇</td><td bgcolor=\"$altbg1\"></td>\n".
				"<td>☆ - <a href=\"member.php?action=viewpro&username=".rawurlencode($thismonth[$i][username])."\">".$thismonth[$i][username]."</a></td><td align=\"right\">".$thismonth[$i][postnum]." 篇</td><td bgcolor=\"$altbg1\"></td>\n".
				"<td>◇ - <a href=\"member.php?action=viewpro&username=".rawurlencode($today[$i][username])."\">".$today[$i][username]."</a></td><td align=\"right\">".$today[$i][postnum]." 篇</td></tr>\n";
		}

		eval("\$statsdata = \"".template("misc_stats_member")."\";");

	} else {

		showmessage("您没有选择要查看的统计类型。");

	}
	eval("\$stats = \"".template("misc_stats")."\";");
	echo $stats;

} else {

	$useraction = "未定义操作 [MISC]";
	showmessage("未定义操作，请返回。");

}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

function statsdata($type, $max, $sort = "yes") {
	global $barno;

	$statsbar = "";
	$sum = 0;

	$datarray = $GLOBALS["stats_$type"];
	if(is_array($datarray)) {
		if($sort == "yes") {
			ksort($datarray);
		}
		foreach($datarray as $count) {
			$sum += $count;
		}
	} else {
		$datarray = array();
	}

	foreach($datarray as $var => $count) {
		$barno ++;
		switch($type) {
			case month:
				$var = substr($var, 0, 4)."-".substr($var, -2);
				break;
			case week:
				switch($var) {
					case 00: $var = "星期日"; break;
					case 01: $var = "星期一"; break;
					case 02: $var = "星期二"; break;
					case 03: $var = "星期三"; break;
					case 04: $var = "星期四"; break;
					case 05: $var = "星期五"; break;
					case 06: $var = "星期六"; break;
				}
				break;
			case hour:
				$var = intval($var);
				if($var < 6) {
					$var = "凌晨 $var 时";
				} elseif($var < 12) {
					$var = "上午 $var 时";
				} elseif($var == 12) {
					$var = "中午 $var 时";
				} else {
					$var -= 12;
					if($var < 6) {
						$var = "下午 $var 时";
					} else {
						$var = "晚上 $var 时";
					}
				}
				break;
			default:
				$var = "<img src=\"images/stats/".strtolower(str_replace("/", "", $var)).".gif\" border=\"0\"> $var";
				break;
		}
		@$width = intval(370 * $count / $max);
		@$percent = sprintf ("%01.1f", 100 * $count / $sum);
		$width = $width ? $width : "2";
		$var = $count == $max ? "<span class=\"bold\"><i>$var</i></span>" : $var;
		$count = "<img src=\"images/common/bar".($barno % 10 + 1).".gif\" width=\"$width\" height=\"10\" border=\"0\"> &nbsp; <span class=\"bold\">$count</span> ($percent%)";
		$statsbar .= "<tr><td width=\"100\">$var</td><td width=\"500\">$count</td></tr>\n";
	}

	return $statsbar;
}

?>