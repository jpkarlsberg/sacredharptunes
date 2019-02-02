<?php
$page = "com";
require("includes/constants.php");
require("includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
$com = $_GET["com"];
$tuneslug = $_GET["tuneslug"];
if(!$com || !is_string($com)) { $com = "NULL"; };
if(!$tuneslug || !is_string($tuneslug)) { $tuneslug = "NULL"; }
$query = "SELECT * FROM users WHERE publicfoldername = '$com' LIMIT 1";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if(mysql_num_rows($result) > 0) {
	$userobj = mysql_fetch_object($result);
	$bywhat = $userobj->firstname." ".$userobj->lastname;
	$firstname = $userobj->firstname;
	$username = $userobj->username;
	$userid = $userobj->id;
	$description = $userobj->description;
	$querytune = "SELECT * FROM tunes WHERE nameslug = '$tuneslug' AND public = 1 AND tunesmithid = $userid LIMIT 1";
	$resulttune = mysql_query($querytune) or die("<p>Error in query: $querytune. ".mysql_error()."</p>");
	if(mysql_num_rows($resulttune) > 0) {
		$tuneobj = mysql_fetch_object($resulttune);
	} elseif($tuneslug != "NULL") {
		$err = "The tune you have requested could not be found.";
	};
} else {
	$bywhat = "Composer";
	if($com != "NULL") {
		$err = "There is no composer named \"$com\" sharing tunes through this site.";
	};
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("includes/head.php"); ?>
</head>

<body class="public">
<div id="page">
<div id="header">
<h1><?php echo SITE_NAME_PUBLIC; ?></h1>
<?php /* Put any alert or maintenance announcement here */ ?>
</div>
	<div id="main">
	<div id="content">
<?php
echo "<h2>By ".$bywhat."</h2>\n\n";
if($err) {
	if($err == "404") { $err = "The page you have requested could not be found."; };
	echo "<p class=\"alert\"><strong>Error:</strong> ".$err."</p>\n\n";
};
if($tuneobj) {
	echo "<p class=\"right rightfloat\" style=\"margin:2em 0 0;\"><a href=\"/".$com."/\">Back to ".$bywhat."'s Tunes &raquo;</a><br />\n";
	echo "<a href=\"/\">Back to List of Composers &raquo;</a></p>\n\n";
	echo "<h2 class=\"section\">".readable_tunename($tuneobj->name,"",$tuneobj->meter)."</h2>\n\n";
	echo "<p>A";
	if(substr($tuneobj->tunekey, 0, 1) == "A" || substr($tuneobj->tunekey, 0, 1) == "E" || substr($tuneobj->tunekey, 0, 1) == "F") { echo "n"; };
	echo " ";
	readable_key($tuneobj->tunekey);
	echo " ";
	echo strtolower($tuneobj->type)." in ";
	if($tuneobj->modeoftime == "mul") { echo "multiple modes of time."; } else { echo $tuneobj->modeoftime." time."; };
	if($tuneobj->threeliner == 1) { echo " A 3-liner."; };
	echo " Composed ".$tuneobj->year;
	if($tuneobj->additionalyear != 0) { echo (2000 - $tuneobj->additionalyear); };
	echo ".</p>";
	echo "<p>Text from ".stripslashes($tuneobj->textsource);
	if($tuneobj->textyear != "0") { echo ", ".$tuneobj->textyear; };
	echo ".</p>";
	$querymp3files = "SELECT * FROM uploadedfiles WHERE tuneid = '".$tuneobj->id."' AND public = '1' AND fileext = 'mp3' ORDER BY title, filenumber";
	$resultmp3files = mysql_query($querymp3files) or die("<p>Error in query: $querymp3files. ".mysql_error()."</p>");
	$queryaddfiles = "SELECT * FROM uploadedfiles WHERE tuneid = '".$tuneobj->id."' AND public = '1' AND (fileext = 'pdf' OR fileext = 'mid') ORDER BY fileext DESC";
	$resultaddfiles = mysql_query($queryaddfiles) or die("<p>Error in query: $queryaddfiles. ".mysql_error()."</p>");
	if (mysql_num_rows($resultmp3files) > 0) {
		echo "<p>Listen to recordings:</p>\n";
		$first = 1;
		while($rowmp3s = mysql_fetch_object($resultmp3files)) {
			if($first != 1) { echo "</li>\n<li>"; } else { $first = 0; echo "<ul class=\"recordings\">\n<li>"; };
			formatted_mp3player($username, $tuneobj->id, $rowmp3s->id, $rowmp3s->fileext, "mp3title", $rowmp3s->filenumber, "echotitle", $rowmp3s->title, 1);
		};
		echo "</li>\n</ul>\n";
	};
	if (mysql_num_rows($resultaddfiles) > 0) {
		echo "<p>Download files: ";
		$first = 1;
		while($rowadds = mysql_fetch_object($resultaddfiles)) {
			if($first != 1) { echo "; "; } else { $first = 0; };
			formatted_filename($username, $tuneobj->id, $rowadds->id, $rowadds->fileext, "EXT", $rowadds->filenumber, "echo", "", 1);
		};
		echo "</span></p>\n";
	};
	if($tuneobj->publicdescription != "") {
		echo "<h3 class=\"section\"><a name=\"description\">Description</a></h3>\n\n";
		echo stripslashes($tuneobj->publicdescription)."\n\n";
	};
	if($tuneobj->text != "") {
		echo "<h3 class=\"section\"><a name=\"text\">Text</a></h3>\n\n";
		echo stripslashes($tuneobj->text)."\n\n";
	};
};
if($userobj && !$tuneobj) {
	/* BEGIN RECENT ACTIVITY */
	$count = 0;
	$query = "SELECT id FROM tunes WHERE tunesmithid = '$userid' AND public = 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$pubtunes = "(";
	if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)) {
			$pubtunes .= $row->id.", ";
		}
		$pubtunes = substr($pubtunes, 0, -2).")";
	}

	$cutoff_date = date("Y-m-d H:i:s", time() - (8 * 7 * 24 * 60 * 60)); /* 8 weeks ago */
	$query = "SELECT * FROM activity WHERE tunesmithid = '$userid' AND tuneid IN $pubtunes AND actiondate > '$cutoff_date' AND "
			."(action LIKE '%public%' OR action = 'edit text' OR action = 'edit type' OR action = 'edit tune name') " // add edit user info somehow
			."ORDER BY actiondate DESC";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

	if(mysql_num_rows($result) > 0) {
		echo "<p>Recent activity: \n";
		while($row = mysql_fetch_object($result)) {
			$reverse_action = str_replace("public", "private", $row->action);
			$query_pr = "SELECT activityid FROM activity WHERE action = '$reverse_action' AND tuneid = ".$row->tuneid." AND actiondate > '".$row->actiondate."'";
			$result_pr = mysql_query($query_pr) or die("<p>Error in query: $query_pr. ".mysql_error()."</p>");
			if(mysql_num_rows($result_pr) > 0) { $do_not_display = 1; };

			if(!$do_not_display && $count < 5) {
				if($row->tuneid) {
					$query_t = "SELECT name FROM tunes WHERE id = ".$row->tuneid." LIMIT 1";
					$result_t = mysql_query($query_t) or die("<p>Error in query: $query_t. ".mysql_error()."</p>");
					if(mysql_num_rows($result_t) > 0) { $row_t = mysql_fetch_object($result_t); $tune_name = stripslashes($row_t->name); } else { $tune_name = "deleted"; };
				};

				$query_a = "SELECT actiontext FROM actions WHERE action = '".$row->action."'";
				$result_a = mysql_query($query_a) or die("<p>Error in query: $query_a. ".mysql_error()."</p>");
				if(mysql_num_rows($result_a) > 0) { $row_a = mysql_fetch_object($result_a); $act = str_replace("--tune--", "<span class=\"success\">$tune_name</span>", $row_a->actiontext); } else { $act = $row->action; };

				if($row->action == "make mp3 public") {
					$query_m = "SELECT title FROM uploadedfiles WHERE id = '".$row->actiondesc."'";
					$result_m = mysql_query($query_m) or die("<p>Error in query: $query_m. ".mysql_error()."</p>");
					if(mysql_num_rows($result_m) > 0) { $row_m = mysql_fetch_object($result_m); $act = str_replace("--mp3title--", "'".stripslashes($row_m->title)."'", $act); };
				};

				if($count != 0) { echo " &hellip; "; };
				echo "<strong>".relative_date(strtotime($row->actiondate))."</strong>: ".$act;
				$count ++;
			};
			$tune_name = "";
			$do_not_display = 0;
		};
		echo ".</p>\n\n";
	}
	/* END RECENT ACTIVITY */

	$query = "SELECT * FROM tunes WHERE public = 1 AND tunesmithid = '$userid' ORDER BY rank, name";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

	if (mysql_num_rows($result) > 0) {
		if ($userobj->username == "jesse" || $userobj->username == "lauren") {
			echo "<table class=\"vibrant\" width=\"100%\">\n\n<thead>\n";
			echo "<tr><td>Tune Name and Description</td><td>First Line</td><td>Files</td></tr>\n";
		} else {
			echo "<table width=\"100%\"><thead>\n";
			echo "<tr><td>Tune Name</td><td>Year</td><td>Type</td><td>Files</td><td>First Line</td></tr>\n\n";
		}
		echo "</thead>\n\n";
		while($row = mysql_fetch_object($result)) {
			$queryfiles = "SELECT * FROM uploadedfiles WHERE tuneid = ".$row->id." AND public = 1 ORDER BY fileext DESC, filenumber";
			$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $queryfiles. ".mysql_error()."</p>");
			// New potential display
			if(mysql_num_rows($resultfiles) > 0 and ($userobj->username == "jesse" || $userobj->username == "lauren")) {
				$query_mp3_count = "SELECT id FROM uploadedfiles WHERE tuneid = ".$row->id." AND public = 1 AND fileext = 'mp3'";
				$result_mp3_count = mysql_query($query_mp3_count) or die("<p>Error in query: $query_mp3_count. ".mysql_error()."</p>");
				if(mysql_num_rows($result_mp3_count) > 1) { $mult_mp3s = 1; } else { $mult_mp3s = 0; };
				echo "<tr>\n";
				echo "<td><a href=\"/".$com."/".$row->nameslug."/\">".readable_tunename($row->name,"",$row->meter,NULL,"strong")."</a> ";
				echo "(".$row->year;
					if($row->additionalyear != 0) { echo "&ndash;".($row->additionalyear - 2000); };
					echo ")";
				echo " &#8226; ";
				readable_key($row->tunekey, "echo", "mm");
				echo strtolower($row->type)." &#8226; ";
				if($row->modeoftime == "mul") { echo "multiple modes of time"; } else { echo $row->modeoftime." time"; };
				echo "</td>\n";
				echo "<td>".neat_text($row->text, "first line")."</td>\n";
				echo "<td>";
				$first = 1;
				$mp3_count = 0;
				while($rowfiles = mysql_fetch_object($resultfiles)) {
					if($first != 1) { echo " &#8226; "; } else { $first = 0; };
					if($rowfiles->fileext == "mp3" && $mult_mp3s == 1) {
					$mp3_count ++;
						$format = $mp3_count;
						$title = 1;
					} elseif($rowfiles->fileext == "mp3") {
						$format = "EXT";
						$title = 1;
					} else {
						$format = "EXT";
						$title = 1;
					}; // option here to change display of MP3s only.
					if($rowfiles->fileext == "mp3") {
						formatted_mp3player($username, $row->id, $rowfiles->id, $rowfiles->fileext, $format, $rowfiles->filenumber, "echo", $title, 1);
					} else {
						formatted_filename($username, $row->id, $rowfiles->id, $rowfiles->fileext, $format, $rowfiles->filenumber, "echo", $title, 1);
					};
				};
				echo "</td>\n";
				echo "</tr>\n\n";
			} // Standard display
			elseif(mysql_num_rows($resultfiles) > 0) {
				$query_mp3_count = "SELECT id FROM uploadedfiles WHERE tuneid = ".$row->id." AND public = 1 AND fileext = 'mp3'";
				$result_mp3_count = mysql_query($query_mp3_count) or die("<p>Error in query: $query_mp3_count. ".mysql_error()."</p>");
				if(mysql_num_rows($result_mp3_count) > 1) { $mult_mp3s = 1; } else { $mult_mp3s = 0; };
				echo "<tr>\n";
				echo "<td><a href=\"/".$com."/".$row->nameslug."/\">".readable_tunename($row->name,"",$row->meter)."</a></td>\n";
				echo "<td>".$row->year;
					if($row->additionalyear != 0) { echo (2000 - $row->additionalyear); };
					echo "</td>\n";
				echo "<td>".$row->type."</td>\n";
				echo "<td>";
				$first = 1;
				$mp3_count = 0;
				while($rowfiles = mysql_fetch_object($resultfiles)) {
					if($first != 1) { echo " &#8226; "; } else { $first = 0; };
					if($rowfiles->fileext == "mp3" && $mult_mp3s == 1) {
					$mp3_count ++;
						$format = $mp3_count;
						$title = 1;
					} elseif($rowfiles->fileext == "mp3") {
						$format = "EXT";
						$title = 1;
					} else {
						$format = "EXT";
						$title = 1;
					}; // option here to change display of MP3s only.
					if($rowfiles->fileext == "mp3") {
						formatted_mp3player($username, $row->id, $rowfiles->id, $rowfiles->fileext, $format, $rowfiles->filenumber, "echo", $title, 1);
					} else {
						formatted_filename($username, $row->id, $rowfiles->id, $rowfiles->fileext, $format, $rowfiles->filenumber, "echo", $title, 1);
					};
				};
				echo "</td>\n";
				echo "<td>".neat_text($row->text, "first line")."</td>\n";
				echo "</tr>\n\n";
			};
		};
		echo "</table>\n\n";
		if($description) { echo "<p><strong>About ".$firstname."</strong></p>\n\n".stripslashes($description)."\n\n"; };
		echo "<p class=\"right\"><a href=\"/\">Back to List of Composers &raquo;</a></p>\n\n";
	};
} elseif(!$userobj) {
	$query_pc = "SELECT * FROM pagecontent WHERE page = 'public/index.php' ORDER BY priority";
	$result_pc = mysql_query($query_pc) or die("<p>Error in query: $query_pc. ".mysql_error()."</p>");
	while($row_pc = mysql_fetch_object($result_pc)) {
		if($row_pc->id != 1) {
			echo "<h3 class=\"section";
			if($prev_pc == 1) { echo "nodivider"; };
			echo"\">".$row_pc->contenttitle."</h3>\n\n";
			echo $row_pc->content;
		} else {
			$query = "SELECT * FROM users WHERE public = 1 ORDER BY lastname, firstname";
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
			$queryfiles = "SELECT id FROM uploadedfiles WHERE public = 1 ";
			$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $query. ".mysql_error()."</p>");

			if (mysql_num_rows($result) > 0 && mysql_num_rows($resultfiles) > 0) {
			?>
				<table width="100%"><thead>
					<tr>
						<td>Name</td><td>Tunes</td>
					</tr>
				</thead>
			<?php
				$i = 1;
				while($row = mysql_fetch_object($result)) {
					$querytunes = "SELECT * FROM tunes WHERE tunesmithid = '$row->id' AND public = 1";
					$resulttunes = mysql_query($querytunes) or die("<p>Error in query: $querytunes. ".mysql_error()."</p>");

					$queryfilestunes = "SELECT * FROM uploadedfiles WHERE tunesmithid = '$row->id' AND public = 1";
					$resultfilestunes = mysql_query($queryfilestunes) or die("<p>Error in query: $querytunes. ".mysql_error()."</p>");
					if(mysql_num_rows($resulttunes) > 0 && mysql_num_rows($resultfilestunes) > 0) {
						echo "<tr><td><a href=\"/".$row->publicfoldername."/\">".$row->firstname." ".$row->lastname."</a>";
						if($row->city && $row->state) {
							echo " (".$row->city.", ".$row->state.")";
						}
						echo "</td><td>".mysql_num_rows($resulttunes)."</td></tr>\n\n";
					}
				};
			?>
				</table>
			<?php
			} else {
			?>
				<p class="alert">No composers have elected to make their tunes public.</p>
			<?php
			};
		};
		$prev_pc = $row_pc->id;
	};
};
// free result set memory
mysql_free_result($result);
?>

<?php mysql_close($connection); ?>
	</div>
	</div>
<?php require("includes/footer.php"); ?>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-3336718-15");
pageTracker._trackPageview();
} catch(err) {}</script>
<script src="/scripts/multiPlayer.js" type="text/javascript"></script>
</body>
</html>
