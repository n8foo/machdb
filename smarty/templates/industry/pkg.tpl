
<table class="sortable">
<CAPTION>Software Packages</CAPTION>
<thead>
	<tr>
		<th>package</th>
		<th>version</th>
		<th>arch</th>
		<th>timestamp</th>
	</tr>
	</thead>
	<tbody>
{foreach from=$pkg item="pkg"}
        <tr>
                <td><a href="browse.php?pkg_name={$pkg.name}">{$pkg.name}</a></td>
                <td><a href="browse.php?pkg_id={$pkg.id}">{$pkg.version}{if $pkg.release}-{$pkg.release}{/if}</a></td>
                <td>{$pkg.arch}</td>
                <td>{$pkg.timestamp}</td>
        </tr>
{foreachelse}
        <tr>
                <td colspan=3>no records</td>
        </tr>
{/foreach}
</tbody>
<tfoot>
</tfoot>
</table>

