"use strict";

var boardroot = "";
function resourceLink(url)
{
	return boardroot + url;
}

// collapsible categories
// code inspired from Neritic Net
function setCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function toggleCat(id)
{
	$('#cat_'+id).toggle();
	$('#cat_'+id+'_lolz').toggle();
	setCookie('catstate['+id+']', $('#cat_'+id).is(':hidden')?1:0, 9999);
}

// autoshrinking breadcrumb action links

/*function breadcrumbShrink()
{
	$('.breadcrumbs th').each(function()
	{
		var children = $(this).children();
		if (children.length < 2) return;
		
		var crumbs = $(children[1]);
		var links = $(children[0]);
		
		links.removeClass('dropdownContainer');
		var lchildren = links.children();
		$(lchildren[0]).hide();
		$(lchildren[1]).removeClass('dropdownMenu').addClass('pipemenu');
		
		if (crumbs.position().left + crumbs.width() + 10 > links.position().left)
		{
			links.addClass('dropdownContainer');
			$(lchildren[0]).show().css('display', 'block');
			$(lchildren[1]).removeClass('pipemenu').addClass('dropdownMenu');
		}
	});
}

$(window).resize(breadcrumbShrink);
$(window).load(breadcrumbShrink);*/

//Spoiler buttons

function toggleSpoiler()
{
	var button = this.parentNode.children[0];
	var div = this.parentNode.children[1];

	if(div.className == "spoiled")
	{
		if(button.className != "spoilerbutton named")
			button.textContent = "Show spoiler";
		div.className = "spoiled hidden";
	}
	else
	{
		if(button.className != "spoilerbutton named")
			button.textContent = "Hide spoiler";
		div.className = "spoiled";
	}
}



/* Quote support
   -------------
   Thanks to Mega-Mario for the idea
 */
function insertQuote(pid)
{
	$.get(boardroot+"ajaxcallbacks.php", "a=q&id="+pid, function(data)
	{
		var editor = $("#text")[0]; //we want the HTMLTextElement kthx
		editor.focus();
		if (document.selection)
			document.selection.createRange().text += data;
		else
			editor.value = editor.value.substring(0, editor.selectionEnd) + data + editor.value.substring(editor.selectionEnd, editor.value.length);
		editor.scrollTop = editor.scrollHeight;
	});
}

function insertChanLink(pid)
{
	var editor = document.getElementById("text");
	var linkText = ">>" + pid + "\r\n";
	editor.focus();
	if (document.selection)
		document.selection.createRange().text += linkText;
	else
		editor.value = editor.value.substring(0, editor. selectionEnd) + linkText + editor.value.substring(editor.selectionEnd, editor.value.length);
	editor.scrollTop = editor.scrollHeight;
}




/* Smiley tricks
   -------------
   Inspired by Mega-Mario's quote system.
 */
 
var smiliesOpened = false;
var usingSmilies = false;
 
function insertSmiley(smileyCode)
{
	var editor = document.getElementById("text");
	editor.focus();
	if (document.selection)
	{
		document.selection.createRange().text += " " + smileyCode;
	}
	else
	{
		editor.value = editor.value.substring(0, editor. selectionEnd) + smileyCode + editor.value.substring(editor.selectionEnd, editor.value.length);
	}
	editor.scrollTop = editor.scrollHeight;
}
function expandSmilies()
{
	var button = document.getElementById("smiliesExpand");
	var expandedSet = $("#expandedSet");
	if(expandedSet.is(":hidden"))
	{
		expandedSet.slideDown(200, function()
		{
			button.textContent = String.fromCharCode(0x25B2);
		});
	}
	else
	{
		expandedSet.slideUp(200, function()
		{
			button.textContent = String.fromCharCode(0x25BC);
		});
	}
}

function hideSmiliesBox()
{
	$('#smilies').hide();
	smiliesOpened = false;
}

function showSmiliesBox(btn)
{
	if (smiliesOpened)
	{
		hideSmiliesBox();
		return;
	}
	
	var btnpos = $(btn).offset();
	$('#smilies').show();
	$('#smilies').offset({ top: btnpos.top - $('#smilies').outerHeight() + $(btn).outerHeight(), left: btnpos.left + $(btn).outerWidth() });
	smiliesOpened = true;
}

