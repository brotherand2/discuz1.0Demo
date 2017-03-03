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


function attachicon($type) {
	if(eregi("image|^(jpg|gif|png|bmp)\t", $type)) {
		$attachicon = "image.gif";
	} elseif(eregi("flash|^(swf|fla|swi)\t", $type)) {
		$attachicon = "flash.gif";
	} elseif(eregi("audio|video|^(wav|mid|mp3|m3u|wma|asf|asx|vqf|mpg|mpeg|avi|wmv)\t", $type)) {
		$attachicon = "av.gif";
	} elseif(eregi("real|^(ra|rm|rv)\t", $type)) {
		$attachicon = "real.gif";
	} elseif(eregi("htm|^(php|js|pl|cgi|asp)\t", $type)) {
		$attachicon = "html.gif";
	} elseif(eregi("text|^(txt|rtf|wri|chm)\t", $type)) {
		$attachicon = "text.gif";
	} elseif(eregi("word|^(doc)\t", $type)) {
		$attachicon = "word.gif";
	} elseif(eregi("powerpoint|^(ppt)\t", $type)) {
		$attachicon = "powerpoint.gif";
	} elseif(eregi("^rar\t", $type)) {
		$attachicon = "rar.gif";
	} elseif(eregi("compressed|^(zip|arj|arc|cab|lzh|lha|tar|gz)\t", $type)) {
		$attachicon = "zip.gif";
	} elseif(eregi("octet-stream", $type)) {
		$attachicon = "binary.gif";
	} else {
		$attachicon = "other.gif";
	}
	$attachicon = "<img src=\"images/attachicon/$attachicon\" align=\"absmiddle\">";
	return $attachicon;
}

function attach_upload() {
	global $attach, $attach_name, $attach_size, $attach_fname, $attachdir, $maxattachsize, $attachextensions;

	if(!function_exists("is_uploaded_file")) {
		if(!is_uploaded_file($attach)) {
			return FALSE;
		}
	} elseif(!($attach != "none" && $attach && trim($attach_name))) {
		return FALSE;
	}

	$attach_name = cdbaddslashes($attach_name);
	if($attachextensions && @!eregi(substr(strrchr($attach_name, "."), 1), $attachextensions)) {
		showmessage("对不起，不允许上传此类扩展名的附件，请返回修改。");
	}

	if(!$attach_size || ($maxattachsize && $attach_size > $maxattachsize)) {
		showmessage("对不起，您的附件大小超过论坛限制，请返回修改。");
	}

	$filename = $attach_name;

	// == 将附件保存到 fid 子目录的 Hack 开始 ==
	/*
	global $fid;
	$attach_subdir = "forumid_$fid/";
	if(!is_dir("$attachdir/$attach_subdir")) {
		mkdir("$attachdir/$attach_subdir", 0777);
	}
	$attach_fname = $attach_subdir;
	*/
	// == 将附件保存到 fid 子目录的 Hack 结束 ==

	$chinese = FALSE;
	$extension = strtolower(substr(strrchr($filename, "."), 1));

	// == 将附件按扩展名保存子目录的 Hack 开始 ==
	/*
	$attach_subdir = "ext_$extension/";
	if(!is_dir("$attachdir/$attach_subdir")) {
		mkdir("$attachdir/$attach_subdir", 0777);
	}
	$attach_fname = $attach_subdir;
	*/
	// == 将附件按扩展名保存子目录的 Hack 结束 ==

	$filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);
	for($i = 0; $i < strlen($filename); $i++) {
		if(ord($filename[$i]) > 127) {
			$chinese = TRUE;
			break;
		}
	}
	if($chinese) {
		$filename = base64_encode($filename);
	}
	if($extension == "php" || $extension == "php3" || $extension == "asp" || $extension == "cgi" || $extension == "pl") {
		$extension = "_".$extension;
	}
	$attach_fname .= random(4)."_".$filename.".".$extension;

	$attach_saved = FALSE;

	$source = stripslashes("$attachdir/$attach_fname");
	if(@copy($attach, $source)) {
		$attach_saved = TRUE;
	} elseif(function_exists("move_uploaded_file")) {
		if(@move_uploaded_file($attach, $source)) {
			$attach_saved = TRUE;
		}
	}

	if(!$attach_saved && is_readable($attach)) {
		@$fp = fopen($attach, "rb");
		@flock($fp, 2);
		@$attachedfile = fread($fp, $attach_size);
		@fclose($fp);

		@$fp = fopen($source, "wb");
		@flock($fp, 3);
		if(@fwrite($fp, $attachedfile)) {
			$attach_saved = TRUE;
		}
		@fclose($fp);
	}

	if(!$attach_saved) {
		showmessage("附件文件无法保存到服务器，可能是目录属性设置问题，请与管理员联系。");
	} else {
		return TRUE;
	}
}

function bbcodeurl($url, $tags) {
	if(!in_array(strtolower(substr($url, 0, 6)), array("http:/", "ftp://", "rtsp:/", "mms://"))) {
		$url = "http://$url";
	}
	return sprintf($tags, $url, $url);
}
//保留><"标记
function cdbhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = cdbhtmlspecialchars($val);
		}
	} else {
		//$string = str_replace("&lt;", "&amp;lt;", $string);
		//$string = str_replace("&gt;", "&amp;gt;", $string);
		$string = str_replace("\"", "&quot;", $string);
		$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);
	}
	return $string;
}

function wordscut($string, $length) { 
	if(strlen($string) > $length) {
		for($i = 0; $i < $length - 3; $i++) {
			if(ord($string[$i]) > 127) {
				$wordscut .= $string[$i].$string[$i + 1];
				$i++;
			} else {
				$wordscut .= $string[$i];
			}
		}
		return $wordscut." ...";
	}
	return $string;
}
	
function cdbexit() {
	global $db;
	cdb_output();
	$db->close();
	exit;
}

