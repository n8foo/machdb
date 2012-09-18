{* Smarty *}

{include file="header.tpl"}

<center>
<table><tr>
{foreach from=$hoststats item="hoststats"}
<th>Hosts Active: 
<td>{$hoststats.hostcount}
<th>Total GB RAM: 
<td>{$hoststats.mem/1024|string_format:"%.0f"}
{/foreach}
{foreach from=$cpustats item="cpustats"}
<th>Total MHz: 
<td>{$cpustats.speed} 
{/foreach}
</tr></table>
</center>

<table><tr><td valign=top>
<table border=0>
	<CAPTION>Recent Host Changes</CAPTION>
	<tr>
		<th>host</th>
		<th>domain</th>
		<th>CPU</th>
		<th>RAM</th>
		<th>OS</th>
		<th>time</th>
	</tr>
	{foreach from=$host item="host"}
		<tr bgcolor="{cycle values="#FFFFFF,#EEEEEE" advance=true}">
			<td><a href="host.php?id={$host.id}">{$host.hostname}</a></td>
			<td>{$host.domain}</td>
			<td>{$host.cpu_count} x {$host.cpu_speed/1000|string_format:"%.1f"} Ghz</td>
			<td>{$host.memtotal/1024|string_format:"%.1f"} GB</td>
			<td>{$host.os_name}</td>
			<td><a href="history.php?host_id={$host.id}">{$host.timestamp|date_format:"%b %e, %Y %H:%M:%S"}</a></td>
		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
</table>

<td>

<table border=0>
	<CAPTION>Recent Disk Changes</CAPTION>
	<tr>
		<th>host</th>
		<th>device</th>
		<th>size</th>
		<th>disk model</th>
		<th>timestamp</th>
		<th>status</th>
	</tr>
	{foreach from=$disk item="disk"}
		<tr bgcolor="{cycle values="#FFFFFF,#EEEEEE" advance=true}">
			<td><a href="host.php?id={$disk.host_id}">{$disk.hostname}</a></td>
			<td>{$disk.device}</td>
			<td>{$disk.size/1024/1024|string_format:"%.1f"} GB</td>
			<td>{$disk.model}</td>
			<td><a href="history.php?host_disk_id={$disk.id}">{$disk.timestamp|date_format:"%b %e, %Y %H:%M:%S"}</a></td>
{if $disk.status eq '1'}
        <td><font color=green>online</font></a></td>
{elseif $disk.status eq '0'}  
        <td><font color=red>offline</font></a></td>
{else}
	<td>N/A</td>
{/if}

		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
</table>

<table border=0>
	<CAPTION>Recent Network Changes</CAPTION>
	<tr>
		<th>host</th>
		<th>interface</th>
		<th>IP</th>
		<th>timestamp</th>
		<th>status</th>
	</tr>
	{foreach from=$nic item="nic"}
		<tr bgcolor="{cycle values="#FFFFFF,#EEEEEE" advance=true}">
			<td><a href="host.php?id={$nic.host_id}">{$nic.hostname}</a></td>
			<td>{$nic.interface}</td>
			<td>{$nic.ipaddr}</td>
			<td><a href="history.php?nic_id={$nic.id}">{$nic.timestamp|date_format:"%b %e, %Y %H:%M:%S"}</a></td>
{if $nic.status eq '1'}
        <td><font color=green>online</font></a></td>
{elseif $nic.status eq '0'}  
        <td><font color=red>offline</font></a></td>
{else}
	<td>N/A</td>
{/if}

		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
</table>

</table>

{include file="footer.tpl"}
