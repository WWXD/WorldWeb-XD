{$numnotifs=count($notifications)}
<style>
img {
  max-width: 100%
}

input, textarea {
  max-width:100%
}
</style>
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
					<tr><td class="cell{cycle values='1,2'} link"><a href="{$url|escape}"{if isset($link.id)} id="{$link.id}"{/if}>{if isset($link.icon)}<span class="fa fa-{$link.icon}"></span>{/if}{$link.text}</a></td></tr>
				{/foreach}
			{/foreach}

			{if $loguserid}
				<tr class="header1"><th><i class="fa fa-user" aria-hidden="true"></i> {$loguserlink}</th></tr>
				{foreach $layout_userpanel as $url=>$text}
					<tr><td class="cell{cycle values='1,2'} link"><a href="{$url|escape}">{$text}</a></td></tr>
				{/foreach}
				<tr><td class="cell{cycle values='1,2'} link"><a href="#" onclick="$('#logout').submit(); return false;">Log out</a></td></tr>
			{else}
				<tr class="header1"><th><i class="fa fa-user" aria-hidden="true"></i> Guest</th></tr>
				<tr><td class="cell{cycle values='1,2'} link"><a href="{actionLink page='register'}">Register</a></td></tr>
				<tr><td class="cell{cycle values='1,2'} link"><a href="{actionLink page='login'}">Log in</a></td></tr>
			{/if}
		</table>
	</div>
	</div>

<table id="main" class="layout-table">
<tr>
<td id="main-header" colspan="3">
	<table id="header" class="outline">
		<tr>
			<td class="cell0 center" colspan="3">
				<table class="layout-table">
				<tr>
				<td>
					<a href="{actionLink page='home'}"><h1>{$boardname}</h1></a>
				</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr class="header1">
			<td class="cell0 center" style="width: 140px">
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
</td>
		</tr>
	</table>
</td>
</tr>
<tr>

<td id="main-page">
	<table id="page-container" class="layout-table">
	<tr><td class="contents-container">
		{$layout_contents}
	</td></tr>
	</table>
</td>
</tr>
<tr>
<td id="main-footer" colspan="3">

	<table id="footer" class="outline">
	<tr>
	<td class="cell2">
		<table class="layout-table" style="line-height: 1.4em;">
			<tr>
			<td style="text-align: center;">
				{$layout_credits}<br>
				{$board_credits}<hr><br>
			</td>
</tr><tr>
			<td style="text-align: center;">
				<br>
				{$perfdata}
				<br><br><hr>
			</td></tr><tr>
			<td style="text-align: center;">
				{$mobileswitch}
			</td></tr>
		</table>
	</td>
	</tr>
	</table>

</td>
</tr>
</table>
