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

$tplnames = "css,header,footer,forumdisplay_whosonline,forumdisplay_thread_lastpost,forumdisplay_thread,forumdisplay";
$tplnames .= $fastpost ? ",forumdisplay_fastpost" : NULL;//如果是否快速发贴,加载相应快速发贴模板
$ismoderator = modcheck($cdbuser);
$useraction = "浏览论坛『$forum[name]』";

if(!$forum[fid] || $forum[type] == "group") {
	showmessage("指定的论坛不存在，请返回。");
}

if($forum[type] == "forum") {
	$navigation .= "&raquo; $forum[name]";
	$navtitle .= " - $forum[name]";
} else {
	$forumup = $CDB_CACHE_VARS[forums][$forum[fup]][name];
	$navigation .= "&raquo; <a href=\"forumdisplay.php?fid=$forum[fup]\">$forumup</a> &raquo; $forum[name]";
	$navtitle .= " - $forumup - $forum[name]";
}

if($forum[password] && $action == "pwverify") {
	if($pw != $forum[password]) {
		showmessage("您输入的密码不正确，不能访问这个论坛。");
	} else {
		setcookie("fidpw$fid", $pw, "0", $cookiepath, $cookiedomain);
		$CDB_SESSION_VARS["fidpw$fid"] = $pw;
		header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	}
}

if($forum[viewperm] && !strstr($forum[viewperm], "\t$groupid\t")) {
	showmessage("对不起，本论坛只有特定用户可以访问，请返回。");
}

if($forum[password] != $_COOKIE["fidpw$fid"] && $forum[password] != $CDB_SESSION_VARS["fidpw$fid"] && $forum[password]) {
	$url = "forumdisplay.php?fid=$fid&action=pwverify";
	eval("\$pwforum = \"".template("forumdisplay_password")."\";");
	showmessage($pwforum, "", 1);
}

$moderatedby = moddisplay($forum[moderator], "forumdisplay");
$moderatedby = !$moderatedby ? "*空缺中*" : $moderatedby;

$subexists = 0;
$forumlist = "";
foreach($CDB_CACHE_VARS[forums] as $sub) {
	if($sub[type] == "sub" && $sub[fup] == $fid && (!$sub[viewperm] || ($sub[viewperm] && strstr($sub[viewperm], "\t$groupid\t")))) {
		$subexists = 1;
		break;
	}
}

preloader($subexists ? "index_forum_lastpost,forumdisplay_subforum,forumdisplay_subforums" : NULL);

if($subexists) {
	$querys = $db->query("SELECT * FROM $table_forums WHERE status='1' AND type='sub' AND fup='$fid' ORDER BY displayorder");
	while($sub = $db->fetch_array($querys)) {
		$forumlist .= forum($sub, "forumdisplay_subforum");
	}
	eval("\$subforums = \"".template("forumdisplay_subforums")."\";");
}

$newpolllink = $allowpostpoll ? "&nbsp;<a href=\"post.php?action=newthread&fid=$fid&poll=yes\"><img src=\"$imgdir/poll.gif\" border=\"0\"></a>" : NULL;

if($page) {
	$start_limit = ($page - 1) * $tpp;
} else {
	$start_limit = 0;
	$page = 1;
}

$forumdisplayadd = $filteradd = "";
if($filter && $filter != "digist") {
	$forumdisplayadd .= "&filter=$filter";
	$filteradd = "AND lastpost>='".($timestamp - $filter)."'";
} elseif($filter == "digist") {
	$forumdisplayadd .= "&filter=digist";
	$filteradd = "AND digist<>'0'";
}

!$ascdesc ? $ascdesc = "DESC" : $forumdisplayadd .= "&ascdesc=$ascdesc";
$dotadd1 = $dotadd2 = "";
if($dotfolders && $cdbuser) {
	$dotadd1 = "DISTINCT p.author AS dotauthor, ";
	$dotadd2 = "LEFT JOIN $table_posts p ON (t.tid=p.tid AND p.author='$cdbuser')";
}

if($whosonlinestatus) {
	$onlinenum = 0;
	$memtally = $table = "";
	$online = array("username" => $cdbuserss, "time" => $timestamp, "action" => "浏览论坛『".strip_tags($forum[name])."』", "status" => $status);
	$query = $db->query("SELECT username, status, time, fid, action, username='' AS guests FROM $table_sessions WHERE fid='$fid' AND !(username='$cdbuser' AND ip='$onlineip') ORDER BY guests");
	do {
		$onlinenum++;
		$online[time] = gmdate("$timeformat", $online[time] + ($timeoffset * 3600));
		$onlinedetail = "时间：$online[time]\n论坛：".strip_tags($forum[name])."\n动作：$online[action]";

		if($online[status] == "论坛管理员") {
			$memtally .= "$table<img src=\"$imgdir/online_admin.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\"><b><i>$online[username]</i></b></a>";
		} elseif($online[status] == "超级版主" || $online[status] == "版主") {
			$memtally .= "$table<img src=\"$imgdir/online_moderator.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\"><b>$online[username]</b></a>";
		} elseif($online[status] == "正式会员") {
			$memtally .= "$table<img src=\"$imgdir/online_member.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\">$online[username]</a>";
		} else {
			$memtally .= "$table<img src=\"$imgdir/online_guest.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <span title=\"$onlinedetail\">游客</span>";
		}
		$table = $onlinenum % 7 == 0 ? "</td></tr><tr><td nowrap>" : "</td><td nowrap>";
	} while($online = $db->fetch_array($query));
	$memberlist = "<tr><td nowrap>$memtally</td></tr>";
	eval("\$forumwhosonline = \"".template("forumdisplay_whosonline")."\";");
} else {
	$forumwhosonline = "";
}

