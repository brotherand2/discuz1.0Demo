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

if($page == "usermaint") {
	$navigation = "&raquo; <a href=\"faq.php\">FAQ</a> &raquo; 用户须知";
	$navtitle .= " - FAQ - 用户须知";
	$useraction = "查看论坛用户须知";
} elseif($page == "using") {
	$navigation = "&raquo; <a href=\"faq.php\">FAQ</a> &raquo; 论坛使用";
	$navtitle .= " - FAQ - 论坛使用";
	$useraction = "查看论坛使用方面的帮助";
} elseif($page == "messages") {
	$navigation = "&raquo; <a href=\"faq.php\">FAQ</a> &raquo; 读写贴子和收发短消息";
	$navtitle .= " - FAQ - 读写贴子和收发短消息";
	$useraction = "查看贴子和短消息方面的帮助";
} elseif($page == "misc") {
	$navigation = "&raquo; <a href=\"faq.php\">FAQ</a> &raquo; 其他问题";
	$navtitle .= " - FAQ - 其他问题";
	$useraction = "查看杂项功能的帮助";
} else {
	$navigation = "&raquo; FAQ";
	$navtitle .= " - FAQ";
	$useraction = "浏览论坛帮助(FAQ)首页";
}

if(!$page) {

	preloader("faq");
	eval("\$faq = \"".template("faq")."\";");

} elseif($page == "usermaint") {

	preloader("faq_usermaint");
	eval("\$faq = \"".template("faq_usermaint")."\";");

} elseif($page == "using") {

	preloader("faq_using");
	eval("\$faq = \"".template("faq_using")."\";");

} elseif($page == "messages") {

	preloader("faq_messages_smilierow,faq_messages");
	$querysmilie = $db->query("SELECT * FROM $table_smilies WHERE type='smiley'");
	while($smilie = $db->fetch_array($querysmilie)) {
		eval("\$smilierows .= \"".template("faq_messages_smilierow")."\";");
	}

	eval("\$faq = \"".template("faq_messages")."\";");

} elseif($page == "misc")
{

	preloader("faq_misc");
	$groupinfo = "<tr>";
	$groupselect = "<select name=\"gid\">\n<option value=\"\">&nbsp;</option>\n";
	$permselect = "<select name=\"permission\">\n<option value=\"\">&nbsp;</option>\n";
	$num = 0;

	if($gid) {
		$current = "刚刚查询";
	} else {
		$current = "目前";
		$gid = $groupid;
	}
	$permname = array("groupid" => "用户组序号", "specifiedusers" => "特别用户", "status" => "头衔", "grouptitle" => "名称",
		"creditshigher" => "{$credittitle}下限", "creditslower" => "{$credittitle}上限", "stars" => "星星数",
		"groupavatar" => "级别头像", "allowcstatus" => "自定义头衔", "allowavatar" => "使用头像", "allowvisit" => "访问论坛",
		"allowsearch" => "搜索论坛", "allowview" => "浏览贴子", "allowpost" => "允许发帖", "allowpostpoll" => "发布投票",
		"allowvote" => "参与投票", "allowkarma" => "参与评分", "allowpostattach" => "上传附件", "allowgetattach" => "下载附件",
		"allowsetviewperm" => "设置贴子权限", "allowsetattachperm" => "设置附件权限", "allowsigbbcode" => "签名 Discuz! 代码",
		"allowsigimgcode" => "签名 [IMG] 代码", "allowviewstats" => "查看统计数据", "ismoderator" => "版主权限", 
		"issupermod" => "超级版主权限", "isadmin" => "管理员权限", "maxu2unum" => "短消息容量", "maxmemonum" => "备忘录容量",
		"maxsigsize" => "最大签名长度", "maxkarmavote" => "最大评分点数", "maxattachsize" => "最大附件尺寸", "attachextensions" => "附件类型");

	$query = $db->query("SELECT * FROM $table_usergroups ORDER BY creditslower");
	while($usergroup = $db->fetch_array($query)) {
		$groupselect .= "<option value=\"$usergroup[groupid]\">$usergroup[grouptitle]</option>\n";
		if($usergroup[groupid] == $gid)
		{
			foreach($usergroup as $key => $val)
			{//给属性添加单位
				if($key == "specifiedusers" || $key == "groupavatar" || $key == "attachextensions") {
					continue;
				} elseif($key == "creditshigher" || $key == "creditslower" || $key == "maxkarmavote") {
					$val .= " $creditunit";
				} elseif($key == "maxattachsize") {
					$val .= " 字节";
				} elseif($key == "allowavatar") {
					if($val == 0) {
						$val = "<b><font color=\"red\">×</font></b>";
					} elseif($val == 1) {
						$val = "论坛提供";
					} elseif($val == 2) {
						$val = "自定义";
					}
				} elseif((strstr($key, "allow") || strstr($key, "is")) && $val == 0) {
					$val = "<b><font color=\"red\">×</font></b>";
				} elseif((strstr($key, "allow") || strstr($key, "is")) && $val == 1) {
					$val = "√";
				}

				$num++;
				$residue = $num % 3;
				$groupinfo .= "<td bgcolor=\"$altbg1\" width=\"105\">$permname[$key]</td><td bgcolor=\"$altbg2\" width=\"95\" align=\"center\">$val</td>\n";
				if($residue == 0) {
					$groupinfo .= "</tr><tr>\n";
				} elseif($num < 3) {
					$groupinfo .= "<td bgcolor=\"$altbg2\" rowspan=\"15\"></td>\n";//估计有15行,其实只有10行
				}
			}
		}
	}

	for($i = 0; $i < $residue - 1; $i++) {
		$groupinfo .= "<td bgcolor=\"$altbg1\"></td><td bgcolor=\"$altbg2\" align=\"center\"></td>\n";
	}

	foreach($permname as $key => $val) {
		if(strstr($key, "allow") || strstr($key, "is")) {
			$permselect .= "<option value=\"$key\">$val</option>\n";
		}
	}

	$groupinfo .= "</tr>";
	$groupselect .= "</select>";
	$permselect .= "</select>";

	if($permission)
	{
		$query = $db->query("SELECT groupid, status, grouptitle, creditshigher, creditslower FROM $table_usergroups WHERE $permission='1'");
		if($db->num_rows($query)) {
			$grouplist = "<ul>找到具有 <span class=\"bold\">$permname[$permission]</span> 权限的用户组如下：<br><br>\n";
			while($group = $db->fetch_array($query)) {
				$grouplist .= "<li><span class=\"bold\"><a href=\"faq.php?page=misc&gid=$group[groupid]#3\">$group[grouptitle]</a></span> - 系统头衔 <span class=\"bold\">$group[status]</span>，{$credittitle}介于 <span class=\"bold\">$group[creditshigher]</span> 到 <span class=\"bold\">$group[creditslower]</span> 间</li>";
			}
		} else {
			$grouplist = "抱歉，没有找到具有 <span class=\"bold\">$permname[$permission]</span> 权限的用户组。";
		}
			
	}
	else
    {
		$grouplist = "";
	}
	eval("\$faq = \"".template("faq_misc")."\";");
}

gettotaltime();
echo $faq;
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();
?>