function cdbaddslashes($string) {
	if(!$GLOBALS[magic_quotes_gpc]) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = cdbaddslashes($val);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
} 


function chcode_convert($content, $codelib) {
	if(is_array($content)) {
		foreach($content as $key => $val) {
			$content[$key] = chcode_convert($val, $codelib);
		}
	} else {
		$length = strlen($content) - 1;
		for($i = 0; $i< $length; $i++){
			$ascii = ord($content[$i]);
			if($ascii >= 160){
				$ascii2  = ord($content[$i + 1]);
				$chinese = ($ascii == 161 && $ascii2 == 64) ? "  " : substr($codelib, ($ascii - 160) * 510 + ($ascii2 - 1) * 2, 2);
				$content[$i] = $chinese[0];
				$content[$i + 1] = $chinese[1];
				$i++;
			}
		}
	}
	return $content;
}

function credithide($creditsrequire, $message) {
	if($GLOBALS[credit] < $creditsrequire && !$GLOBALS[issupermod]) {
		return "<b>**** 隐藏信息 $GLOBALS[credittitle]高于 $creditsrequire 点才能显示 ****</b>";
	} else {
		return "<span class=\"bold\">以下为$GLOBALS[credittitle]达到 $creditsrequire $GLOBALS[creditunit]显示的隐藏信息</span><br>==============================<br><br>$message<br><br>==============================";
	}
}
//功能重要为添加 后面SID
function url_rewriter($url, $tag = "") {
	global $sid;
	$tag = stripslashes($tag);
	if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, "sid="))) {
		$pos = strpos($url, "#");//没有 sid,一般是<a href="">才进行添加,里面不能有 http,mailto,javascript
		if($pos) {
			$urlret = substr($url, $pos);//#后面索引标记
			$url = substr($url, 0, $pos);
		}
		$url .= strpos($url, "?") ? "&" : "?";
		$url .= "sid=$sid$urlret";//添加 sid
	}
	return $tag.$url;
}

