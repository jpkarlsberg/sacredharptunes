<?php $page = "Statistics";
require("../includes/init.php");

/* Start Page-Specific Code */ 
$textsource = $_GET["textsource"];
$tunemeter = $_GET["tunemeter"];

?>
		<h2>Statistics</h2>
<?php


function array_qsort (&$array, $column=0, $order=SORT_ASC, $first=0, $last= -2)
{
// $array - the array to be sorted
// $column - index (column) on which to sort
// can be a string if using an associative array
// $order - SORT_ASC (default) for ascending or SORT_DESC for descending
// $first - start index (row) for partial array sort
// $last - stop index (row) for partial array sort
// $keys - array of key values for hash array sort

	$keys = array_keys($array);
	if($last == -2) $last = count($array) - 1;
	if($last > $first) {
		$alpha = $first;
		$omega = $last;
		$key_alpha = $keys[$alpha];
		$key_omega = $keys[$omega];
		$guess = $array[$key_alpha][$column];
		while($omega >= $alpha) {
			if($order == SORT_ASC) {
				while($array[$key_alpha][$column] < $guess) {$alpha++; $key_alpha = $keys[$alpha]; }
				while($array[$key_omega][$column] > $guess) {$omega--; $key_omega = $keys[$omega]; }
			} else {
				while($array[$key_alpha][$column] > $guess) {$alpha++; $key_alpha = $keys[$alpha]; }
				while($array[$key_omega][$column] < $guess) {$omega--; $key_omega = $keys[$omega]; }
		}
		if($alpha > $omega) break;
			$temporary = $array[$key_alpha];
			$array[$key_alpha] = $array[$key_omega]; $alpha++;
			$key_alpha = $keys[$alpha];
			$array[$key_omega] = $temporary; $omega--;
			$key_omega = $keys[$omega];
		}
		array_qsort ($array, $column, $order, $first, $omega);
		array_qsort ($array, $column, $order, $alpha, $last);
	}
}







