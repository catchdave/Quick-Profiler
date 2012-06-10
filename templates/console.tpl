<script type="text/javascript" src="{$resourcesUri}quick-profiler.js"></script>
<link rel="stylesheet" type="text/css" href="{$resourcesUri}quick-profiler.css" />
<style>
#pqp-metrics td {
	width: {$metricWidthPercent}%;
}
</style>
<div id="pqp-container" style="display:none">
<div id="pqp-inner" class="console">
	<table id="pqp-metrics">
		<tr>
			<td class="green selected" onclick="QP.changeTab('console', this);">
				<var>{$totals.logs.count}</var>
				<h4>Console</h4>
			</td>
			<td class="blue" onclick="QP.changeTab('time', this);">
				<var>{$totals.time.total}</var>
				<h4>Load Time</h4>
			</td>
			<td class="orange" onclick="QP.changeTab('memory', this);">
				<var>{$totals.memory.used}</var>
				<h4>Memory Used</h4>
			</td>
			{foreach from=$modules item=module key=moduleName}
				<td class="{$module.colour}" onclick="QP.changeTab('{$module.type}', this);">
					<var>{$totals.$moduleName.count} {$module.heading}</var>
					<h4>{$moduleName}</h4>
				</td>
			{/foreach}
			<td class="red" onclick="QP.changeTab('files', this);">
				<var>{$totals.files.count} Files</var>
				<h4>Included</h4>
			</td>
			<td class="red" onclick="QP.changeTab('classes', this);">
				<var>{$totals.classes.count} Classes</var>
				<h4>Defined</h4>
			</td>
		</tr>
	</table>
	
	<div id="pqp-console" class="pqp-box green selected">
		{if $totals.logs.count == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
			<tr>
				<td class="alt1"><var>{$data.logs.counts.log}</var><h4>Logs</h4></td>
				<td class="alt2"><var>{$data.logs.counts.error}</var> <h4>Errors</h4></td>
			</tr>
			<tr>
				<td class="alt3"><var>{$data.logs.counts.memory}</var> <h4>Memory</h4></td>
				<td class="alt4"><var>{$data.logs.counts.time}</var> <h4>Time</h4></td>
			</tr>
			</table>
			<table class="main">
				{foreach from=$data.logs.console item=log}
					<tr class="log-{$log.type}">
						<td class="type">{$log.type}</td>
						<td class="{cycle values="alt,"}">
							{if $log.type == 'log'} 
								<div><pre>{$log.data}</pre></div>
							{elseif $log.type == 'memory'}
								<div><pre>{$log.memory}</pre> <em>{$log.dataType}</em>: {$log.name} </div>
							{elseif $log.type == 'time'}
								<div><pre>{$log.time}</pre> <em>{$log.name}</em></div>
							{elseif $log.type == 'error'}
								<div><em>Line {$log.line}</em> : {$log.data} <pre>{$log.file}</pre></div>
							{/if}
						</td>
						</tr>
				{/foreach}
			</table>
		{/if}
	</div>
	
	<div id="pqp-time" class="pqp-box blue">
		{if !isset($data.logs.counts) || $data.logs.counts.time == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
				<tr><td><var>{$totals.time.total}</var><h4>Load Time</h4></td></tr>
				<tr><td class="alt"><var>{$totals.time.allowed} s</var> <h4>Max Execution Time</h4></td></tr>
			</table>
		
			<table class="main">
			{foreach from=$data.logs.console item=log}
				{if $log.type == 'time'}
					<tr class="log-{$log.type}">
						<td class="{cycle values="alt,"}"><b>{$log.time}</b> {$log.name}</td>
					</tr>
				{/if}
			{/foreach}
			</table>
		{/if}
	</div>
	
	{foreach from=$modules item=module key=moduleName}
		{include file="{$module.type}.tpl" subItems=$module.items}
	{/foreach}
	
	<div id="pqp-memory" class="pqp-box orange">
		{if !isset($data.logs.counts) || $data.logs.counts.memory == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class="side">
				<tr><td><var>{$totals.memory.used}</var><h4>Used Memory</h4></td></tr>
				<tr><td class="alt"><var>{$totals.memory.total}</var> <h4>Total Available</h4></td></tr>
			</table>
		
			<table class="main">
			{foreach from=$data.logs.console item=log}
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
				<tr><td><var>{$totals.files.count}</var><h4>Total Files</h4></td></tr>
				<tr><td class="alt"><var>{$totals.files.size}</var> <h4>Total Size</h4></td></tr>
				<tr><td><var>{$totals.files.largest}</var> <h4>Largest</h4></td></tr>
			</table>
			<table class="main">
				{foreach from=$data.files item=file}
					<tr><td class="{cycle values="alt,"}"><b>{$file.size}</b> {$file.name}</td></tr>
				{/foreach}
			</table>
	</div>
	
	<div id="pqp-classes" class="pqp-box red">
			<table class="side">
				<tr><td><var>{$totals.classes.count}</var><h4>Total Classes</h4></td></tr>
				<tr><td class="alt"><var>{$totals.classes.lines}</var> <h4>Total Lines</h4></td></tr>
				<tr><td><var>{$totals.classes.largest}</var> <h4>Largest</h4></td></tr>
			</table>
			<table class="main">
				{foreach from=$data.classes item=class}
					<tr><td class="{cycle values="alt,"}"><b>{$class.lines} lines</b> {$class.name}</td></tr>
				{/foreach}
			</table>
	</div>
	
	<table id="pqp-footer">
		<tr>
			<td class="credit">
				<a href="http://particletree.com" target="_blank">QuickProfiler</a></td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="QP.hide();return false" title="Minimise">-</a>
				<a href="javascript:void(0);" onclick="QP.toggleDetails();return false">Details</a>
				<a href="javascript:void(0);" onclick="QP.toggleHeight();return false">Height</a>
			</td>
		</tr>
	</table>
</div>
<div id="pqp-small-panel" onclick="QP.show();return false" title="Restore panel">
	<a href="javascript:void(0);">+</a>
	<br />{$totals.time.total} <br /> {$totals.memory.used}
</div>
</div>