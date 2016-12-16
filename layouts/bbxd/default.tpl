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
<table id="main" class="layout-table">
<tr>
<td id="main-header" colspan="3">
	<table id="header" class="outline">
		<tr>
			{if $poratext}<td class="cell0 left" colspan="3">
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
								{$poratext}
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
			</td>{else}<td class="cell0 center" colspan="3">
				<table class="layout-table">
				<tr>
				<td>
					<a href="{actionLink page='home'}"><img id="theme_banner" src="{$layout_logopic}" alt="{$boardname}" title="{$boardname}"></a>
				</td>
				</tr>
				</table>
			</td>{/if}
		</tr>
		<tr class="header1">
			<td class="cell0 center" style="width: 140px">
				{$layout_views}
			</td>
			<th id="navBar">
				<div style="display:inline-block; float:right;">
					{if $loguserid}
					<div id="userMenuContainer" class="dropdownContainer">
						<div id="userMenuButton" class="navButton">
							{$loguserlink}
							<i class="icon-caret-down"></i>
						</div>
						<ul class="dropdownMenu">
							{foreach $layout_userpanel as $url=>$text}
								<li><a href="{$url|escape}">{$text}</a>
							{/foreach}
							<li><a href="#" onclick="$('#logout').submit(); return false;">Log out</a>
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
					{else}
					<div id="userMenuContainer" class="dropdownContainer">
						<div id="userMenuButton" class="navButton">
							Guest
							<i class="icon-caret-down"></i>
						</div>
						<ul class="dropdownMenu">
							<li><a href="{actionLink page='register'}">Register</a>
							<li><a href="{actionLink page='login'}">Log in</a>
						</ul>
					</div>
					{/if}
				</div>
				<div id="navMenuContainer">
					<span class="navButton"><a href="{actionLink page='home'}">Home</a></span>
					<div id="userMenuContainer" class="dropdownContainer">
						<div id="userMenuButton" class="navButton">
							<a href="{actionLink page='board'}">Forums</a>
							<i class="icon-caret-down"></i>
						</div>
						<ul class="dropdownMenu">
							{foreach $dropdownlinks as $cat=>$links}
								{foreach $links as $url=>$text}
									<li><a href="{$url|escape}">{$text}</a>
								{/foreach}
							{/foreach}
						</ul>
					</div>
					<div id="userMenuContainer" class="dropdownContainer">
						<div id="userMenuButton" class="navButton">
							<a href="{actionLink page='boardlistlayout'}">Board Listing</a>
							<i class="icon-caret-down"></i>
						</div>
						<ul class="dropdownMenu">
							<li><a href="{actionLink page='board'}">BlargBoard</a>
							<li><a href="{actionLink page='board2'}">RHCafe</a>
						</ul>
					</div>
					{foreach $headerlinks as $url=>$text}
						<span class="navButton"><a href="{$url|escape}">{$text}</a></span>
					{/foreach}
				</div>
			</th>
			<td class="cell0 center" style="width: 140px">
				{$layout_time}
			</td>
		</tr>
		<tr class="cell0">
			<td class="smallFonts center" colspan="3">
				{$layout_onlineusers}{if $layout_birthdays}<br>{$layout_birthdays}{/if}
			</td>
		</tr>
		<tr class="header1"><th id="header-sep" colspan="3"></th></tr>
	</table>
</td>
</tr>

	<tr><td class="crumb-container" colspan="3">
		{$smarty.capture.breadcrumbs}
	</td></tr>

<tr>

<td id="main-page">
	<table id="page-container" class="layout-table">
	<tr><td class="contents-container">
		{$layout_contents}
	</td></tr>
	</table>
</td>
</tr>
	<tr><td class="crumb-container" colspan="3">
		{$smarty.capture.breadcrumbs}
	</td></tr>
<tr>
<td id="main-footer" colspan="3">

	<table id="footer" class="outline">
	<tr>
	<td class="cell2">
		<table class="layout-table" style="line-height: 1.4em;">
			<tr>
			<td style="text-align: left;">
				{$layout_credits}<br>
				{$board_credits}
			</td>
			<td style="text-align: center;">
				{$perfdata}
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
