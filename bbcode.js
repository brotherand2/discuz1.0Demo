/******************************************************************************
  Crossday Discuz! Board - BB Code Insert
  Modified by: Crossday Studio (http://crossday.com), Weiming Bianzhou
  Based upon:  XMB CodeInsert (http://www.xmbforum.com), matt
*******************************************************************************/

defmode = "normalmode";		// 默认模式，可选 normalmode, advmode, 或 helpmode

if (defmode == "advmode") {
        helpmode = false;
        normalmode = false;
        advmode = true;
} else if (defmode == "helpmode") {
        helpmode = true;
        normalmode = false;
        advmode = false;
} else {
        helpmode = false;
        normalmode = true;
        advmode = false;
}
function chmode(swtch){
        if (swtch == 1){
                advmode = false;
                normalmode = false;
                helpmode = true;
                alert("Discuz! 代码 - 帮助信息\n\n点击相应的代码按钮即可获得相应的说明和提示");
        } else if (swtch == 0) {
                helpmode = false;
                normalmode = false;
                advmode = true;
                alert("Discuz! 代码 - 直接插入\n\n点击代码按钮后不出现提示即直接插入相应代码");
        } else if (swtch == 2) {
                helpmode = false;
                advmode = false;
                normalmode = true;
                alert("Discuz! 代码 - 提示插入\n\n点击代码按钮后出现向导窗口帮助您完成代码插入");
        }
}

function AddText(NewCode) {
        if(document.all){
        	insertAtCaret(document.input.message, NewCode);
        	setfocus();
        } else{
        	document.input.message.value += NewCode;
        	setfocus();
        }
}

function storeCaret (textEl){
        if(textEl.createTextRange){
                textEl.caretPos = document.selection.createRange().duplicate();
        }
}

function insertAtCaret (textEl, text){
        if (textEl.createTextRange && textEl.caretPos){
                var caretPos = textEl.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 2) == ' ' ? text + ' ' : text;
        } else if(textEl) {
                textEl.value += text;
        } else {
        	textEl.value = text;
        }
}

