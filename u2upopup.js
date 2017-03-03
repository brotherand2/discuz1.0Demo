/******************************************************************************
  Crossday Discuz! Board - U2U PopUp
  Modified by: Crossday Studio (http://crossday.com)
  Based upon:  vBulletin PopUp JavaScript
*******************************************************************************/

var ns = (document.layers);
var ie = (document.all);
var w3 = (document.getElementById && !ie);

function initPopup(){
	if(!ns && !ie && !w3) return;
	if(ie) popupDiv = eval('document.all.u2upopup.style');
	else if(ns) popupDiv = eval('document.layers["u2upopup"]');
	else if(w3) popupDiv = eval('document.getElementById("u2upopup").style');
	if (ie||w3) popupDiv.visibility = "visible";
	else        popupDiv.visibility = "show";
	showPopup();
}

function showPopup(){
	if (ie) {
		documentWidth = document.body.offsetWidth / 2 + document.body.scrollLeft - 20;
		documentHeight= document.body.offsetHeight / 2 + document.body.scrollTop - 20;
	} else if (ns) {
		documentWidth = window.innerWidth / 2 + window.pageXOffset - 20;
		documentHeight = window.innerHeight/2 + window.pageYOffset - 20;
	} else if (w3) {
		documentWidth = self.innerWidth / 2 + window.pageXOffset - 20;
		documentHeight = self.innerHeight / 2 + window.pageYOffset - 20;
	}

	popupDiv.left = documentWidth - 200;
	popupDiv.top = documentHeight - 110;
	setTimeout("showPopup()", 100);
}

function closePopup() {
	if (ie||w3) popupDiv.display = "none";
	else popupDiv.visibility = "hide";
}

onload=initPopup;