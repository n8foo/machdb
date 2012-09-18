{* Smarty *}

{include file="header.tpl"}
<table class="sortable">

{if $cpu}
<tr>
	<th>hostname</th>
	<th>num</th>
	<th>cpu</th>
	<th>speed</th>
	<th>cache</th>
	<th>RAM</th>
	<th>swap</th>
</tr>
{foreach from=$cpu item="cpu"}
<tr>
	<td><a href="host.php?id={$cpu.host_id}">{$cpu.hostname}</a></td>
	<td><a href="browse.php?cpu_count={$cpu.cpu_count}">{$cpu.cpu_count}</a></td>
	{if $cpu.shortname}
	<td><a href="browse.php?cpu_id={$cpu.id}">{$cpu.shortname}</a></td>
	{else}
	<td><a href="browse.php?cpu_id={$cpu.id}">{$cpu.name}</a></td>
	{/if}
	<td><a href="browse.php?cpu_speed={$cpu.speed}">{$cpu.speed}</a></td>
	<td><a href="browse.php?cpu_cache={$cpu.cache}">{$cpu.cache}</a></td>
	<td><a href="browse.php?memtotal={$cpu.memtotal}">{$cpu.memtotal}</a></td>
	<td><a href="browse.php?swaptotal={$cpu.swaptotal}">{$cpu.swaptotal}</a></td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}


{if $pkg}
<tr>
<th>hostname</th>
<th>package</th>
<th>version</th>
<th>arch</th>
<th>timestamp</th>
</tr>
{foreach from=$pkg item="pkg"}
<tr>
	<td><a href="host.php?id={$pkg.host_id}">{$pkg.hostname}</a></td>
        <td><a href="browse.php?pkg_name={$pkg.name}">{$pkg.name}</a></td>
        <td><a href="browse.php?pkg_id={$pkg.id}">{$pkg.version}{if $pkg.release}-{$pkg.release}{/if}</a></td>
        <td>{$pkg.arch}</td>
	<td>{$pkg.timestamp}</td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}


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
<th>ipaddr</th>
<th>hostname</th>
<th>interface</th>
<th>macaddr</th>
<th>netmask</th>
<th>broadcast</th>
<th>timestamp</th>
</tr>
{foreach from=$nic item="nic"}
<tr>
        <td sorttable_customkey="{$nic.num_ipaddr}"><a href="browse.php?ipaddr={$nic.ipaddr}">{$nic.ipaddr}</a></td>
	<td><a href="host.php?id={$nic.host_id}">{$nic.hostname}</a></td>
	<td>{$nic.interface}</td>
	<td><a href="browse.php?macaddr={$nic.macaddr}">{$nic.macaddr}</a></td>
	<td sorttable_customkey="{$nic.num_netmask}"><a href="browse.php?netmask={$nic.netmask}">{$nic.netmask}</a></td>
	<td sorttable_customkey="{$nic.num_broadcast}"><a href="browse.php?broadcast={$nic.broadcast}">{$nic.broadcast}</a></td>
	<td><a href="history.php?nic_id={$nic.id}">{$nic.timestamp}<a/></td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}


{if $os}
<tr>
<th>hostname</th>
<th>OS</th>
<th>kernel</th>
<th>arch</th>
<th>vendor</th>
<th>release date</th>
</tr>
{foreach from=$os item="os"}
<tr>
	<td><a href="host.php?id={$os.id}">{$os.hostname}</a></td>
	<td><a href="browse.php?os={$os.name}">{$os.name}</a></td>
	<td><a href="browse.php?kernel={$os.kernel}">{$os.kernel}</a></td>
	<td><a href="browse.php?arch={$os.arch}">{$os.arch}</a></td>
	<td><a href="browse.php?vendor={$os.vendor}">{$os.vendor}</a></td>
	<td><a href="browse.php?release_date={$os.release_date}">{$os.release_date}</a></td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}

{/if}

</table>


{include file="footer.tpl"}

