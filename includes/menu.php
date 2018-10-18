<div id="menucontent">
<ul id="menu">
<li class="primary"><a href="index.php">View All</a></li>
<li class="primary"><a href="add.php">Add a Tune</a></li>
<li class="primary"><a href="stats.php">Statistics</a></li>
<li class="primary"><a href="settings.php">Settings</a></li>
<?php
$query = "SELECT type FROM tunes WHERE tunesmithid = '".$usernameid."' ORDER BY type";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if (mysql_num_rows($result) > 0) {
	echo "<p>By Type:</p>\n\n";
	while($row  = mysql_fetch_object($result)) {
		if($types) {
			if(!in_array($row->type, $types)) {
?>
<li><a href="index.php?type=<?php echo $row->type; ?>"><?php echo $row->type; ?>s</a></li>
<?php
				$types[] = $row->type;
			};
		} else {
?>
<li><a href="index.php?type=<?php echo $row->type; ?>"><?php echo $row->type; ?>s</a></li>
<?php
			$types[] = $row->type;
		};
	};
};
?>
<?php
$query = "SELECT year FROM tunes WHERE tunesmithid = '".$usernameid."' ORDER BY year DESC";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if (mysql_num_rows($result) > 0) {
	echo "<p>By Year:</p>\n\n";
	while($row  = mysql_fetch_object($result)) {
		if($years) {
			if(!in_array($row->year, $years)) {
?>
<li><a href="index.php?year=<?php echo $row->year; ?>"><?php echo $row->year; ?></a></li>
<?php
				$years[] = $row->year;
			};
		} else {
?>
<li><a href="index.php?year=<?php echo $row->year; ?>"><?php echo $row->year; ?></a></li>
<?php
			$years[] = $row->year;
		};
	};
};
?>
</ul>
</div>