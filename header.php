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


define("IN_CDB", TRUE);
require "./config.php";
require "./functions.php";
require "./lib/$database.php";

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_magic_quotes_runtime(0);//magic_quotes_runtime 打开时，所有外部引入的数据库资料或者文件等等都会自动转为含有反斜线溢出字符的资料

$mtime = explode(" ", microtime());
$starttime = $mtime[1] + $mtime[0];

$timestamp = time();
$cdb_charset = $charset;
$poweredbycdb = "CDB - Crossday Bulletin";
$magic_quotes_gpc = get_magic_quotes_gpc();
$register_globals = @get_cfg_var("register_globals");

$PHP_SELF = $_SERVER["PHP_SELF"];
$boardurl = "http://$_SERVER[HTTP_HOST]".dirname($_SERVER[PHP_SELF])."/";
$CDB_SESSION_VARS = $CDB_CACHE_VARS = array();//CDB_CACHE_VARS统计页面的缓存,用户信息如设置,主题,通告等,有缓存哪些变量,可查看updatecache函数
$url_redirect = "";

$tables = array('attachments', 'announcements', 'banned', 'caches', 'favorites', 'forumlinks', 'forums', 'members', 'memo',
	'news', 'posts', 'searchindex', 'sessions', 'settings', 'smilies', 'stats', 'subscriptions', 'templates', 'themes',
	'threads', 'u2u', 'usergroups', 'words', 'buddys');
foreach($tables as $tablename)
{
	${"table_".$tablename} = $tablepre.$tablename;//加上 table 前缀是为国解决可能的命名重复
}
unset($tablename);

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $pconnect);
$db->select_db($dbname);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

$cachenames = "'settings'";//需要查看哪些缓存表
$currscript = basename($PHP_SELF);
switch($currscript) {
    //setting后台常规选项里面的配置,announcements通告,news 是滚动新闻,forumlinks显示在下面的联盟论坛数据
	case "index.php": $cachenames .= !$gid ? ", 'announcements', 'news', 'forumlinks'" : NULL; break;//首页才显示通告等
	case "forumdisplay.php": $cachenames .= ", 'forums', 'censor'"; break;
	case "viewthread.php": $cachenames .= ", 'forums', 'usergroups', 'censor', 'smilies'"; break;
	case "post.php": $cachenames .= ", 'smilies', 'picons', 'censor'"; break;
	case "misc.php": $cachenames .= $_GET[action] == "search" ? ", 'forums'" : NULL; break;
	case "member.php": $cachenames .= ", 'censor'"; break;
	case "u2u.php": $cachenames .= ", 'censor', 'smilies'"; break;
}
$query = $db->query("SELECT * FROM $table_caches WHERE cachename IN ($cachenames)");
while($cache = $db->fetch_array($query)) {
	$CDB_CACHE_VARS[$cache[cachename]] = unserialize($cache[cachevars]);
}
@extract($CDB_CACHE_VARS[settings]);//解压基本设置里面的变量
unset($cache);