$query = "SELECT * FROM tunes WHERE tunesmithid = '".$usernameid."' ORDER BY rank, name";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
$types = array(); /* check */
$textsources = array();
$tunekeys = array();
$majmin = array();
$modesoftime = array();
$meters = array();
$years = array();
$i = 0;
if (mysql_num_rows($result) > 0) {
	$total = mysql_num_rows($result);
	$querypublic = "SELECT id FROM tunes WHERE tunesmithid = '".$usernameid."' AND public = 1";
	$resultpublic = mysql_query($querypublic) or die("<p>Error in query: $querypublic. ".mysql_error()."</p>");
	$totalpublic = mysql_num_rows($resultpublic);
	$queryfiles = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."'";
	$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $queryfiles. ".mysql_error()."</p>");
	$totalfiles = mysql_num_rows($resultfiles);
	$querymidfiles = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND fileext = 'mid'";
	$resultmidfiles = mysql_query($querymidfiles) or die("<p>Error in query: $querymidfiles. ".mysql_error()."</p>");
	$totalmidfiles = mysql_num_rows($resultmidfiles);
	$querymp3files = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND fileext = 'mp3'";
	$resultmp3files = mysql_query($querymp3files) or die("<p>Error in query: $querymp3files. ".mysql_error()."</p>");
	$totalmp3files = mysql_num_rows($resultmp3files);
	$querymusfiles = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND fileext = 'mus'";
	$resultmusfiles = mysql_query($querymusfiles) or die("<p>Error in query: $querymusfiles. ".mysql_error()."</p>");
	$totalmusfiles = mysql_num_rows($resultmusfiles);
	$querypdffiles = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND fileext = 'pdf'";
	$resultpdffiles = mysql_query($querypdffiles) or die("<p>Error in query: $querypdffiles. ".mysql_error()."</p>");
	$totalpdffiles = mysql_num_rows($resultpdffiles);
	$querypublicfiles = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND public = 1";
	$resultpublicfiles = mysql_query($querypublicfiles) or die("<p>Error in query: $querypublicfiles. ".mysql_error()."</p>");
	$totalpublicfiles = mysql_num_rows($resultpublicfiles);
	$querydlc = "SELECT fileext, SUM(downloadcount) FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' GROUP BY fileext ORDER BY fileext"; 
	$resultdlc = mysql_query($querydlc) or die("<p>Error in query: $querydlc. ".mysql_error()."</p>");
	$querydlct = "SELECT tunesmithid, SUM(downloadcount) FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' GROUP BY tunesmithid"; 
	$resultdlct = mysql_query($querydlct) or die("<p>Error in query: $querydlct. ".mysql_error()."</p>");
	if(mysql_num_rows($resultdlct) > 0) {
		$rowdlct = mysql_fetch_array($resultdlct);
		$totaldlct = $rowdlct['SUM(downloadcount)'];
	};
	while($row = mysql_fetch_object($result)) {
		if(isset($types[$row->type])) {
			$types[$row->type][1] ++;
		} else {
			$types[$row->type] = array($row->type, 1);
		};
		if(isset($tunekeys[$row->tunekey])) {
			$tunekeys[$row->tunekey][1] ++;
		} else {
			$tunekeys[$row->tunekey] = array($row->tunekey, 1);
		};
		if(isset($textsources[$row->textsource])) {
			$textsources[$row->textsource][1] ++;
		} else {
			$textsources[$row->textsource] = array($row->textsource, 1);
		};
		if(isset($majmin[substr($row->tunekey, -1)])) {
			$majmin[substr($row->tunekey, -1)][1] ++;
		} else {
			$majmin[substr($row->tunekey, -1)] = array(substr($row->tunekey, -1), 1);
		};
		if(isset($modeoftime[$row->modeoftime])) {
			$modeoftime[$row->modeoftime][1] ++;
		} else {
			$modeoftime[$row->modeoftime] = array($row->modeoftime, 1);
		};
		if(isset($meters[$row->meter])) {
			$meters[$row->meter][1] ++;
		} else {
			$meters[$row->meter] = array($row->meter, 1);
		};
		if(isset($years[$row->year])) {
			$years[$row->year][1] ++;
		} else {
			$years[$row->year] = array($row->year, 1);
		};
		if($row->additionalyear != 0) {
			if(isset($years[$row->additionalyear])) {
				$years[$row->additionalyear][1] ++;
			} else {
				$years[$row->additionalyear] = array($row->additionalyear, 1);
			};
		}
	}
	
/* ?>
		<form action="add.php" method="post">
			<input type="text" size="25" name="tunename" id="tunename" maxlength="45" value="Tune Name" />
			<input type="submit" name="submit" id="submit" value="Add Tune" />
		</form>

<?php */
	echo "<p>Total number of tunes by ".$name.": ".$total.". Public tunes: ".$totalpublic.".<br />\n";
	echo "Total files: ".$totalfiles." (MIDI: ".$totalmidfiles."; MP3: ".$totalmp3files."; MUS: ".$totalmusfiles."; PDF: ".$totalpdffiles."). Public files: ".$totalpublicfiles.".<br />\n";
	echo "Total downloads: ".$totaldlct."";
	if(mysql_num_rows($resultdlc) > 0) {
		$preformatting = " (";
		while ($rowdlc = mysql_fetch_array($resultdlc)) {
			if($rowdlc['SUM(downloadcount)'] > 0) {
				echo $preformatting.strtoupper($rowdlc['fileext']).": ".$rowdlc['SUM(downloadcount)'];
				$preformatting = "; ";
			};
		};
		if($preformatting == "; ") { echo ")"; };
	}
	echo ".</p>\n\n";
	
	echo "<table class=\"leftfloat\"><thead><tr><td colspan=\"2\">Year</td></tr></thead>\n";
	array_qsort($years, 1, SORT_DESC);
	foreach( $years as $key => $value){
		echo "<tr><td><a href=\"index.php?year=".$value[0]."\">".$value[0]."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
	}
	echo "</table>\n";
	echo "<table class=\"leftfloat nudge\"><thead><tr><td colspan=\"2\">Type of Tune</td></tr></thead>\n";
	array_qsort($types, 1, SORT_DESC);
	foreach( $types as $key => $value){
		echo "<tr><td><a href=\"index.php?type=".$value[0]."\">".$value[0]."s</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
	}
	echo "</table>\n";


	echo "<table class=\"leftfloat nudge\"><thead><tr><td colspan=\"2\">Text Source</td></tr></thead>\n";
	array_qsort($textsources, 1, SORT_DESC);
	$justone = 0;
	foreach( $textsources as $key => $value){
		if($textsource == "full") {
			echo "<tr><td class=\"textsource\"><a href=\"index.php?textsource=".urlencode($value[0])."\">".stripslashes($value[0])."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
		} elseif($value[1] > 1) {
			echo "<tr><td class=\"textsource\"><a href=\"index.php?textsource=".urlencode($value[0])."\">".stripslashes($value[0])."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
		} else { $justone ++; };
	}
	if($justone > 0) {
		echo "<tr><td class=\"subtle\"><a href=\"stats.php?textsource=full&tunemeter=".$tunemeter."\">Others:</a></td><td class=\"subtle\" align=\"right\">".$justone."</td></tr>\n";
	} else {
		echo "<tr><td colspan=\"2\" class=\"subtle\"><a href=\"stats.php?tunemeter=".$tunemeter."\">Hide Others</a></td></tr>\n";	
	};
	echo "</table>\n";


	echo "<table class=\"leftfloat nudge\"><thead><tr><td colspan=\"2\">Text Meter</td></tr></thead>\n";
	array_qsort($meters, 1, SORT_DESC);


	$justone = 0;
	foreach( $meters as $key => $value){
		if($tunemeter == "full") {
			if($value[0] == "none") { $meter = "No meter"; } else { $meter = $value[0]; };
			echo "<tr><td><a href=\"index.php?meter=".urlencode($value[0])."\">".$meter."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
		} elseif($value[1] > 1) {
			if($value[0] == "none") { $meter = "No meter"; } else { $meter = $value[0]; };
			echo "<tr><td><a href=\"index.php?meter=".urlencode($value[0])."\">".$meter."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
		} else { $justone ++; };
	}
	if($justone > 0) {
		echo "<tr><td class=\"subtle\"><a href=\"stats.php?tunemeter=full&textsource=".$textsource."\">Others:</a></td><td class=\"subtle\" align=\"right\">".$justone."</td></tr>\n";
	} else {
		echo "<tr><td colspan=\"2\" class=\"subtle\"><a href=\"stats.php?textsource=".$textsource."\">Hide Others</a></td></tr>\n";	
	};


	echo "</table>\n";
	echo "<table class=\"leftfloat nudge\"><thead><tr><td colspan=\"2\">Key</td></tr></thead>\n";
	array_qsort($tunekeys, 1, SORT_DESC);
	array_qsort($majmin, 1, SORT_DESC);
	foreach( $majmin as $key => $value){
		if($value[0] == "M") { $majorminor = "Major"; } elseif($value[0] == "m") { $majorminor = "Minor"; } else { $majorminor = ""; };
		echo "<tr><td><a href=\"index.php?type=".$majorminor."\">".$majorminor."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
	}
	foreach( $tunekeys as $key => $value){
		$tunekey =  substr($value[0], 0, 1);
		if(substr($value[0], 1, 1) == "b") { $tunekey .= "&#9837;"; } elseif(substr($value[0], 1, 1) == "#") { $tunekey .= "&#9839;"; };
		if(substr($value[0], -1) == "m") { $tunekey .= " minor"; } else { $tunekey .= " major"; };
	
		echo "<tr><td><a href=\"index.php?key=".str_replace("#", "s", $value[0])."\">".$tunekey."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
	}
	echo "</table>\n";
	echo "<table class=\"leftfloat nudge\"><thead><tr><td colspan=\"2\">Mode of Time</td></tr></thead>\n";
	array_qsort($modeoftime, 1, SORT_DESC);
	foreach( $modeoftime as $key => $value){
		if($value[0] == "mul") { $value[0] = "Multiple"; };
		echo "<tr><td><a href=\"index.php?modeoftime=".$value[0]."\">".$value[0]."</a>:</td><td align=\"right\">".$value[1]."</td></tr>\n";
	}
	echo "</table>\n";
	/*
	for($counter = 0; sizeof($types); $counter ++) {
		echo "<p>".$types[$counter][0].": ".$types[$counter][1]."</p>\n";
	};
	*/
	} else {
?>
	<p class="alert">No tunes in database.</p>
<?php
}
// free result set memory
mysql_free_result($result); ?>

<?php require("../includes/shutdown.php"); ?>