//===================================
// Functions for Niko's Forum Editor

var fid = 0;
var hint = true;

function geteditforaurl()
{
	if((document.location+"").indexOf("?") == -1) 
		return document.location + "?action=";
	else
		return document.location + "&action=";
}

function dopermselects()
{
	$('.permselect').change(function() { this.style.background = this.selectedOptions[0].style.background; }).change();
}

function pickForum(id) {
	if (hint == true) {
		$("#hint").remove();
		hint = false;
	}
	$(".f, .c").removeClass("fe_selected");
	$("#forum"+id).addClass("fe_selected");
	if ($("#editcontent").is(":hidden")) $("#editcontent").show();
	fid = id;
	$("#editcontent").load(geteditforaurl()+'editforum&fid='+id, '', function(){dopermselects();});
}

function pickCategory(id) {
	if (hint == true) {
		$("#hint").remove();
		hint = false;
	}
	$(".f, .c").removeClass("fe_selected");
	$("#cat"+id).addClass("fe_selected");
	if ($("#editcontent").is(":hidden")) $("#editcontent").show();
	$("#editcontent").load(geteditforaurl()+'editcategory&cid='+id);
	fid = id;
}

function changeForumInfo(id)
{
	var postdata = $("#forumform").serialize();
	$.post(geteditforaurl()+"updateforum", postdata, function(data) {
		data = $.trim(data);
		if(data == "Ok")
		{
			$("#flist").load(geteditforaurl()+"forumtable", '',
				function(){$("#forum"+id).addClass("fe_selected");});
			
			$("#editcontent").load(geteditforaurl()+'editforum&fid='+id, '',
				function(){$('#status').html('Forum saved!').show().animate({opacity: 0}, 2000, 'linear', function(){$('#status').hide();});dopermselects();});
		}
		else
			alert("Error: "+data);
	});
}


function changeCategoryInfo(id)
{
	var postdata = $("#forumform").serialize();
	$.post(geteditforaurl()+"updatecategory", postdata, function(data) {
		data = $.trim(data);
		if(data == "Ok")
		{
			$("#flist").load(geteditforaurl()+"forumtable", '', 
				function(){$("#cat"+id).addClass("fe_selected");});
				
			$("#editcontent").load(geteditforaurl()+'editcategory&cid='+id, '',
				function(){$('#status').html('Category saved!').show().animate({opacity: 0}, 2000, 'linear', function(){$('#status').hide();});});
		}
		else
			alert("Error: "+data);
	});
}

function addForum()
{
	var postdata = $("#forumform").serialize();

	$.post(geteditforaurl()+"addforum", postdata, function(data) {
		data = $.trim(data);
		if(data.substring(0,2) == "Ok")
		{
			var id = parseInt(data.substring(3));
			
			$("#flist").load(geteditforaurl()+"forumtable", '',
				function(){$("#forum"+id).addClass("fe_selected");});
			
			$("#editcontent").load(geteditforaurl()+'editforum&fid='+id, '',
				function(){$('#status').html('Forum saved!').show().animate({opacity: 0}, 2000, 'linear', function(){$('#status').hide();});dopermselects();});
		}
		else
			alert("Error: "+data);
	});
}

function addCategory()
{
	var postdata = $("#forumform").serialize();

	$.post(geteditforaurl()+"addcategory", postdata, function(data) {
		data = $.trim(data);
		if(data.substring(0,2) == "Ok")
		{
			var id = parseInt(data.substring(3));
			
			$("#flist").load(geteditforaurl()+"forumtable", '', 
				function(){$("#cat"+id).addClass("fe_selected");});
				
			$("#editcontent").load(geteditforaurl()+'editcategory&cid='+id, '',
				function(){$('#status').html('Category saved!').show().animate({opacity: 0}, 2000, 'linear', function(){$('#status').hide();});});
		}
		else
			alert("Error: "+data);
	});
}

function deleteForum()
{
	var postdata = $("#forumform").serialize();

	if(!confirm("Are you sure that you want to delete the forum?"))
		return;

	$.post(geteditforaurl()+"deleteforum", postdata, function(data) {
		data = $.trim(data);
		if(data == "Ok")
		{
			$("#flist").load(geteditforaurl()+"forumtable");
			$("#editcontent").html("");
		}
		else
			alert("Error: "+data);
	});
}


function deleteCategory()
{
	var postdata = $("#forumform").serialize();

	if(!confirm("Are you sure that you want to delete the category?"))
		return;

	$.post(geteditforaurl()+"deletecategory", postdata, function(data) {
		data = $.trim(data);
		if(data == "Ok")
		{
			$("#flist").load(geteditforaurl()+"forumtable");
			$("#editcontent").html("");
		}
		else
			alert("Error: "+data);
	});
}

function newForum()
{
	$('#editcontent').load(geteditforaurl()+'editforumnew', '', function(){dopermselects();});
	$(".f, .c").removeClass("fe_selected");
}

function newCategory()
{
	$('#editcontent').load(geteditforaurl()+'editcategorynew');
	$(".f, .c").removeClass("fe_selected");
}

function showDeleteForum()
{
	$("#deleteforum").slideDown("slow");
}

function hideDeleteForum()
{
	$("#deleteforum").slideUp("slow");
}