if($filteradd) {
	$query = $db->query("SELECT COUNT(*) FROM $table_threads WHERE fid='$fid' $filteradd");
	$topicsnum = $db->result($query, 0);
} else {
	$topicsnum = $forum[threads];
}

$multipage = multi($topicsnum, $tpp, $page, "forumdisplay.php?fid=$fid$forumdisplayadd");
$delthread = $ismoderator && $topicsnum ? " &nbsp; &nbsp; <img src=\"$imgdir/delthread.gif\" border=\"0\" align=\"absmiddle\"> <a href=\"###\" onclick=\"this.document.delthread.submit();\">删除选定主题</a>" : NULL;
$showthread = $filter == "digist" ? "<a href=\"forumdisplay.php?fid=$fid\">查看全部主题</a>" : "<a href=\"forumdisplay.php?fid=$fid&filter=digist\">查看本版精华</a>";

$query = $db->query("SELECT $dotadd1 t.* FROM $table_threads t $dotadd2 WHERE t.fid='$fid' $filteradd ORDER BY t.topped DESC, t.lastpost $ascdesc LIMIT $start_limit, $tpp");
while($thread = $db->fetch_array($query)) {
	if($thread[lastposter] != "游客") {
		$lastposter = "<a href=\"member.php?action=viewpro&username=".rawurlencode($thread[lastposter])."\">$thread[lastposter]</a>";
	}
	$lastreplytime = gmdate("$dateformat $timeformat", $thread[lastpost] + ($timeoffset * 3600));
	$lastpost = "$lastreplytime<br>by $lastposter";
	eval("\$lastpostrow = \"".template("forumdisplay_thread_lastpost")."\";");
	if($thread[icon]) {
		$thread[icon] = "<img src=\"$smdir/$thread[icon]\" align=\"absmiddle\">";
	} else {
		$thread[icon] = "&nbsp;";
	}

	if($thread[closed]) {
		if(substr($thread[closed], 0, 5) == "moved") {
			$prefix = "移动：";
			$thread[tid] = substr($thread[closed], 6);
			$thread[replies] = "-";
			$thread[views] = "-";
		}
		$folder = "lock_folder.gif";
	} else {
		$folder = "folder.gif";
		$folder = $lastvisit < $thread[lastpost] && !strstr($_COOKIE[oldtopics], "\t$thread[tid]\t") ? "red_$folder" : $folder;
		$folder = $thread[replies] >= $hottopic ? "hot_$folder" : $folder;
		$folder = $dotfolders && $thread[dotauthor] == $cdbuser && $cdbuser ? "dot_$folder" : $folder;
	}
	$folder = "<a href=\"viewthread.php?tid=$thread[tid]\" target=\"_blank\"><img src=\"$imgdir/$folder\" border=\"0\"></a>";

	$thread[subject] = censor($thread[subject]);
	$thread[subject] .= $thread[creditsrequire] ? " - [$credittitle<span class=\"bold\">$thread[creditsrequire]</span>$creditunit]" : NULL;
	$authorinfo = $thread[author] != "游客" ? "<a href=\"member.php?action=viewpro&username=".rawurlencode($thread[author])."\">$thread[author]</a>" :$thread[author];
	$authorinfo .= "<br>".gmdate($dateformat, $thread[dateline] + ($timeoffset * 3600));

	$postsnum = $thread[replies] + 1;
	if($postsnum  > $ppp) {
		$posts = $postsnum;
		$topicpages = ceil($posts / $ppp);
		for ($i = 1; $i <= $topicpages; $i++) {
			$pagelinks .= "<a href=\"viewthread.php?tid=$thread[tid]&page=$i\">$i</a> ";
			if($i == 6) {
				$i = $topicpages + 1;
			}
		}
		if($topicpages > 6) {
			$pagelinks .= " .. <a href=\"viewthread.php?tid=$thread[tid]&page=$topicpages\">$topicpages</a> ";
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
	if($ismoderator) {
		$prefix = "<input type=\"checkbox\" name=\"delete[]\" value=\"$thread[tid]\"> $prefix";
	}

	eval("\$threadlist .= \"".template("forumdisplay_thread")."\";");
	$topicsnum++;
	$prefix = "";
}

if(!$topicsnum) {
	eval("\$threadlist = \"".template("forumdisplay_nothreads")."\";");
}

$check[$filter] = "selected=\"selected\"";
$ascdesc == "ASC" ? $check[asc] = "selected=\"selected\"" : $check[desc] = "selected=\"selected\"";

$forumselect = forumselect();
if($fastpost && ((!$forum[postperm] && $allowpost) || ($forum[postperm] && strstr($forum[postperm], "\t$groupid\t")))) {
	if($signature) {
		$usesigcheck = "checked";
	}
	if((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postattachperm], "\t$groupid\t"))) {
		$enctype = "enctype=\"multipart/form-data\"";
	} else {
		$enctype = "";
	}
	eval("\$fastpost_newthread = \"".template("forumdisplay_fastpost")."\";");
}

eval("\$forumdisplay = \"".template("forumdisplay")."\";");
echo $forumdisplay;

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

?>
