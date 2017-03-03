# Identify: MTQ3OTg3MzExNiwxLjAsYWxsLDEsMw==
#
# Discuz! 数据备份(Discuz! Data Dump Volume 3)
# 版本: Discuz! 1.0
# 备份时间: 2016-11-23 24
# 备份方式: 全部备份
# 数据库前缀: cdb_
#
# 官方网站: http://www.Discuz.net
# 请随时访问以上地址以获得最新的软件升级信息
# --------------------------------------------------------


INSERT INTO cdb_templates VALUES('129','post_reply_review_toolong','0','<tr bgcolor=\\\"$altbg1\\\">\n<td colspan=\\\"2\\\" valign=\\\"top\\\" width=\\\"20%\\\">本主题回复较多，请 <a href=\\\"$threadlink\\\">点击这里</a> 查看。</td></tr>');
INSERT INTO cdb_templates VALUES('130','post_edit','0','<script language=\\\"JavaScript\\\" src=\\\"bbcode.js\\\"></script>\n<script language=\\\"JavaScript\\\">\nvar postmaxchars = $maxpostsize;\nvar isadmin = \\\"$isadmin\\\";\nfunction checklength(theform) {\n        if (postmaxchars != 0) { message = \\\"系统允许：$maxpostsize 字符\\\"; }\n        else { message = \\\"\\\"; }\n        alert(\\\"\\\\n当前长度：\\\"+theform.message.value.length+\\\" 字符\\\\n\\\\n\\\"+message);\n}\nfunction validate(theform) {\n        if (theform.message.value == \\\"\\\" && theform.subject.value == \\\"\\\") {\n                alert(\\\"请完成标题或内容栏。\\\");\n                return false; }\n        if (postmaxchars != 0 && isadmin != 1) {\n                if (theform.message.value.length > $maxpostsize) {\n                        alert(\\\"您的贴子长度超过限制\\\\n\\\\n当前长度：\\\"+theform.message.value.length+\\\" 字符\\\\n系统允许：$maxpostsize 字符\\\");\n                        return false; }\n                else { return true; }\n        } else { return true; }\n}\n</script>\n$preview\n<form method=\\\"post\\\" name=\\\"input\\\" action=\\\"post.php?action=edit\\\" $enctype onSubmit=\\\"return validate(this)\\\">\n<input type=\\\"hidden\\\" name=\\\"editsubmit\\\" value=\\\"submit\\\">\n<input type=\\\"hidden\\\" name=\\\"page\\\" value=\\\"$page\\\">\n\n<table cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" border=\\\"0\\\" width=\\\"$tablewidth\\\" align=\\\"center\\\">\n<tr><td bgcolor=\\\"$bordercolor\\\">\n\n<table border=\\\"0\\\" cellspacing=\\\"$borderwidth\\\" cellpadding=\\\"$tablespace\\\" width=\\\"100%\\\">\n<tr>\n<td colspan=\\\"2\\\" class=\\\"header\\\">编辑贴子</td>\n</tr>\n$loggedin \n$viewpermission\n<tr>\n<td bgcolor=\\\"$altbg1\\\" width=\\\"20%\\\">主题：</td>\n<td bgcolor=\\\"$altbg2\\\">\n<input type=\\\"text\\\" name=\\\"subject\\\" size=\\\"45\\\" value=\\\"$postinfo[subject]\\\" tabindex=\\\"3\\\">\n<input type=\\\"hidden\\\" name=\\\"origsubject\\\" value=\\\"$postinfo[subject]\\\">\n</td></tr>\n\n<tr>\n<td bgcolor=\\\"$altbg1\\\">图标：</td><td bgcolor=\\\"$altbg2\\\">$icons</td>\n</tr>\n$bbcodeinsert\n<tr>\n<td bgcolor=\\\"$altbg1\\\" valign=\\\"top\\\">贴子内容 <a href=\\\"javascript:checklength(document.input);\\\">[字数检查]</a>：<br><br>\nhtml 状态 $allowhtml<br>\nSmilies 状态 $allowsmilies<br>\n<a href=\\\"faq.php?page=misc#1\\\" target=\\\"_blank\\\">Discuz! 代码</a> 状态 $allowbbcode<br>\n[img]代码 状态 $allowimgcode\n<br><br><br><br>\n$smilieinsert\n</td>\n\n<td bgcolor=\\\"$altbg2\\\">\n<textarea rows=\\\"18\\\" cols=\\\"85\\\" name=\\\"message\\\" tabindex=\\\"4\\\" onSelect=\\\"javascript: storeCaret(this);\\\" onClick=\\\"javascript: storeCaret(this);\\\" onKeyUp=\\\"javascript: storeCaret(this);\\\" onKeyDown=\\\"javascript: ctlent();\\\">$postinfo[message]</textarea>\n<br><br>\n<input type=\\\"checkbox\\\" name=\\\"parseurloff\\\" value=\\\"1\\\" $urloffcheck> 禁用 URL 识别<br>\n<input type=\\\"checkbox\\\" name=\\\"smileyoff\\\" value=\\\"1\\\" $smileyoffcheck> 禁用 Smilies<br>\n<input type=\\\"checkbox\\\" name=\\\"bbcodeoff\\\" value=\\\"1\\\" $codeoffcheck> 禁用 Discuz! 代码<br>\n<input type=\\\"checkbox\\\" name=\\\"usesig\\\" value=\\\"1\\\" $usesigcheck> 使用个人签名<br>\n<input type=\\\"checkbox\\\" name=\\\"delete\\\" value=\\\"1\\\"> <b>!删除本贴</b></td>\n</tr>\n$attachfile\n</table>\n</td></tr></table><br>\n<input type=\\\"hidden\\\" name=\\\"fid\\\" value=\\\"$fid\\\">\n<input type=\\\"hidden\\\" name=\\\"tid\\\" value=\\\"$tid\\\">\n<input type=\\\"hidden\\\" name=\\\"pid\\\" value=\\\"$pid\\\">\n<input type=\\\"hidden\\\" name=\\\"postsubject\\\" value=\\\"$postinfo[subject]\\\">\n<center><input type=\\\"submit\\\" name=\\\"editsubmit\\\" value=\\\"编辑贴子\\\" tabindex=\\\"5\\\">\n<input type=\\\"submit\\\" name=\\\"previewpost\\\" value=\\\"预览贴子\\\" tabindex=\\\"6\\\"></center>\n</form>');
INSERT INTO cdb_templates VALUES('131','post_bbcodeinsert','0','<tr>\n<td bgcolor=\\\"$altbg1\\\">Discuz! 代码辅助模式：<br>\n<input type=\\\"radio\\\" name=\\\"mode\\\" value=\\\"2\\\" onclick=\\\"chmode(\\\'2\\\')\\\" checked> 提示插入<br>\n<input type=\\\"radio\\\" name=\\\"mode\\\" value=\\\"0\\\" onclick=\\\"chmode(\\\'0\\\')\\\"> 直接插入<br>\n<input type=\\\"radio\\\" name=\\\"mode\\\" value=\\\"1\\\" onclick=\\\"chmode(\\\'1\\\')\\\"> 帮助信息</td>\n<td bgcolor=\\\"$altbg2\\\">\n<select name=\\\"font\\\" onChange=\\\"chfont(this.options[this.selectedIndex].value)\\\" size=\\\"1\\\">\n  <option value=\\\"宋体\\\">宋体</option>\n  <option value=\\\"黑体\\\">黑体</option>\n  <option value=\\\"Arial\\\">Arial</option>\n  <option value=\\\"Book Antiqua\\\">Book Antiqua</option>\n  <option value=\\\"Century Gothic\\\">Century Gothic</option>\n  <option value=\\\"Courier New\\\">Courier New</option>\n  <option value=\\\"Georgia\\\">Georgia</option>\n  <option value=\\\"Impact\\\">Impact</option>\n  <option value=\\\"Tahoma\\\">Tahoma</option>\n  <option value=\\\"Times New Roman\\\">Times New Roman</option>\n  <option value=\\\"Verdana\\\" selected>Verdana</option></select>\n<select name=\\\"size\\\" onChange=\\\"chsize(this.options[this.selectedIndex].value)\\\" size=\\\"1\\\">\n  <option value=\\\"-2\\\">-2</option>\n  <option value=\\\"-1\\\">-1</option>\n  <option value=\\\"1\\\">1</option>\n  <option value=\\\"2\\\">2</option>\n  <option value=\\\"3\\\" selected>3</option>\n  <option value=\\\"4\\\">4</option>\n  <option value=\\\"5\\\">5</option>\n  <option value=\\\"6\\\">6</option></select>\n <select name=\\\"color\\\" onChange=\\\"chcolor(this.options[this.selectedIndex].value)\\\" size=\\\"1\\\">\n  <option value=\\\"White\\\" style=\\\"color:white;\\\">White</option>\n  <option value=\\\"Black\\\" style=\\\"color:black;\\\">Black</option>\n  <option value=\\\"Red\\\" style=\\\"color:red;\\\">Red</option>\n  <option value=\\\"Yellow\\\" style=\\\"color:yellow;\\\">Yellow</option>\n  <option value=\\\"Pink\\\" style=\\\"color:pink;\\\">Pink</option>\n  <option value=\\\"Green\\\" style=\\\"color:green;\\\">Green</option>\n  <option value=\\\"Orange\\\" style=\\\"color:orange;\\\">Orange</option>\n  <option value=\\\"Purple\\\" style=\\\"color:purple;\\\">Purple</option>\n  <option value=\\\"Blue\\\" style=\\\"color:blue;\\\">Blue</option>\n  <option value=\\\"Beige\\\" style=\\\"color:beige;\\\">Beige</option>\n  <option value=\\\"Brown\\\" style=\\\"color:brown;\\\">Brown</option>\n  <option value=\\\"Teal\\\" style=\\\"color:teal;\\\">Teal</option>\n  <option value=\\\"Navy\\\" style=\\\"color:navy;\\\">Navy</option>\n  <option value=\\\"Maroon\\\" style=\\\"color:maroon;\\\">Maroon</option>\n  <option value=\\\"LimeGreen\\\" style=\\\"color:limegreen;\\\">LimeGreen</option></select>\n<br><br>\n<a href=\\\"javascript:bold()\\\"><img src=\\\"$imgdir/bb_bold.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入粗体文本\\\"></a>\n<a href=\\\"javascript:italicize()\\\"><img src=\\\"$imgdir/bb_italicize.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入斜体文本\\\"></a>\n<a href=\\\"javascript:underline()\\\"><img src=\\\"$imgdir/bb_underline.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入下划线\\\"></a>\n<a href=\\\"javascript:center()\\\"><img src=\\\"$imgdir/bb_center.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"居中对齐\\\"></a>\n<a href=\\\"javascript:hyperlink()\\\"><img src=\\\"$imgdir/bb_url.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入超级链接\\\"></a>\n<a href=\\\"javascript:email()\\\"><img src=\\\"$imgdir/bb_email.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入邮件地址\\\"></a>\n<a href=\\\"javascript:image()\\\"><img src=\\\"$imgdir/bb_image.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入图像\\\"></a>\n<a href=\\\"javascript:flash()\\\"><img src=\\\"$imgdir/bb_flash.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入 flash\\\"></a>\n<a href=\\\"javascript:code()\\\"><img src=\\\"$imgdir/bb_code.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入代码\\\"></a>\n<a href=\\\"javascript:quote()\\\"><img src=\\\"$imgdir/bb_quote.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入引用\\\"></a>\n<a href=\\\"javascript:list()\\\"><img src=\\\"$imgdir/bb_list.gif\\\" border=\\\"0\\\" width=\\\"23\\\" height=\\\"22\\\" alt=\\\"插入列表\\\"></a>\n</td></tr>');
INSERT INTO cdb_templates VALUES('132','post_smilieinsert','0','<table cellpadding=\\\"3\\\" cellspacing=\\\"1\\\" border=\\\"0\\\" bgcolor=\\\"$altbg1\\\" style=\\\"border-width: 2px; border-style: outset\\\" align=\\\"center\\\">\n<tr>\n<td colspan=\\\"$smcols\\\" align=\\\"center\\\" bgcolor=\\\"$altbg1\\\" style=\\\"border-width:1px; border-style:inset\\\">表情符号</td>\n</tr>\n<tr align=\\\'center\\\'>\n$smilies\n</tr></table>');
INSERT INTO cdb_templates VALUES('133','footer','0','<br>\n<table cellspacing=\\\"0\\\" cellpadding=\\\"1\\\" border=\\\"0\\\" width=\\\"$tablewidth\\\" align=\\\"center\\\" bgcolor=\\\"$bordercolor\\\"> \n<tr><td><table width=\\\"100%\\\" cellspacing=\\\"0\\\" cellpadding=\\\"2\\\" bgcolor=\\\"$altbg1\\\" style=\\\"table-layout: fixed\\\">\n<tr><td class=\\\"nav\\\" width=\\\"90%\\\" nowrap>&nbsp;<a href=\\\"index.php\\\">$bbname</a> $navigation</td>\n<td align=\\\"right\\\" width=\\\"10%\\\"><a href=\\\"#top\\\"><img src=\\\"$imgdir/arrow_up.gif\\\" border=\\\"0\\\"></a></td>        \n</tr></table></td></tr></table>\n\n<br><br><center><span class=\\\"mediumtxt\\\">\n&lt; <a href=\\\"mailto:$adminemail\\\" class=\\\"mediumtxt\\\">联系我们</a> - <a href=\\\"$siteurl\\\" class=\\\"mediumtxt\\\" target=\\\"_blank\\\">$sitename</a> &gt;</span>\n<br><br><br><font style=\\\"font-size: 11px; font-family: Tahoma, Arial\\\">\nPowered by <a href=\\\"http://www.Discuz.net\\\" style=\\\"color: $text\\\" target=\\\"_blank\\\"><b>Discuz!</b> $version</a>\n&copy; 2002, <b><a href=\\\"http://www.crossday.com\\\" target=\\\"_blank\\\" style=\\\"color: $text\\\">Crossday Studio</a></b> of <b><a href=\\\"http://11cn.org\\\" target=\\\"_blank\\\" style=\\\"color: $text\\\">11cn.org</a></b>\n$debuginfo</font></center>\n<a name=\\\"#bottom\\\"></a>\n\n</body></html>');
INSERT INTO cdb_templates VALUES('134','post_attachmentbox','0','<tr>\n<td bgcolor=\\\"$altbg1\\\" $valign>附件 (小于 $maxattachsize_kb kb)：</td>\n<td bgcolor=\\\"$altbg2\\\">$setattachperm<input type=\\\"file\\\" name=\\\"attach\\\" size=\\\"20\\\">$allowattachextensions</td>\n</tr>');
INSERT INTO cdb_templates VALUES('135','post_viewpermission','0','<tr>\n<td bgcolor=\\\"$altbg1\\\">浏览所需{$credittitle}：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"viewperm\\\" size=\\\"6\\\" value=\\\"$currcredits\\\"> $creditunit (0 为不限制)</td>\n</tr>');
INSERT INTO cdb_templates VALUES('136','post_edit_attachmentbox_edit','0','<tr>\n<td bgcolor=\\\"$altbg1\\\" $valign>附件 (小于 $maxattachsize_kb kb)：</td>\n<td bgcolor=\\\"$altbg2\\\">目前附件：$attachicon <a href=\\\"viewthread.php?action=attachment&aid=$postinfo[aid]\\\" target=\\\"_blank\\\">$postattach[filename]</a> ($attachsize ，已被下载 $postattach[downloads] 次)<br>\n<input type=\\\"radio\\\" checked name=\\\"attachedit\\\" value=\\\"\\\" onclick=\\\"this.form.attach.disabled=this.form.attachperm.disabled=this.checked;this.form.origattachperm.disabled=!this.checked\\\"> 保留附件 $setorigattachperm<br>\n<input type=\\\"radio\\\" name=\\\"attachedit\\\" value=\\\"delete\\\" onclick=\\\"this.form.origattachperm.disabled=this.form.attach.disabled=this.form.attachperm.disabled=this.checked\\\"> 删除附件<br>\n<input type=\\\"radio\\\" name=\\\"attachedit\\\" value=\\\"new\\\" onclick=\\\"this.form.origattachperm.disabled=this.checked;this.form.attachperm.disabled=this.form.attach.disabled=!this.checked\\\"> 上传新附件 $setattachperm<input type=\\\"file\\\" name=\\\"attach\\\" size=\\\"20\\\" disabled>$allowattachextensions\n</tr>');
INSERT INTO cdb_templates VALUES('137','post_edit_attachmentbox','0','<tr>\n<td bgcolor=\\\"$altbg1\\\" $valign>附件 (小于 $maxattachsize_kb kb)：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"hidden\\\" name=\\\"attachedit\\\" value=\\\"new\\\">\n$setattachperm<input type=\\\"file\\\" name=\\\"attach\\\" size=\\\"20\\\">$allowattachextensions</td>\n</tr>');
INSERT INTO cdb_templates VALUES('138','post_notloggedin','0','<tr>\n<td bgcolor=\\\"$altbg1\\\">用户名：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"username\\\" tabindex=\\\"1\\\" size=\\\"25\\\" maxlength=\\\"25\\\" value=\\\"$username\\\"> &nbsp;<a href=\\\"member.php?action=reg\\\">立即注册</a></td>\n</tr>\n\n<tr>\n<td bgcolor=\\\"$altbg1\\\">密码：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"password\\\" name=\\\"password\\\" tabindex=\\\"2\\\" size=\\\"25\\\" value=\\\"$password\\\"> &nbsp;<a href=\\\"misc.php?action=lostpw\\\">取回密码</a></td>\n</tr>');
INSERT INTO cdb_templates VALUES('139','post_loggedin','0','<tr>\n<td bgcolor=\\\"$altbg1\\\">用户名：</td>\n<td bgcolor=\\\"$altbg2\\\">$cdbuserss  [<a href=\\\"member.php?action=logout\\\">退出登录</a>]</td>\n</tr>');
INSERT INTO cdb_templates VALUES('140','topicadmin_emailfriend','0','<form method=\\\"post\\\" action=\\\"topicadmin.php?action=emailfriend\\\">\n<table cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" border=\\\"0\\\" width=\\\"$tablewidth\\\" align=\\\"center\\\">\n<tr><td bgcolor=\\\"$bordercolor\\\">\n\n<table border=\\\"0\\\" cellspacing=\\\"$borderwidth\\\" cellpadding=\\\"$tablespace\\\" width=\\\"100%\\\">\n<tr class=\\\"header\\\">\n<td colspan=\\\"2\\\">推荐给朋友</td>\n</tr>\n\n<tr>\n<td bgcolor=\\\"$altbg1\\\" width=\\\"21%\\\">您的名字</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"fromname\\\" size=\\\"25\\\" maxlength=\\\"40\\\" value=\\\"$cdbuserss\\\"></td>\n</tr>\n\n<tr>\n<td bgcolor=\\\"$altbg1\\\">您的 Email：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"fromemail\\\" size=\\\"25\\\" value=\\\"$this[email]\\\"></td>\n</tr>\n\n<tr>\n<td bgcolor=\\\"$altbg1\\\">接收人姓名：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"sendtoname\\\" size=\\\"25\\\" value=\\\"\\\"></td>\n</tr>\n<tr>\n<td bgcolor=\\\"$altbg1\\\">接收人 Email：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"sendtoemail\\\" size=\\\"25\\\" value=\\\"\\\"></td>\n</tr>\n<tr>\n<td bgcolor=\\\"$altbg1\\\">主题：</td>\n<td bgcolor=\\\"$altbg2\\\"><input type=\\\"text\\\" name=\\\"subject\\\" size=\\\"65\\\" value=\\\"推荐：$thread[subject]\\\"></td>\n</tr>\n<tr>\n<td bgcolor=\\\"$altbg1\\\" valign=\\\"top\\\">消息：</td>\n<td bgcolor=\\\"$altbg2\\\"><textarea rows=\\\"9\\\" cols=\\\"65\\\" name=\\\"message\\\">\n你好！我在 $bbname($boardurl)\n看到了这篇贴子，认为很有价值，特推荐给你。\n\n地址 $threadurl\n\n希望你能喜欢。\n</textarea></td>\n</tr>\n</table>\n</td></tr></table><br>\n<center><input type=\\\"submit\\\" name=\\\"sendsubmit\\\" value=\\\"发  送\\\"></center>\n<input type=\\\"hidden\\\" name=\\\"tid\\\" value=\\\"$tid\\\">\n</form>');
INSERT INTO cdb_templates VALUES('141','index_news','0','<table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"$tablewidth\\\" bgcolor=\\\"$bordercolor\\\" align=\\\"center\\\">\n<tr><td>\n<table border=\\\"0\\\" cellspacing=\\\"$borderwidth\\\" cellpadding=\\\"$tablespace\\\" width=\\\"100%\\\">\n<tr class=\\\"header\\\"><td colspan=\\\"3\\\">$bbname 滚动新闻</td></tr>\n<tr bgcolor=\\\"$altbg2\\\">\n<td colspan=\\\"3\\\" align=\\\"center\\\">\n<script language=\\\"JavaScript\\\">\nvar delay = 3;\nvar bcolor = \\\"$altbg2\\\";\nvar tcolor = \\\"$tabletext\\\";\nvar fcontent = new Array();\nbegintag = \\\'\\\';\n$index_news_row\nclosetag = \\\'\\\';\n</script>\n<script language=\\\"JavaScript\\\" src=\\\"newsfader.js\\\"></script>\n</td></tr></table>\n</td></tr></table>');
INSERT INTO cdb_templates VALUES('142','forumdisplay_whosonline','0','<table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"$tablewidth\\\" bgcolor=\\\"$bordercolor\\\" align=\\\"center\\\">\n<tr><td>\n<table border=\\\"0\\\" cellspacing=\\\"$borderwidth\\\" cellpadding=\\\"$tablespace\\\" width=\\\"100%\\\">\n<tr class=\\\"header\\\"><td width=\\\"92%\\\">正在浏览此论坛的会员</td><td width=\\\"8%\\\" align=\\\"center\\\" nowrap><a href=\\\"javascript: this.location.reload();\\\"><font class=\\\"navtd\\\" style=\\\"font-weight: normal\\\">刷 新</font></a></td></tr>\n<tr bgcolor=\\\"$altbg2\\\">\n<td colspan=\\\"2\\\">\n<table cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" border=\\\"0\\\" width=\\\"98%\\\" align=\\\"center\\\">\n<!-- <tr><td colspan=\\\"6\\\"><img src=\\\"$imgdir/online_admin.gif\\\"> 论坛管理员 &nbsp; &nbsp; &nbsp;<img src=\\\"$imgdir/online_moderator.gif\\\"> 版主 &nbsp; &nbsp; &nbsp;<img src=\\\"$imgdir/online_member.gif\\\"> 会员 &nbsp; &nbsp; &nbsp;<img src=\\\"$imgdir/online_guest.gif\\\"> 游客</td></tr> -->\n$memberlist\n</table></td></tr></table></td></tr></table>\n<br>');
INSERT INTO cdb_templates VALUES('143','viewthread_printable_attachmentimage','0','<br><br>$attachicon 本贴包含图片附件 &nbsp;URL {$boardurl}viewthread.php?action=attachment&aid=$postattach[aid]<br><br><a href=\\\"$attachurl/$postattach[attachment]\\\" target=\\\"_blank\\\"><img src=\\\"$attachurl/$postattach[attachment]\\\" border=\\\"0\\\" alt=\\\"点击查看全图\\\" onload=\\\"javascript:if(this.width>screen.width-200) this.width=screen.width-50\\\"></a>');
INSERT INTO cdb_templates VALUES('144','viewthread_post_attachment','0','<br><br>$attachicon 附件：<a href=\\\"viewthread.php?action=attachment&aid=$postattach[aid]\\\" target=\\\"_blank\\\">$postattach[filename]</a> ($attachsize)<br><font class=\\\"smalltxt\\\">该附件已经被下载 $postattach[downloads] 次$creditrequire</font><br>');
INSERT INTO cdb_templates VALUES('145','viewthread_printable_attachment','0','<br><br>$attachicon 附件：$postattach[filename] (大小 $attachsize / 下载 $postattach[downloads] 次$creditsrequire)<br>URL {$boardurl}viewthread.php?action=attachment&aid=$postattach[aid]<br>');
INSERT INTO cdb_templates VALUES('146','viewthread_post_attachmentimage','0','<br><br>$attachicon 本贴包含图片附件：<br><br><a href=\\\"$attachurl/$postattach[attachment]\\\" target=\\\"_blank\\\"><img src=\\\"$attachurl/$postattach[attachment]\\\" border=\\\"0\\\" alt=\\\"点击查看全图\\\" onload=\\\"javascript:if(this.width>screen.width-200) this.width=screen.width-200\\\"></a>');
INSERT INTO cdb_templates VALUES('147','viewthread_post_attachmentswf','0','<br><br>$attachicon 本贴包含 flash 附件：<br><br><embed width=\\\"400\\\" height=\\\"300\\\" src=\\\"$attachurl/$postattach[attachment]\\\" type=\\\"application/x-shockwave-flash\\\"></embed>');
INSERT INTO cdb_templates VALUES('148','viewthread_post_sig','0','</td></tr><tr><td valign=\\\"bottom\\\"><hr noshade size=\\\"0\\\" width=\\\"50%\\\" color=\\\"$sigline\\\" align=\\\"left\\\">$post[signature]');
INSERT INTO cdb_templates VALUES('149','forumdisplay_newtopic','0','<a href=\\\"post.php?action=newthread&fid=$fid\\\"><img src=\\\"$imgdir/newtopic.gif\\\" border=\\\"0\\\"></a>');
INSERT INTO cdb_templates VALUES('150','forumdisplay_newpoll','0','� <a href=\\\"post.php?action=newthread&fid=$fid&poll=yes\\\"><img src=\\\"$imgdir/poll.gif\\\" border=\\\"0\\\"></a>');
INSERT INTO cdb_templates VALUES('151','index_forum_lastpost','0','<table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" border=\\\"0\\\" width=\\\"100%\\\">\n<tr><td align=\\\"right\\\" nowrap><font class=\\\"smalltxt\\\">$lastpost</font></td>\n<td nowrap>&nbsp;<a href=\\\"viewthread.php?goto=lastpost&fid=$forum[fid]\\\"><img src=\\\"$imgdir/lastpost.gif\\\" border=\\\"0\\\"></a></td>\n</tr></table>');
INSERT INTO cdb_templates VALUES('152','forumdisplay_thread_lastpost','0','<table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" border=\\\"0\\\" width=\\\"100%\\\"><tr align=\\\"right\\\">\n<td nowrap><font class=\\\"smalltxt\\\">$lastpost</font></td>\n<td nowrap>&nbsp;<a href=\\\"viewthread.php?goto=lastpost&tid=$thread[tid]$highlight\\\"><img src=\\\"$imgdir/lastpost.gif\\\" border=\\\"0\\\"></a></td>\n</tr></table>');
INSERT INTO cdb_templates VALUES('153','memcp_home_subs_none','0','<tr><td bgcolor=\\\"$altbg1\\\" colspan=\\\"6\\\">目前没有被收藏的主题。</td></tr>');
INSERT INTO cdb_templates VALUES('154','memcp_home_subs_row','0','<tr><td bgcolor=\\\"$altbg1\\\" width=\\\"5%\\\" align=\\\"center\\\">$sub[icon]</td>\n<td bgcolor=\\\"$altbg2\\\" onMouseOver =\\\"this.style.backgroundColor=\\\'$altbg1\\\'\\\" onMouseOut =\\\"this.style.backgroundColor=\\\'$altbg2\\\'\\\"><a href=\\\"viewthread.php?tid=$sub[tid]\\\">$sub[subject]</a></td>\n<td bgcolor=\\\"$altbg1\\\" align=\\\"center\\\"><a href=\\\"forumdisplay.php?fid=$sub[fid]\\\">$sub[name]</a></td>\n<td bgcolor=\\\"$altbg2\\\" align=\\\"center\\\">12</td>\n<td bgcolor=\\\"$altbg1\\\" align=\\\"center\\\"><font class=\\\"smalltxt\\\">$lastpost</font></td>\n</tr>');
INSERT INTO cdb_templates VALUES('155','cdb_version','0','1.0');

