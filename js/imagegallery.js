
var imageGalleries = new Array();

function popupImageGallery(id)
{
	$('body').add(document.createElement('div'));//.css('position:fixed;top:0px;left:0px;bottom:0px;right:0px;background:rgba(0,0,0,0.5);');
}

function makeImageGallery()
{
	var id = this.id;
	var imglist = $(this).attr('images').split(';');
	
	imageGalleries[id] = imglist;
	
	$(this).html('<img class="imageGallery_min" style="background:url(\'img/wait.gif\');" src="'+imglist[0]+'" onload="this.style.background=\'\';" onclick="popupImageGallery(\''+id+'\');">');
}

window.addEventListener("load", function(e) 
{
	$('.imgGallery').each(makeImageGallery);
});
