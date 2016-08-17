
	<table class="outline margin editfora_list" style="width:49.7%;">
		<tbody>
		<tr class="header1">
			<th>Edit forum list</th>
		</tr>
		<tr class="cell2">
			<td>
				<span style="float:right;">
					{$btnNewForum}
					{$btnNewCategory}
				</span>
			</td>
		</tr>
		</tbody>
		
		{foreach $boards as $bid=>$board}
		<tr class="header0"><th>{$board.name}</th></tr>
			
			{foreach $cats.{$bid} as $cid=>$cat}
			<tbody id="cat{$cid}" class="c">
				<tr class="cell{cycle values='0,1'}">
					<td class="c" style="cursor: pointer;" onmousedown="pickCategory({$cid});">
						<strong>{$cat.name}</strong>
					</td>
				</tr>
				
				{foreach $forums.{$cid} as $fid=>$forum}
				
				{$fpadding=24*$forum.level}
				<tr class="cell{cycle values='0,1'}">
					<td style="cursor: pointer; padding-left: {$fpadding}px;{if $fid==$selectedForum} outline: 1px solid #888;{/if}" class="f" onmousedown="pickForum({$fid});" id="forum{$fid}">
						{$forum.title}<br>
						<small style="opacity: 0.75;">{$forum.description}</small>
					</td>
				</tr>
				
				{foreachelse}
				<tr class="cell{cycle values='0,1'}">
					<td style="padding-left: 24px;" class="f">
						<span style="opacity:0.75;">(no forums in this category)</span>
					</td>
				</tr>
				{/foreach}
				
			</tbody>
			{/foreach}
			
		{/foreach}
		
		<tbody>
		<tr class="header1"><th style="height:5px;"></th></tr>
		<tr class="cell2">
			<td>
				<span style="float:right;">
					{$btnNewForum}
					{$btnNewCategory}
				</span>
			</td>
		</tr>
		</tbody>
	</table>