DROP TABLE IF EXISTS cdb_themes;
CREATE TABLE cdb_themes (
	themeid smallint(6) unsigned NOT NULL auto_increment,
	themename varchar(30) NOT NULL,
	bgcolor varchar(25) NOT NULL,
	altbg1 varchar(15) NOT NULL,
	altbg2 varchar(15) NOT NULL,
	link varchar(15) NOT NULL,
	bordercolor varchar(15) NOT NULL,
	headercolor varchar(15) NOT NULL,
	headertext varchar(15) NOT NULL,
	catcolor varchar(15) NOT NULL,
	tabletext varchar(15) NOT NULL,
	text varchar(15) NOT NULL,
	borderwidth varchar(15) NOT NULL,
	tablewidth varchar(15) NOT NULL,
	tablespace varchar(15) NOT NULL,
	font varchar(40) NOT NULL,
	fontsize varchar(40) NOT NULL,
	nobold tinyint(1) NOT NULL,
	boardimg varchar(50) NOT NULL,
	imgdir varchar(120) NOT NULL,
	smdir varchar(120) NOT NULL,
	cattext varchar(15) NOT NULL,
	PRIMARY KEY (themeid),
	KEY themename (themename)
);

INSERT INTO cdb_themes VALUES('1','标准界面','#FFFFFF','#E3E3EA','#EEEEF6','#3A4273','#000000','header_bg.gif','#F1F3FB','cat_bg.gif','#464F86','#464F86','1','99%','3','Tahoma, Verdana','12px','0','toplogo.jpg','images/standard','images/smilies','#D9D9E9');