function cdb_output() {
	global $cdb_charset, $cdbuser, $CDB_SESSION_VARS, $charset, $db, $fid, $onlinehold, $onlineip, $sid, $status, $table_sessions, $table_members, $timestamp, $useraction;

	$useraction = addslashes(wordscut($useraction, 60));
	$status = addslashes($status);
	$sessionvars = addslashes(serialize($CDB_SESSION_VARS));
    $expire=($timestamp - $CDB_SESSION_VARS[lastvisit]) > $onlinehold;//是否会放过期
	if($cdbuser != $CDB_SESSION_VARS[cdbuser] || $expire) {//过期或用户换了
		$db->query("UPDATE $table_members SET lastvisit=$timestamp WHERE username='$cdbuser'");
	}

	if($GLOBALS[sessionexists] == 1) {
		$db->query("UPDATE $table_sessions SET username='$cdbuser', status='$status', time='$timestamp', fid='$fid', action='$useraction', sessionvars='$sessionvars' WHERE sid='$sid'");
	} elseif($GLOBALS[sessionexists] == -1) {
		$db->query("DELETE FROM $table_sessions WHERE sid='$sid' OR time<'".($timestamp - $onlinehold)."' OR (ip='$onlineip' AND time>'".($timestamp - 60)."')");
	} else {//条件1 600秒前登录的删除会话,条件2 删除当前用户的会话,条件3   当前 IP  60内的 会话信息
		$db->query("DELETE FROM $table_sessions WHERE time<'".($timestamp - $onlinehold)."' OR (username<>'' AND username='$cdbuser') OR (ip='$onlineip' AND time>'".($timestamp - 60)."')");//
		$db->query("INSERT INTO $table_sessions (sid, username, status, time, ip, fid, action, sessionvars)
			VALUES ('$sid', '$cdbuser', '$status', '$timestamp', '$onlineip', '$fid', '$useraction', '$sessionvars')");
	}

	if(!$GLOBALS[$_COOKIE][sid]) {
		$content = ob_get_contents();//对所有 A 标签加 sid
		$content = preg_replace("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies", "url_rewriter('\\3','<a\\1href=\\2')", $content);//(\s*[^\>]+\s*)为空白   其它属性 空白,第2()为单引号或双引号,第3部份为非单引号和双引号
		$content = preg_replace("/(\<form.+?\>)/is", "\\1\n<input type=\"hidden\" name=\"sid\" value=\"$sid\">", $content);//在 form表单下面添加一个 sid 表单,原因可能是用户关闭 COOKIE 功能
	}

	if($cdb_charset != $charset) {
		$mode = $cdb_charset == "big5" ? "big5-gb" : "gb-big5";
		$table = "./lib/$mode.table";
		$fp = fopen($table, "r");
		$codelib = fread($fp, filesize($table));
		fclose($fp);

		if(!$content) {
			$content = ob_get_contents();
		}
		//$content = chcode_convert($content, $codelib);
	}

	if($content) {
		ob_end_clean();
		echo $content;
		ob_end_flush();
	}
}
//查询 IP 所在地
function convertip($ip, $datadir = "./"){
	$ip_detail = explode(".", $ip);
	if(file_exists("{$datadir}ipdata/$ip_detail[0].txt")) {
		$ip_fdata = fopen("{$datadir}ipdata/$ip_detail[0].txt", "r");
	} else {
		if(!($ip_fdata = fopen("{$datadir}ipdata/0.txt", "r"))) {
			echo "IP 数据文件错误";
		}
	}
	for ($i = 0; $i <= 3; $i++) {
		$ip_detail[$i] = sprintf("%03d", $ip_detail[$i]);
	}
	$ip=join(".", $ip_detail);
	do {
		$ip_data = fgets($ip_fdata, 200);
		$ip_data_detail = explode("|", $ip_data);
		if($ip >= $ip_data_detail[0] && $ip <= $ip_data_detail[1]) {
			fclose($ip_fdata);
			return $ip_data_detail[2].$ip_data_detail[3];
		}
	} while(!feof($ip_fdata));
	fclose($ip_fdata);
	return "未知地区";
}

function censor($message) {
	return $GLOBALS[CDB_CACHE_VARS][censor] ? preg_replace($GLOBALS[CDB_CACHE_VARS][censor][find], $GLOBALS[CDB_CACHE_VARS][censor][replace], $message) : $message;
}
//cookie 过期时间
function cookietime() {
	global $cookietime, $timestamp;
	if(isset($cookietime)) {
		return $cookietime ? $timestamp + $cookietime : 0;
	} else {
		return $timestamp + (86400 * 30);
	}
}

function encrypt($password) {
	global $encrypt;
	if($encrypt == "md5") {
		if(!($encrypted = md5($password))) {
			echo "您的系统无法使用 MD5 加密，请在config.php中改 \$encrypt 变量设置";
			cdbexit();
		}
		return $encrypted;
	} elseif ($encrypt == "des") {
		if(!($encrypted = crypt($password))) {
			echo "您的系统无法使用 DES 加密，请在config.php中改 \$encrypt 变量设置";
			cdbexit();
		}
		return $encrypted;
	} else {
		return $password;
	}
}

function image($imageinfo, $basedir = "", $remark = "") {
	if($basedir) {
		$basedir .= "/";
	}
	if(strstr($imageinfo, ",")) {
		$flash = explode(",", $imageinfo);
		return "<embed src=\"$basedir".trim($flash[0])."\" width=\"".trim($flash[1])."\" height=\"".trim($flash[2])."\" type=\"application/x-shockwave-flash\" $remark></embed>";
	} else {
		return "<img src=\"$basedir"."$imageinfo\" $remark border=\"0\">";
	}
}

function template($name) {
	$tempcache = array();
	global $tempcache, $table_templates, $db;
	if (!isset($tempcache[$name])) {
		//echo"|$name|";
		$query = $db->query("SELECT template FROM $table_templates WHERE name='$name'");
		$tempcache[$name] = trim(str_replace("\\'", "'", $db->result($query, 0)));
	}

	return $GLOBALS["debug"] ? "<!-- $name -->\n$tempcache[$name]\n<!-- /$name -->\n\n" : "$tempcache[$name]\n";
}

function loadtemplates($templatenames) {
	global $db, $tempcache, $table_templates;
	//echo"||$templatenames||";
	$names = $comma = "";
	foreach(explode(",", $templatenames) as $name) {
		if(!isset($tempcache[$name])) {
			$names .= "$comma'$name'";
			$comma = ", ";
		}
	}
	if($names) {
		$query = $db->query("SELECT * FROM $table_templates WHERE name IN ($names)");
		while($template = $db->fetch_array($query)) {
			$tempcache[$template[name]] = trim(str_replace("\\'", "'", $template[template]));
		}
	}
}

function codedisp($code) {
	global $bordercolor, $thisbg, $codecount, $post_codecount, $codehtml;
	$post_codecount++;
	$code = htmlspecialchars(str_replace("\\\"", "\"", preg_replace("/^[\n\r]*(.+?)[\n\r]*$/is", "\\1", $code)));
	$codehtml[$post_codecount] = "<br><br><center><table border=\"0\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;&nbsp;代码：</td><td align=\"right\"><a href=\"###\" onclick=\"copycode(findobj('code$codecount'));\">[复制到剪贴板]&nbsp;&nbsp;</a></td></tr><tr><td colspan=\"2\"><table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"$bordercolor\"><tr><td width=\"100%\" bgcolor=\"$thisbg\" style=\"word-break:break-all\" id=\"code$codecount\">$code</td></tr></table></td></tr></table></center><br>";
	$codecount++;
	return "|\tCDB_CODE_$post_codecount\t|";
}

function postify($message, $smileyoff, $bbcodeoff, $parseurloff, $allowsmilies = 1, $allowhtml = 1, $allowbbcode = 1, $allowimgcode = 1)
{
	global $credit, $tid, $cdbuser, $codehtml, $post_codecount, $imgdir, $bordercolor, $highlight, $table_posts, $db, $smdir, $searcharray, $replacearray, $thisbg, $bordercolor, $ismoderator;

	$post_codecount = -1;

	if(!$bbcodeoff && $allowbbcode) {
		$message = preg_replace("/\s*\[code\](.+?)\[\/code\]\s*/ies", "codedisp('\\1')", $message);
	}

	if(!$allowhtml) {
		$message = cdbhtmlspecialchars($message);
	}

	$message = " $message";

	if(!$smileyoff && $allowsmilies) {
		$smiliescache = $GLOBALS[CDB_CACHE_VARS][smilies];
		if(is_array($smiliescache)) {
			foreach($smiliescache as $smiliey) {
				$message = str_replace("$smiliey[code]", "<img src=\"$smdir/$smiliey[url]\" align=\"absmiddle\" border=\"0\">",$message);
			}
		}
	}

	if(!$parseurloff) {
		$message = preg_replace(array(	"/(?<=[^\]A-Za-z0-9-=\"'\\/])(https?|ftp|gopher|news|telnet|mms){1}:\/\/([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/ies",
						"/([\n\s])www\.([a-z0-9\-]+)\.([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\[\]\\()|]+)((?:[^,\t\s\n\r]*)?)/is",
						"/(?<=[^\]A-Za-z0-9\/\-_.~?=:.])([A-Za-z0-9\-_.]+)@([A-Za-z0-9\-_][.][A-Za-z0-9\-_.]+)/is"	),
					array(	"urlcut('\\1://\\2')",
						"\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>",
						"<a href=\"mailto:\\1@\\2\">\\1@\\2</a>"	),
					$message);
	}

	if(!$bbcodeoff && $allowbbcode) {

		if(!$searcharray[bbcode] || !$replacearray[bbcode]) {
			$nests = 2;
			$searcharray[bbcode] = array(
				"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
				"/\[url\]\s*(www.|https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/){1}(.+?)\s*\[\/url\]/ies",
				"/\[url=www.(.+?)\](.+?)\[\/url\]/is",
				"/\[url=(https?|ftp|gopher|news|telnet|rtsp|mms){1}:\/\/(.+?)\](.+?)\[\/url\]/is",
				"/\[email\]\s*([A-Za-z0-9\-_.]+)@([A-Za-z0-9\-_][.][A-Za-z0-9\-_.]+)\s*\[\/email\]/is",
				"/\[email=([A-Za-z0-9\-_.]+)@([A-Za-z0-9\-_][.][A-Za-z0-9\-_.]+)\](.+?)\[\/email\]/is",
				"/\[color=(.+?)\](.+?)\[\/color\]/is",
				"/\[size=(.+?)\](.+?)\[\/size\]/is",
				"/\[font=(.+?)\](.+?)\[\/font\]/is",
				"/\[align=(.+?)\](.+?)\[\/align\]/is"
			);
			$replacearray[bbcode] = array(
				"<br><br><center><table border=\"0\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;&nbsp;引用：</td></tr><tr><td><table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"$bordercolor\"><tr><td width=\"100%\" bgcolor=\"$thisbg\">\\1</td></tr></table></td></tr></table></center><br>",
				"urlcut('\\1\\2')",
				"<a href=\"http://www.\\1\" target=\"_blank\">\\2</a>",
				"<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>",
				"<a href=\"mailto:\\1\">\\1</a>",
				"<a href=\"mailto:\\1\" target=\"_blank\">\\2</a>",
				"<font color=\"\\1\">\\2</font>",
				"<font size=\"\\1\">\\2</font>",
				"<font face=\"\\1\">\\2</font>",
				"<p align=\"\\1\">\\2</p>",
			);

			for($i = (count($searcharray[bbcode]) - 1) * $nests; $i >= 0; $i -= $nests) {
				for($j = $i; $j > $i - $nests; $j--) {
					$searcharray[bbcode][$j] = $searcharray[bbcode][(($i + 1) / $nests)];
					$replacearray[bbcode][$j] = $replacearray[bbcode][(($i + 1) / $nests)];
				}
			}

			$searcharray[bbcode] = array_merge($searcharray[bbcode], array(
							"/\[b\](.+?)\[\/b\]/is",
							"/\[i\](.+?)\[\/i\]/is",
							"/\[u\](.+?)\[\/u\]/is",
							"/\[fly\](.+?)\[\/fly\]/is",
							"/\[list\](.+?)\[\/list\]/isU"));
			
			$replacearray[bbcode] = array_merge($replacearray[bbcode], array(
							"<b>\\1</b>",
							"<i>\\1</i>",
							"<u>\\1</u>",
							"<marquee width=\"90%\" behavior=\"alternate\" scrollamount=\"3\">\\1</marquee>",
							"<ul type=square>\\1</ul>"));

		}
		$message = preg_replace($searcharray[bbcode], $replacearray[bbcode], $message);

		$message = str_replace("[list=1]", "<ol type=1>", $message);
		$message = str_replace("[list=a]", "<ol type=A>", $message);
		$message = str_replace("[list=A]", "<ol type=A>", $message);
		$message = str_replace("[/list]", "</ol>", $message);
		$message = str_replace("[*]", "<li>", $message);

		if(preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", $message)) {
			if(strstr($message, "[hide]")) {
				$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid' AND author='$cdbuser'");
				if($ismoderator || $db->result($query, 0)) {
					$message = preg_replace("/\[hide\]\s*(.+?)\s*\[\/hide\]/is", "<span class=\"bold\">以下为回复后显示的隐藏信息</span><br>==============================<br><br>\\1<br><br>==============================", $message);
				} else {
					$message = preg_replace("/\[hide\](.+?)\[\/hide\]/is", "<b>**** 隐藏信息 跟贴后才能显示 *****</b>", $message);
				}
			}
			$message = preg_replace("/\[hide=(\d+)\]\s*(.+?)\s*\[\/hide\]/ies", "credithide(\\1,'\\2')", $message);
		}
	}

	if(!$bbcodeoff && $allowimgcode) {
		if(!$searcharray[imgcode] || !$replacearray[imgcode]) {
			$searcharray[imgcode] = array(
				"/\[swf\]\s*(.+?)\s*\[\/swf\]/ies",
				"/\[swf=(\d+?)[x|\,](\d+?)\]\s*(.+?)\s*\[\/swf\]/ies",
				"/\[img\]\s*(.+?)\s*\[\/img\]/ies",
				"/\[img=(\d+?)[x|\,](\d+?)\]\s*(.+?)\s*\[\/img\]/ies",
				"/\[iframe\]\s*(.+?)\s*\[\/iframe\]/ies",
				"/\[wmv\]\s*(.+?)\s*\[\/wmv\]/ies",
				"/\[mid\]\s*(.+?)\s*\[\/mid\]/is",
				"/\[ra\]\s*(.+?)\s*\[\/ra\]/ies",
				"/\[rm\]\s*(.+?)\s*\[\/rm\]/ies",
			);
			$replacearray[imgcode] = array(
				"bbcodeurl('\\1', '<a href=\"%s\" target=\"_blank\">[ 开新窗口播放 ]</a><br><embed width=\"550\" height=\"375\" src=\"%s\" type=\"application/x-shockwave-flash\"></embed>')",
				"bbcodeurl('\\3', '<embed width=\"\\1\" height=\"\\2\" src=\"%s\" type=\"application/x-shockwave-flash\"></embed>')",
				"bbcodeurl('\\1', '<img src=\"%s\" border=\"0\" onload=\"if(this.width>screen.width-200) {this.width=screen.width-200;this.alt=\'点击查看全图\';}\" onmouseover=\"if(this.alt) this.style.cursor=\'hand\';\" onclick=\"if(this.alt) window.open(\'%s\');\">')",
				"bbcodeurl('\\3', '<img width=\"\\1\" height=\"\\2\" src=\"%s\" border=\"0\" onload=\"if(this.width>screen.width-200) {this.width=screen.width-200;this.alt=\'点击查看全图\';}\" onmouseover=\"if(this.alt) this.style.cursor=\'hand\';\" onclick=\"if(this.alt) window.open(\'%s\');\">')",
				"bbcodeurl('\\1', '<iframe src=\"%s\" frameborder=\"0\" allowtransparency=\"true\" scrolling=\"yes\" width=\"97%%\" height=\"480\"></iframe>')",
				"bbcodeurl('\\1', '<embed src=\"%s\" height=\"256\" width=\"314\" autostart=\"0\"></embed>')",
				"bbcodeurl('\\1', '<embed src=\"%s\" height=\"45\" width=\"314\" autostart=0 ></embed>')",
				"bbcodeurl('\\1', '<object classid=\"clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA\" id=\"RAOCX\" width=\"253\" height=\"60\"><param name=\"_ExtentX\" value=\"6694\"><param name=\"_ExtentY\" value=\"1588\"><param name=\"AUTOSTART\" value=\"0\"><param name=\"SHUFFLE\" value=\"0\"><param name=\"PREFETCH\" value=\"0\"><param name=\"NOLABELS\" value=\"0\"><param name=\"SRC\" value=\"%s\"><param name=\"CONTROLS\" value=\"StatusBar,ControlPanel\"><param name=\"LOOP\" value=\"0\"><param name=\"NUMLOOP\" value=\"0\"><param name=\"CENTER\" value=\"0\"><param name=\"MAINTAINASPECT\" value=\"0\"><param name=\"BACKGROUNDCOLOR\" value=\"#000000\"><embed src=\"%s\" width=\"253\" autostart=\"true\" height=\"60\"></embed></object>')",
				"bbcodeurl('\\1', '<object classid=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" height=\"241\" id=\"Player\" width=\"316\" viewastext><param name=\"_ExtentX\" value=\"12726\"><param name=\"_ExtentY\" value=\"8520\"><param name=\"AUTOSTART\" value=\"0\"><param name=\"SHUFFLE\" value=\"0\"><param name=\"PREFETCH\" value=\"0\"><param name=\"NOLABELS\" value=\"0\"><param name=\"CONTROLS\" value=\"ImageWindow\"><param name=\"CONSOLE\" value=\"_master\"><param name=\"LOOP\" value=\"0\"><param name=\"NUMLOOP\" value=\"0\"><param name=\"CENTER\" value=\"0\"><param name=\"MAINTAINASPECT\" value=\"%s\"><param name=\"BACKGROUNDCOLOR\" value=\"#000000\"></object><br><object classid=clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA height=32 id=Player width=316 VIEWASTEXT><param name=\"_ExtentX\" value=\"18256\"><param name=\"_ExtentY\" value=\"794\"><param name=\"AUTOSTART\" value=\"-1\"><param name=\"SHUFFLE\" value=\"0\"><param name=\"PREFETCH\" value=\"0\"><param name=\"NOLABELS\" value=\"0\"><param name=\"CONTROLS\" value=\"controlpanel\"><param name=\"CONSOLE\" value=\"_master\"><param name=\"LOOP\" value=\"0\"><param name=\"NUMLOOP\" value=\"0\"><param name=\"CENTER\" value=\"0\"><param name=\"MAINTAINASPECT\" value=\"0\"><param name=\"BACKGROUNDCOLOR\" value=\"#000000\"><param name=\"SRC\" value=\"\\1\"></object>')"
			);
		}
		$message = preg_replace($searcharray[imgcode], $replacearray[imgcode], $message);
	}

	for($i = 0; $i <= $post_codecount; $i++) {
		$message = str_replace("|\tCDB_CODE_$i\t|", $codehtml[$i], $message);
	}

	if($highlight) {
		foreach(explode("+", $highlight) as $ret) {
			$ret = str_replace("/", "\/", trim($ret));
			if($ret) {
				$message = preg_replace("/([^[:alpha:]]|^)$ret([^[:alpha:]]|$)/is", "\\1<u><b><font color=\"#FF0000\">$ret</font></b></u>\\2", $message);
			}
		}
	}

	$message = nl2br(censor($message));

	$message = str_replace("\t", "&nbsp; &nbsp; &nbsp; &nbsp; ", $message);
	$message = str_replace("   ", "&nbsp; &nbsp;", $message);
	$message = str_replace("  ", "&nbsp;&nbsp;", $message);

	return $message;
}
//预加载必要的模板css,header,footer
function preloader($additional, $headeroutput = 1) {
	global $css, $header;
	extract($GLOBALS, EXTR_OVERWRITE);
	$tplnames = $tplnames ? "$tplnames,$additional" : "css,header,footer,$additional";
	loadtemplates($tplnames);
	eval("\$css = \"".template("css")."\";");
	eval("\$header = \"".template("header")."\";");
	echo $headeroutput ? $header : NULL;
}
//检查是否有修改权限
function modcheck($username, $fid = 0) {
	global $isadmin, $issupermod, $ismoderator;
	if($fid) {
		global $table_forums, $db;
		$query = $db->query("SELECT moderator FROM $table_forums WHERE fid='$fid'");
		$forum = $db->fetch_array($query);
	} else {
		global $forum;
	}
	if($isadmin || $issupermod) {
		return 1;
	} elseif($ismoderator && eregi("(,|^)"."$username"."(,|$)", str_replace(" ", "", $forum[moderator]))) {
		return 1;
	} else {
		return 0;
	}
}
//版主的显示
function moddisplay($mod, $moddisplay) {
	global $imgdir;
	if($moddisplay == "selectbox")
	{
		$modlist .= "<img src=\"$imgdir/moderate.gif\" align=\"absmiddle\"><select name=\"modlist\" id=\"showmod\" onchange=\"if(this.options[this.selectedIndex].value != '') {\n";
		$modlist .= "window.location=('member.php?action=viewpro&username='+this.options[this.selectedIndex].value) }\">\n";
		$modlist .= "<option value=\"\">版&nbsp;&nbsp;主</option>\n";
		$modlist .= "<option value=\"\">----------</option>\n";
		if($mod) {
			$mods = explode(",", $mod);
			for($i = 0; $i < count($mods); $i++) {
				$mods[$i] = trim($mods[$i]);
				$modlist .= "<option value=\"".rawurlencode($mods[$i])."\">$mods[$i]</option>\n";
			}
		}
		$modlist .= "</select>\n";
		return $modlist;
	}
	else
	    {
		if($moddisplay == "forumdisplay") {
			$modicon = "<img src=\"$imgdir/online_moderator.gif\" align=\"absmiddle\"> ";
		} else {
			$modicon = "";
		}
		if($mod != "") {
			$mods = explode(",", $mod);
			$modlist = "";
			for($i = 0; $i < count($mods); $i++) {
				$mods[$i] = trim($mods[$i]);
				$modlist .= "$comma$modicon<a href=\"member.php?action=viewpro&username=".rawurlencode($mods[$i])."\">$mods[$i]</a>";
				$comma = ", ";
			}
		} else {
			$modlist = "";
		}
		return $modlist;
	}
}
//更新会员发贴数量,
function updatemember($operator, $username, $posts = 1) {
	global $db, $CDB_SESSION_VARS, $table_members, $cdbuser;

	if(strpos($username, ","))
	{
		$member = $post = array();
		foreach(explode(",", $username) as $user) {
			$member[$user]++;
		}

		$curr_posts = $member[$user];
		$curr_username = $next_username = $curr_comma = $next_comma = "";
		foreach($member as $user => $postnum) {
		 	if($postnum == $curr_posts) {
		 		$curr_username .= "$curr_comma'$user'";
		 		$curr_comma = ", ";
		 	} else {
		 		for($i = 0; $i < $member[$user]; $i++) {
		 			$next_username .= "$next_comma$user";
		 			$next_comma = ",";
		 		}
			}
		}

		$username = $curr_username;
		$posts = $posts * $curr_posts;
	} else
	    {
		$username = "'$username'";
	}

	$postcredits = $GLOBALS[postcredits] * $posts;//增加或减少的积分
	if($username == $cdbuser || strstr($username, "'$cdbuser'")) { //当前用户在些论坛发表过贴子
		$operator == "+" ? $CDB_SESSION_VARS[credit] += $postcredits : $CDB_SESSION_VARS[credit] -= $postcredits;
		$operator == "+" ? $CDB_SESSION_VARS[postnum] += $posts : $CDB_SESSION_VARS[postnum] -= $posts;
	}
	$db->query("UPDATE $table_members SET postnum=postnum$operator$posts, credit=credit$operator($postcredits) WHERE username IN ($username)");

	if($next_username) {
		updatemember($operator, $next_username, strpos($next_username, ",") ? $posts / $curr_posts : $member[$next_username]);
	}
}
//加载论坛信息
function forum($forum, $template) {
	global $timeformat, $dateformat, $cdbuser, $status, $groupid, $lastvisit, $moddisplay, $timeoffset, $hideprivate, $onlinehold, $altbg1, $altbg2, $imgdir;

	if($forum[icon]) {
		$forum[icon] = "<a href=\"forumdisplay.php?fid=$forum[fid]\">".image($forum[icon], "", "align=\"left\"")."</a>";
	}

	if(str_replace("\t", "", $forum[lastpost]))
	{//$lastvisit < $lastpost[1]表示访问后在该论坛有发贴子,显示有新贴子图标
		$lastpost = explode("\t", $forum[lastpost]);
		$folder = $lastvisit < $lastpost[1] ? "<img src=\"$imgdir/red_forum.gif\">" : "<img src=\"$imgdir/forum.gif\">";
		if($lastpost[2] != "游客") {
			$lastpost[2] = "<a href=\"member.php?action=viewpro&username=".rawurlencode($lastpost[2])."\">$lastpost[2]</a>";
		}
		$lastpost = "<span title=\"标题：$lastpost[0]\">".gmdate("$dateformat $timeformat", $lastpost[1] + ($timeoffset * 3600))."<br>by $lastpost[2]</span>";
		eval("\$lastpostrow = \"".template("index_forum_lastpost")."\";");
	} else {
		$folder = $lastvisit < $lastpost[1] ? "<img src=\"$imgdir/red_forum.gif\">" : "<img src=\"$imgdir/forum.gif\">";
		$lastpostrow = "从未";
	}
	$forum[moderator] = moddisplay($forum[moderator], $moddisplay)."&nbsp;";

	if(!$forum[viewperm] || ($forum[viewperm] && strstr($forum[viewperm], "\t$groupid\t"))) {
		eval("\$foruminfo .= \"".template("$template")."\";");//没有设置权限,或设置了并且有查看权限,显示论坛
	} elseif(!$hideprivate) {
		$lastpostrow = "隐藏论坛";//显示无权访问的论坛
		eval("\$foruminfo .= \"".template("$template")."\";");
	}

	return $foruminfo;
}

function forumselect() {
	global $db, $groupid, $allowview;
	$forumlist = "";
	$forums = $GLOBALS[CDB_CACHE_VARS][forums];
	if(!is_array($forums))
	{
		$query = $db->query("SELECT fid, type, name, fup, viewperm FROM $GLOBALS[table_forums] WHERE status='1' ORDER BY displayorder");
		while($forum = $db->fetch_array($query)) {
			$forum[name] = strip_tags($forum[name]);
			$forums[$forum[fid]] = $forum;
		}
	}

	foreach($forums as $fid1 => $forum1) {
		if($forum1[type] == "group") {
			$forumlist .= "<option value=\"\">$forum1[name]</option>\n";
			foreach($forums as $fid2 => $forum2) {
				if($forum2[fup] == $fid1 && $forum2[type] == "forum" && ((!$forum2[viewperm] && $allowview) || ($forum2[viewperm] && strstr($forum2[viewperm], "\t$groupid\t")))) {
					$forumlist .= "<option value=\"$fid2\">&nbsp; &gt; $forum2[name]</option>\n";
					foreach($forums as $fid3 => $forum3) {
						if($forum3[fup] == $fid2 && $forum3[type] == "sub" && ((!$forum3[viewperm] && $allowview) || ($forum3[viewperm] && strstr($forum3[viewperm], "\t$groupid\t")))) {
							$forumlist .= "<option value=\"$fid3\">&nbsp; &nbsp; &nbsp; &gt; $forum3[name]</option>\n";
						}
					}
				}
			}
			$forumlist .= "<option value=\"\">&nbsp;</option>\n";
		} elseif(!$forum1[fup] && $forum1[type] == "forum" && ((!$forum1[viewperm] && $allowview) || ($forum1[viewperm] && strstr($forum1[viewperm], "\t$groupid\t")))) {
			$forumlist .= "<option value=\"$fid1\"> &nbsp; &gt; $forum1[name]</option>\n";
			foreach($forums as $fid2 => $forum2) {
				if($forum2[fup] == $fid1 && $forum2[type] == "sub" && ((!$forum2[viewperm] && $allowview) || ($forum2[viewperm] && strstr($forum2[viewperm], "\t$groupid\t")))) {
					$forumlist .= "<option value=\"$fid2\">&nbsp; &nbsp; &nbsp; &gt; $forum2[name]</option>";
				}
			}
			$forumlist .= "<option value=\"\">&nbsp;</option>\n";
		}

	}

	return $forumlist;
}
//在网页底部显示处理用时,查询次数
function gettotaltime() {
	if($GLOBALS["debug"]) {
		global $db, $starttime, $debuginfo;
		$mtime = explode(" ", microtime());
		$endtime = $mtime[1] + $mtime[0];
		$totaltime = ($endtime - $starttime);
		$totaltime = number_format($totaltime, 7);
		$debuginfo = "<br>Processed in $totaltime second(s), $db->querynum queries";
	}
}
//显示分页信息,如上一页,下一页
function multi($num, $perpage, $curr_page, $mpurl)
{
	if($num > $perpage)
	{
		$page = 10;//显示多少个页号
		$offset = 2;//点比较右的页号时  左边显示多少个页号

		$pages = ceil($num / $perpage);//多少页
		$from = $curr_page - $offset;//起始页号
		$to = $curr_page + $page - $offset - 1;//结束页号
		if($page > $pages)
		{
			$from = 1;
			$to = $pages;//页数太少,不分页
		} else
        {
			if($from < 1)//页号太小
			{
				$to = $curr_page + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;//page<=pages,所以($to - $from) < $page成立时,($to - $from) < $pages也成立,多余
				}
			} elseif($to > $pages)
            {
				$from = $curr_page - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}
		$fwd_back .= "<a href=\"$mpurl&page=1\">&lt;&lt;</a> &nbsp;";
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
				$fwd_back .= "<a href=\"$mpurl&page=$i\">[$i]</a>&nbsp;";
			} else {
				$fwd_back .= "<u><b>[$i]</b></u>&nbsp;";
			}
		}
		$fwd_back .= $pages > $page ? " ... <a href=\"$mpurl&page=$pages\"> [$pages] &gt;&gt;</a>" : " <a href=\"$mpurl&page=$pages\">&gt;&gt;</a>";
		$multipage = $fwd_back;
	}
	return $multipage;
}

