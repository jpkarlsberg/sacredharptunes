<?php include("../includes/init.php");
echo "<h2>Populate Tune Slugs</h2>\n\n";
$query = "SELECT * FROM tunes ORDER BY tunesmithid";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if (mysql_num_rows($result) > 0) {
	$slugs = array();
	while($row = mysql_fetch_object($result)) {
		echo "<p>".$row->tunesmithid.": ";
		$slug = strtolower(preg_replace('/[^A-Za-z0-9()]/', '-', preg_replace('/[^A-Za-z0-9 -]/', '', trim($row->name))));
		$q_slug = "SELECT * FROM tunes WHERE tunesmithid = '".$row->tunesmithid."' AND nameslug = '".$slug."'";
		$r_slug = mysql_query($q_slug) or die("<p>Error in query: $q_slug. ".mysql_error()."</p>");
		if(mysql_num_rows($r_slug) == 0) {
			$q_add_slug = "UPDATE tunes SET nameslug = '".$slug."' WHERE id = '".$row->id."'";
			$r_add_slug = mysql_query($q_add_slug) or die("<p>Error in query: $q_add_slug. ".mysql_error()."</p>");
			echo $slug;
		} else {
			for($int = 2; ; $int ++) {
				$slug_appended = $slug."-".$int;
				$q_slug = "SELECT * FROM tunes WHERE tunesmithid = '".$row->tunesmithid."' AND nameslug = '".$slug_appended."'";
				$r_slug = mysql_query($q_slug) or die("<p>Error in query: $q_slug. ".mysql_error()."</p>");
				if(mysql_num_rows($r_slug) == 0) { break; };
			};
			$q_add_slug = "UPDATE tunes SET nameslug = '".$slug_appended."' WHERE id = '".$row->id."'";
			$r_add_slug = mysql_query($q_add_slug) or die("<p>Error in query: $q_add_slug. ".mysql_error()."</p>");
			echo $slug_appended;
			$slug_appended = "";
		};
		echo "</p>\n";
	}
} else {
	echo "<p>No tunes in query.</p>\n";
};
include("../includes/shutdown.php"); ?>