DROP TABLE IF EXISTS cdb_threads;
CREATE TABLE cdb_threads (
	tid mediumint(8) unsigned NOT NULL auto_increment,
	fid smallint(6) NOT NULL,
	creditsrequire smallint(6) unsigned NOT NULL,
	icon varchar(30) NOT NULL,
	author varchar(25) NOT NULL,
	subject varchar(100) NOT NULL,
	dateline int(10) unsigned NOT NULL,
	lastpost int(10) unsigned NOT NULL,
	lastposter varchar(25) NOT NULL,
	views smallint(6) unsigned NOT NULL,
	replies smallint(6) unsigned NOT NULL,
	topped tinyint(1) NOT NULL,
	digist tinyint(1) NOT NULL,
	closed varchar(15) NOT NULL,
	pollopts text NOT NULL,
	attachment varchar(50) NOT NULL,
	PRIMARY KEY (tid),
	KEY lastpost (topped,lastpost,fid)
);

INSERT INTO cdb_threads VALUES('1','3','0','','admin','尿性','1478166458','1478166458','Crossday','2','0','0','0','','','');
INSERT INTO cdb_threads VALUES('2','3','0','','admin','nihao','1478309702','1478309702','admin','0','0','0','0','','','');
INSERT INTO cdb_threads VALUES('3','3','0','','zyh','我来了','1478310205','1478310205','zyh','1','0','0','0','','','');
INSERT INTO cdb_threads VALUES('4','3','0','icon1.gif','admin','i am java','1478335199','1478335199','admin','0','0','0','0','','','');
INSERT INTO cdb_threads VALUES('5','8','0','','admin','javav','1478335268','1479802777','admin','4','1','0','0','','','');
INSERT INTO cdb_threads VALUES('6','3','0','','zyh','测试','1478336209','1478336244','zyh','5','1','0','0','','','');
INSERT INTO cdb_threads VALUES('7','3','0','','admin','adf','1478339982','1478339982','admin','1','0','0','0','','','');
INSERT INTO cdb_threads VALUES('8','3','0','','admin','dsdfsdf','1478340066','1478340066','admin','19','0','0','0','1','','');
INSERT INTO cdb_threads VALUES('9','3','0','icon2.gif','admin','wreqwerqwe','1478483105','1478483166','admin','3','1','0','0','','1\r\n||~|~|| 0#|#2\r\n||~|~|| 0#|#3\r\n||~|~|| 0#|#4\r\n||~|~|| 0#|#5\r\n||~|~|| 1#|#6\r\n||~|~|| 0#|#7||~|~|| 0#|# admin','');
INSERT INTO cdb_threads VALUES('10','3','5','','admin','code','1478483459','1478484725','admin','3','1','0','0','','','');
INSERT INTO cdb_threads VALUES('11','3','0','','hua','smile','1478485088','1478485088','hua','2','0','0','0','','','');
INSERT INTO cdb_threads VALUES('12','3','0','icon8.gif','hua','hide','1478485153','1478485221','zeng','5','1','0','0','','','');
INSERT INTO cdb_threads VALUES('13','3','0','','hua','test 冇柄士巴拉得棚牙','1478485726','1478485780','hua','2','1','0','0','','','');
INSERT INTO cdb_threads VALUES('14','3','0','','hua','测试签名','1478486594','1478486625','hua','7','1','0','0','','','');
INSERT INTO cdb_threads VALUES('15','3','0','','zeng','zeng','1478486806','1479802740','admin','24','2','0','0','','','cer	application/x-x509-ca-cert');
INSERT INTO cdb_threads VALUES('16','3','0','','admin','admin','1478489612','1479802707','admin','6','1','0','0','','','');
INSERT INTO cdb_threads VALUES('17','5','0','','admin','附件','1479776126','1479776126','admin','3','0','0','0','','','');
INSERT INTO cdb_threads VALUES('18','5','2','','admin','下载 ZIP文件','1479798443','1479798559','admin','3','1','0','0','','','');

