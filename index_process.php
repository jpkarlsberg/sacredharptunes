<?php
/* connect to db */
require("includes/constants.php");
require("includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");

$public_process = "Y";
require("includes/user.php");

$id = $_GET["id"];
$action = $_GET["action"];

/* prepare form data and add to db */
if(is_numeric($id) && $action == "downloadcount") {
	$query = "SELECT * FROM uploadedfiles WHERE id = '$id' LIMIT 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_object($result);
		$queryuser = "SELECT username FROM users WHERE id = '".$row->tunesmithid."' LIMIT 1";
		$resultuser = mysql_query($queryuser) or die("<p>Error in query: $queryuser. ".mysql_error()."</p>");
		if(mysql_num_rows($resultuser) > 0) {
			$rowuser = mysql_fetch_object($resultuser);
			if($username != $rowuser->username) {
				$newdlcount = $row->downloadcount + 1;
			} else {
				$newdlcount = $row->downloadcount;
			}
			$filename = filename($rowuser->username, $row->tuneid, $row->fileext, $row->filenumber);
			$query = "UPDATE uploadedfiles SET downloadcount = '".$newdlcount."' WHERE id = '".$row->id."'";
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
			// free result set memory
			mysql_close($connection);
			header( 'Location: '.PDF_VIEWER_CODE.SITE_FILES_URL.$filename );
		};
	};
} else {
// free result set memory
mysql_close($connection);
header( 'Location: index.php?deleted='.$tunename );
};
?>