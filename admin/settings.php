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
if(!$settingsubmit) {

	$query = $db->query("SELECT * FROM $table_settings");
	$settings = $db->fetch_array($query);

	$themelist = "<select name=\"themenew\">\n";
	$query = $db->query("SELECT themename FROM $table_themes");
	while($themeinfo = $db->fetch_array($query)) {
		$selected = $themeinfo[themename] == $settings[theme] ? "selected=\"selected\"" : NULL;
		$themelist .= "<option value=\"$themeinfo[themename]\" $selected>$themeinfo[themename]</option>\n";
	}
	$themelist .= "</select>";

	$settings[moddisplay] == "selectbox" ? $modselectbox = "checked" : $modflat = "checked";
	$settings[timeformat] == "H:i" ? $check24 = "checked" : $check12 = "checked";

	$settings[dateformat] = str_replace("n", "mm", $settings[dateformat]);
	$settings[dateformat] = str_replace("j", "dd", $settings[dateformat]);
	$settings[dateformat] = str_replace("y", "yy", $settings[dateformat]);
	$settings[dateformat] = str_replace("Y", "yyyy", $settings[dateformat]);

	if($settings[avastatus]) {
		$avataron = "checked";
	} elseif($avastatus == "list") {
		$avatarlist = "checked";
	} else {
		$avataroff = "checked";
	}

?>
<tr bgcolor="<?=$altbg2?>">
<td align="center">
<br>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td align="center">[<a href="#基本设置">基本设置</a>] - [<a href="#用户注册与访问控制">用户注册与访问控制</a>] - 
[<a href="#界面与显示方式">界面与显示方式</a>] - [<a href="#论坛功能">论坛功能</a>] - 
[<a href="#用户权限">用户权限</a>] - [<a href="#其他设置">其他设置</a>]</td></tr>
</table></td></tr></table>

<br><form method="post" action="admincp.php?action=settings">
<input type="hidden" name="chcodeorig" value="<?=$settings[chcode]?>">
<?
	showtype("基本设置", "top");
	showsetting("论坛名称：", "bbnamenew", $settings[bbname], "text", "论坛名称，将显示在导航条和标题中");
	showsetting("网站名称：", "sitenamenew", $settings[sitename], "text", "网站名称，将显示在页面底部的联系方式处");
	showsetting("网站 URL：", "siteurlnew", $settings[siteurl], "text", "网站 URL，将作为链接显示在页面底部");
	showsetting("论坛关闭：", "bbclosednew", $settings[bbclosed], "radio", "暂时将论坛关闭，其他人无法访问，但不影响管理员访问");
	showsetting("论坛关闭的原因", "closedreasonnew", $settings[closedreason], "textarea", "论坛关闭时出现的提示信息");

	showtype("用户注册与访问控制");
	showsetting("允许新用户注册：", "regstatusnew", $settings[regstatus], "radio", "选择“否”将禁止游客注册成为会员，但不影响过去已注册的会员的使用");
	showsetting("保留用户名：", "censorusernew", $settings[censoruser], "text", "系统保留的用户名称，新用户将无法以这些名字注册。多个用户名间请用半角逗号 \",\" 分割");
	showsetting("允许同一 Email 注册不同用户：", "doubleenew", $settings[doublee], "radio", "选择“否”将只允许一个 Email 地址只能注册一个用户名");
	showsetting("新用户注册需发邮件验证 Email 地址：", "emailchecknew", $settings[emailcheck], "radio", "选择“是”将向用户注册 Email 发送一封验证邮件以确认邮箱的有效性，用户收到邮件并激活账号后才能拥有正常的权限");
	showsetting("隐藏无权访问的论坛：", "hideprivatenew", $settings[hideprivate], "radio", "如果用户权限达不到某个论坛的访问要求，系统将这些论坛隐藏不显示");
	showsetting("新用户注册发送短消息：", "welcommsgnew", $settings[welcommsg], "radio", "新用户注册后系统自动发送一条欢迎短消息");
	showsetting("欢迎短消息内容：", "welcommsgtxtnew", $settings[welcommsgtxt], "textarea", "欢迎短消息的详细内容");
	showsetting('注册时显示许可协议：', "bbrulesnew", $settings[bbrules], "radio", '新用户注册时显示许可协议，同意后才能继续注册');
	showsetting('许可协议内容：', "bbrulestxtnew", $settings[bbrulestxt], "textarea", '注册许可协议的详细内容');

	showtype("界面与显示方式");
	showsetting("默认论坛风格：", "", "", $themelist, "论坛默认的界面风格，游客和使用默认风格的会员将以此风格显示");
	showsetting("每页显示主题数：", "topicperpagenew", $settings[topicperpage], "text", "注意：修改以下三项设置只影响游客和新注册的会员，老会员仍按自身的设置显示");
	showsetting("每页显示贴数：", "postperpagenew", $settings[postperpage], "text");
	showsetting("每页显示会员数：", "memberperpagenew", $settings[memberperpage], "text");
	showsetting("热门话题最低贴数：", "hottopicnew", $settings[hottopic], "text", "超过一定贴子数的话题将显示为热门话题");
	showsetting("版主显示方式：", "", "", "<input type=\"radio\" name=\"moddisplaynew\" value=\"flat\" $modflat> 平面显示 &nbsp; <input type=\"radio\" name=\"moddisplaynew\" value=\"selectbox\" $modselectbox> 下拉菜单</td>", "首页论坛列表中版主显示方式");
	showsetting("快速发帖：", "fastpostnew", $settings[fastpost], "radio", "浏览论坛和贴子页面底部显示快速发帖表单");

	showtype("论坛功能");
	showsetting("使用论坛流量统计：", "statstatusnew", $settings[statstatus], "radio", "选择“是”将打开论坛统计功能，提供详细的论坛访问统计信息，此功能可能会影响效率");
	showsetting("显示程序运行信息：", "debugnew", $settings[debug], "radio", "选择“是”将在页脚处显示程序运行时间和数据库查询次数");
	showsetting("页面 gzip 压缩：", "gzipcompressnew", $settings[gzipcompress], "radio", "将页面内容以 gzip 压缩后传输，可以加快传输速度，需 PHP 4.0.4 以上才能使用");
	showsetting("记录并显示在线用户：", "whosonlinenew", $settings[whosonlinestatus], "radio", "在首页和论坛列表页显示在线会员列表");
	showsetting("贴子中显示作者状态：", "vtonlinestatusnew", $settings[vtonlinestatus], "radio", "浏览贴子时显示作者在线状态");
	showsetting("本人发起或回复的主题显示加点图标：", "dotfoldersnew", $settings[dotfolders], "radio", "在浏览者发起或恢复的主题中显示加点图标，此功能非常影响效率");
	showsetting("发帖增加积分：", "postcreditsnew", $settings[postcredits], "text", "每发一贴作者增加积分数，范围为 0～127 内的整数，建议设置为 0(发帖不加积分) 或 1(发帖积分加 1)。如果修改本项设置，全部会员的积分将与发帖数相对应重新计算");
	showsetting("被收入精华增加积分：", "digistcreditsnew", $settings[digistcredits], "text", "贴子被收入精华区作者增加积分数，范围为 -128～+127 内的整数");
	showsetting("预防灌水时间(秒)：", "floodctrlnew", $settings[floodctrl], "text", "为防止恶意灌水，会员两次发帖间隔不得小于此时间设置");
	showsetting("两次评分最小间隔(秒)：", "karmactrlnew", $settings[karmactrl], "text", "为防止刷屏作弊，低于此设置时间的加分将被禁止，0 为不限制，版主和管理员不受限制");

	showtype("用户权限");
	showsetting("允许查看会员列表：", "memliststatusnew", $settings[memliststatus], "radio", "允许会员和游客查看会员列表和相关信息");
	showsetting("允许向版主反应贴子：", "reportpostnew", $settings[reportpost], "radio", "允许会员通过短消息像版主和管理员反应贴子");
	showsetting("允许用户自定义 GB/BIG5 内码：", "chcodenew", $settings[chcode], "radio", "运行用户在控制面板选择自己的内码浏览论坛，此功能影响效率");
	showsetting("贴子最大字数：", "maxpostsizenew", $settings[maxpostsize], "text", "会员发帖长度不能超过此字数设置，管理员不受限制");
	showsetting("头像最大尺寸(像素)：", "maxavatarsizenew", $settings[maxavatarsize], "text", "会员头像文件的长宽不能超过此尺寸设置，需 PHP 4.0.5 以上，否则请设置为 0");

	showtype("其他设置");
	showsetting("时间格式：", "", "", "<input type=\"radio\" name=\"timeformatnew\" value=\"24\" $check24> 24 小时制 <input type=\"radio\" name=\"timeformatnew\" value=\"12\" $check12> 12 小时制</td>", "注意：修改以下三项设置只影响游客和新注册的会员，老会员仍按自身的设置显示");
	showsetting("日期格式：", "dateformatnew", $settings[dateformat], "text", "请用 yyyy(yy)、mm、dd 表示，如格式 yyyy-mm-dd 为 2002-10-27");
	showsetting("系统时差：", "timeoffsetnew", $settings[timeoffset], "text", "论坛时间与 GMT 标准时间的时差，北京时间请设置为 +8，除非服务器时间不准，否则无需更改默认设定");
	showsetting("积分名称：", "credittitlenew", $settings[credittitle], "text", "显示于与积分相关的内容中，您可修改为自己的个性化名称");
	showsetting("积分单位：", "creditunitnew", $settings[creditunit], "text", "积分名称的相关单位");
	showsetting("编辑贴子附加编辑记录：", "editedbynew", $settings[editedby], "radio", "60 秒后编辑贴子附加“本贴由...于...最后编辑”的记录，但管理员不会被记录");
	showsetting("贴子中显示图片/动画附件：", "attachimgpostnew", $settings[attachimgpost], "radio", "在贴子中直接将图片或动画附件显示出来，而不需要点击附件链接");
	showsetting("发帖页面 BB 代码辅助：", "bbinsertnew", $settings[bbinsert], "radio", "发帖页面包含 BB 代码高级插入工具，可以简化代码和贴子的编写");
	showsetting("发帖时 Smilies 代码辅助：", "smileyinsertnew", $settings[smileyinsert], "radio", "发帖页面包含 Smilies 快捷工具，点击图标即可插入 Smilies");
	showsetting("每行显示 Smilies 个数：", "smcolsnew", $settings[smcols], "text", "发帖页面每行显示 Smilies 的个数");
	showtype("", "bottom");
?>

</table></td></tr></table><br><br>
<center><input type="submit" name="settingsubmit" value="确认修改"></center>
</form>

</td></tr>

<?

} else {

	if(!$credittitlenew || strlen($credittitlenew) > 10 || strlen($creditunitnew) > 10) {
		cpmsg("积分名称或单位为空或超过 10 个字符，请返回修改。");
	}

	if(PHP_VERSION < "4.0.4" && $gzipcompressnew) {
		cpmsg("您的 PHP 版本低于 4.0.4，无法使用 gzip 压缩功能，请返回修改。");
	}

	if(PHP_VERSION < "4.0.5" && $maxavatarsizenew) {
		cpmsg("您的 PHP 版本低于 4.0.5，无法限制头像大小，请返回修改。");
	}

	$timeformatnew = $timeformatnew == "24" ? "H:i" : "h:i A";

	$bbnamenew = cdbhtmlspecialchars($bbnamenew);
	$bbrulestxtnew = cdbhtmlspecialchars($bbrulestxtnew);
	$welcommsgtxtnew = cdbhtmlspecialchars($welcommsgtxtnew);

	$dateformatnew = str_replace("mm", "n", $dateformatnew);
	$dateformatnew = str_replace("dd", "j", $dateformatnew);
	$dateformatnew = str_replace("yyyy", "Y", $dateformatnew);
	$dateformatnew = str_replace("yy", "y", $dateformatnew);

	$query = $db->query("SELECT postcredits FROM $table_settings");
	$postcredits = $db->result($query, 0);
	if($postcredits != $postcreditsnew) {
		$db->query("UPDATE $table_members SET credit=credit+(postnum*($postcreditsnew-$postcredits))");
	}

	$db->query("UPDATE $table_settings SET bbname='$bbnamenew', regstatus='$regstatusnew', censoruser='$censorusernew',
		doublee='$doubleenew', emailcheck='$emailchecknew', bbrules='$bbrulesnew', bbrulestxt='$bbrulestxtnew',
		welcommsg='$welcommsgnew', welcommsgtxt='$welcommsgtxtnew', bbclosed='$bbclosednew', closedreason='$closedreasonnew',
		sitename='$sitenamenew', siteurl='$siteurlnew', theme='$themenew', credittitle='$credittitlenew',
		creditunit='$creditunitnew', moddisplay='$moddisplaynew', floodctrl='$floodctrlnew', karmactrl='$karmactrlnew',
		hottopic='$hottopicnew', topicperpage='$topicperpagenew', postperpage='$postperpagenew', memberperpage='$memberperpagenew',
		maxpostsize='$maxpostsizenew', maxavatarsize='$maxavatarsizenew', smcols='$smcolsnew', whosonlinestatus='$whosonlinenew',
		vtonlinestatus='$vtonlinestatusnew', chcode='$chcodenew', gzipcompress='$gzipcompressnew', postcredits='$postcreditsnew',
		digistcredits='$digistcreditsnew', hideprivate='$hideprivatenew', fastpost='$fastpostnew', memliststatus='$memliststatusnew',
		statstatus='$statstatusnew', debug='$debugnew', reportpost='$reportpostnew', bbinsert='$bbinsertnew',
		smileyinsert='$smileyinsertnew', editedby='$editedbynew', dotfolders='$dotfoldersnew', attachimgpost='$attachimgpostnew',
		timeformat='$timeformatnew', dateformat='$dateformatnew', timeoffset='$timeoffsetnew'");


	if($chcodenew != $chcodeorig) {
		$db->query("UPDATE $table_members SET charset='$cdb_charset'");
	}

	updatecache("settings");
	cpmsg("Discuz! 常规选项成功更新。");
}

?>