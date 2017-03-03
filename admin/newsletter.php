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

if(!$newslettersubmit) {

?>
<br><br><form method="post" action="admincp.php?action=newsletter">
<table cellspacing="0" cellpadding="0" border="0" width="550" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="2">论坛通知</td></tr>

<tr>
<td bgcolor="<?=$altbg1?>">收件人：</td><td bgcolor="<?=$altbg2?>">
<select name="nlstatus">
<option value="All">全部用户</option>
<option value="online">在线用户</option>
<option value="版主">版主</option>
<option value="超级版主">超级版主</option>
<option value="论坛管理员">论坛管理员</option>
</select></td></tr>

<tr>
<td bgcolor="<?=$altbg1?>">主题：</td><td bgcolor="<?=$altbg2?>"><input type="text" name="newssubject" size="70"></td></tr>

<tr>
<td bgcolor="<?=$altbg1?>" valign="top">内容：</td><td bgcolor="<?=$altbg2?>">
<textarea cols="70" rows="10" name="newsmessage"></textarea></td></tr>

<tr>
<td bgcolor="<?=$altbg1?>">通过：</td><td bgcolor="<?=$altbg2?>"><input type="radio" value="email" checked name="sendvia"> Email
<input type="radio" value="u2u" checked name="sendvia"> 短消息</td></tr>

</table></td></tr></table><br>
<center><input type="submit" name="newslettersubmit" value="发送通知"></center>
</form>
<?

} else {

	if($newssubject && $newsmessage) {
		$emails = "";
		$newssubject = "[Discuz!] ".$newssubject;
		if($nlstatus == "All") {
			$query = $db->query("SELECT username, email FROM $table_members WHERE newsletter='1'");
		} elseif($nlstatus == "online")
        {
			$query = $db->query("SELECT m.username, m.email FROM $table_members m, $table_sessions s WHERE s.username<>'' AND m.username=s.username AND m.newsletter='1'");
		} else {
			$query = $db->query("SELECT username, email FROM $table_members WHERE newsletter='1' AND status='$nlstatus'");
		}

		$sendto = $comma = "";
		while($memnews = $db->fetch_array($query)) {
			if($sendvia == "u2u") {
				$sendto .= "$comma'$memnews[username]'";
				$comma = ", ";
				$db->query("INSERT INTO $table_u2u (msgto, msgfrom, folder, new, subject, dateline, message)
					VALUES('$memnews[username]', '$cdbuser', 'inbox', '1', '$newssubject', '$timestamp', '$newsmessage')"); 
			} else {
				$emails .= $memnews[email].",";
			}
		}
		if($sendvia == "email") {
			if(@mail("nobody@localhost", "$newssubject", "$newsmessage", "Bcc: $emails\r\nFrom: $bbname <$adminemail>")) {
				cpmsg("Email 发送失败，请检查服务器设置。");
			} else {
				cpmsg("论坛通知成功发送。");
			}
		} else {
			if($sendto)
			{
				$db->query("UPDATE $table_members SET newu2u='1' WHERE username IN ($sendto)");
				$db->query("DELETE FROM $table_sessions");
			}
			cpmsg("论坛通知成功发送。");
		}
	} else {
		cpmsg("您没有输入消息的标题或内容，请返回修改。");
	}

}

?>