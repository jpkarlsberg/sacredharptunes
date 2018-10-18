<?php
require("../includes/constants.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
require("../includes/user.php");
if($_POST['submit'] == "Initialize User") {
	// make sure you have a username and email
	if(empty($_POST['username']) || $_POST['username'] == "New User Login" || !ctype_lower($_POST['username'])) {
		$error = 16;
	} else {
		$query_name = "SELECT id FROM users WHERE username = '".$_POST['username']."'";
		$result_name = mysql_query($query_name) or die("<p>Error in query: $query_name. ".mysql_error()."</p>");
		if(mysql_num_rows($result_name) > 0) { 
			$error = 17;
		} else {
			$name = mysql_escape_string($_POST['username']);
			if(empty($_POST['email']) || $_POST['email'] == "New User Email") {
				$error = 18;
			} else {
				$email = mysql_escape_string($_POST['email']);
			};
		};
	};
	if(!$error) { 
		// enter username and email into users table
		$query = "INSERT INTO users (username, email, admin, dateadded, useradded) VALUES ('$name', '$email', 0, '".date("Y-m-d H:i:s")."', '$username')";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		$newuserid = mysql_insert_id();
		$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES (".$newuserid.", 'initialize user', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		// create redirect message
		$action = "initialize";
	};
} elseif($_POST['submit'] == "Send Initialization E-mail") {
	$name = mysql_escape_string($_POST['name']);
	$email = mysql_escape_string($_POST['useremail']);
	// send e-mail to new user
	$subject = SITE_NAME_PRIVATE.": Initialize Your Account";
	$body = "Hi ".ucwords($name).",\n\n";
	$body .= "I've created a user account for you with the ".SITE_NAME_PRIVATE.". You can use the organizer to rank, categorize, and share your tunes. You can also use the organizer to upload PDFs, MIDI, MP3, and Melody/Harmony Assistant files so that you can access them anywhere you go off of the Internet.\n\n";
	$body .= "Click on the link below or copy it into your browser to set up your account.\n\n";
	$body .= SITE_URL.SITE_LOGIN_PATH."initialize.php?name=".$name."\n\n";
	$body .= "Enjoy!\n";
	$body .= "Jesse";
	$headers = "From: ".ADMIN_NAME." - ".SITE_ABBR." Admin <".ADMIN_EMAIL.">\r\n" .
		"X-Mailer: php";
	if (mail($email, $subject, $body, $headers)) {
		// success
	} else {
		die("<p>Message delivery failed...</p>");
	};
	$query = "UPDATE users SET initialized = 1, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE username = '$name'";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tunesmithid, action, actiondesc, actiondate) VALUES (".$usernameid.", 'sent initialization email', 'To: ".$email."\n\nSubject: ".mysql_escape_string($subject)."\n\n".mysql_escape_string($body)."', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	// create redirect message
	$action = "sent email";
} elseif($_POST['submit'] == "Reset Pass") {
	$userid = $_POST['userid'];
	$name = $_POST['name'];
	$email = $_POST['useremail'];
	$query = "UPDATE users SET password = '' WHERE id = $userid";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$query = "UPDATE users SET ispasswordreset = 1, dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = $userid";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES (".$userid.", 'reset pass', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	// send e-mail to user
	$subject = SITE_NAME_PRIVATE.": Password Reset";
	$body = "Hi ".$name.",\n\n";
	$body .= "Your password for the ".SITE_NAME_PRIVATE." has been reset.\n\n";
	$body .= "Click on the link below or copy it into your browser to select a new password.\n\n";
	$body .= SITE_URL.SITE_LOGIN_PATH."initialize.php?id=".$userid."&action=pass\n\n";
	$body .= "Thanks,\n";
	$body .= "Jesse";
	$headers = "From: ".ADMIN_NAME." - ".SITE_ABBR." Admin <".ADMIN_EMAIL.">\r\n" .
		"X-Mailer: php";
	if (mail($email, $subject, $body, $headers)) {
		// success
	} else {
		die("<p>Email: ".$email.", Message delivery failed...</p>");
	}
	// create redirect message
	$action = "reset";
} elseif($_POST['submit'] == "+ Admin") {
	$userid = $_POST['userid'];
	$name = $_POST['name'];
	$query = "UPDATE users SET admin = '1', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = $userid";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES (".$userid.", 'make admin', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$action = "addadmin";
} elseif($_POST['submit'] == "? Admin") {
	$userid = $_POST['userid'];
	$name = $_POST['name'];
	$query = "UPDATE users SET admin = '0', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = $userid";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES (".$userid.", 'remove admin', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	$action = "subtractadmin";
} elseif($_POST['submit'] == "Send E-mail") {
	if($_POST['users'] == "Select a User") {
		$error = 14;
	} elseif($_POST['users'] == "all") {
		$query = "SELECT * FROM users WHERE active = 1";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		$email = ""; $name == "all users";
		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result)) { 
				$email .= $row->email.", ";
			}
		}
	} else {
		$id = $_POST['users'];
		$query = "SELECT * FROM users WHERE id = $id LIMIT 1";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result)) { 
				$email = $row->email;
				if($row->lastname && $row->firstname) {
					$name = $row->firstname." ".$row->lastname;
				} else {
					$name = $row->username;
				}
			}
		};
	};
	if(!$error) {
		$subject = stripslashes($_POST['emailsubject']);
		$body = stripslashes($_POST['emailbody']);
		$body .= "\n\n----\n";
		$body .= "This e-mail was sent through the ".SITE_NAME_PRIVATE." administrative interface.\n";
		$body .= "Visit ".SITE_ABBR.": ".SITE_URL."\n";
		$headers = "From: ".ADMIN_NAME." - ".SITE_ABBR." Admin <".ADMIN_EMAIL.">\r\n" . "X-Mailer: php";
		// send e-mail to user
		if (mail($email, $subject, $body, $headers)) {
			// success
			// create redirect message
			$action = "email";
			$query = "INSERT INTO activity (tunesmithid, action, actiondesc, actiondate) VALUES (".$usernameid.", 'sent admin e-mail', 'To: ".$_POST['users']."\n\nSubject: ".mysql_escape_string($subject)."\n\nBody:\n".mysql_escape_string($body)."', '".date("Y-m-d H:i:s")."')";
			$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		} else {
			$error = 15;
		}
	};
} elseif($_POST['submit'] == "Trash Deleted Users") {
	$query = "SELECT * FROM users WHERE active = '0'";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$name = mysql_num_rows($result);

	$query = "DELETE FROM users WHERE active = 0";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$query = "DELETE FROM tunes WHERE active = 0";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$query = "DELETE FROM uploadedfiles WHERE active = 0";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$query = "DELETE FROM notes WHERE active = 0";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	
	$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES ('".$userid."', 'trash deleted users', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");

	$action = "trashdeletedusers";
} elseif($_POST['submit'] == "Delete") { /* Add functionality deleting tunes and tune files... but first put in a confirmation check... or at least make tunes "inactive" */
	/* This functionality was moved to the delete.php and delete_process.php pages on Jan. 31, 2010. */
} elseif($_POST['submit'] == "No, Cancel") {
	$action = "canceldelete";
	$name = $_POST['username'];
} elseif($_POST['submit'] == "Edit Page Content") { /* make more sophisticated so that it first selects distinct pages and then queries movable pagecontent for each page */
	$query = "SELECT * FROM pagecontent WHERE movable = 1";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	if(mysql_num_rows($result) > 0) {
		$priorities = array();
		while($row = mysql_fetch_object($result)) {
			if(in_array($_POST['priority_'.$row->id], $priorities)) {
				$error = 19;
			} else {
				array_push($priorities, $_POST['priority_'.$row->id]);
			};
		}
		if(!$error) {
			$query = "SELECT * FROM pagecontent WHERE movable = 1";
			$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
			if(mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result)) {
					if($_POST['priority_'.$row->id] != $row->priority) {
						$query_p = "UPDATE pagecontent SET priority = ".$_POST['priority_'.$row->id]." WHERE id = ".$row->id;
						$result_p = mysql_query($query_p) or die("<p>Error in query: $query_p. ".mysql_error()."</p>");
						$action = "editpagecontent";
					}
				}
			}
		}		
	}
	$admintools = "pubpage";
};
if(!$action && !$error) { $action = "noaction"; };
// redirect to admin.php with message from above
if($error) {
	$actionerror = "?error=".$error;
} else {
	$actionerror = "?action=".$action;
};
if($admintools == "pubpage") {
	$admintools = "&admintools=pubpage";
} else {
	$admintools = "&admintools=user";
}
header( 'Location: admin.php'.$actionerror.'&modname='.$name.$admintools );
?>