DROP TABLE IF EXISTS cdb_u2u;
CREATE TABLE cdb_u2u (
	u2uid int(10) unsigned NOT NULL auto_increment,
	msgto varchar(25) NOT NULL,
	msgfrom varchar(25) NOT NULL,
	folder varchar(10) NOT NULL,
	new tinyint(1) NOT NULL,
	subject varchar(75) NOT NULL,
	dateline int(10) unsigned NOT NULL,
	message text NOT NULL,
	PRIMARY KEY (u2uid),
	KEY msgto (msgto)
);

INSERT INTO cdb_u2u VALUES('1','zyh','zyh','inbox','2','西大坨村','1478310249','术有专攻');
INSERT INTO cdb_u2u VALUES('2','zyh','zyh','outbox','1','西大坨村','1478310249','术有专攻');
INSERT INTO cdb_u2u VALUES('5','admin','zyh','1478336228','0','有用户向您反映下面这个贴子，请查看：http://sites/Discuz!-','0','1');
INSERT INTO cdb_u2u VALUES('6','hua','hua','inbox','2','547996854@qq.com','1478485361','dfdf547996854@qq.com');
INSERT INTO cdb_u2u VALUES('8','zyh','hua','inbox','1','nihao','1478486128',' 西大坨村');
INSERT INTO cdb_u2u VALUES('9','zeng','admin','inbox','2','fadf','1478489418','asdfa');
INSERT INTO cdb_u2u VALUES('10','zeng','admin','outbox','1','fadf','1478489418','asdfa');
INSERT INTO cdb_u2u VALUES('11','hua','admin','1478489457','0','有用户向您反映下面这个贴子，请查看：http://sites/Discuz!-','0','1');
INSERT INTO cdb_u2u VALUES('12','admin','admin','1478489457','0','有用户向您反映下面这个贴子，请查看：http://sites/Discuz!-','0','1');

