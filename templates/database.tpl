<div id="pqp-{$moduleName}" class="pqp-box purple module-database">
	<table class="side">
		<tr><td><var>{$totals.$moduleName.count}</var><h4>Total Queries</h4></td></tr>
		<tr><td class="alt"><var>{$totals.$moduleName.time}</var> <h4>Total Time</h4></td></tr>
		<tr><td><var>{$totals.$moduleName.duplicates}</var> <h4>Duplicates</h4></td></tr>
	</table>
	{if $totals.$moduleName.count == 0}
	<h3>This panel has no log items.</h3>
	{else}
		<table class="main">
		{foreach from=$subItems item=query}
				<tr>
					<td class="{cycle values="alt,"}">
						{%if $query.meta%}<span class="red">{%$query.meta%}</span>{%/if%}
						<b>{%$query.time%}<span> | </span>{%$query.memory%}</b>
						{$query.sql}
						{if $query.explain}
						<em>
							Possible keys: <b>{$query.explain.possible_keys}</b> &middot; 
							Key Used: <b>{$query.explain.key}</b> &middot; 
							Type: <b>{$query.explain.type}</b> &middot; 
							Rows: <b>{$query.explain.rows}</b> &middot; 
						</em>
						{/if}
					</td>
				</tr>
		{/foreach}
		</table>
	{/if}
</div>
