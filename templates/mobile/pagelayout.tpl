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
			<tr class="header1"><th style="height:5px;"></th></tr>

			{if $layout_actionlinks}
				{foreach $layout_actionlinks as $link}
					<tr><td class="cell{cycle values='1,2'} link">{$link}</td></tr>
				{/foreach}
				
				<tr class="header1"><th style="height:5px;"></th></tr>
			{/if}
			
			{foreach $sidelinks as $cat=>$links}
				{if !$links@first}
				<tr class="header1"><th>{$cat}</th></tr>
				{/if}
				{foreach $links as $url=>$text}
					<tr><td class="cell{cycle values='1,2'} link"><a href="{$url|escape}">{$text}</a></td></tr>
				{/foreach}
			{/foreach}
			
			{if $loguserid}
				<tr class="header1"><th>{$loguserlink}</th></tr>
				{foreach $layout_userpanel as $url=>$text}
					<tr><td class="cell{cycle values='1,2'} link"><a href="{$url|escape}">{$text}</a></td></tr>
				{/foreach}
				<tr><td class="cell{cycle values='1,2'} link"><a href="#" onclick="$('#logout').submit(); return false;">Log out</a></td></tr>
			{else}
				<tr><td class="cell{cycle values='1,2'} link"><a href="{actionLink page='register'}">Register</a></td></tr>
				<tr><td class="cell{cycle values='1,2'} link"><a href="{actionLink page='login'}">Log in</a></td></tr>
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
		
	</th></tr>
	</table>

	{$layout_contents}

	</div>
	<div class="footer" style="clear:both; margin-bottom:1.2em;">
		{$mobileswitch}
	</div>
</div>
