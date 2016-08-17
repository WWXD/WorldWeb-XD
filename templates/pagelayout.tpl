<table id="main" class="layout-table">
<tr>
<td id="main-header" colspan="2">
	<table id="header" class="outline">
		<tr>
			<td class="cell0 left">
				<table class="layout-table">
				<tr>
				<td>
					<a href="{actionLink page='home'}"><img id="theme_banner" src="{$layout_logopic}" alt="{$boardname}" title="{$boardname}"></a>
				</td>
				<td>
					<table class="outline" id="headerInfo">
						<tr class="header1"><th>{$poratitle}</th></tr>
						<tr>
							<td class="cell1 center">
								{$layout_time} &mdash; {$layout_views}
								{if $layout_birthdays}<br><br>{$layout_birthdays}{/if}
								{if $poratext}<br><br>{$poratext}{/if}
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr class="header1">
			<th id="navBar">
				<div style="display:inline-block; float:right;">
					{if $loguserid}
					{if HasPermission('admin.viewadminpanel')}
					<span class="navButton">
						<a href="{actionLink page='admin'}">Admin</a>
					</span>
					{/if}
					<div id="userMenuContainer" class="dropdownContainer">
						<div id="userMenuButton" class="navButton">
							{$loguserlink}
							<i class="icon-caret-down"></i>
						</div>
						<ul class="dropdownMenu">
							{foreach $layout_userpanel as $url=>$text}
								<li><a href="{$url|escape}">{$text}</a>
							{/foreach}
						</ul>
					</div>
					{$numnotifs=count($notifications)}
					<div id="notifMenuContainer" class="dropdownContainer {if $numnotifs}hasNotifs{else}noNotif{/if}">
						<div id="notifMenuButton" class="navButton">
							Notifications
							<span id="notifCount">{$numnotifs}</span>
							<i class="icon-caret-down"></i>
						</div>
						<ul id="notifList" class="dropdownMenu">
						{if $numnotifs}
							{foreach $notifications as $notif}
								<li>{$notif.text}<br><small>{$notif.formattedDate}</small>
							{/foreach}
						{/if}
						</ul>
					</div>
					<span class="navButton">
						<a href="#" onclick="$('#logout').submit(); return false;">Log out</a>
					</span>
					{else}
					<span class="navButton">
						<a href="{actionLink page='register'}">Register</a>
					</span>
					<span class="navButton">
						<a href="{actionLink page='login'}">Log in</a>
					</span>
					{/if}
				</div>
				<div id="navMenuContainer">
					{foreach $headerlinks as $url=>$text}
						<span class="navButton"><a href="{$url|escape}">{$text}</a></span>
					{/foreach}
				</div>
			</th>
		</tr>
		<tr class="cell0">
			<td class="smallFonts center">
				{$layout_onlineusers}
			</td>
		</tr>
		<tr class="header1"><th id="header-sep"></th></tr>
	</table>
</td>
</tr>

<tr>
<td id="main-sidebar">
	<table id="sidebar" class="outline">
		<tr>
			<td class="cell1">
				<table class="outline margin">
					{foreach $sidelinks as $cat=>$links}
					<tr class="header0"><th>{$cat}</th></tr>
						{foreach $links as $url=>$text}
							<tr class="cell{cycle values='2,1'}"><td><a href="{$url|escape}">{$text}</a></td></tr>
						{/foreach}
					{/foreach}
				</table>
			</td>
		</tr>
	</table>
</td>

{capture "breadcrumbs"}
{if $layout_crumbs || $layout_actionlinks}
		<table class="outline breadcrumbs"><tr class="header1">
			<th>
				{if $layout_actionlinks && count($layout_actionlinks)}
				<div class="actionlinks" style="float:right;">
					<ul class="pipemenu smallFonts">
					{foreach $layout_actionlinks as $alink}
						<li>{$alink}
					{/foreach}
					</ul>
				</div>
				{/if}
				{if $layout_crumbs && count($layout_crumbs)}
				<ul class="crumbLinks">
				{foreach $layout_crumbs as $url=>$text}
					<li><a href="{$url|escape}">{$text}</a>
				{/foreach}
				</ul>
				{/if}
			</th>
		</tr></table>
{/if}
{/capture}

<td id="main-page">
	<table id="page-container" class="layout-table">
	<tr><td class="crumb-container">
		{$smarty.capture.breadcrumbs}
	</td></tr>
	<tr><td class="contents-container">
		{$layout_contents}
	</td></tr>
	<tr><td class="crumb-container">
		{$smarty.capture.breadcrumbs}
	</td></tr>
	</table>
</td>
</tr>

<tr>
<td id="main-footer" colspan="2">

	<table id="footer" class="outline">
	<tr>
	<td class="cell2">
		<table class="layout-table" style="line-height: 1.4em;">
			<tr>
			<td style="text-align: left;">
				{$layout_credits}
				<!-- {$perfdata} -->
			</td>
			<td style="text-align: right;">
				{$mobileswitch}
			</td>
		</table>
	</td>
	</tr>
	</table>

</td>
</tr>
</table>
