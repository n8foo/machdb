{include file="header.tpl"}

<h1>{$host.hostname}.{$host.domain}</h1>
<table><tr><td valign=top>
<table>
<tr>
<th>Last Updated  	</th><td> <a href="history.php?host_id={$host.id}">{$host.timestamp}</a></td>
<tr>
{if $bios.name}
<th>Name	</th><td>{$bios.name}</td>
<th>Vendor	</th><td>{$bios.vendor}</td>
</tr>
	{if $bios.serial}
<tr>
<th>Serial #	</th><td>{$bios.serial}</td>
<th>UUID	</th><td>{$bios.uuid}</td>
</tr>
	{/if}
{/if}
<tr>
<th>OS  	</th><td> <a href="browse.php?os={$os.name}">{$os.name}</a></td>
<th>kernel	</th><td> <a href="browse.php?kernel={$os.kernel}">{$os.kernel}</a></td>
</tr>
<tr>
<th>RAM 	</th><td> <a href="browse.php?memtotal={$host.memtotal}">{$host.memtotal/1024|string_format:"%.1f"} GB</a></td>
<th>swap	</th><td> <a href="browse.php?swaptotal={$host.swaptotal}">{$host.swaptotal/1024|string_format:"%.1f"} GB</a></td>
</tr>
<tr>
<th>arch	</th><td> <a href="browse.php?arch={$os.arch}">{$os.arch}</a></td>
<th>CPU		</th><td colspan=3> <a href="browse.php?cpu_id={$cpu.id}">{$cpu.name}
			<br>{$host.cpu_count} X {$cpu.speed} Mhz with {$cpu.cache} Kb {if $cpu.unified_cache} unified {/if} cache</a></td>
</tr>
{if $os.basearch}
<tr>
<th>basearch    </th><td> <a href="browse.php?basearch={$os.basearch}">{$os.basearch}</a></td>
<th>&nbsp;</th><td colspan=3>&nbsp;</td>
</tr>
{/if}
</table>

{if $snmp}
<td valign=top>
<a href=# onClick="snmpCollect.update('h={$host.hostname}.{$host.domain}');"><img border=0 src="icons/information3.gif"></a>
<pre>
<span id='snmpContainer'>
</span>
</td>
{/if}


</table>

<table class="sortable">
<CAPTION>Disk Drives</CAPTION>
<thead>
<tr>
<th>device</th>
<th>model</th>
<th>size</th>
<th>type</th>
<th>timestamp</th>
</tr>
</thead>
<tbody>
{foreach from=$disk item="disk"}
	<tr>
		<td>{$disk.device}</td>
		<td><a href="browse.php?disk_id={$disk.id}">{$disk.model}</a></td>
		<td><a href="browse.php?disk_size={$disk.size}">{$disk.size/1024/1024|string_format:"%.1f"} GB</a></td>
		<td><a href="browse.php?disk_type={$disk.type}">{$disk.type}</a></td>
		<td><a href="history.php?host_disk_id={$disk.host_disk_id}">{$disk.timestamp}</a></td>
	</tr>
{foreachelse}
        <tr>
		<td colspan=3>no records</td>
	</tr>
{/foreach}
</tbody>
</table>

<table class="sortable">
<CAPTION>Filesystem Mount Points</CAPTION>
<tr>
<th>mountpoint</th>
<th>size</th>
<th>type</th>
<th>device</th>
<th>timestamp</th>
</tr>
{foreach from=$fs item="fs"}
	<tr>
		<td>{$fs.mountpoint}</td>
		<td>{$fs.size/1024/1024|string_format:"%.1f"} GB</td>
		<td>{$fs.type}</td>
		<td>{$fs.device}</td>
		<td>{$fs.timestamp}</td>
	</tr>
{foreachelse}
        <tr>
		<td colspan=3>no records</td>
	</tr>
{/foreach}
</table>

<table class="sortable">
<CAPTION>Network Interfaces</CAPTION>
<tr>
<th>interface</th>
<th>macaddr</th>
<th>ipaddr</th>
<th>netmask</th>
<th>broadcast</th>
<th>timestamp</th>
</tr>
{foreach from=$nic item="nic"}
	<tr>
		<td>{$nic.interface}</td>
		<td><a href="browse.php?macaddr={$nic.macaddr}">{$nic.macaddr}</a></td>
		<td><a href="browse.php?ipaddr={$nic.ipaddr}">{$nic.ipaddr}</a></td>
		<td><a href="browse.php?netmask={$nic.netmask}">{$nic.netmask}</a></td>
		<td><a href="browse.php?broadcast={$nic.broadcast}">{$nic.broadcast}</a></td>
		<td><a href="history.php?nic_id={$nic.id}">{$nic.timestamp}<a/></td>
	</tr>
{foreachelse}
        <tr>
                <td colspan=3>no records</td>
        </tr>
{/foreach}
</table>

<span id=pkgContainer>
<a href=#pkgContainer onClick="pkgList.update('id={$host.id}');">Software Packages</a>
<script language="JavaScript" type="text/javascript">
   var snmpCollect = new ajaxObject('snmpContainer', 'snmp.php');
   var pkgList = new ajaxObject('pkgContainer', 'pkg.php');
</script>
</span>
{include file="footer.tpl"}
