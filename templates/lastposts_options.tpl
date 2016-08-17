	<div class="smallFonts margin">
		Show posts from within:
		<ul class="pipemenu">
		{foreach $timelinks as $link}
			<li>{$link}
		{/foreach}
		</ul>
		&mdash; 
		<ul class="pipemenu">
		{foreach $misclinks as $link}
			<li>{$link}
		{/foreach}
		</ul>
	</div>
