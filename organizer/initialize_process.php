<?php
require("../includes/constants.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
$errors = "";
$guestpassword = NULL;
$extras = "";
if($_POST['submit'] == "Set up Account") {
	$id = $_POST['id'];
	$username = $_POST['username'];
	// make sure fields were filled out correctly
	if(empty($_POST['lastname']) || empty($_POST['firstname'])) {
		if($errors == "") { $errors = "&errors=8"; } else { $errors .= ",8"; };
	} else {
		$firstname = mysql_escape_string($_POST['firstname']);
		$lastname = mysql_escape_string($_POST['lastname']);
		$extras .= "&fn=".$firstname."&ln=".$lastname;
	};
	if(empty($_POST['email'])) {
		if($errors == "") { $errors = "&errors=9"; } else { $errors .= ",9"; };
	} else {
		$email = mysql_escape_string($_POST['email']);
	};
	if(empty($_POST['password']) || empty($_POST['passwordcheck']) || $_POST['passwordcheck'] != $_POST['password']) {
		if(empty($_POST['password'])) { if($errors == "") { $errors = "&errors=10"; } else { $errors .= ",10"; }; }
		elseif(empty($_POST['passwordcheck'])) { if($errors == "") { $errors = "&errors=11"; } else { $errors .= ",11"; }; }
		elseif($_POST['passwordcheck'] != $_POST['password']) { if($errors == "") { $errors = "&errors=12"; } else { $errors .= ",12"; }; };
	} else {
		$password = mysql_escape_string($_POST['password']);
	};
	if($_POST['guestpasswordcheck'] != $_POST['guestpassword']) {
		if($errors == "") { $errors = "&errors=13"; } else { $errors .= ",13"; };
	} elseif(!empty($_POST['guestpassword'])) {
		$guestpassword = mysql_escape_string($_POST['password']);
		$action = "setupwithguest";
		$extras .= "&gl=".$username."guest&gp=".$guestpassword;
	};
	if($errors == "") { // if no errors, update the user's row
		$query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', password = '$password', guestpassword = '$guestpassword', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

		$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES ('$id', 'set up account', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	};
	mysql_close($connection);
	if(!$action) { $action = "setup"; };

} elseif($_POST['submit'] == "Set Password") {
	$id = $_POST['id'];
	$username = $_POST['username'];
	if(empty($_POST['password']) || empty($_POST['passwordcheck']) || $_POST['passwordcheck'] != $_POST['password']) {
		if(empty($_POST['password'])) { if($errors == "") { $errors = "&errors=10"; } else { $errors .= ",10"; }; }
		elseif(empty($_POST['passwordcheck'])) { if($errors == "") { $errors = "&errors=11"; } else { $errors .= ",11"; }; }
		elseif($_POST['passwordcheck'] != $_POST['password']) {if($errors == "") { $errors = "&errors=12"; } else { $errors .= ",12"; }; };
	} else {
		$password = mysql_escape_string($_POST['password']);
	};
	if($errors == "") { // if no errors, update the user's row
		$query = "UPDATE users SET password = '$password', ispasswordreset = '0', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$id'";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

		$query = "INSERT INTO activity (tunesmithid, action, actiondate) VALUES ('$id', 'set new password', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	};
	mysql_close($connection);
	if($errors == "") { $action = "changepass"; } else { $action = "pass"; $extras .= "&id=".$id; };
};
if(!$action || ($errors != "" && $action != "changepass" && $action != "pass")) { $action = "noaction"; };
// redirect to admin.php with message from above
$action = "?action=".$action;
if($firstname && $lastname && $errors == "") { $name = $firstname." ".$lastname; } else { $name = $username; };

header( 'Location: initialize.php'.$action.'&name='.$name.$extras.$errors );
?>