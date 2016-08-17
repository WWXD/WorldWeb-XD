
var sidebarOn = false;

function closeSidebar()
{
	$('#mobile-sidebar-container').hide();
	$('#realbody').css('max-width', 'none');
	$('#realbody').css('max-height', 'none');
	$('#realbody').css('overflow', 'auto');
	
	$('#mobile-sidebar-deactivate').off('click.mobilesidebar');
	sidebarOn = false;
}

function openSidebar()
{
	$('#mobile-sidebar-container').show();
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