function expandPostHelp()
{
	var button = document.getElementById("postHelpExpand");
	var expandedSet = $("#expandedHelp");

	if(expandedSet.is(":hidden"))
	{
		expandedSet.slideDown(700, function()
		{
			button.textContent = String.fromCharCode(0x25B2);
		});
	}
	else
	{
		expandedSet.slideUp(700, function()
		{
			button.textContent = String.fromCharCode(0x25BC);
		});
	}
}



/* Bare metal AJAX support functions
   ---------------------------------
   Press button, recieve content.
 */
var xmlHttp = null; //Cache our request object

// is this really used anymore now that we mostly use jQuery?
function GetXmlHttpObject()
{
	//If we already have one, just return that.
	if (xmlHttp != null) return xmlHttp;
	xmlHttp = new XMLHttpRequest();
	return xmlHttp;
}



function startPoraUpdate()
{
	var ta = document.getElementById("editbox");
	var tt = document.getElementById("title");
	var prt = document.getElementById("previewtext");
	var pri = document.getElementById("previewtitle");

	prt.innerHTML = ta.value;//.replace("\n", "<br />");
	pri.textContent = tt.value;
	//setTimeout("startPoraUpdate();", 100);
}


var onlineFID = 0;

function startOnlineUsers()
{
	//onlineFID = fid;
	//setTimeout("getOnlineUsers()", 10000);
	//var onlineUsersBar = $('.header0').get(1);
	//onlineUsersBar.id="onlineUsersBar";
	var tmrid = window.setInterval("getOnlineUsers();", 10000);

	$(window).blur(function() {
		if (tmrid != -9999)
		{
			window.clearInterval(tmrid);
			tmrid = -9999;
		}
	});

	$(window).focus(function() {
		getOnlineUsers();
		if (tmrid == -9999)
			tmrid = window.setInterval("getOnlineUsers();", 10000);
	});
}

function getOnlineUsers()
{
	// view count
	$.get(boardroot+"ajaxcallbacks.php", "a=vc", function(data)
	{
	    var viewCount = $("#viewCount");
	    var oldCount = viewCount[0].innerHTML;
	    if(oldCount != data)
	    {
			viewCount.html(data);
		}
	});
	
	// online users
	$.get(boardroot+"ajaxcallbacks.php", "a=ou&f=" + onlineFID, function(data)
	{
	    var onlineUsers = $("#onlineUsers");
	    var oldOnline = onlineUsers[0].innerHTML;
	    if(oldOnline != data)
	    {
			onlineUsers.html(data);
		}
	});
	
	// notifications
	$.getJSON(boardroot+"ajaxcallbacks.php", "a=no", function(data)
	{
	    var notiflist = '';
		for (var i=0; i < data.length; i++)
			notiflist += '<li>' + data[i].text + '<br><small>' + data[i].formattedDate + '</small>';
		
		$('#notifList').html(notiflist);
		
		if ($('#notifCount').html() != data.length)
		{
			$('#notifCount').html(data.length);
			
			$('#notifMenuContainer').removeClass('noNotif hasNotifs');
			if (data.length)
				setTimeout(function(){$('#notifMenuContainer').addClass('hasNotifs');},20); // need to do it later so the CSS animation plays
			else
				$('#notifMenuContainer').addClass('noNotif');
		}
	});
}


function showEditProfilePart(newId)
{
	var tables = document.getElementsByClassName('eptable');
	for (var i=0;i<tables.length;i++) {
		tables[i].style.display = "none";
	}
	document.getElementById(newId).style.display = "table";
	var tabs = document.getElementsByClassName('tab');
	for (var i=0;i<tabs.length;i++) {
		tabs[i].className = "tab";
	}
	document.getElementById(newId+"Button").className = "tab selected";
}

var textEditor;
function hookUpControls()
{
	//Now functional!
	textEditor = document.getElementById("text");
	textEditor.addEventListener("keypress", HandleKey, true);
	ConstructToolbar();
}

