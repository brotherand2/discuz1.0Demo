# Identify: MTQ3ODMzNTY3NSwxLjAsc3RhbmRhcmQsLA==
#
# Discuz! 数据备份(Discuz! Data Dump)
# 版本: Discuz! 1.0
# 备份时间: 2016-11-5 24
# 备份方式: 标准备份
# 数据库前缀: cdb_
#
# 官方网站: http://www.Discuz.net
# 请随时访问以上地址以获得最新的软件升级信息
# --------------------------------------------------------


DROP TABLE IF EXISTS cdb_announcements;
CREATE TABLE cdb_announcements (
	id smallint(6) unsigned NOT NULL auto_increment,
	author varchar(25) NOT NULL,
	subject varchar(250) NOT NULL,
	starttime int(10) unsigned NOT NULL,
	endtime int(10) unsigned NOT NULL,
	message text NOT NULL,
	PRIMARY KEY (id)
);


DROP TABLE IF EXISTS cdb_attachments;
CREATE TABLE cdb_attachments (
	aid mediumint(8) unsigned NOT NULL auto_increment,
	tid mediumint(8) unsigned NOT NULL,
	pid int(10) unsigned NOT NULL,
	creditsrequire smallint(6) unsigned NOT NULL,
	filename varchar(255) NOT NULL,
	filetype varchar(50) NOT NULL,
	filesize int(12) unsigned NOT NULL,
	attachment varchar(255) NOT NULL,
	downloads smallint(6) NOT NULL,
	PRIMARY KEY (aid)
);


DROP TABLE IF EXISTS cdb_banned;
CREATE TABLE cdb_banned (
	id smallint(6) unsigned NOT NULL auto_increment,
	ip1 smallint(3) NOT NULL,
	ip2 smallint(3) NOT NULL,
	ip3 smallint(3) NOT NULL,
	ip4 smallint(3) NOT NULL,
	admin varchar(25) NOT NULL,
	dateline int(10) unsigned NOT NULL,
	PRIMARY KEY (id),
	KEY ip1 (ip1),
	KEY ip2 (ip2),
	KEY ip3 (ip3),
	KEY ip4 (ip1)
);


DROP TABLE IF EXISTS cdb_buddys;
CREATE TABLE cdb_buddys (
	username varchar(25) NOT NULL,
	buddyname varchar(25) NOT NULL
);

INSERT INTO cdb_buddys VALUES('zyh','zyh');

DROP TABLE IF EXISTS cdb_caches;
CREATE TABLE cdb_caches (
	cachename varchar(20) NOT NULL,
	cachevars text NOT NULL,
	KEY cachename (cachename)
);

