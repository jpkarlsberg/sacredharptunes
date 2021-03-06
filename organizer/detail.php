<?php $page = "Detail";
require("../includes/init.php");

/* Start Page-Specific Code */
$id = $_GET["id"];
if(!$_GET["id"]) { $id = -1; };

$query = "SELECT * FROM tunes WHERE id = ".$id." LIMIT 1";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if (mysql_num_rows($result) > 0) {
	while($row = mysql_fetch_object($result)) {
?>
<h2><?php echo readable_tunename($row->name,"",$row->meter); ?></h2>
<p><?php
	echo "A";
	if(substr($row->tunekey, 0, 1) == "A" || substr($row->tunekey, 0, 1) == "E" || substr($row->tunekey, 0, 1) == "F") { echo "n"; };
	echo " ";
	readable_key($row->tunekey);
	echo " ";
	echo strtolower($row->type)." in ";
	if($row->modeoftime == "mul") { echo "multiple modes of time."; } else { echo $row->modeoftime." time."; };
	if($row->threeliner == 1) { echo " A 3-liner."; };
	echo " Composed ".$row->year;
	if($row->additionalyear != 0) { echo (2000 - $row->additionalyear); };
	echo ".";
?></p>
<p><?php
	echo "Text from ".stripslashes($row->textsource);
	if($row->textyear != "0") { echo ", ".$row->textyear; };
	echo ".";
?></p>
<?php 
$querydlc = "SELECT downloadcount, fileext, title FROM uploadedfiles WHERE tuneid = '".$row->id."' AND downloadcount > 0 ORDER BY fileext";
$resultdlc = mysql_query($querydlc) or die("<p>Error in query: $querydlc. ".mysql_error()."</p>");
if($row->public == 1 || mysql_num_rows($resultdlc) > 0) {
	echo "<p>";
	if($row->public == 1) {
		echo "This tune is being shared <span class=\"success\">publicly</span>."; 
	};
	if(mysql_num_rows($resultdlc) > 0) {
		echo " File downloads: ";
		$preformatting = "";
			$dlctotal = 0;
			while($rowdlc = mysql_fetch_object($resultdlc)) {
			echo $preformatting."<span title=\"".$rowdlc->title."\">".$rowdlc->fileext."</span>: ".$rowdlc->downloadcount;
			$preformatting = "; ";
			$dlctotal += $rowdlc->downloadcount;
		};
		echo ". Total: ".$dlctotal.".";
	};
	echo "</p>\n\n"; 
}; ?>
<?php
	$querymp3s = "SELECT * FROM uploadedfiles WHERE tuneid = '".$row->id."' AND (fileext = 'mp3' OR fileext = 'm4a') ORDER BY filetype";
	$resultmp3s = mysql_query($querymp3s) or die("<p>Error in query: $querymp3s. ".mysql_error()."</p>");
	if (mysql_num_rows($resultmp3s) > 0) {
		echo "<p class=\"clear section\">Download MP3s/M4As: ";
		$first = 1;
		while($rowmp3s = mysql_fetch_object($resultmp3s)) {
			if($first != 1) { echo ", "; } else { $first = 0; };
			formatted_filename($username, $row->id, $rowmp3s->id, $rowmp3s->fileext, "mp3title", $rowmp3s->filenumber);
	};
	echo "</p>";
}; ?>

<div class="actions"><?php if($guest == 0) { ?><div class="forms"><form action="add.php" method="post"><input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" /><input type="submit" name="submit" id="submit" value="Edit" /></form> <form action="delete.php" method="post"><input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" />
<input type="submit" name="submit" id="submit" value="Delete" /></form></div><?php };
	$queryfiles = "SELECT * FROM uploadedfiles WHERE tuneid = '".$row->id."' AND (fileext != 'mp3' AND fileext != 'm4a') ORDER BY filetype";
	$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $queryfiles. ".mysql_error()."</p>");
	if (mysql_num_rows($resultfiles) > 0) {
		echo "<p>";
		$first = 1;
		while($rowfiles = mysql_fetch_object($resultfiles)) {
			if($first != 1) { echo ", "; } else { $first = 0; };
			if($rowfiles->fileext == "indd") { $downloadtext = "Download an "; } else { $downloadtext = "Download a "; };
			formatted_filename($username, $row->id, $rowfiles->id, $rowfiles->fileext, $downloadtext."EXT", $rowfiles->filenumber);
	};
	echo "</p>";
};
?></div>
<?php if($row->publicdescription != "" && $row->public == 1) { ?>
<h3 class="clear section"><a name="publicdescription">Public Description</a></h3>
<?php echo stripslashes($row->publicdescription); ?>
<?php }; ?>
<?php
	$query_notes = "SELECT * FROM notes WHERE tuneid = ".$row->id." ORDER BY createdate, modifydate";
	$result_notes = mysql_query($query_notes) or die("<p>Error in query: $query_notes. ".mysql_error()."</p>");
	if(mysql_num_rows($result_notes) > 0) {
		echo "<h3 class=\"clear section\"><a name=\"notes\">Note";
		if(mysql_num_rows($result_notes) > 1) { echo "s"; };
		echo "</a></h3>\n\n";
		while($row_n = mysql_fetch_object($result_notes)) {
			echo "<h4>Note written ".date("M j, Y", strtotime($row_n->createdate)).":</h4>\n\n".stripslashes($row_n->note);
		};
	} else { echo "NO"; };
?>
<?php if($row->text != "") { ?>
<h3 class="clear section"><a name="text">Text</a></h3>
<?php echo stripslashes($row->text); ?>
<?php };
if($row->rank <= 0) {
//	echo "<p>This tune is currently unranked</p>\n\n";
} else {
	$querytype = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND type = '".$row->type."'";
	$resulttype = mysql_query($querytype) or die("<p>Error in query: $querytype. ".mysql_error()."</p>");
	$typerank = mysql_num_rows($resulttype);
	$queryyear = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND year = '".$row->year."'";
	$resultyear = mysql_query($queryyear) or die("<p>Error in query: $queryyear. ".mysql_error()."</p>");
	$yearrank = mysql_num_rows($resultyear);
	$queryadditionalyear1 = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND year = '".$row->additionalyear."'";
	$resultadditionalyear1 = mysql_query($queryadditionalyear1) or die("<p>Error in query: $queryadditionalyear1. ".mysql_error()."</p>");
	$queryadditionalyear2 = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND additionalyear = '".$row->additionalyear."'";
	$resultadditionalyear2 = mysql_query($queryadditionalyear2) or die("<p>Error in query: $queryadditionalyear2. ".mysql_error()."</p>");
	$additionalyearrank = mysql_num_rows($resultadditionalyear1) + mysql_num_rows($resultadditionalyear2);
	$querykey = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND tunekey = '".$row->tunekey."'";
	$resultkey = mysql_query($querykey) or die("<p>Error in query: $querykey. ".mysql_error()."</p>");
	$keyrank = mysql_num_rows($resultkey);
	$querymodeoftime = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND rank <= ".$row->rank." AND rank != 0 AND modeoftime = '".$row->modeoftime."'";
	$resultmodeoftime = mysql_query($querymodeoftime) or die("<p>Error in query: $querymodeoftime. ".mysql_error()."</p>");
	$modeoftimerank = mysql_num_rows($resultmodeoftime);

?>
<table class="clear">
<thead><tr><td colspan="4">Rank</td></tr></thead>
<?php
	echo "<tr><td class=\"right strong\">Overall Rank:</td><td class=\"space strong\">".$row->rank."</td>";
	echo "<td class=\"right\">".$row->type."s:</td><td class=\"space\">".$typerank."</td></tr>\n";
	echo "<tr><td class=\"right\">Tunes in ".substr($row->tunekey, 0, 1);
		if(substr($row->tunekey, 1, 1) == "b") { echo "&#9837;"; } elseif(substr($row->tunekey, 1, 1) == "#") { echo "&#9839;"; };
		if(substr($row->tunekey, -1) == "m") { echo " minor "; } else { echo " major "; };
	echo ":</td><td class=\"space\">".$keyrank."</td>";
	echo "<td class=\"right\">Tunes in ";
		if($row->modeoftime == "mul") { echo "multiple modes of time"; } else { echo $row->modeoftime." time"; };
	echo ":</td><td class=\"space\">".$modeoftimerank."</td></tr>\n";
	echo "<tr><td class=\"right\">Tunes from ".$row->year.":</td><td class=\"space\">".$yearrank."</td>";
	if($row->additionalyear) { echo "<td class=\"right\">Tunes from ".$row->additionalyear.":</td><td class=\"space\">".$additionalyearrank."</td></tr>\n"; } else { echo "<td></td><td></td></tr>\n"; };

?>
</table>
<?php }; ?>
<p class="footer clear">Return to the list of <a href="index.php"> All Tunes</a>.</p>
<?php
	}
} else {
?>
	<h2>Tune Detail View</h2>
<?php if($id == -1) { ?>
	<p class="alert">You have not selected a tune to view.</p>
	<p>Visit the list of <a href="index.php">All Tunes</a>.</p>
<?php } else { ?>
	<p class="alert">There is no tune in the database with the id <?php echo $id; ?>.</p>
	<p>Visit the list of <a href="index.php">All Tunes</a>.</p>
<?php }; ?>
	<form action="add.php" method="post">
		<input type="text" size="25" name="tunename" id="tunename" maxlength="45" value="Tune Name" />
		<input type="submit" name="submit" id="submit" value="Add Tune" />
	</form>
<?php
}
// free result set memory
mysql_free_result($result); ?>

<?php require("../includes/shutdown.php"); ?>