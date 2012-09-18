{* Smarty *}

{include file="header.tpl"}
<table><tr>
<td valign=top>
<table><tr>
<td valign=top>
<table border=0 width=150px class=sortable>
	<thead>
        	<tr>
			<th>Package Name</th>
		</tr>
	</thead>
	<tbody>
        	{foreach from=$pkg_name item="pkg_name"}
		<tr>
                        <td><a href="browse.php?pkg_name={$pkg_name.name}">{$pkg_name.name}</a></td>
                </tr>
        	{foreachelse}
                <tr><td colspan=3>no records</td></tr>
        	{/foreach}
	</tbody>
</table>
<td valign=top>
<table border=0 width=150px class=sortable>
	<thead>
        	<tr>
			<th>Operating Systems</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$os item="os"}
		<tr>
			<td><a href="browse.php?os={$os.name}">{$os.name}</a></td>
		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
	</tbody>
</table>
<td valign=top>
<table border=0 width=150px class=sortable>
	<thead>
        	<tr>
			<th>Kernel Versions</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$kernel item="kernel"}
		<tr>
			<td><a href="browse.php?kernel={$kernel.kernel}">{$kernel.kernel}</a></td>
		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
	</tbody>
</table>
<td valign=top>
<table border=0 width=150px class=sortable>
	<thead>
        	<tr>
			<th>OS Architectures</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$os_arch item="os_arch"}
		<tr>
			<td><a href="browse.php?arch={$os_arch.arch}">{$os_arch.arch}</a></td>
		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
	</tbody>
</table>
<td valign=top>
<table border=0 width=150px class=sortable>
	<thead>
        	<tr>
			<th>OS Vendors</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$os_vendor item="os_vendor"}
		<tr>
			<td><a href="browse.php?vendor={$os_vendor.vendor}">{$os_vendor.vendor}</a></td>
		</tr>
	{foreachelse}
		<tr><td colspan=3>no records</td></tr>
	{/foreach}
	</tbody>
</table>

</table>

{include file="footer.tpl"}