INSERT INTO cdb_caches VALUES('settings','a:43:{s:6:\"bbname\";s:6:\"勇华\";s:9:\"regstatus\";s:1:\"1\";s:8:\"bbclosed\";s:1:\"0\";s:12:\"closedreason\";s:0:\"\";s:8:\"sitename\";s:10:\"hua 论坛\";s:7:\"siteurl\";s:20:\"http://www.yxee.com/\";s:5:\"theme\";s:12:\"标准界面\";s:11:\"credittitle\";s:6:\"天使\";s:10:\"creditunit\";s:3:\"币\";s:10:\"moddisplay\";s:4:\"flat\";s:9:\"floodctrl\";s:2:\"15\";s:9:\"karmactrl\";s:3:\"300\";s:8:\"hottopic\";s:2:\"10\";s:12:\"topicperpage\";s:2:\"20\";s:11:\"postperpage\";s:2:\"10\";s:13:\"memberperpage\";s:2:\"25\";s:11:\"maxpostsize\";s:5:\"10000\";s:13:\"maxavatarsize\";s:1:\"0\";s:6:\"smcols\";s:1:\"3\";s:16:\"whosonlinestatus\";s:1:\"1\";s:14:\"vtonlinestatus\";s:1:\"1\";s:6:\"chcode\";s:1:\"0\";s:12:\"gzipcompress\";s:1:\"1\";s:11:\"postcredits\";s:1:\"1\";s:13:\"digistcredits\";s:2:\"10\";s:11:\"hideprivate\";s:1:\"1\";s:10:\"emailcheck\";s:1:\"0\";s:8:\"fastpost\";s:1:\"1\";s:13:\"memliststatus\";s:1:\"1\";s:10:\"statstatus\";s:1:\"0\";s:5:\"debug\";s:1:\"1\";s:10:\"reportpost\";s:1:\"1\";s:8:\"bbinsert\";s:1:\"1\";s:12:\"smileyinsert\";s:1:\"1\";s:8:\"editedby\";s:1:\"1\";s:10:\"dotfolders\";s:1:\"0\";s:13:\"attachimgpost\";s:1:\"1\";s:10:\"timeformat\";s:3:\"H:i\";s:10:\"dateformat\";s:5:\"Y-n-j\";s:10:\"timeoffset\";s:1:\"8\";s:7:\"version\";s:3:\"1.0\";s:12:\"onlinerecord\";s:12:\"3	1478310166\";s:10:\"lastmember\";s:3:\"zyh\";}');
INSERT INTO cdb_caches VALUES('usergroups','a:17:{i:0;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:10:\"论坛管理员\";s:10:\"grouptitle\";s:10:\"论坛管理员\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"9\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:1;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"超级版主\";s:10:\"grouptitle\";s:8:\"超级版主\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"8\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:2;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:4:\"版主\";s:10:\"grouptitle\";s:4:\"版主\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"7\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:3;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"社区乞丐\";s:13:\"creditshigher\";s:8:\"-9999999\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:4;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"等待验证\";s:10:\"grouptitle\";s:12:\"等待验证会员\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:5;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:4:\"游客\";s:10:\"grouptitle\";s:4:\"游客\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:6;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"禁止访问\";s:10:\"grouptitle\";s:14:\"用户被禁止访问\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:7;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:6:\"禁止IP\";s:10:\"grouptitle\";s:12:\"用户IP被禁止\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:8;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"禁止发言\";s:10:\"grouptitle\";s:14:\"用户被禁止发言\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:9;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"新手上路\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:2:\"10\";s:5:\"stars\";s:1:\"1\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:10;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"初级会员\";s:13:\"creditshigher\";s:2:\"10\";s:12:\"creditslower\";s:2:\"50\";s:5:\"stars\";s:1:\"2\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:11;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"高级会员\";s:13:\"creditshigher\";s:2:\"50\";s:12:\"creditslower\";s:3:\"150\";s:5:\"stars\";s:1:\"3\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:12;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"支柱会员\";s:13:\"creditshigher\";s:3:\"150\";s:12:\"creditslower\";s:3:\"300\";s:5:\"stars\";s:1:\"4\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:13;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"青铜长老\";s:13:\"creditshigher\";s:3:\"300\";s:12:\"creditslower\";s:3:\"600\";s:5:\"stars\";s:1:\"5\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:14;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"黄金长老\";s:13:\"creditshigher\";s:3:\"600\";s:12:\"creditslower\";s:4:\"1000\";s:5:\"stars\";s:1:\"6\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:15;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"白金长老\";s:13:\"creditshigher\";s:4:\"1000\";s:12:\"creditslower\";s:4:\"3000\";s:5:\"stars\";s:1:\"7\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:16;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"本站元老\";s:13:\"creditshigher\";s:4:\"3000\";s:12:\"creditslower\";s:7:\"9999999\";s:5:\"stars\";s:1:\"8\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}}');
INSERT INTO cdb_caches VALUES('announcements','a:0:{}');
INSERT INTO cdb_caches VALUES('forums','a:6:{i:2;a:4:{s:4:\"type\";s:5:\"group\";s:4:\"name\";s:12:\"编程分类\";s:3:\"fup\";s:1:\"0\";s:8:\"viewperm\";s:0:\"\";}i:3;a:4:{s:4:\"type\";s:5:\"forum\";s:4:\"name\";s:3:\"C++\";s:3:\"fup\";s:1:\"2\";s:8:\"viewperm\";s:0:\"\";}i:4;a:4:{s:4:\"type\";s:5:\"group\";s:4:\"name\";s:6:\"音乐\";s:3:\"fup\";s:1:\"0\";s:8:\"viewperm\";s:0:\"\";}i:5;a:4:{s:4:\"type\";s:5:\"forum\";s:4:\"name\";s:12:\"古典音乐\";s:3:\"fup\";s:1:\"4\";s:8:\"viewperm\";s:0:\"\";}i:6;a:4:{s:4:\"type\";s:3:\"sub\";s:4:\"name\";s:20:\"19世纪日本古典\";s:3:\"fup\";s:1:\"5\";s:8:\"viewperm\";s:0:\"\";}i:8;a:4:{s:4:\"type\";s:5:\"forum\";s:4:\"name\";s:4:\"java\";s:3:\"fup\";s:1:\"2\";s:8:\"viewperm\";s:0:\"\";}}');
INSERT INTO cdb_caches VALUES('forumlinks','a:1:{i:0;a:6:{s:2:\"id\";s:1:\"2\";s:12:\"displayorder\";s:1:\"0\";s:4:\"name\";s:13:\"Discuz! Board\";s:3:\"url\";s:21:\"http://www.Discuz.net\";s:4:\"note\";s:91:\"本站论坛程序 Discuz! 的官方站点，专门讨论 Discuz! 的使用与 Hack，提供论坛升级与技术支持等。\";s:4:\"logo\";s:15:\"images/logo.gif\";}}');
INSERT INTO cdb_caches VALUES('smilies','a:9:{i:0;a:4:{s:2:\"id\";s:2:\"19\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":)\";s:3:\"url\";s:9:\"smile.gif\";}i:1;a:4:{s:2:\"id\";s:2:\"20\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":(\";s:3:\"url\";s:7:\"sad.gif\";}i:2;a:4:{s:2:\"id\";s:2:\"21\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":D\";s:3:\"url\";s:11:\"biggrin.gif\";}i:3;a:4:{s:2:\"id\";s:2:\"22\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\";)\";s:3:\"url\";s:8:\"wink.gif\";}i:4;a:4:{s:2:\"id\";s:2:\"23\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:6:\":cool:\";s:3:\"url\";s:8:\"cool.gif\";}i:5;a:4:{s:2:\"id\";s:2:\"24\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:5:\":mad:\";s:3:\"url\";s:7:\"mad.gif\";}i:6;a:4:{s:2:\"id\";s:2:\"25\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":o\";s:3:\"url\";s:11:\"shocked.gif\";}i:7;a:4:{s:2:\"id\";s:2:\"26\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":P\";s:3:\"url\";s:10:\"tongue.gif\";}i:8;a:4:{s:2:\"id\";s:2:\"27\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:5:\":lol:\";s:3:\"url\";s:7:\"lol.gif\";}}');
INSERT INTO cdb_caches VALUES('picons','a:9:{i:0;a:4:{s:2:\"id\";s:2:\"28\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon1.gif\";}i:1;a:4:{s:2:\"id\";s:2:\"29\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon2.gif\";}i:2;a:4:{s:2:\"id\";s:2:\"30\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon3.gif\";}i:3;a:4:{s:2:\"id\";s:2:\"31\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon4.gif\";}i:4;a:4:{s:2:\"id\";s:2:\"32\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon5.gif\";}i:5;a:4:{s:2:\"id\";s:2:\"33\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon6.gif\";}i:6;a:4:{s:2:\"id\";s:2:\"34\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon7.gif\";}i:7;a:4:{s:2:\"id\";s:2:\"35\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon8.gif\";}i:8;a:4:{s:2:\"id\";s:2:\"36\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon9.gif\";}}');
INSERT INTO cdb_caches VALUES('censor','a:2:{s:7:\"replace\";a:0:{}s:4:\"find\";a:0:{}}');
INSERT INTO cdb_caches VALUES('news','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:7:\"subject\";s:59:\"有疑问请光临 Discuz! 技术支持论坛，我们会尽快解决您的问题。\";s:4:\"link\";s:22:\"http://www.Discuz!.net\";}i:1;a:3:{s:2:\"id\";s:1:\"2\";s:7:\"subject\";s:44:\"我们会不断完善程序，也希望得到您的继续支持。\";s:4:\"link\";s:25:\"http://forum.crossday.com\";}i:2;a:3:{s:2:\"id\";s:1:\"3\";s:7:\"subject\";s:44:\"Crossday Studio 向您问好，欢迎您选择 Discuz!\";s:4:\"link\";s:0:\"\";}i:3;a:3:{s:2:\"id\";s:1:\"4\";s:7:\"subject\";s:32:\"欢迎我们的新会员 {$lastmember}！\";s:4:\"link\";s:46:\"member.php?action=viewpro&username=$encodemember\";}}');

