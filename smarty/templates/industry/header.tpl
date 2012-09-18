<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>MachDB: {$pagetitle}</title>
		<link rel="stylesheet" type="text/css" href="smarty/templates/industry/style/industry.css" />
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
		<script type="text/javascript" src="smarty/templates/industry/js/misc.js"></script>
		<script type="text/javascript" src="smarty/templates/industry/js/ajax.js"></script>
		<script type="text/javascript" src="smarty/templates/industry/js/sorttable.js"></script>
	</head>
	<BODY onLoad="putFocus(0,1);">

		<ul id="nav">
			<li><a href=".">Main</a></li>
			<li><a href="search.php">Search</a></li>
			<li><a href="software.php">Software</a></li>
		</ul>
		<div id="main">
			<a name="top" class="nodisplay"></a>
			<div id="header">
				<div class="gear"> </div>
				<h1 class="shad"><em>mach</em>db</h1>
				<h1><em>mach</em>db</h1>
			</div>

			<div id="body">
				<div id="content">
				<h2>{$pagetitle}</h2>
					<div class="meta">
					<span class="date">{$date|date_format:"%b %e, %Y %H:%M:%S"}</span>
					<span class="queryString"> {foreach from=$querystring item="querystring"} {$querystring} {/foreach}</span>
					</div>
				<p>


