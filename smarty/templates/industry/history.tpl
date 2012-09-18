{* Smarty *}

{include file="header.tpl"}
<table class="sortable">


{if $disk}
<tr>
<th>hostname</th>
<th>device</th>
<th>model</th>
<th>size</th>
<th>type</th>
<th>timestamp</th>
<th>status</th>
</tr>
{foreach from=$disk item="disk"}
<tr>
	<td><a href="host.php?id={$disk.host_id}">{$disk.hostname}</a></td>
	<td>{$disk.device}</td>
	<td><a href="browse.php?disk_id={$disk.disk_id}">{$disk.model}</a></td>
	<td><a href="browse.php?disk_size={$disk.size}">{$disk.size}</a></td>
	<td><a href="browse.php?disk_type={$disk.type}">{$disk.type}</a></td>
	<td><a href="history.php?host_disk_id={$disk.host_disk_id}">{$disk.timestamp}</a></td>
{if $disk.status eq '1'}
	<td><font color=green>online</font></a></td>
{else}
	<td><font color=red>offline</font></a></td>
{/if}
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}

{if $nic}
<tr>
<th>hostname</th>
<th>interface</th>
<th>macaddr</th>
<th>ipaddr</th>
<th>netmask</th>
<th>broadcast</th>
<th>timestamp</th>
</tr>
{foreach from=$nic item="nic"}
<tr>
	<td><a href="host.php?id={$nic.host_id}">{$nic.hostname}</a></td>
	<td>{$nic.interface}</td>
	<td><a href="browse.php?macaddr={$nic.macaddr}">{$nic.macaddr}</a></td>
	<td><a href="browse.php?ipaddr={$nic.ipaddr}">{$nic.ipaddr}</a></td>
	<td><a href="browse.php?netmask={$nic.netmask}">{$nic.netmask}</a></td>
	<td><a href="browse.php?broadcast={$nic.broadcast}">{$nic.broadcast}</a></td>
	<td><a href="history.php?nic_id={$nic.id}">{$nic.timestamp}<a/></td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}




{if $host}
<tr>
<th>hostname</th>
<th>domain</th>
<th>CPU</th>
<th>Mhz</th>
<th>KB cache</th>
<th>RAM</th>
<th>SWAP</th>
<th>OS</th>
<th>kernel</th>
<th>architecture</th>
<th>basearch</th>
<th>timestamp</th>
</tr>
{foreach from=$host item="host"}
<tr>
	<td>{$host.hostname}</td>
	<td>{$host.domain}</td>
{if $host.shortname}
	<td><a href="browse.php?cpu_id={$host.cpu_id}">{$host.shortname}</a></td>
{else}
	<td><a href="browse.php?cpu_id={$host.cpu_id}">{$host.cpu_name}</a></td>
{/if}
	<td><a href="browse.php?cpu_id={$host.cpu_id}">{$host.speed}</a></td>
	<td><a href="browse.php?cpu_id={$host.cpu_id}">{$host.cache}</a></td>
	<td><a href="browse.php?memtotal={$host.memtotal}">{$host.memtotal}</a></td>
	<td><a href="browse.php?swaptotal={$host.swaptotal}">{$host.swaptotal}</a></td>
	<td><a href="browse.php?os={$host.os_name}">{$host.os_name}</a></td>
	<td><a href="browse.php?kernel={$host.kernel}">{$host.kernel}</a></td>
	<td><a href="browse.php?arch={$host.arch}">{$host.arch}</a></td>
	<td>{$host.basearch}</td>
	<td>{$host.timestamp}</td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}

</table>


</table>


{include file="footer.tpl"}

