{capture "breadcrumbs"}
{if $layout_crumbs || $layout_actionlinks}
		<div class="w3-bar w3-WWXD-theme">
				{if $layout_actionlinks && count($layout_actionlinks)}
				<div class="actionlinks w3-button w3-right">
					<ul class="pipemenu smallFonts">
					{foreach $layout_actionlinks as $alink}
						<li>{$alink}
					{/foreach}
					</ul>
				</div>
				{/if}
				{if $layout_crumbs && count($layout_crumbs)}
				<ul class="crumbLinks w3-button">
				{foreach $layout_crumbs as $url=>$text}
					<li><a href="{$url|escape}">{$text}</a>
				{/foreach}
				</ul>
				{/if}
		</div>
{else}<div class="w3-bar w3-WWXD-theme" style="min-height: 2px;"></div>{/if}
{/capture}

<div style="margin: 10px 10px 10px 10px;">
	{if $poratext}
		<a href="{pageLink name='home'}">{$logo}</a>
		<table class="w3-table-all w3-centered" style="float: right;">
			<tr><th>{$poratitle}</th></tr>
			<tr><td>{$poratext}</td></tr>
		</table>
	{else}
		<center><a href="{pageLink name='home'}">{$logo}</a></center>
	{/if}
</div>
<div class="w3-bar w3-WWXD-theme">
	<a href="/" class="w3-bar-item w3-button w3-mobile">Home</a>
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button w3-mobile">Forums <i class="icon-caret-down"></i></button>
		<div class="w3-dropdown-content w3-bar-block w3-card-4 w3-mobile">
			<a href="{actionLink page='board'}" class="w3-bar-item w3-button w3-mobile">Index</a>
			<a href="{actionLink page='faq'}" class="w3-bar-item w3-button w3-mobile">FAQ</a>
			<a href="{actionLink page='lastposts'}" class="w3-bar-item w3-button w3-mobile">Latest posts</a>
			<a href="{actionLink page='search'}" class="w3-bar-item w3-button w3-mobile">Search</a>
			<a href="{actionLink page='memberlist'}" class="w3-bar-item w3-button w3-mobile">Member list</a>
			<a href="{actionLink page='ranks'}" class="w3-bar-item w3-button w3-mobile">Ranks</a>
		</div>
	</div>
	{foreach $headerlinks as $url=>$text}
		<a href="{$url|escape}" class="w3-mobile w3-bar-item w3-button">{$text}</a>
	{/foreach}
	{if $loguserid}
		<div class="w3-dropdown-hover w3-right w3-mobile">
			<button class="w3-button w3-mobile">{$loguserlink} <i class="icon-caret-down"></i></button>
			<div class="w3-dropdown-content w3-bar-block w3-card-4 w3-mobile">
				{foreach $layout_userpanel as $url=>$text}
					<a href="{$url|escape}" class="w3-bar-item w3-button w3-mobile">{$text}</a>
				{/foreach}
				<a href="#" onclick="$('#logout').submit(); return false;" class="w3-bar-item w3-button w3-mobile">Log out</a>
			</div>
		</div> <!-- Too lazy to convert this to W3CSS RN. I'll convert it later -->
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
		<div class="w3-dropdown-hover w3-right w3-mobile">
			<button class="w3-button w3-mobile">Guest <i class="icon-caret-down"></i></button>
			<div class="w3-dropdown-content w3-bar-block w3-card-4 w3-mobile">
				<a href="{actionLink page='login'}" class="w3-bar-item w3-button w3-mobile">Login</a>
				<a href="{actionLink page='register'}" class="w3-bar-item w3-button w3-mobile">Register</a>
			</div>
		</div>
	{/if}
</div>
<center>{$layout_onlineusers}{if $layout_birthdays}<br>{$layout_birthdays}{/if}</center>
<div id="main-page">
	<div class="crumb-container">{$smarty.capture.breadcrumbs}</div>
	<div class="contents-container">{$layout_contents}</div>
	<div class="crumb-container">{$smarty.capture.breadcrumbs}</div>
</div>
<div class="w3-bar w3-WWXD-theme">
	<div class="w3-button">{$layout_credits}<br>
	{$board_credits}</div>
	<div class="w3-button w3-right">{$mobileswitch}</div>
</div>
