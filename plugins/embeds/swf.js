
/* Flashloops */
function startFlashClicked()
{
	var id = this.id.substr(4);
	var url = document.getElementById("swf" + id + "url").innerHTML;
	var mainPanel = document.getElementById("swf" + id + "main");
	mainPanel.innerHTML = '<object data="' + url + '" style="width: 100%; height: 100%;"><embed src="' + url + '" style="width: 100%; height: 100%;"></embed></object>';
}
function stopFlashClicked()
{
	var id = this.id.substr(4);
	var mainPanel = document.getElementById("swf" + id + "main");
	mainPanel.innerHTML = '';
}

$(document).ready(function() {
	$(".startFlash").click(startFlashClicked);
	$(".stopFlash").click(stopFlashClicked);
});

