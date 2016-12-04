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


<td id="main-page">
	<table id="page-container" class="layout-table">
	<tr><td class="contents-container">
	<table>
		{if $poratext}<tr class="cell2 left" colspan="3">
			
			<td colspan="4"><a href="/"><img id="theme_banner" src="{$layout_logopic}" alt="{$boardname}" title="{$boardname}"></a></td>
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
		{else}<tr class="cell2 center" colspan="3">
			
			<td colspan="4"><a href="/"><img id="theme_banner" src="{$layout_logopic}" alt="{$boardname}" title="{$boardname}"></a></td>
		</tr>
		{/if}
				<tr>
			<td class="cell1 center" style="width:75px">{$layout_time}</td>
			<td class="cell1 center">
				<span class="navButton"><a href="{actionLink page='home'}">Home</a></span> |
				 <span class="navButton"><a href="{actionLink page='board'}">Forums</a></span> |
				 {foreach $dropdownlinks as $cat=>$links}
					{foreach $links as $url=>$text}
						<span class="navButton"><a href="{$url|escape}">{$text}</a></span> | 
					{/foreach}
				 {/foreach}
				 {foreach $headerlinks as $url=>$text}
						<span class="navButton"><a href="{$url|escape}">{$text}</a></span> | 
				 {/foreach}
			</td>
			<td class="center cell1" style="width:75px;"><span>{$layout_views}</span></td>
			
				
			
		</tr><tr class="cell2 center" colspan="3">
			<td colspan="4">{if $loguserid}
							{$loguserlink} : 
							{foreach $layout_userpanel as $url=>$text}
								<span class="navButton"><a href="{$url|escape}">{$text}</a></span> | 
							{/foreach}
							<span class="navButton"><a href="#" onclick="$('#logout').submit(); return false;">Log out</a></span>
					{else}
							Guest: <span class="navButton"><a href="{actionLink page='register'}">Register</a></span> | 
							<span class="navButton"><a href="{actionLink page='login'}">Log in</a></span>
						</ul>
					</div>
					{/if}</td>
		</tr>
		<tr class="cell1 center" colspan="3">
			<td colspan="4">{$layout_onlineusers}</td>
		</tr>
		{$numnotifs=count($notifications)}
		{if $numnotifs}
		<tr class="cell2 center" colspan="3">
			<td colspan="4">You have {$numnotifs} new notification:</td>
		</tr>
		{foreach $notifications as $notif}
			<tr class="cell{cycle values='1,2'} center" colspan="3">
				<td colspan="4">{$notif.text}<br>This was sent on {$notif.formattedDate}</td>
			</tr>
		{/foreach}
		{/if}
		<tr class="cell2 center" colspan="3">
			<td colspan="4" class="crumb-container">{$smarty.capture.breadcrumbs}</td>
		</tr>
			<tr colspan="4"><td class="contents-container" colspan="4">
		{$layout_contents}
	</td></tr>
	<tr colspan="2">
<td id="main-footer" colspan="4">

	<table id="footer" class="outline">
	{if $smarty.capture.breadcrumbs}<tr class="crumb-container cell1"><td>
		{$smarty.capture.breadcrumbs}
	</td></tr>{/if}
	<tr>
	<td class="cell2">
		<table class="layout-table" style="line-height: 1.4em;">
			<tr>
			<td style="text-align: left;">
				{$layout_credits}<br>
				{$board_credits}<br>
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
</td>
</tr>
	
			</table>
	</td></tr>

</table>