function random($length, $type = "") {
	$chars = !$type ? "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz" : "0123456789abcdef";
	$max = strlen($chars) - 1;
	mt_srand((double)microtime() * 1000000);
	for($i = 0; $i < $length; $i++) {
		$string .= $chars[mt_rand(0, $max)];
	}
	return $string;
}

function sendmail($to, $subject, $message, $additional_headers = "") {
	if(!@mail($to, $subject, $message, $additional_headers)) {
		showmessage("Email 发送失败，请与管理员联系。");
	}
}

function showmessage($show_message, $url_forward = "", $no_border = 0) {
	loadtemplates("css,header,footer,showmessage");

	extract($GLOBALS, EXTR_OVERWRITE);
	$GLOBALS[useraction] = "提示信息 / 页面跳转";

	$show_message = "<br>".$show_message;

	if(strpos($show_message, "返回")) {
		$show_message .= "<br><br><a href=\"javascript:history.go(-1);\" class=\"mediumtxt\">[ 点击这里返回上一页 ]</a>\n";
	} elseif(strpos($show_message, "您的级别")) {
		$show_message .= "<br><br><a href=\"faq.php?page=misc#3\" class=\"mediumtxt\">[ 点击这里查看本论坛的权限设定 ]</a>\n";
	}

	if($url_forward) {
		$url_forward = url_rewriter($url_forward);
		$show_message .= "<br><br><a href=\"$url_forward\">如果您的浏览器没有自动跳转，请点击这里</a>\n";
		$url_redirect = "<meta http-equiv=\"refresh\" content=\"2;url=$url_forward\">\n";
	}

	eval("\$css = \"".template("css")."\";");
	eval("\$header = \"".template("header")."\";");
	echo $header;

	if($no_border) {
		echo "<center><span class=\"mediumtxt\">$show_message</span></center>\n";
	} else {
		$show_message .= "<br><br>";
		eval("\$message = \"".template("showmessage")."\";");
		echo $message;
	}

	gettotaltime();
	$debuginfo = $GLOBALS["debuginfo"];
	eval("\$footer = \"".template("footer")."\";");
	echo $footer;
	cdbexit();
}