DROP TABLE IF EXISTS cdb_usergroups;
CREATE TABLE cdb_usergroups (
	groupid smallint(6) unsigned NOT NULL auto_increment,
	specifiedusers text NOT NULL,
	status varchar(20) NOT NULL,
	grouptitle varchar(30) NOT NULL,
	creditshigher int(10) NOT NULL,
	creditslower int(10) NOT NULL,
	stars tinyint(3) NOT NULL,
	groupavatar varchar(60) NOT NULL,
	allowcstatus tinyint(1) NOT NULL,
	allowavatar tinyint(1) NOT NULL,
	allowvisit tinyint(1) NOT NULL,
	allowview tinyint(1) NOT NULL,
	allowpost tinyint(1) NOT NULL,
	allowpostpoll tinyint(1) NOT NULL,
	allowgetattach tinyint(1) NOT NULL,
	allowpostattach tinyint(1) NOT NULL,
	allowvote tinyint(1) NOT NULL,
	allowsearch tinyint(1) NOT NULL,
	allowkarma tinyint(1) NOT NULL,
	allowsetviewperm tinyint(1) NOT NULL,
	allowsetattachperm tinyint(1) NOT NULL,
	allowsigbbcode tinyint(1) NOT NULL,
	allowsigimgcode tinyint(1) NOT NULL,
	allowviewstats tinyint(1) NOT NULL,
	ismoderator tinyint(1) NOT NULL,
	issupermod tinyint(1) NOT NULL,
	isadmin tinyint(1) NOT NULL,
	maxu2unum smallint(6) unsigned NOT NULL,
	maxmemonum smallint(6) unsigned NOT NULL,
	maxsigsize smallint(6) unsigned NOT NULL,
	maxkarmavote tinyint(3) unsigned NOT NULL,
	maxattachsize mediumint(8) unsigned NOT NULL,
	attachextensions tinytext NOT NULL,
	PRIMARY KEY (groupid),
	KEY status (status),
	KEY creditshigher (creditshigher),
	KEY creditslower (creditslower)
);

