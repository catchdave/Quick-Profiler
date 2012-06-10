<div id="pqp-{$moduleName}" class="pqp-box green module-cache">
	<table class="side">
		<tr><td><var>{$totals.$moduleName.get}</var><h4>Total Gets</h4></td></tr>
		<tr><td class="alt"><var>{$totals.$moduleName.get}</var><h4>Total Sets</h4></td></tr>
		<tr><td><var>{$totals.$moduleName.time}</var> <h4>Total Time</h4></td></tr>
		<tr><td class="alt"><var>{$totals.$moduleName.duplicates}</var> <h4>Duplicates</h4></td></tr>
	</table>
	{if $totals.$moduleName.count == 0}
		<h3>This panel has no log items.</h3>
	{else}
		<table class="main">
		{foreach from=$subItems item=query}
				<tr>
					<td class="{%cycle values="alt,"%}">
						<b>{% $query.time %}<span> | </span>{% $query.memory %}</b>
						<span class="{% $query.type %}">{% $query.type %}</span>
						<a href="javascript:void(0);" onclick="PQP.toggleValue(this)">{% $query.name %}</a>
						<div class="details pQp-hide">{% $query.value|escape:'html' %}</div>
					</td>
				</tr>
		{/foreach}
		</table>
	{/if}
</div>