function sizecount($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . " G";
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . " M";
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . " K";
	} else {
		$filesize = $filesize . " bytes";
	}
	return $filesize;
}
//更新,指定表的缓存
function updatecache($cachename) {
	global $db, $table_caches;
	$cols = "*";//查查找哪些字段
	$conditions = "";
	$type = "array";
	switch($cachename) {
		case settings:
			$table = $GLOBALS[table_settings];
			$cols = "bbname, regstatus, bbclosed, closedreason, sitename, siteurl, theme, credittitle, creditunit, moddisplay, floodctrl, karmactrl, hottopic, topicperpage, postperpage, memberperpage, maxpostsize, maxavatarsize, smcols, whosonlinestatus, vtonlinestatus, chcode, gzipcompress, postcredits, digistcredits, hideprivate, emailcheck, fastpost, memliststatus, statstatus, debug, reportpost, bbinsert, smileyinsert, editedby, dotfolders, attachimgpost, timeformat, dateformat, timeoffset, version, onlinerecord, lastmember";
			break;
		case usergroups:
			$table = $GLOBALS[table_usergroups];
			$cols = "specifiedusers, status, grouptitle, creditshigher, creditslower, stars, groupavatar, allowavatar, allowsigbbcode, allowsigimgcode";
			$conditions = "ORDER BY creditslower ASC";
			break;
		case announcements:
			$table = $GLOBALS[table_announcements];
			$cols = " id, subject, starttime, endtime";
			$conditions = "ORDER BY starttime DESC, id DESC";
			break;
		case forums:
			$table = $GLOBALS[table_forums];
			$cols = "fid, type, name, fup, viewperm";
			$conditions = "WHERE status='1' ORDER BY displayorder";
			break;
		case forumlinks:
			$table = $GLOBALS[table_forumlinks];
			$conditions = "ORDER BY displayorder";
			break;
		case smilies:
			$table = $GLOBALS[table_smilies];
			$conditions = "WHERE type='smiley'";
			break;
		case picons:
			$table = $GLOBALS[table_smilies];
			$conditions = "WHERE type='picon'";
			break;
		case news:
			$table = $GLOBALS[table_news];
			$conditions = "ORDER BY id";
			break;
		case censor:
			$table = $GLOBALS[table_words];
			break;
	}

	$data = array();
	$query = $db->query("SELECT $cols FROM $table $conditions");
	if($cachename == "settings") {
		$data = $db->fetch_array($query);
	} elseif($cachename == "censor") {
		$data[find] = $data[replace] = array();
		while($censor = $db->fetch_array($query)) {
			$data[find][] = "/([^[:alpha:]]|^)$censor[find]([^[:alpha:]]|$)/is";
			$data[replace][] = "\\1$censor[replacement]\\2";
		}	
	} elseif($cachename == "forums") {
		while($forum = $db->fetch_array($query)) {
			$data[$forum[fid]] = array(	"type" => $forum[type],
							"name" => strip_tags($forum[name]),
							"fup" => $forum[fup],
							"viewperm" => $forum[viewperm]
						);
		}						
	} else {
		while($datarow = $db->fetch_array($query)) {
			$data[] = $datarow;
		}
	}
	$cachevars = addslashes(serialize($data));
	$db->query("UPDATE $table_caches SET cachevars='$cachevars' WHERE cachename='$cachename'");
}