function ConstructToolbar()
{
	var smilbox = document.getElementById('smilies');
	var toolbar = document.createElement("DIV");
	toolbar.className = "postToolbar";

	var buttons =
	[
		{ label: "icon-bold", title: "Bold", style: "font-weight: bold", insert: "b" },
		{ label: "icon-italic", title: "Italic", style: "font-style: italic", insert: "i" },
		{ label: "icon-underline", title: "Underlined", style: "text-decoration: underline", insert: "u" },
		{ label: "icon-strikethrough", title: "Strikethrough", style: "text-decoration: line-through", insert: "s" },
		{ label: "-" },
		{ label: "icon-superscript", title: "Superscript", insert: "sup", html: true },
		{ label: "icon-subscript", title: "Subscript", insert: "sub", html: true },
		//{ label: "A", title: "Big", insert: "big", html: true },
		//{ label: "a", title: "Small", insert: "small", html: true },
		{ label: "-" },
		{ label: "icon-link", title: "Link", style: "color: #66f; text-decoration: underline", insert: "url" },
		{ label: "icon-picture", title: "Image", insert: "img" },
		{ label: "-" },
		{ label: "icon-quote-left", title: "Quote", insert: "quote" },
		{ label: "icon-collapse", title: "Spoiler", insert: "spoiler" },
		//{ label: "abc", title: "Insert code block", style: "font-family: monospace", insert: "code" },

	];

	for(var i = 0; i < buttons.length; i++)
	{
		var button = buttons[i];
		if(button.label == "-")
		{
			toolbar.innerHTML += " ";
			continue;
		}
		var newButton = "<button ";
		if (button.title != undefined)
			newButton += "title=\"" + button.title + "\" ";
		newButton += "onclick=\"Insert('" + button.insert + "', " + button.html + "); return false;\">";
		//if (button.style != undefined)
		//	newButton += "<span style=\"" + button.style + "\">";
		newButton += '<i class="'+button.label+'"></i>';
		//if (button.style != undefined)
		//	newButton += "</span>";
		newButton += "</button>";
		toolbar.innerHTML += newButton;
	}

	textEditor.parentNode.insertBefore(toolbar, textEditor);
}
function HandleKey()
{
	if(event.ctrlKey && !event.altKey)
	{
		var charCode = event.charCode ? event.charCode : event.keyCode;
		var c = String.fromCharCode(charCode).toLowerCase();
		if (c == "b" || c == "i" || c == "u")
		{
			textEditor.focus();
			Insert(c);
			event.preventDefault();
			return false;
		}
	}
}
function Insert(stuff, html)
{
	var oldSelS = textEditor.selectionStart;
	var oldSelE = textEditor.selectionEnd;
	var scroll = textEditor.scrollTop;
	var selectedText = textEditor.value.substr(oldSelS, oldSelE - oldSelS);

	if(html)
		textEditor.value = textEditor.value.substr(0, oldSelS) + "<" + stuff + ">" + selectedText + "</" + stuff + ">" + textEditor.value.substr(oldSelE);
	else
		textEditor.value = textEditor.value.substr(0, oldSelS) + "[" + stuff + "]" + selectedText + "[/" + stuff + "]" + textEditor.value.substr(oldSelE);

	textEditor.selectionStart = oldSelS + stuff.length + 2;
	textEditor.selectionEnd = oldSelS + stuff.length + 2 + selectedText.length;
	textEditor.scrollTop = scroll;
	textEditor.focus();
}



var refreshUrl = "";

function startPageUpdate()
{
	var tmrid = window.setInterval("doPageUpdate();", 30000);

	$(window).blur(function() {
		if (tmrid != -9999) {
			window.clearInterval(tmrid);
			tmrid = -9999;
		}
	});

	$(window).focus(function() {
		doPageUpdate();
		if (tmrid == -9999)
			tmrid = window.setInterval("doPageUpdate();", 30000);
	});
}

function doPageUpdate()
{
	$.get(refreshUrl, "", function(data)
	{
		$("#page_contents").html(data);
	});
}



// Live theme changer by Mega-Mario
function ChangeTheme(newtheme)
{
	$.get(boardroot+"ajaxcallbacks.php", "a=tf&t="+newtheme, function(data)
	{
		var stuff = data.split('|');
		$("#theme_css")[0].href = stuff[0];
		$("#theme_banner")[0].src = stuff[1];
	});
}



//Search page pager
function ChangePage(newpage)
{
        var pagenums = document.getElementsByClassName('pagenum');
        for (i = 0; i < pagenums.length; i++)
                pagenums[i].href = '#';

        pagenums = document.getElementsByClassName('pagenum'+newpage);
        for (i = 0; i < pagenums.length; i++)
                pagenums[i].removeAttribute('href');

        var pages = document.getElementsByClassName('respage');
        for (i = 0; i < pages.length; i++)
                pages[i].style.display = 'none';

        pages = document.getElementsByClassName('respage'+newpage);
        for (i = 0; i < pages.length; i++)
                pages[i].style.display = '';
}




