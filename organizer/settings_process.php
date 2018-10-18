<?php
/* connect to db */
require("../includes/constants.php");
require("../includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");

/* prepare form data and add to db */

if($_POST['submit'] == "Update Public Tunes Settings") {
	$usernameid = $_POST['usernameid'];
	$public = $_POST['public'];
	$publictunes = $_POST['publictune'];
	$mp3s = $_POST['mp3'];
	$mp3ranks = $_POST['mp3rank'];
	$pdfs = $_POST['pdf'];
	$mids = $_POST['mid'];
	$removes = $_POST['remove'];
	$tunestoadd = $_POST['tunetoadd'];

	$publicfoldername = $_POST['publicfoldername'];
	$err_pfn = "";
	$regex = "/^[a-z0-9\-]+$/";
	if(preg_match($regex, $publicfoldername)) {
		$query = "SELECT publicfoldername FROM users WHERE publicfoldername = '$publicfoldername' AND id != $usernameid";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			$err_pfn = "&errpfn=20";
		} else {
			$query = "UPDATE users SET publicfoldername = '".$publicfoldername."', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = ".$usernameid;
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		};
	} else {
		$err_pfn = "&errpfn=21";
	};
	// $mp3s contains sets of tune ids and uploaded file ids. This separates the array into two arrays, $mp3s_row for the tune ids, and $mp3s_mp3 for the uploaded file ids.
	$mp3s_row = array();
	$mp3s_mp3 = array();
	for($i = 0; $i < sizeof($mp3s); $i ++) {
		//echo "<p>".$mp3s[$i]."</p>";
		if(strpos($mp3s[$i], ',')) {
			$this_row = split(',', $mp3s[$i], 2);
			$mp3s_row[$i] = $this_row[0];
			$mp3s_mp3[$i] = $this_row[1];
			//echo "<p>".$mp3s_row[$i]." ".$mp3s_mp3[$i]."</p>";
		} else {
			$mp3s_row[$i] = -1;
			$mp3s_mp3[$i] = -1;
		}
	}
	
	function to_one_zero($onoff) {
		if($onoff == "on") { $onezero = 1; } else { $onezero = 0; };
		return $onezero;
	}
	$query = "UPDATE users SET public = ".to_one_zero($public).", dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = ".$usernameid;
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

	if($public == "on") {
		for($i = 0; $i < sizeof($publictunes); $i ++) {
			if(is_array($removes) && in_array($publictunes[$i], $removes)) { // set public to 0 if removing tune from public tunes list
				$query = "UPDATE tunes SET public = 0, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = ".$publictunes[$i];
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				$query = "UPDATE uploadedfiles SET public = 0, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE tuneid = ".$publictunes[$i];
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$publictunes[$i].", '$usernameid', 'make private', '".date("Y-m-d H:i:s")."')";
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
			} else { // otherwise, set appropriate access level for PDF and MIDI, then deal with MP3s
				if(is_array($pdfs) && in_array($publictunes[$i], $pdfs)) {
					$this_pdf = 1; // prepare to set pdf to public
					$query = "SELECT id FROM uploadedfiles WHERE tuneid = ".$publictunes[$i]." AND fileext = 'pdf' AND public = 0";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) { // if pdf was previously private, add action of making it public to activity table
						$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$publictunes[$i].", '$usernameid', 'make pdf public', '".date("Y-m-d H:i:s")."')";
						$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					}
				} else {
					$this_pdf = 0; // prepare to set pdf to private
					$query = "SELECT id FROM uploadedfiles WHERE tuneid = ".$publictunes[$i]." AND fileext = 'pdf' AND public = 1";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) { // if pdf was previously public, add action of making it private to activity table
						$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$publictunes[$i].", '$usernameid', 'make pdf private', '".date("Y-m-d H:i:s")."')";
						$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					}
				};
				$query = "UPDATE uploadedfiles SET public = ".$this_pdf." WHERE tuneid = ".$publictunes[$i]." AND fileext = 'pdf'";
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

				if(is_array($mids) && in_array($publictunes[$i], $mids)) {
					$this_mid = 1; // prepare to set mid to public
					$query = "SELECT id FROM uploadedfiles WHERE tuneid = ".$publictunes[$i]." AND fileext = 'mid' AND public = 0";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) { // if midi was previously private, add action of making it public to activity table
						$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$publictunes[$i].", '$usernameid', 'make mid public', '".date("Y-m-d H:i:s")."')";
						$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					}
				} else {
					$this_mid = 0; // prepare to set mid to private
					$query = "SELECT id FROM uploadedfiles WHERE tuneid = ".$publictunes[$i]." AND fileext = 'mid' AND public = 1";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) { // if midi was previously public, add action of making it private to activity table
						$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$publictunes[$i].", '$usernameid', 'make mid private', '".date("Y-m-d H:i:s")."')";
						$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
					}
				};
				$query = "UPDATE uploadedfiles SET public = ".$this_mid." WHERE tuneid = ".$publictunes[$i]." AND fileext = 'mid'";
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

				$public_mp3s = array();
				if(is_array($mp3s) && in_array($publictunes[$i], $mp3s_row)) { // if any mp3s have been checked off as public, find them
					$condition_statement = "";
					for($j = 0; $j < sizeof($mp3s_row); $j ++) {
						if($mp3s_row[$j] == $publictunes[$i]) {
							// not working...
							$public_mp3s[] = array($mp3s_mp3[$j], $mp3ranks[$j]);
							$query_r = "UPDATE uploadedfiles SET rank = '".$mp3ranks[$i]."' WHERE id = ".$mp3s_mp3[$j]; // find the mp3 if already public,
							$result_r = mysql_query($query_r) or die("<p>Error in query: $query_r. ".mysql_error()."</p>");
							if($condition_statement != "") { // assemble a condition statement,
								$condition_statement .= " OR id = '".$mp3s_mp3[$j]."'";
							} else {
								$condition_statement = "id = '".$mp3s_mp3[$j]."'";
							}
							$query_p = "SELECT id FROM uploadedfiles WHERE id = ".$mp3s_mp3[$j]." AND public = 1"; // find the mp3 if already public,
							$result_p = mysql_query($query_p) or die("<p>Error in query: $query_p. ".mysql_error()."</p>");
							if(mysql_num_rows($result_p) <= 0) { // add action of making this mp3 public to activity table,
								$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondesc, actiondate) VALUES (".$publictunes[$i].", ".$usernameid.", 'make mp3 public', '".$mp3s_mp3[$j]."', '".date("Y-m-d H:i:s")."')";
								$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
							}
						}
					}
					// set all mp3s for this tune to private,
					$query = "UPDATE uploadedfiles SET public = 0, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE tuneid = ".$publictunes[$i]." AND fileext = 'mp3'";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

					// and set those files that have been checked off to public.
					$query = "UPDATE uploadedfiles SET public = 1, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE ".$condition_statement;
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				} else {
					// if no public mp3s, then set all mp3s for this tune to private
					$query = "UPDATE uploadedfiles SET public = 0, rank = NULL, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE tuneid = ".$publictunes[$i]." AND fileext = 'mp3'";
					$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				};
			};
		}
		
		for($i = 0; $i < sizeof($tunestoadd); $i ++) {
			if($tunestoadd[$i] != "Select a Tune") { 
				$query = "UPDATE tunes SET public = 1, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = ".$tunestoadd[$i];
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES (".$tunestoadd[$i].", ".$usernameid.", 'make public', '".date("Y-m-d H:i:s")."')";
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
/*				$query = "SELECT * FROM tunes WHERE id = ".$tunestoadd[$i]." LIMIT 1";
				$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
				if(mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) {
						echo "<p>".$i.": ".$row->id.", ".readable_tunename($row->name,$row->nameadditional,$row->meter)."</p>\n\n";
					};
				};
*/			};
		};
	};
};
if($_POST['submit'] == "Update Personal Settings") {
	$query = "UPDATE users SET firstname = '".$_POST['tunesmithfirst']."', lastname = '".$_POST['tunesmithlast']."', city = '".$_POST['city']."', state = '".$_POST['state']."', description = '".mysql_escape_string($_POST['description'])."' WHERE id = '".$_POST['usernameid']."'";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES (".$_POST['usernameid'].", 'edit personal settings', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	if($_POST['tunesmithlogin'] != $_POST['username']) {
		$query = "SELECT id FROM users WHERE username = '".$_POST['tunesmithlogin']."'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			$usernameerror = "&ue=1";
		} else {
			$query = "UPDATE users SET username = '".$_POST['tunesmithlogin']."' WHERE id = '".$_POST['usernameid']."'";
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
			$oldfolder = "users/".$_POST['username'];
			$newfolder = "users/".$_POST['tunesmithlogin'];
			rename($oldfolder, $newfolder);
			setcookie("username", $_POST['tunesmithlogin']);

		};
	};
};
/* redirect to add.php */
if($_POST['submit'] == "Update Public Tunes Settings") { $action = "publictunes"; } else { $action = "personalsettings".$usernameerror; };
$action = "?action=".$action;
header( 'Location: settings.php'.$action.$err_pfn );

$clear = 1;
// free result set memory
?>