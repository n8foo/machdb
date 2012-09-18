{* Smarty *}

{include file="header.tpl"}

<form action=search.php method="GET">
<table><tr><td>
Hostname:
<td>
<input style="width: 300px;" name="hostname" size="30" type="text">
<td>
<input type="checkbox" name="archive"> archive
<tr><td colspan=2>
<center><input type="submit" value="Search" /></center>
</table>
</form>

<table>

{if $hostname}
<tr>
	<th>hostname</th>
	<th>timestamp</th>
</tr>
{foreach from=$hostname item="hostname"}
<tr>
	<td><a href="host.php?id={$hostname.id}">{$hostname.hostname}</a></td>
	<td><a href="history.php?host_id={$hostname.id}">{$hostname.timestamp}</a></td>
</tr>
{foreachelse}
        <tr>
                <td colspan=20>no records</td>
        </tr>
{/foreach}
{/if}

</table>


{include file="footer.tpl"}

