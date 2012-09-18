{if $tcpcons}
<table>
<tr><th>LoadAvg <td>{$load1} {$load5} {$load15}
<th>MemFree <td>{$mem/1024|string_format:"%.2f"} MB
<tr><th>TCP Conn <td>{$tcpcons}
<th>SwapUsed <td>{$swap/1024|string_format:"%.2f"} MB
<tr><th>Uptime <td>{$uptime}
<th>RootDisk <td>{$root|string_format:"%.2f"} %
</table>
{else}
could not connect to SNMP host
{/if}
