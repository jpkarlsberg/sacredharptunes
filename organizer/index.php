<?php $page = "Home Page";
require("../includes/init.php");

/* Start Page-Specific Code */
$year = $_GET["year"];
$type = $_GET["type"];
$textsource = $_GET["textsource"];
$meter = $_GET["meter"];
$key = $_GET["key"];
$modeoftime = $_GET["modeoftime"];

$action = $_GET["action"];
$tunename = $_GET["tunename"];
$filedelfailure = $_GET["filedelfailure"];
$fileupload = $_GET["fileupload"];
$filefailure = $_GET["filefailure"];

$where = " WHERE tunesmithid = '$usernameid'";
if($year) { /* figure out which tunes to display further down. done up here so we can display the correct title. */
	$where .= " AND year=".$year." OR tunesmithid = '$usernameid' AND additionalyear=".$year;
	$title = "All the Tunes from ".$year;
	$category = 1;
} elseif($type) {
	$where .= " AND type='".$type."'";
	$title = "All of the ".$type."s";
	$category = 1;
} elseif($textsource) {
	$where .= " AND textsource='".$textsource."'";
	$title = "All of the Tunes with Words from ".$textsource;
	$category = 1;
} elseif($meter) {
	$where .= " AND meter='".$meter."'";
	$title = "All of the ".$meter." Tunes";
	$category = 1;
} elseif($key) {
	$where .= " AND tunekey='".str_replace("s", "#", $key)."'";
	$title = "All of the Tunes in ".readable_key($key, "return");
	$category = 1;
} elseif($modeoftime) {
	$where .= " AND modeoftime='".str_replace("Multiple", "mul", $modeoftime)."'";
	$title = "All of the Tunes in ".$modeoftime." Time";
	$category = 1;
} else {
	$title = "All the Tunes";
	$category = 0;
}
?>
		<h2><?php echo stripslashes(stripslashes($title)); ?></h2>
<?php 
/*
		<form action="add.php" method="post">
			<input type="text" size="25" name="tunename" id="tunename" maxlength="45" value="Tune Name" />
			<input type="submit" name="submit" id="submit" value="Add Tune" />
		</form>
*/
require("../includes/rank.php"); /* check if a rank has been changed, then implement the change */

if($action && $tunename) { /* user feedback from previous file add/edit */
	echo "<p class=\"success\">Your tune ".stripslashes(stripslashes($tunename))." was sucessfully ".$action.".</p>\n\n";
	if($filedel) { /* successful file deletion message */
		if(strrpos(stripslashes(stripslashes($filedel)), ",")) { $plural = "(s)"; } else { $plural = ""; };
		echo "<p class=\"success\">You have deleted the following file".$plural.": ".stripslashes(stripslashes($filedel)).".</p>\n\n";
	};
	if($filedelfailure) { /* failed file deletion message */
		$fdf_arr = explode(",", $filedelfailure);
		foreach ($fdf_arr as $fdf_val) {
			$query = "SELECT error FROM errors WHERE id = $fdf_val LIMIT 1";
			$result = mysql_query($query) or die("<p>There was an error with the previous operation. Error in query: $query. ".mysql_error()."</p>");
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_object($result);
				echo "<p class=\"alert\">".$row->error."</p>\n\n";
			} else {
				echo "<p class=\"alert\">There was an error with the previous operation.</p>";
			};
		};
	};
	if($fileupload) { /* successful file upload message */
		if(strrpos(stripslashes(stripslashes($fileupload)), ",")) { $plural = "(s)"; } else { $plural = ""; };
		echo "<p class=\"success\">You have uploaded the following file".$plural.": ".stripslashes(stripslashes(stripslashes($fileupload))).".</p>\n\n";
	};
	if($filefailure) { /* failed file upload message */
		$ff_arr = explode(",", $filefailure);
		foreach ($ff_arr as $ff_val) {
			$query = "SELECT error FROM errors WHERE id = $ff_val LIMIT 1";
			$result = mysql_query($query) or die("<p>There was an error with the previous operation. Error in query: $query. ".mysql_error()."</p>");
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_object($result);
				echo "<p class=\"alert\">".$row->error."</p>\n\n";
			} else {
				echo "<p class=\"alert\">There was an error with the previous operation.</p>";
			};
		};
	};
}

