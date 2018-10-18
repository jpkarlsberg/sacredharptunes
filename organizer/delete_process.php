<?php
/* connect to db */
require("../includes/constants.php");
require("../includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");

/* prepare form data and query db */
$id = $_POST['id'];

if($_POST['deletetype'] == "tune") {
	$query = "SELECT name, meter, rank, tunesmithid FROM tunes WHERE id = '$id' LIMIT 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_object($result);
		$tunename = stripslashes($row->name);
		$tunenameadditional = stripslashes($row->nameadditional);
		$meter = stripslashes($row->meter);
		$rank = $row->rank;
		$tunesmithid = $row->tunesmithid;
		$querytunesmith = "SELECT username FROM users WHERE id = '$tunesmithid' LIMIT 1";
		$resulttunesmith = mysql_query($querytunesmith) or die("<p>Error in query: $querytunesmith. ".mysql_error()."</p>");
		if(mysql_num_rows($resulttunesmith) > 0) {
			$rowtunesmith = mysql_fetch_object($resulttunesmith);
			$username = $rowtunesmith->username;
		};
		if($rank > 0) {
			$query = "SELECT rank, id FROM tunes WHERE tunesmithid = '$tunesmithid' AND rank > '$rank'";
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
			if(mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result)) {
					$newrank = $row->rank - 1;
					$rerankid = $row->id;
					$queryrerank = "UPDATE tunes SET rank = '$newrank' WHERE id = '$rerankid' LIMIT 1";
					$resultrerank = mysql_query($queryrerank) or die("<p>Error in query: $queryrerank. ".mysql_error()."</p>");
				};
			};
		};
		
		$query = "DELETE FROM tunes WHERE id = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "DELETE FROM notes WHERE tuneid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "SELECT * FROM uploadedfiles WHERE tuneid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			/* initialize S3 object for file-related processes */
			require("../includes/S3.php");
			$s3 = new s3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
			while($row = mysql_fetch_object($result)) {
				if($s3->deleteObject(S3_BUCKET, $row->filename)) { /* file deleted... what should it do to validate this for the user? */ }; 
			}
		}
		
		$query = "DELETE FROM uploadedfiles WHERE tuneid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
		$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'delete tune', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		// free result set memory
		mysql_close($connection);
		
		header( 'Location: index.php?deleted='.$tunename );
	} else { echo "<p>There has been an error. Return to <a href=\"index.php\">home</a></p>"; };
 } elseif($_POST['deletetype'] == "user") {
	$query = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_object($result);
		
		$query = "UPDATE users SET active = 0 WHERE id = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "UPDATE tunes SET active = 0 WHERE tunesmithid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "UPDATE uploadedfiles SET active = 0 WHERE tunesmithid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "UPDATE notes SET active = 0 WHERE tunesmithid = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		
		$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES ('$id', 'delete user', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		
		if($row->firstname || $row->lastname) {
			$modname = $row->firstname." ".$row->lastname;
		} else {
			$modname = $row->username;
		}
		// free result set memory
		mysql_close($connection);
		
		header( 'Location: admin.php?action=delete&modname='.$modname );
	} else { echo "<p>There has been an error. Return to <a href=\"index.php\">home</a></p>"; };
 };
?>