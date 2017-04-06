
var sidebarOn = false;

function closeSidebar()
{
	$('#mobile-sidebar-container').hide('slide', {direction: 'right'}, 250);
	$('#realbody').css('max-width', 'none');
	$('#realbody').css('max-height', 'none');
	$('#realbody').css('overflow', 'auto');
	
	$('#mobile-sidebar-deactivate').off('click.mobilesidebar');
	sidebarOn = false;
}

function openSidebar()
{
	$('#mobile-sidebar-container').show('slide', {direction: 'right'}, 250);
	$('#realbody').css('max-width', '100%');
	$('#realbody').css('max-height', '100%');
	$('#realbody').css('overflow', 'hidden');
	
	setTimeout(function()
	{
		$('#mobile-sidebar-deactivate').on('click.mobilesidebar', function(e)
		{
			closeSidebar();
			e.preventDefault();
		});
	}, 0);
	sidebarOn = true;
}

(function(document,navigator,standalone) {
	// prevents links from apps from oppening in mobile safari
	// this javascript must be the first script in your <head>
	if ((standalone in navigator) && navigator[standalone]) {
		var curnode, location=document.location, stop=/^(a|html)$/i;
		document.addEventListener('click', function(e) {
			curnode=e.target;
			while (!(stop).test(curnode.nodeName)) {
				curnode=curnode.parentNode;
			}
			// Conditions to do this only on links to your own app
			// if you want all links, use if('href' in curnode) instead.
			if('href' in curnode && ( curnode.href.indexOf('http') || ~curnode.href.indexOf(location.host) ) ) {
				e.preventDefault();
				location.href = curnode.href;
			}
		},false);
	}
})(document,window.navigator,'standalone');
