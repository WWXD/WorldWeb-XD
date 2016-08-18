{$numnotifs=count($notifications)}
<div id="realbody">
	
	<div id="mobile-sidebar-container" style="display:none;">
	<div id="mobile-sidebar-deactivate"></div>
	<div id="mobile-sidebar">
		<table class="outline opaque">
			<tr class="header1"><th>{$boardname}</th></tr>
			
			<tr><td class="cell{cycle values='1,2'} center">{$layout_time}</td></tr>
			
			{if $poratext}<tr><td class="cell{cycle values='1,2'} center">{$poratext}</td></tr>{/if}
			
			{if $loguserid && $numnotifs}
				<tr class="header1"><th>Notifications</th></tr>
				{foreach $notifications as $notif}
				<tr><td class="cell{cycle values='1,2'} mobileNotif">
					<div>{$notif.text}<br><small>{$notif.formattedDate}</small></div>
				</td></tr>
				{/foreach}
			{/if}
			
	<tr class="header0"><th>Navigation</th></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/"><i class="icon-home" aria-hidden="true"></i> Home</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=board">Forums</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=FAQ"><i class="icon-question" aria-hidden="true"></i> FAQ</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=memberlist"><i class="icon-group" aria-hidden="true"></i> Member list</a></td></tr>
	{if $loguserid}<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=online"><i class="icon-eye-open" aria-hidden="true"></i> Online users</a></td></tr>{/if}
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=lastposts"><i class="icon-reorder" aria-hidden="true"></i> Last posts</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=search"><i class="icon-search" aria-hidden="true"></i> Search</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=IRC"><i class="icon-quote-right" aria-hidden="true"></i> IRC</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=wiki"><i class="fa fa-edit fa-fw" aria-hidden="true"></i> Wiki</a></td></tr>
	<tr class="header0 center"><th><i class="fa fa-user" aria-hidden="true"></i> {if $loguserid}{$loguserlink}{else}Guest{/if}</th></tr>
	{if $loguserid}{if HasPermission('user.editprofile')}<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=editprofile"><i class="icon-pencil" aria-hidden="true"></i> Edit profile</a></td></tr>
	{if HasPermission('user.editavatars')}<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=editavatars"><i class="icon-picture" aria-hidden="true"></i> Mood avatars</a></td></tr>
	{/if}{/if}
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=private"><i class="icon-envelope" aria-hidden="true"></i> Private messages</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=favorites"><i class="icon-star" aria-hidden="true"></i> Favorites</a></td></tr>
	{if HasPermission('admin.viewadminpanel')}<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=admin"><i class="icon-cogs" aria-hidden="true"></i> Admin</a></td></tr>{/if}
	<tr><td class="cell{cycle values='1,2'} link"><a href="#" onclick="$('#logout').submit(); return false;"><i class="icon-signout" aria-hidden="true"></i> Log out</a></td></tr>
	{else}
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=login"><i class="icon-signin" aria-hidden="true"></i> Login</a></td></tr>
	<tr><td class="cell{cycle values='1,2'} link"><a href="/?page=register"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></td></tr>
					{/if}
					{if $layout_actionlinks}
					<tr class="header0"><th>Page Settings</th></tr>
									{foreach $layout_actionlinks as $link}
						<tr><td class="cell{cycle values='1,2'} link">{$link}</td></tr>
					{/foreach}
					{/if}
							</table>
	</div>
	</div>

	<div id="main" style="padding:0px;">
	
	<table class="outline" id="mobile-crumbs">
	<tr class="header0"><th>
		<span style="float:right;">
			<button onclick="openSidebar();"{if !$numnotifs}>...{else} class="notifs">{$numnotifs}{/if}</button>
		</span>
		
		{if count($layout_crumbs)>1}
		{$crumburls=array_keys($layout_crumbs)}
		{$prevcrumb=$crumburls[count($crumburls)-2]}
		{$thiscrumb=$crumburls[count($crumburls)-1]}
		<button onclick="window.location='{$prevcrumb|escape}';">&lt;</button> {$layout_crumbs[$thiscrumb]}
		{/if}
		
		<center> <h1>
			{$boardname}</h1>
		</center>
		
	</th></tr>
	</table>

	{$layout_contents}

	</div>
<table class="layout-table" style="line-height: 1.4em;">
			<tr class="cell1 link">
			<td style="text-align: center;">
				{$layout_credits}<hr>
			</td>
			</tr>
			<tr class="cell1 link">
			<td style="text-align: center;">
				{$mobileswitch}
			</td>
			</tr>
</table>
</div>