function expandTable(tableName, button)
{
	var table = document.getElementById(tableName);
	var rows = table.getElementsByTagName("tr");

	for(var i = 0; i < rows.length; i++)
	{
		//alert(rows[i].className + ", " + rows[i].style['display']);
		if(rows[i].className == "header1")
			continue;

		if(rows[i].style['display'] == "none")
			rows[i].style['display'] = "";
		else
			rows[i].style['display'] = "none";
	}
}

function hideTricks(pid)
{
	$("#dyna_"+pid).hide(200);//, function()
	$("#meta_"+pid).show(200);
}

function showRevisions(pid)
{
	$("#meta_"+pid).hide(200);//, function()
	$("#dyna_"+pid).load(boardroot+"ajaxcallbacks.php", "a=srl&id="+pid, function()
	{
		$("#dyna_"+pid).show(200);
	});
}

function showRevision(pid, rev)
{
	var post = $("#post_"+pid);
	$.get(boardroot+"ajaxcallbacks.php", "a=sr&id="+pid+"&rev="+rev, function(data)
	{
		post.fadeOut(200, function()
		{
			post[0].innerHTML = data;
			post.fadeIn(200);
		});
	});
}

function deletePost(link)
{
	var reason = prompt('Enter a reason for deleting the post, or leave blank for no reason.');
	if (reason == null) return;

	var href = link.href + '&reason=' + encodeURIComponent(reason);
	document.location.href = href;
}

function checkAll()
{
	var ca = document.getElementById("ca");
	var checked = ca.checked;
	var checks = document.getElementsByTagName("INPUT");
	for(var i = 0; i < checks.length; i++)
		checks[i].checked = checked;
}


function hookUploadCheck(id, type, size)
{
	var obj = document.getElementById(id);
	if(type == 0)
	{
		obj.onchange = function()
		{
			var submit = document.getElementById("submit");
			var sizeWarning = document.getElementById("sizeWarning");
			var typeWarning = document.getElementById("typeWarning");

			submit.disabled = (obj.value == "");

			if(obj.files != undefined)
			{
				var file = obj.files[0];
				var fileSize = 0;
				if(file != undefined)
					fileSize = file.size;
				sizeWarning.style['display'] = (fileSize > size) ? "inline" : "none";
				submit.disabled = (fileSize > size);
				if(file != undefined)
				{
					switch(file.type)
					{
						case "image/jpeg":
						case "image/png":
						case "image/gif":
							typeWarning.style['display'] = "none";
							break;
						default:
							typeWarning.style['display'] = "inline";
							submit.disabled = true;
					}
				}
			}
		};
	}
	else if(type == 1)
	{
		obj.onchange = function()
		{
			var submit = document.getElementById("submit");
			var sizeWarning = document.getElementById("sizeWarning");
			var typeWarning = document.getElementById("typeWarning");

			submit.disabled = (obj.value == "");
			if(obj.files != undefined)
			{
				var file = obj.files[0];
				var fileSize = 0;
				if(file != undefined)
					fileSize = file.size;
				sizeWarning.style['display'] = (fileSize > size) ? "inline" : "none";
				submit.disabled = (fileSize > size);
				if(file != undefined)
				{
					switch(file.type)
					{
						case "application/x-msdownload":
						case "text/html":
							typeWarning.style['display'] = "inline";
							submit.disabled = true;
							break;
						default:
							typeWarning.style['display'] = "none";
					}
				}
			}
		};
	}
}

function replacePost(id, opened)
{
	$.get(boardroot+"ajaxcallbacks.php?a=rp"+(opened ? "&o":"")+"&id="+id, function(data)
	{
		$("#post"+id).replaceWith(data);
		$("#post"+id+" .spoilerbutton").click(toggleSpoiler);
	});
}

function searchThemes(query) {
	if (window.themes === undefined) {
		window.themes = document.getElementsByClassName("theme");

		window.themeNames = {};
		
		for (var i = 0; i < window.themes.length; i++) {
			window.themeNames[window.themes[i].title] = i;
		}
	}

	var themeKeys = Object.keys(window.themeNames);
	query = query.toLowerCase();
	for (var i = 0; i < window.themes.length; i++) {
		if (query == "" || themeKeys[i].toLowerCase().indexOf(query) !== -1) {
			themes[i].style.display = "inline-block";
		} else {
			themes[i].style.display = "none";
		}
	}
}

$(document).ready(function() 
{
	$(".spoilerbutton").click(toggleSpoiler);
});