function email() {
        if (helpmode) {
                alert("插入邮件地址\n\n插入邮件地址连接。\n例如：\n[email]support@crossday.com[/email]\n[email=support@crossday.com]Dai Zhikang[/email]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[email]" + range.text + "[/email]";
	} else if (advmode) {
	      AddTxt="[email] [/email]";
                AddText(AddTxt);
        } else { 
                txt2=prompt("请输入链接显示的文字，如果留空则直接显示邮件地址。",""); 
                if (txt2!=null) {
                        txt=prompt("请输入邮件地址。","name@domain.com");      
                        if (txt!=null) {
                                if (txt2=="") {
                                        AddTxt="[email]"+txt+"[/email]";
                
                                } else {
                                        AddTxt="[email="+txt+"]"+txt2+"[/email]";
                                } 
                                AddText(AddTxt);                
                        }
                }
        }
}


function chsize(size) {
        if (helpmode) {
                alert("设置字号\n\n将标签所包围的文字设置成指定字号。\n例如：[size=3]文字大小为 3[/size]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[size=" + size + "]" + range.text + "[/size]";
        } else if (advmode) {
                AddTxt="[size="+size+"] [/size]";
                AddText(AddTxt);
        } else {                       
                txt=prompt("请输入要设置为字号 "+size+" 的文字。","文字"); 
                if (txt!=null) {             
                        AddTxt="[size="+size+"]"+txt;
                        AddText(AddTxt);
                        AddText("[/size]");
                }        
        }
}

function chfont(font) {
        if (helpmode){
                alert("设定字体\n\n将标签所包围的文字设置成指定字体。\n例如：[font=仿宋]字体为仿宋[/font]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[font=" + font + "]" + range.text + "[/font]";
        } else if (advmode) {
                AddTxt="[font="+font+"] [/font]";
                AddText(AddTxt);
        } else {                  
                txt=prompt("请输入要设置成 "+font+" 的文字。","文字");
                if (txt!=null) {             
                        AddTxt="[font="+font+"]"+txt;
                        AddText(AddTxt);
                        AddText("[/font]");
                }        
        }  
}


function bold() {
        if (helpmode) {
                alert("插入粗体文本\n\n将标签所包围的文本变成粗体。\n例如：[b]Crossday 工作室[/b]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[b]" + range.text + "[/b]";
        } else if (advmode) {
                AddTxt="[b] [/b]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请输入要设置成粗体的文字。","文字");     
                if (txt!=null) {           
                        AddTxt="[b]"+txt;
                        AddText(AddTxt);
                        AddText("[/b]");
                }       
        }
}

function italicize() {
        if (helpmode) {
                alert("插入斜体文本\n\n将标签所包围的文本变成斜体。\n例如：[i]Crossday 工作室[/i]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[i]" + range.text + "[/i]";
        } else if (advmode) {
                AddTxt="[i] [/i]";
                AddText(AddTxt);
        } else {   
                txt=prompt("请输入要设置成斜体的文字。","文字");     
                if (txt!=null) {           
                        AddTxt="[i]"+txt;
                        AddText(AddTxt);
                        AddText("[/i]");
                }               
        }
}

function quote() {
        if (helpmode){
                alert("插入引用\n\n将标签所包围的文本作为引用特殊显示。\n例如：[quote]Discuz! 版权所有 - Crossday Studio[/quote]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[quote]" + range.text + "[/quote]";
        } else if (advmode) {
                AddTxt="\r[quote]\r[/quote]";
                AddText(AddTxt);
        } else {   
                txt=prompt("请输入要作为引用显示的文字。","文字");     
                if(txt!=null) {          
                        AddTxt="\r[quote]\r"+txt;
                        AddText(AddTxt);
                        AddText("\r[/quote]");
                }               
        }
}

function chcolor(color) {
        if (helpmode) {
                alert("插入定义颜色文本\n\n将标签所包围的文本变为制定颜色。\n例如：[color=red]红颜色[/color]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[color=" + color + "]" + range.text + "[/color]";
        } else if (advmode) {
                AddTxt="[color="+color+"] [/color]";
                AddText(AddTxt);
        } else {  
        txt=prompt("请输入要设置成颜色 "+color+" 的文字。","文字");
                if(txt!=null) {
                        AddTxt="[color="+color+"]"+txt;
                        AddText(AddTxt);
                        AddText("[/color]");
                }
        }
}

function center() {
        if (helpmode) {
                alert("居中对齐\n\n将标签所包围的文本居中对齐显示。\n例如：[align=center]内容居中[/align]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[center]" + range.text + "[/center]";
        } else if (advmode) {
                AddTxt="[align=center] [/align]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请输入要居中对齐的文字。","文字");     
                if (txt!=null) {          
                        AddTxt="\r[align=center]"+txt;
                        AddText(AddTxt);
                        AddText("[/align]");
                }              
        }
}

function hyperlink() {
        if (helpmode) {
                alert("插入超级链接\n\n插入一个超级连接。\n例如：\n[url]http://www.crossday.com[/url]\n[url=http://www.crossday.com]Crossday 工作室[/url]");
        } else if (advmode) {
                AddTxt="[url] [/url]";
                AddText(AddTxt);
        } else { 
                txt2=prompt("请输入链接显示的文字，如果留空则直接显示链接。",""); 
                if (txt2!=null) {
                        txt=prompt("请输入 URL。","http://");      
                        if (txt!=null) {
                                if (txt2=="") {
                                        AddTxt="[url]"+txt;
                                        AddText(AddTxt);
                                        AddText("[/url]");
                                } else {
                                        AddTxt="[url="+txt+"]"+txt2;
                                        AddText(AddTxt);
                                        AddText("[/url]");
                                }         
                        } 
                }
        }
}

function image() {
        if (helpmode){
                alert("插入图像\n\n在文本中插入一幅图像。\n例如：[img]http://www.crossday.com/cdb/images/logo.gif[/img]");
        } else if (advmode) {
                AddTxt="[img] [/img]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请输入图像的 URL。","http://");    
                if(txt!=null) {            
                        AddTxt="\r[img]"+txt;
                        AddText(AddTxt);
                        AddText("[/img]");
                }       
        }
}

function flash() {
        if (helpmode){
                alert("插入 flash\n\n在文本中插入 flash 动画。\n例如：[swf]http://www.crossday.com/cdb/images/banner.swf[/swf]");
        } else if (advmode) {
                AddTxt="[swf] [/swf]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请输入 flash 动画的 URL。","http://");    
                if(txt!=null) {            
                        AddTxt="\r[swf]"+txt;
                        AddText(AddTxt);
                        AddText("[/swf]");
                }       
        }
}

function code() {
        if (helpmode) {
                alert("插入代码\n\n插入程序或脚本原始代码。\n例如：[code]echo\"这里是我们的论坛\";[/code]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[code]" + range.text + "[/code]";
        } else if (advmode) {
                AddTxt="\r[code]\r[/code]";
                AddText(AddTxt);
        } else {   
                txt=prompt("请输入要插入的代码。","");     
                if (txt!=null) {          
                        AddTxt="\r[code]"+txt;
                        AddText(AddTxt);
                        AddText("[/code]");
                }              
        }
}

function list() {
        if (helpmode) {
                alert("插入列表项\n\n插入可由浏览器显示来的规则列表项。\n例如：\n[list]\n[*]；列表项 #1\n[*]；列表项 #2\n[*]；列表项 #3\n[/list]");
        } else if (advmode) {
                AddTxt="\r[list]\r[*]\r[*]\r[*]\r[/list]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请选择列表格式：字母式列表输入 \"A\"；数字式列表输入 \"1\"。此处也可留空。","");               
                while ((txt!="") && (txt!="A") && (txt!="a") && (txt!="1") && (txt!=null)) {
                        txt=prompt("错误：列表格式只能选择输入 \"A\" 或 \"1\"。","");               
                }
                if (txt!=null) {
                        if (txt=="") {
                                AddTxt="\r[list]\r\n";
                        } else {
                                AddTxt="\r[list="+txt+"]\r";
                        } 
                        txt="1";
                        while ((txt!="") && (txt!=null)) {
                                txt=prompt("请输入列表项目内容，如果留空表示项目结束。",""); 
                                if (txt!="") {             
                                        AddTxt+="[*]"+txt+"\r"; 
                                }                   
                        } 
                        AddTxt+="[/list]\r\n";
                        AddText(AddTxt); 
                }
        }
}

function underline() {
        if (helpmode) {
                alert("插入下划线\n\n给标签所包围的文本加上下划线。\n例如：[u]Crossday 工作室[/u]");
	} else if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[u]" + range.text + "[/u]";
        } else if (advmode) {
                AddTxt="[u] [/u]";
                AddText(AddTxt);
        } else {  
                txt=prompt("请输入要加下划线的文字。","文字");
                if (txt!=null) {           
                        AddTxt="[u]"+txt;
                        AddText(AddTxt);
                        AddText("[/u]");
                }               
        }
}

function setfocus() {
        document.input.message.focus();
}