DROP TABLE IF EXISTS cdb_favorites;
CREATE TABLE cdb_favorites (
	tid mediumint(8) unsigned NOT NULL,
	username varchar(25) NOT NULL,
	KEY tid (tid)
);


DROP TABLE IF EXISTS cdb_forumlinks;
CREATE TABLE cdb_forumlinks (
	id smallint(6) unsigned NOT NULL auto_increment,
	displayorder tinyint(3) NOT NULL,
	name varchar(100) NOT NULL,
	url varchar(100) NOT NULL,
	note varchar(200) NOT NULL,
	logo varchar(100) NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO cdb_forumlinks VALUES('1','0','Discuz! Board','http://www.Discuz.net','本站论坛程序 Discuz! 的官方站点，专门讨论 Discuz! 的使用与 Hack，提供论坛升级与技术支持等。','images/logo.gif');

DROP TABLE IF EXISTS cdb_forums;
CREATE TABLE cdb_forums (
	fid smallint(6) unsigned NOT NULL auto_increment,
	fup smallint(6) unsigned NOT NULL,
	type enum('group','forum','sub') NOT NULL default 'forum',
	icon varchar(100) NOT NULL,
	name varchar(50) NOT NULL,
	description text NOT NULL,
	status tinyint(1) NOT NULL,
	displayorder tinyint(3) NOT NULL,
	moderator tinytext NOT NULL,
	threads smallint(6) unsigned NOT NULL,
	posts mediumint(8) unsigned NOT NULL,
	lastpost varchar(130) NOT NULL,
	allowsmilies tinyint(1) NOT NULL,
	allowhtml tinyint(1) NOT NULL,
	allowbbcode tinyint(1) NOT NULL,
	allowimgcode tinyint(1) NOT NULL,
	password varchar(12) NOT NULL,
	postcredits tinyint(1) NOT NULL default '-1',
	viewperm tinytext NOT NULL,
	postperm tinytext NOT NULL,
	getattachperm tinytext NOT NULL,
	postattachperm tinytext NOT NULL,
	PRIMARY KEY (fid),
	KEY status (status)
);

INSERT INTO cdb_forums VALUES('2','0','group','','编程分类','','1','0','','0','0','','0','0','0','0','','-1','','','','');
INSERT INTO cdb_forums VALUES('3','2','forum','','C++','','1','0','','4','4','','1','1','1','1','','-1','','','','');
INSERT INTO cdb_forums VALUES('4','0','group','','音乐','','1','0','','0','0','','0','0','0','0','','-1','','','','');
INSERT INTO cdb_forums VALUES('5','4','forum','images/standard/zeng.jpg','古典音乐','打造一个良好的音乐交流平台,HELLO,我来了','1','0','','0','0','','0','0','0','0','','-1','','','','');
INSERT INTO cdb_forums VALUES('6','5','sub','','19世纪日本古典','','1','0','','0','0','','0','0','0','0','','-1','','','','');
INSERT INTO cdb_forums VALUES('8','2','forum','','java','','1','1','','1','1','javav	1478335268	admin','0','0','0','0','','-1','','','','');

DROP TABLE IF EXISTS cdb_members;
CREATE TABLE cdb_members (
	uid mediumint(8) unsigned NOT NULL auto_increment,
	username varchar(25) NOT NULL,
	password varchar(40) NOT NULL,
	gender tinyint(1) NOT NULL,
	status varchar(20) NOT NULL,
	regip varchar(20) NOT NULL,
	regdate int(10) unsigned NOT NULL,
	lastvisit int(10) unsigned NOT NULL,
	postnum smallint(6) unsigned NOT NULL,
	credit smallint(6) NOT NULL,
	charset varchar(10) NOT NULL,
	email varchar(60) NOT NULL,
	site varchar(75) NOT NULL,
	icq varchar(12) NOT NULL,
	oicq varchar(12) NOT NULL,
	yahoo varchar(40) NOT NULL,
	msn varchar(40) NOT NULL,
	location varchar(30) NOT NULL,
	bday date NOT NULL default '0000-00-00',
	bio text NOT NULL,
	avatar varchar(100) NOT NULL,
	signature text NOT NULL,
	customstatus varchar(20) NOT NULL,
	tpp tinyint(3) unsigned NOT NULL,
	ppp tinyint(3) unsigned NOT NULL,
	theme varchar(30) NOT NULL,
	dateformat varchar(10) NOT NULL,
	timeformat varchar(5) NOT NULL,
	showemail tinyint(1) NOT NULL,
	newsletter tinyint(1) NOT NULL,
	timeoffset char(3) NOT NULL,
	ignoreu2u text NOT NULL,
	newu2u tinyint(1) NOT NULL,
	pwdrecover varchar(30) NOT NULL,
	pwdrcvtime int(10) unsigned NOT NULL,
	PRIMARY KEY (uid),
	KEY username (username)
);

INSERT INTO cdb_members VALUES('1','admin','e10adc3949ba59abbe56e057f20f883e','0','论坛管理员','127.0.0.1','1478102400','1478335268','5','2','gb2312','name@domain.com','www.baidu.com','','','','','','0000-00-00','sfsdfsdfsdf','','你好,世界','将军','20','10','标准界面','Y-n-j','24','1','1','8','','0','','0');
INSERT INTO cdb_members VALUES('2','zyh','e10adc3949ba59abbe56e057f20f883e','1','正式会员','127.0.0.1','1478310164','1478334108','1','1','utf8','547996854@qq.com','www.baidu.com','asdfad','sfsd','adfa','adsfas','a','1991-11-25','wo 是啥样嘿','','我最牛','','10','15','标准界面','yy/n/j','H:i','1','1','','','0','','0');

DROP TABLE IF EXISTS cdb_memo;
CREATE TABLE cdb_memo (
	id int(10) unsigned NOT NULL auto_increment,
	username varchar(25) NOT NULL,
	type enum('address','notebook','collections') NOT NULL default 'address',
	dateline int(10) unsigned NOT NULL,
	var1 varchar(50) NOT NULL,
	var2 varchar(100) NOT NULL,
	var3 tinytext NOT NULL,
	PRIMARY KEY (id),
	KEY username (username),
	KEY type (type)
);


DROP TABLE IF EXISTS cdb_news;
CREATE TABLE cdb_news (
	id smallint(6) unsigned NOT NULL auto_increment,
	subject varchar(100) NOT NULL,
	link varchar(100) NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO cdb_news VALUES('1','有疑问请光临 Discuz! 技术支持论坛，我们会尽快解决您的问题。','http://www.Discuz.net');
INSERT INTO cdb_news VALUES('2','我们会不断完善程序，也希望得到您的继续支持。','http://www.Discuz.net');
INSERT INTO cdb_news VALUES('3','Crossday Studio 向您问好，欢迎您选择 Discuz!','');
INSERT INTO cdb_news VALUES('4','欢迎我们的新会员 {$lastmember}！','member.php?action=viewpro&username=$encodemember');

DROP TABLE IF EXISTS cdb_posts;
CREATE TABLE cdb_posts (
	fid smallint(6) unsigned NOT NULL,
	tid mediumint(8) unsigned NOT NULL,
	pid int(10) unsigned NOT NULL auto_increment,
	aid mediumint(8) unsigned NOT NULL,
	icon varchar(30) NOT NULL,
	author varchar(25) NOT NULL,
	subject varchar(100) NOT NULL,
	dateline int(10) unsigned NOT NULL,
	message text NOT NULL,
	useip varchar(20) NOT NULL,
	usesig tinyint(1) NOT NULL,
	bbcodeoff tinyint(1) NOT NULL,
	smileyoff tinyint(1) NOT NULL,
	parseurloff tinyint(1) NOT NULL,
	PRIMARY KEY (pid),
	KEY fid (fid),
	KEY tid (tid,dateline)
);

INSERT INTO cdb_posts VALUES('3','1','1','0','','admin','尿性','1478166458','nihao测试\r\n\r\n\r\n术有专攻可','127.0.0.1','1','0','0','0');
INSERT INTO cdb_posts VALUES('3','2','2','0','','admin','nihao','1478309702','sdfadf\r\nasdfasd','127.0.0.1','1','0','0','0');
INSERT INTO cdb_posts VALUES('3','3','3','0','','zyh','我来了','1478310205','测试','127.0.0.1','1','0','0','0');
INSERT INTO cdb_posts VALUES('3','4','4','0','icon1.gif','admin','i am java','1478335199','adfasdfadf:cool::cool::cool:','127.0.0.1','1','0','0','0');
INSERT INTO cdb_posts VALUES('8','5','5','0','','admin','javav','1478335268','adfasdf','127.0.0.1','1','0','0','0');

DROP TABLE IF EXISTS cdb_settings;
CREATE TABLE cdb_settings (
	bbname varchar(50) NOT NULL,
	regstatus tinyint(1) NOT NULL,
	censoruser text NOT NULL,
	doublee tinyint(1) NOT NULL,
	emailcheck tinyint(1) NOT NULL,
	bbrules tinyint(1) NOT NULL,
	bbrulestxt text NOT NULL,
	welcommsg tinyint(1) NOT NULL,
	welcommsgtxt text NOT NULL,
	bbclosed tinyint(1) NOT NULL,
	closedreason text NOT NULL,
	sitename varchar(50) NOT NULL,
	siteurl varchar(60) NOT NULL,
	theme varchar(30) NOT NULL,
	credittitle varchar(20) NOT NULL,
	creditunit varchar(10) NOT NULL,
	moddisplay enum('flat','selectbox') NOT NULL default 'flat',
	floodctrl smallint(6) unsigned NOT NULL,
	karmactrl smallint(6) unsigned NOT NULL,
	hottopic tinyint(3) unsigned NOT NULL,
	topicperpage tinyint(3) unsigned NOT NULL,
	postperpage tinyint(3) unsigned NOT NULL,
	memberperpage tinyint(3) unsigned NOT NULL,
	maxpostsize smallint(6) unsigned NOT NULL,
	maxavatarsize tinyint(3) unsigned NOT NULL,
	smcols tinyint(3) unsigned NOT NULL,
	postcredits tinyint(3) NOT NULL,
	digistcredits tinyint(3) NOT NULL,
	whosonlinestatus tinyint(1) NOT NULL,
	vtonlinestatus tinyint(1) NOT NULL,
	chcode tinyint(1) NOT NULL,
	gzipcompress tinyint(1) NOT NULL,
	hideprivate tinyint(1) NOT NULL,
	fastpost tinyint(1) NOT NULL,
	memliststatus tinyint(1) NOT NULL,
	statstatus tinyint(1) NOT NULL,
	debug tinyint(1) NOT NULL,
	reportpost tinyint(1) NOT NULL,
	bbinsert tinyint(1) NOT NULL,
	smileyinsert tinyint(1) NOT NULL,
	editedby tinyint(1) NOT NULL,
	dotfolders tinyint(1) NOT NULL,
	attachimgpost tinyint(1) NOT NULL,
	timeformat varchar(5) NOT NULL,
	dateformat varchar(10) NOT NULL,
	timeoffset char(3) NOT NULL,
	version varchar(30) NOT NULL,
	onlinerecord varchar(30) NOT NULL,
	lastmember varchar(25) NOT NULL
);

INSERT INTO cdb_settings VALUES('勇华','1','','1','0','0','','0','','0','','hua 论坛','http://www.yxee.com/','标准界面','天使','币','flat','15','300','10','20','10','25','10000','0','3','1','10','1','1','0','1','1','1','1','0','1','1','1','1','1','0','1','H:i','Y-n-j','8','1.0','3	1478310166','zyh');

DROP TABLE IF EXISTS cdb_smilies;
CREATE TABLE cdb_smilies (
	id smallint(6) unsigned NOT NULL auto_increment,
	type enum('smiley','picon') NOT NULL default 'smiley',
	code varchar(10) NOT NULL,
	url varchar(30) NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO cdb_smilies VALUES('1','smiley',':)','smile.gif');
INSERT INTO cdb_smilies VALUES('2','smiley',':(','sad.gif');
INSERT INTO cdb_smilies VALUES('3','smiley',':D','biggrin.gif');
INSERT INTO cdb_smilies VALUES('4','smiley',';)','wink.gif');
INSERT INTO cdb_smilies VALUES('5','smiley',':cool:','cool.gif');
INSERT INTO cdb_smilies VALUES('6','smiley',':mad:','mad.gif');
INSERT INTO cdb_smilies VALUES('7','smiley',':o','shocked.gif');
INSERT INTO cdb_smilies VALUES('8','smiley',':P','tongue.gif');
INSERT INTO cdb_smilies VALUES('9','smiley',':lol:','lol.gif');
INSERT INTO cdb_smilies VALUES('10','picon','','icon1.gif');
INSERT INTO cdb_smilies VALUES('11','picon','','icon2.gif');
INSERT INTO cdb_smilies VALUES('12','picon','','icon3.gif');
INSERT INTO cdb_smilies VALUES('13','picon','','icon4.gif');
INSERT INTO cdb_smilies VALUES('14','picon','','icon5.gif');
INSERT INTO cdb_smilies VALUES('15','picon','','icon6.gif');
INSERT INTO cdb_smilies VALUES('16','picon','','icon7.gif');
INSERT INTO cdb_smilies VALUES('17','picon','','icon8.gif');
INSERT INTO cdb_smilies VALUES('18','picon','','icon9.gif');

DROP TABLE IF EXISTS cdb_stats;
CREATE TABLE cdb_stats (
	type varchar(20) NOT NULL,
	var varchar(20) NOT NULL,
	count int(10) unsigned NOT NULL,
	KEY type (type),
	KEY var (var)
);

INSERT INTO cdb_stats VALUES('total','hits','0');
INSERT INTO cdb_stats VALUES('total','members','0');
INSERT INTO cdb_stats VALUES('total','guests','0');
INSERT INTO cdb_stats VALUES('os','Windows','0');
INSERT INTO cdb_stats VALUES('os','Mac','0');
INSERT INTO cdb_stats VALUES('os','Linux','0');
INSERT INTO cdb_stats VALUES('os','FreeBSD','0');
INSERT INTO cdb_stats VALUES('os','SunOS','0');
INSERT INTO cdb_stats VALUES('os','BeOS','0');
INSERT INTO cdb_stats VALUES('os','OS/2','0');
INSERT INTO cdb_stats VALUES('os','AIX','0');
INSERT INTO cdb_stats VALUES('os','Other','0');
INSERT INTO cdb_stats VALUES('browser','MSIE','0');
INSERT INTO cdb_stats VALUES('browser','Netscape','0');
INSERT INTO cdb_stats VALUES('browser','Mozilla','0');
INSERT INTO cdb_stats VALUES('browser','Lynx','0');
INSERT INTO cdb_stats VALUES('browser','Opera','0');
INSERT INTO cdb_stats VALUES('browser','Konqueror','0');
INSERT INTO cdb_stats VALUES('browser','Other','0');
INSERT INTO cdb_stats VALUES('week','0','0');
INSERT INTO cdb_stats VALUES('week','1','0');
INSERT INTO cdb_stats VALUES('week','2','0');
INSERT INTO cdb_stats VALUES('week','3','0');
INSERT INTO cdb_stats VALUES('week','4','0');
INSERT INTO cdb_stats VALUES('week','5','0');
INSERT INTO cdb_stats VALUES('week','6','0');
INSERT INTO cdb_stats VALUES('hour','00','0');
INSERT INTO cdb_stats VALUES('hour','01','0');
INSERT INTO cdb_stats VALUES('hour','02','0');
INSERT INTO cdb_stats VALUES('hour','03','0');
INSERT INTO cdb_stats VALUES('hour','04','0');
INSERT INTO cdb_stats VALUES('hour','05','0');
INSERT INTO cdb_stats VALUES('hour','06','0');
INSERT INTO cdb_stats VALUES('hour','07','0');
INSERT INTO cdb_stats VALUES('hour','08','0');
INSERT INTO cdb_stats VALUES('hour','09','0');
INSERT INTO cdb_stats VALUES('hour','10','0');
INSERT INTO cdb_stats VALUES('hour','11','0');
INSERT INTO cdb_stats VALUES('hour','12','0');
INSERT INTO cdb_stats VALUES('hour','13','0');
INSERT INTO cdb_stats VALUES('hour','14','0');
INSERT INTO cdb_stats VALUES('hour','15','0');
INSERT INTO cdb_stats VALUES('hour','16','0');
INSERT INTO cdb_stats VALUES('hour','17','0');
INSERT INTO cdb_stats VALUES('hour','18','0');
INSERT INTO cdb_stats VALUES('hour','19','0');
INSERT INTO cdb_stats VALUES('hour','20','0');
INSERT INTO cdb_stats VALUES('hour','21','0');
INSERT INTO cdb_stats VALUES('hour','22','0');
INSERT INTO cdb_stats VALUES('hour','23','0');

DROP TABLE IF EXISTS cdb_subscriptions;
CREATE TABLE cdb_subscriptions (
	username varchar(25) NOT NULL,
	email varchar(60) NOT NULL,
	tid mediumint(8) unsigned NOT NULL,
	lastnotify int(10) unsigned NOT NULL,
	KEY username (username),
	KEY tid (tid)
);


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

INSERT INTO cdb_themes VALUES('1','标准界面','#FFFFFF','#E3E3EA','#EEEEF6','#3A4273','#000000','header_bg.gif','#F1F3FB','cat_bg.gif','#464F86','#464F86','1','99%','3','Tahoma, Verdana','12px','0','logo.gif','images/standard','images/smilies','#D9D9E9');

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
INSERT INTO cdb_threads VALUES('5','8','0','','admin','javav','1478335268','1478335268','admin','1','0','0','0','','','');

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
INSERT INTO cdb_usergroups VALUES('4','','正式会员','社区乞丐','-9999999','0','0','','0','0','1','1','1','0','0','0','0','0','0','0','0','1','0','0','0','0','0','10','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('5','','正式会员','新手上路','0','10','1','','0','0','1','1','1','0','0','0','0','1','0','0','0','1','0','1','0','0','0','30','3','50','0','0','');
INSERT INTO cdb_usergroups VALUES('6','','正式会员','初级会员','10','50','2','','0','1','1','1','1','1','1','1','1','1','0','0','0','1','0','1','0','0','0','40','5','50','0','128000','gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('7','','正式会员','高级会员','50','150','3','','0','2','1','1','1','1','1','1','1','1','1','0','0','1','0','1','0','0','0','50','10','100','2','256000','gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('8','','正式会员','支柱会员','150','300','4','','1','21','1','1','1','1','1','1','1','1','1','0','0','1','0','1','0','0','0','50','15','100','3','512000','zip,rar,chm,txt,gif,jpg,png');
INSERT INTO cdb_usergroups VALUES('9','','正式会员','青铜长老','300','600','5','','1','2','1','1','1','1','1','1','1','1','1','0','1','1','0','1','0','0','0','50','20','100','4','1024000','');
INSERT INTO cdb_usergroups VALUES('10','','正式会员','黄金长老','600','1000','6','','1','2','1','1','1','1','1','1','1','1','1','0','1','1','0','1','0','0','0','50','25','100','5','1024000','');
INSERT INTO cdb_usergroups VALUES('11','','正式会员','白金长老','1000','3000','7','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','0','0','50','30','100','6','2048000','');
INSERT INTO cdb_usergroups VALUES('12','','正式会员','本站元老','3000','9999999','8','','1','2','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0','0','0','50','40','100','8','2048000','');
INSERT INTO cdb_usergroups VALUES('13','','等待验证','等待验证会员','0','0','0','','0','0','1','1','0','0','0','0','0','0','0','0','0','1','0','0','0','0','0','10','0','50','0','0','');
INSERT INTO cdb_usergroups VALUES('14','','游客','游客','0','0','0','','0','0','1','1','0','0','0','0','0','1','0','0','0','0','0','1','0','0','0','0','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('15','','禁止访问','用户被禁止访问','0','0','0','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','10','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('16','','禁止IP','用户IP被禁止','0','0','0','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','');
INSERT INTO cdb_usergroups VALUES('17','','禁止发言','用户被禁止发言','0','0','0','','0','0','1','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','10','0','0','0','0','');

DROP TABLE IF EXISTS cdb_words;
CREATE TABLE cdb_words (
	id smallint(6) unsigned NOT NULL auto_increment,
	find varchar(60) NOT NULL,
	replacement varchar(60) NOT NULL,
	PRIMARY KEY (id)
);