if($_POST[cookie_time]) {
	$cookietime = $_POST[cookie_time];
	setcookie("cookietime", $cookietime, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);//cookietime这个变量有效时间一年
} elseif(!$_COOKIE["cookietime"]) {
	$cookietime = 2592000;// cookie 默认有效时间一个月
	setcookie("cookietime", $cookietime, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
} else {
	$cookietime = $_COOKIE[cookietime];
}
//获取用户 IP
if(getenv("HTTP_CLIENT_IP")) {
	$onlineip = getenv("HTTP_CLIENT_IP");
} elseif(getenv("HTTP_X_FORWARDED_FOR")) {
	$onlineip = getenv("HTTP_X_FORWARDED_FOR");
} elseif(getenv("REMOTE_ADDR")) {
	$onlineip = getenv("REMOTE_ADDR");
} else {
	$onlineip = $_SERVER[REMOTE_ADDR];
}
//获取会话 ID,用于识别用户,查看用户配置,优先级,post>get>cookie,一个 IP 一个会话,相同 IP 换浏览器,换用户,当前 IP 的会话会覆盖
$sid = $_GET[sid] ? $_GET[sid] : $_COOKIE[sid];
$sid = $_POST[sid] ? $_POST[sid] : $sid;

$query = $db->query("SELECT ip, sessionvars FROM $table_sessions WHERE sid='$sid'");
@extract($db->fetch_array($query), EXTR_OVERWRITE);//查找当前会话的配置信息
if($ip != $onlineip || ($sid != $_COOKIE[sid] && $_COOKIE) || !$sid) {
	$sid = random(32, "session");//!$sid未登录或重新打开,$ip != $onlineip在另一台机子登录,$sid != $_COOKIE[sid]在另一浏览器登录需要重新创建会话
	$sessionvars = "";
}

$currtime = cookietime();//过期时间
$currtime = $currtime <= ($timestamp + 3600) ? $currtime : $timestamp + 3600;//确保 COOKIE 在1小时内不过期
setcookie("sid", $sid, $timestamp + 3600, $cookiepath, $cookiedomain);//设置 sid 进 COOKIE,1小时
if(!$sessionvars) {
	$sessionexists = 0;
	$status = "游客";//用户身份
	$ips = explode(".", $onlineip);
	$query = $db->query("SELECT id FROM $table_banned WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')");
	if($db->num_rows($query)) {
		$CDB_SESSION_VARS[ipbanned] = 1;
		$status = "禁止IP";
	} else {
		$CDB_SESSION_VARS[ipbanned] = 0;
	}

	$cdbuser = cdbaddslashes($_COOKIE[_cdbuser]);
	$cdbpw = cdbaddslashes($_COOKIE[_cdbpw]);
	if($cdbuser) {
		$query = $db->query("SELECT m.username as cdbuser, m.password as cdbpw, m.uid, m.charset, m.timeoffset,	m.theme, m.tpp, m.ppp, m.credit,
			m.timeformat, m.dateformat, m.signature, m.avatar, m.lastvisit,	m.newu2u, u.*, u.specifiedusers LIKE '%\t$cdbuser\t%' AS specifieduser
			FROM $table_members m LEFT JOIN $table_usergroups u ON u.specifiedusers LIKE '%\t$cdbuser\t%' OR (u.status=m.status
			AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
			WHERE username='$cdbuser' AND password='$cdbpw' ORDER BY specifieduser DESC");
		$member = $db->fetch_array($query);//当前用户的配置
	}
	if(!$member[uid]) {//当前用户不存在
		$query = $db->query("SELECT * FROM $table_usergroups WHERE status='$status'");//使用当前用户组的配置,一般为游客
		$member = $db->fetch_array($query);
		$cdbuser = $CDB_SESSION_VARS[cdbuser] = $_COOKIE[_cdbuser] = "";
		$lastvisit = $CDB_SESSION_VARS[lastvisit] = $_COOKIE[lastvisit];
		if(($timestamp - $lastvisit) > $onlinehold) {
			setcookie("lastvisit", $timestamp, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
		}
	} else {
		$member[cdbuser] = addslashes($member[cdbuser]);
		$member[signature] = $member[signature] ? 1 : 0;
	}
	$CDB_SESSION_VARS = array_merge($CDB_SESSION_VARS, $member);//会放加上了用户的配置或默认用户组的配置,CDB_SESSION_VARS变量此处开始变多
	$CDB_SESSION_VARS[theme] = $member[theme] ? $member[theme] : $CDB_CACHE_VARS[settings][theme];
	unset($member, $ips);
}
else {
	$sessionexists = 1;
	$CDB_SESSION_VARS = unserialize($sessionvars);
	if($CDB_SESSION_VARS[ipbanned]) {
		$query = $db->query("SELECT * FROM $table_usergroups WHERE status='禁止IP'");
		$CDB_SESSION_VARS = $db->fetch_array($query);
		$cdbuser = $CDB_SESSION_VARS[cdbuser] = $_COOKIE[_cdbuser] = "";
	}
	unset($sessionvars);
}

$style = $_GET[style] ? $_GET[style] : $_POST[style];
if($style) {
	$CDB_SESSION_VARS[theme] = $style;
	$CDB_SESSION_VARS[themename] = "";
}
if(!$CDB_SESSION_VARS[themename]) {
	$query = $db->query("SELECT * FROM $table_themes WHERE themename='$CDB_SESSION_VARS[theme]'");
	if(!$thememember = $db->fetch_array($query)) {//没有主题
		$query = $db->query("SELECT * FROM $table_themes LIMIT 0, 1");//默认使用第1条
		$thememember = $db->fetch_array($query);
	}
	$CDB_SESSION_VARS = array_merge($CDB_SESSION_VARS, $thememember);//保存主题
	unset($thememember);
}

@extract($CDB_SESSION_VARS, EXTR_OVERWRITE);
$cdbuserss = stripslashes($cdbuser);
$credit = intval($credit);
$charset="utf-8";
if($headercharset) {
	header("Content-Type: text/html; charset=$charset");
}

if($_POST) {
	if($cdb_charset != $charset) {
		$mode = $cdb_charset == "big5" ? "gb-big5" : "big5-gb";
		$table = "./lib/$mode.table";
		$fp = fopen($table, "r");
		$codelib = fread($fp, filesize($table));
		fclose($fp);

		@extract(cdbaddslashes(chcode_convert($_POST, $codelib)), EXTR_OVERWRITE);
		unset($codelib);
	} else {
		@extract(cdbaddslashes($_POST), EXTR_OVERWRITE);
	}
}

if(!$register_globals || !$magic_quotes_gpc) {
	@extract(cdbaddslashes($_GET), EXTR_OVERWRITE);
	if(!$register_globals) {
		foreach($_FILES as $key => $val) {
			$$key = $val[tmp_name];
			${$key."_name"} = $val[name];
			${$key."_size"} = $val[size];
			${$key."_type"} = $val[type];
		}
	}
}

unset($_POST, $_FILES);

if($statstatus) {//统计流量,如多少点击,多少客人访问,当前月份有多少访问
	$visitor[agent] = $_SERVER[HTTP_USER_AGENT];
	$visitor[month] = gmdate("Ym", $timestamp + ($timeoffset * 3600));
	$visitor[week] = gmdate("w", $timestamp + ($timeoffset * 3600));
	$visitor[hour] = gmdate("H", $timestamp + ($timeoffset * 3600));

	if(!$sessionexists) {
		if(ereg("MSIE", $visitor[agent])) {
			$visitor[browser] = "MSIE";
		} elseif(ereg("Nav|Gold|X11|Netscape", $visitor[agent])) {
			$visitor[browser] = "Netscape";
		} elseif(ereg("Lynx", $visitor[agent])) {
			$visitor[browser] = "Lynx";
		} elseif(ereg("Opera", $visitor[agent])) {
			$visitor[browser] = "Opera";
		} elseif(ereg("Konqueror", $visitor[agent])) {
			$visitor[browser] = "Konqueror";
		} elseif(ereg("Mozilla", $visitor[agent])) {
			$visitor[browser] = "Mozilla";
		} else {
			$visitor[browser] = "Other";
			$fp = fopen("datatemp/temp.temp", "a");
			$fwrite($fp, $visitor[agent]);
			fclose($fp);
		}

		if(ereg("Win", $visitor[agent])) {
			$visitor[os] = "Windows";
		} elseif((ereg("Mac|PPC", $visitor[agent]))) {
			$visitor[os] = "Mac";
		} elseif(ereg("Linux", $visitor[agent])) {
			$visitor[os] = "Linux";
		} elseif(ereg("FreeBSD", $visitor[agent])) {
			$visitor[os] = "FreeBSD";
		} elseif(ereg("SunOS", $visitor[agent])) {
			$visitor[os] = "SunOS";
		} elseif(ereg("BeOS", $visitor[agent])) {
			$visitor[os] = "BeOS";
		} elseif(ereg("OS/2", $visitor[agent])) {
			$visitor[os] = "OS/2";
		} elseif(ereg("AIX", $visitor[agent])) {
			$visitor[os] = "AIX";
		} else {
			$visitor[os] = "Other";
			$fp = fopen("datatemp/temp.temp", "a");
			$fwrite($fp, $visitor[agent]);
			fclose($fp);
		}
		$visitorsadd = "OR (type='browser' AND var='$visitor[browser]') OR (type='os' AND var='$visitor[os]')";
		$visitorsadd .= $cdbuser ? " OR (type='total' AND var='members')" : " OR (type='total' AND var='guests')";
		$updatedrows = 7;
	} else {
		$visitorsadd = "";
		$updatedrows = 4;//会话不存在才要统计系统,浏览器
	}

	$db->query("UPDATE $table_stats SET count=count+1 WHERE (type='total' AND var='hits') $visitorsadd OR (type='month' AND var='$visitor[month]') OR (type='week' AND var='$visitor[week]') OR (type='hour' AND var='$visitor[hour]')");
	if($updatedrows > $db->affected_rows()) {//小于7或4时,说明过了一个月,当前月的数据没有,要插入
		$db->query("INSERT INTO $table_stats (type, var, count)
			VALUES ('month', '$visitor[month]', '1')");
	}
}

if(!$tpp || !$ppp) {
	$tpp = $topicperpage;
	$ppp = $postperpage;
}

if($regstatus && !$cdbuser) {//允许注册并且没有用户名
	$reglink = "<a href=\"member.php?action=reg\"><font class=\"navtd\">注册</font></a>";
}

$poweredbycdb .= ", www.crossday.com, Dai Zhikang";
if(!$referer) {//不是从某个网站跳过来,如后台退出
	$referer = preg_replace("/(?:([\?&]sid\=[a-z0-9]{32}&?))/is", "", $_SERVER[HTTP_REFERER]);
	$referer = substr($referer, -1) == "?" ? substr($referer, 0, -1) : $referer;//去掉 sid 和?
}

if($cdbuser) {
	$welcome = "<span class=\"bold\">$cdbuserss</span>：";//用户名
	$loginout = "<a href=\"member.php?action=logout\"><font class=\"navtd\">退出</font></a>";
	$memcplink = " | <a href=\"memcp.php\"><font class=\"navtd\">控制面板</font></a>";
	$u2ulink = " | <a href=\"###\" onclick=\"Popup('u2u.php?sid=$sid', 'Window', 580, 450);\"><font class=\"navtd\">短消息</font></a>";
	if($isadmin) {
		$cplink = " | <a href=\"admincp.php\" target=\"_blank\"><font class=\"navtd\">系统设置</font></a>";
	}
	$header_welcome = "";
} else {
	$welcome = "<span class=\"bold\">$grouptitle</span>：";//系统头 xian
	$loginout = " | <a href=\"member.php?action=login\"><font class=\"navtd\">登录</font></a>";
	$header_welcome = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><form action=\"member.php?action=login\" method=\"post\"><tr><td nowrap><input type=\"text\" name=\"username\" size=\"7\">&nbsp;<input type=\"password\" name=\"password\" size=\"7\"> <input type=\"submit\" name=\"loginsubmit\" value=\"登 录\"></td></tr></form></table>";
}

if($tid){
	$query = $db->query("SELECT f.*, t.fid FROM $table_forums f, $table_threads t WHERE t.tid='$tid' AND f.fid=t.fid LIMIT 0, 1");
	$forum = $db->fetch_array($query);
	$fid = $forum[fid];//对应主题的论坛
} elseif($fid) {
	$query = $db->query("SELECT * FROM $table_forums WHERE fid='$fid'");
	$forum = $db->fetch_array($query);
}

$lastvisittext = "现在时间是 ".gmdate("$dateformat $timeformat", $timestamp + ($timeoffset * 3600));

if($lastvisit && $cdbuser) {
	$header_welcome = "欢迎回到 $bbname<br>您上次访问是在 ".gmdate("$dateformat $timeformat", $lastvisit + ($timeoffset * 3600));
} else {
	$lastvisittext .= "<br><br>您还没有<a href=\"member.php?action=reg\">注册</a>或<a href=\"member.php?action=login\">登录</a>，您可能需要<a href=\"member.php?action=reg\">注册</a>才能发贴和回复<br>如果您第一次访问本论坛，请阅读论坛上方的 <a href=\"faq.php\">FAQ</a>";
}

$verify = "CD"."B - Cros"."sday Bul"."letin, w";
$gzipcompress && $cdb_charset == $charset && $_COOKIE[sid] ? ob_start("ob_gzhandler") : ob_start();

$faqlink = " | <a href=\"faq.php\"><font class=\"navtd\">FAQ</font></a>";
$searchlink = $allowsearch ? " | <a href=\"misc.php?action=search\"><font class=\"navtd\">搜索</font></a>" : NULL;
$memlistlink = $memliststatus ? " | <a href=\"member.php?action=list\"><font class=\"navtd\">会员</font></a>" : NULL;
$statslink = $statstatus ? " | <a href=\"misc.php?action=stats\"><font class=\"navtd\">统计</font></a>" : NULL;
$memolink = $maxmemonum ? " | <a href=\"memo.php\"><font class=\"navtd\">备忘录</font></a>" : NULL;

if(is_array($plugins)) {
	foreach($plugins as $plugarray) {
		if($plugarray[name] && $plugarray[url]) {
			$pluglink .= " | <a href=\"$plugarray[url]\"><font class=\"navtd\">$plugarray[name]</font></a> ";
		}
	}
}

$verify .= "ww.cr"."ossd"."ay.com, Da"."i Zhika"."ng";
$bgcode = strpos($bgcolor, ".") ? "background-image: url(\"$imgdir/$bgcolor\")" : "background-color: $bgcolor";
$catbgcode = strpos($catcolor, ".") ? "background-image: url(\"$imgdir/$catcolor\")" : "background-color: $catcolor";
$headerbgcode = strpos($headercolor, ".") ? "background-image: url(\"$imgdir/$headercolor\")" : "background-color: $headercolor";
$boardlogo = image($boardimg, $imgdir, "alt=\"$bbname\"");
$bold = $nobold ? "normal" : "bold";

if($allowvisit == 0) {
	setcookie("_cdbuser", $CDB_SESSION_VARS[cdbuser], 86400 * 365, $cookiepath, $cookiedomain);
	setcookie("_cdbpw", $CDB_SESSION_VARS[cdbpw], 86400 * 365, $cookiepath, $cookiedomain);
	showmessage("对不起，本论坛不欢迎你的来访。");
} elseif($verify != $poweredbycdb || ($action != "login" && $bbclosed && !$isadmin)) {
	$currtime = $timestamp - (86400 * 365);
	setcookie("_cdbuser", "", $currtime, $cookiepath, $cookiedomain);
	setcookie("_cdbpw", "", $currtime, $cookiepath, $cookiedomain);
	showmessage("$closedreason<br><br>本论坛暂时关闭。");
}

?>