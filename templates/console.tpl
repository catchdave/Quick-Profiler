<script type="text/javascript" src="quick-profiler.js"></script>
<div id="pqp-container" style="display:none">
<div id="pqp-inner" class="console">
	<table id="pqp-metrics">
		<tr>
			<td class="green selected" onclick="PQP.changeTab('console');">
				<var>{$logs.console|@count}</var>
				<h4>Console</h4>
			</td>
			<td class="blue" onclick="PQP.changeTab('speed');">
				<var>{$totals.speed.total}</var>
				<h4>Load Time</h4>
			</td>
			<td class="purple" onclick="PQP.changeTab('database');">
				<var>{$totals.database.count} Queries</var>
				<h4>Database</h4>
			</td>
			<td class="orange" onclick="PQP.changeTab('memory');">
				<var>{$totals.memory.used}</var>
				<h4>Memory Used</h4>
			</td>
			<td class="red" onclick="PQP.changeTab('files');">
				<var>{$totals.file.count} Files</var>
				<h4>Included</h4>
			</td>
		</tr>
	</table>
	
	<div id="pqp-console" class="pqp-box green selected">
		{if $logs.console|@count == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
			<tr>
				<td class="alt1"><var>{$logs.logCount}</var><h4>Logs</h4></td>
				<td class="alt2"><var>{$logs.errorCount}</var> <h4>Errors</h4></td>
			</tr>
			<tr>
				<td class="alt3"><var>{$logs.memoryCount}</var> <h4>Memory</h4></td>
				<td class="alt4"><var>{$logs.speedCount}</var> <h4>Speed</h4></td>
			</tr>
			</table>
			<table class="main">
				{foreach from=$logs.console item=log}
					<tr class="log-{$log.type}">
						<td class="type">{$log.type}</td>
						<td class="{cycle values="alt,"}">
							{if $log.type == 'log'} 
								<div><pre>{$log.data}</pre></div>
							{elseif $log.type == 'memory'}
								<div><pre>{$log.memory}</pre> <em>{$log.dataType}</em>: {$log.name} </div>
							{elseif $log.type == 'speed'}
								<div><pre>{$log.speed}</pre> <em>{$log.name}</em></div>
							{elseif $log.type == 'error'}
								<div><em>Line {$log.line}</em> : {$log.data} <pre>{$log.file}</pre></div>
							{/if}
						</td>
						</tr>
				{/foreach}
			</table>
		{/if}
	</div>
	
	<div id="pqp-speed" class="pqp-box blue">
		{if $logs.speedCount == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
				<tr><td><var>{$totals.speed.total}</var><h4>Load Time</h4></td></tr>
				<tr><td class="alt"><var>{$totals.speed.allowed} s</var> <h4>Max Execution Time</h4></td></tr>
			</table>
		
			<table class="main">
			{foreach from=$logs.console item=log}
				{if $log.type == 'speed'}
					<tr class="log-{$log.type}">
						<td class="{cycle values="alt,"}"><b>{$log.speed}</b> {$log.name}</td>
					</tr>
				{/if}
			{/foreach}
			</table>
		{/if}
	</div>
	
	<div id="pqp-database" class="pqp-box purple">
		{if $totals.database.count == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
			<tr><td><var>{$totals.database.count}</var><h4>Total Queries</h4></td></tr>
			<tr><td class="alt"><var>{$totals.database.time}</var> <h4>Total Time</h4></td></tr>
			<tr><td><var>{$totals.database.duplicates}</var> <h4>Duplicates</h4></td></tr>
			</table>
			
			<table class="main">
			{foreach from=$database item=query}
					<tr>
						<td class="{cycle values="alt,"}">
							{$query.sql}
							{if $query.explain}
							<em>
								Possible keys: <b>{$query.explain.possible_keys}</b> &middot; 
								Key Used: <b>{$query.explain.key}</b> &middot; 
								Type: <b>{$query.explain.type}</b> &middot; 
								Rows: <b>{$query.explain.rows}</b> &middot; 
								Speed: <b>{$query.time}</b>
							</em>
							{/if}
						</td>
					</tr>
			{/foreach}
			</table>
		{/if}
	</div>

	<div id="pqp-memory" class="pqp-box orange">
		{if $logs.memoryCount == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
				<tr><td><var>{$totals.memory.used}</var><h4>Used Memory</h4></td></tr>
				<tr><td class="alt"><var>{$totals.memory.total}</var> <h4>Total Available</h4></td></tr>
			</table>
		
			<table class="main">
			{foreach from=$logs.console item=log}
				{if $log.type == 'memory'}
					<tr class="log-{$log.type}">
						<td class="{cycle values="alt,"}"><b>{$log.memory}</b> <em>{$log.dataType}</em>: {$log.name}</td>
					</tr>
				{/if}
			{/foreach}
			</table>
		{/if}
	</div>

	<div id="pqp-files" class="pqp-box red">
			<table class="side">
				<tr><td><var>{$totals.file.count}</var><h4>Total Files</h4></td></tr>
				<tr><td class="alt"><var>{$totals.file.size}</var> <h4>Total Size</h4></td></tr>
				<tr><td><var>{$totals.file.largest}</var> <h4>Largest</h4></td></tr>
			</table>
			<table class="main">
				{foreach from=$files item=file}
					<tr><td class="{cycle values="alt,"}"><b>{$file.size}</b> {$file.name}</td></tr>
				{/foreach}
			</table>
	</div>
	
	<table id="pqp-footer">
		<tr>
			<td class="credit">
				<a href="http://particletree.com" target="_blank">QuickProfiler</a></td>
			<td class="actions">
				<a href="#" onclick="PQP.toggleDetails();return false">Details</a>
				<a class="heightToggle" href="#" onclick="PQP.toggleHeight();return false">Height</a>
			</td>
		</tr>
	</table>
</div>
</div>