function updateforumcount($fid) {
	global $db, $table_threads, $table_forums;
	$query = $db->query("SELECT COUNT(*) AS threadcount, SUM(t.replies) + COUNT(*) AS replycount FROM $table_threads t, $table_forums f WHERE (f.fid='$fid' OR (f.fup='$fid' AND f.type<>'group')) AND t.fid=f.fid AND t.closed NOT LIKE 'moved|%'");
	extract($db->fetch_array($query), EXTR_OVERWRITE);

	$query = $db->query("SELECT subject, lastpost, lastposter FROM $table_threads WHERE fid='$fid' ORDER BY lastpost DESC LIMIT 1");
	$thread = $db->fetch_array($query);
	$thread[subject] = addslashes($thread[subject]);
	$thread[lastposter] = addslashes($thread[lastposter]);
	$db->query("UPDATE $table_forums SET posts='$replycount', threads='$threadcount', lastpost='$thread[subject]\t$thread[lastpost]\t$thread[lastposter]' WHERE fid='$fid'");
}

function updatethreadcount($tid) {
	global $db, $table_threads, $table_posts;
	$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid'");
	$replycount = $db->result($query, 0) - 1;
	if($replycount < 0) {
		$db->query("DELETE FROM $table_threads WHERE tid='$tid'");
	}
	$query = $db->query("SELECT dateline, author FROM $table_posts WHERE tid='$tid' ORDER BY dateline DESC LIMIT 0, 1");
	$lastpost = $db->fetch_array($query);
	$lastpost[author] = addslashes($lastpost[author]);
	$db->query("UPDATE $table_threads SET replies='$replycount', lastposter='$lastpost[author]', lastpost='$lastpost[dateline]' WHERE tid='$tid'");
}

function urlcut($url) {
	$length = 65;
	//$url = substr($url, -1) == "." ? substr($url, 0, -1) : $url;
	$urllink = "<a href=\"".(substr(strtolower($url), 0, 4) == "www." ? "http://$url" : $url)."\" target=\"_blank\">";
	if(strlen($url) > $length) {
		$url = substr($url, 0, intval($length * 0.5))." ... ".substr($url, - intval($length * 0.3));
	}
	$urllink .= "$url</a>";
	return $urllink;
}

?>