INSERT INTO cdb_usergroups VALUES('1','','论坛管理员','论坛管理员','0','0','9','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','100','100','500','16','2048000','');
INSERT INTO cdb_usergroups VALUES('2','','超级版主','超级版主','0','0','8','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','90','60','300','12','2048000','');
INSERT INTO cdb_usergroups VALUES('3','','版主','版主','0','0','7','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','0','80','40','200','10','2048000','');
INSERT INTO cdb_usergroups VALUES('13','','等待验证','等待验证会员','0','0','0','','0','0','1','1','0','0','0','0','0','0','0','0','0','1','0','0','0','0','0','10','0','50','0','0','');
INSERT INTO cdb_usergroups VALUES('14','','游客','游客','0','0','0','','0','0','1','1','0','0','0','0','0','1','0','0','0','0','0','1','0','0','0','0','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('15','','禁止访问','用户被禁止访问','0','0','0','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','10','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('16','','禁止IP','用户IP被禁止','0','0','0','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('17','','禁止发言','用户被禁止发言','0','0','0','','0','0','1','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','10','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('27','','正式会员','社区乞丐','-9999999','0','0','','0','0','1','1','1','0','0','0','0','0','0','0','0','1','0','0','0','0','0','10','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('28','','正式会员','新手上路','0','10','1','','0','0','1','1','1','0','0','0','0','1','0','0','0','1','0','1','0','0','0','30','3','50','0','0','');
INSERT INTO cdb_usergroups VALUES('29','','正式会员','初级会员','10','50','2','','0','1','1','1','1','1','1','1','1','1','0','0','0','1','0','1','0','0','0','40','5','50','0','128000','gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('30','','正式会员','高级会员','50','150','3','','0','2','1','1','1','1','1','1','1','1','1','0','0','1','0','1','0','0','0','50','10','100','2','256000','gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('31','','正式会员','支柱会员','150','300','4','','1','2','1','1','1','1','1','1','1','1','1','0','0','1','0','1','0','0','0','50','15','100','3','512000','zip,rar,chm,txt,gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('32','','正式会员','青铜长老','300','600','5','','1','2','1','1','1','1','1','1','1','1','1','0','1','1','0','1','0','0','0','50','20','100','4','1024000','');
INSERT INTO cdb_usergroups VALUES('33','','正式会员','黄金长老','600','1000','6','','1','2','1','1','1','1','1','1','1','1','1','0','1','1','0','1','0','0','0','50','25','100','5','1024000','');
INSERT INTO cdb_usergroups VALUES('34','','正式会员','白金长老','1000','3000','7','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','0','0','50','30','100','6','2048000','');
INSERT INTO cdb_usergroups VALUES('35','','正式会员','本站元老','3000','9999999','8','','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','0','0','50','40','100','8','2048000','');
INSERT INTO cdb_usergroups VALUES('36','	zyh	zeng	','正式会员','发布员','0','0','0','','1','2','1','1','1','1','0','0','1','0','0','1','0','1','1','0','0','0','0','0','0','0','0','0','');

DROP TABLE IF EXISTS cdb_words;
CREATE TABLE cdb_words (
	id smallint(6) unsigned NOT NULL auto_increment,
	find varchar(60) NOT NULL,
	replacement varchar(60) NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO cdb_words VALUES('1','测试','程序员');