if($deleted) {
	echo "<p class=\"alert\">You have deleted the tune $deleted.</p>\n\n";
};

if($order) {
	if($order == "title") {
		$orderby = "name";
	} elseif($order == "firstline") {
		$orderby = "ISNULL(text), text, rank, name";
	} elseif($order == "year") {
		$orderby = "year, additionalyear, rank, name";
	} elseif($order == "type") {
		$orderby = "type, rank, name";
	} else {
		$orderby = "rank, name";
	};
} else {
	$orderby = "rank, name";
};

$query = "SELECT * FROM tunes".$where." ORDER BY ".$orderby;
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

if (mysql_num_rows($result) > 0) {
?>
	<table width="100%"><thead>
		<tr>
<?php if($category == 1) {
	echo "<td>c</td>";
};
?>
			<td class="tunes_order"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">#</a></td>
			<td class="tunes_title"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?order=title">Title</a></td>
			<td class="tunes_firstline"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?order=firstline">First Line</a></td>
			<td class="tunes_year"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?order=year">Year</a></td>
			<td class="tunes_type"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?order=type">Type</a></td><?php if($guest == 0) { ?><td class="tunes_tools">Tools</td><?php }; ?>
		</tr>
	</thead>
<?php
	$i = 1;
	while($row = mysql_fetch_object($result)) {
		if(
			($key && substr($key, -1) == "m" && substr($row->tunekey, -1) == "M") ||
			($key && substr($key, -1) == "M" && substr($row->tunekey, -1) == "m")
		) { /* Don't print if wrong maj/min */ } else {
?>
		<tr>
			<?php if($category == 1 && $row->rank != 0) { echo "<td>".$i."</td>"; $i++; } elseif($category == 1) { echo "<td></td>"; }	; ?> 
			<td><?php if($row->rank == 0) { echo "-"; } else { echo $row->rank."."; }; ?></td>
			<td class="space"><?php echo "<a href=\"detail.php?id=".$row->id."\">".readable_tunename($row->name,"",$row->meter)."</a>"; 
			$queryfiles = "SELECT * FROM uploadedfiles WHERE tuneid = '".$row->id."' ORDER BY filetype, filenumber";
			$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $queryfiles. ".mysql_error()."</p>");
			if (mysql_num_rows($resultfiles) > 0) {
				echo " <span class=\"subtle\">&ndash; ";
				$first = 1;
				while($rowfiles = mysql_fetch_object($resultfiles)) {
					if($first != 1) { echo ", "; } else { $first = 0; };
					formatted_filename($username, $row->id, $rowfiles->id, $rowfiles->fileext, "EXT", $rowfiles->filenumber, "echo", 1);
				};
				echo "</span>";
			};
			?></td>
			<td><a href="detail.php?id=<?php echo $row->id."#text"; ?>"><?php echo neat_text($row->text, "first line"); ?></a></td>
			<td><?php echo "<a href=\"?year=".$row->year."\">".$row->year."</a>"; if($row->additionalyear != 0) { echo "<a href=\"?year=".$row->additionalyear."\">".(2000 - $row->additionalyear)."</a>"; }; ?></td>
			<td<?php if ($guest == 0) { ?> class="space"<?php }; ?>><?php echo "<a href=\"?type=".$row->type."\">".$row->type."</a>"; ?></td>
			<?php if ($guest == 0) { ?><td><form action="index.php" method="post"><input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" /><input type="hidden" name="name" id="name" value="<?php echo $row->name; ?>" /><input class="rank" type="text" style="width:25px;" name="rank" id="rank" maxlength="11" /> <input type="submit" name="submitrank" id="submitrank" value="Rank" /></form>
			<form action="add.php" method="post"><input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" /><input type="submit" name="submit" id="submit" value="Edit" /></form></td><?php }; ?>
		</tr>
<?php	
	}; };
?>
	</table>
<?php
} else {
?>
	<p class="alert">No tunes in database.</p>
<?php
}
// free result set memory
mysql_free_result($result);
?>

<?php require("../includes/shutdown.php"); ?>