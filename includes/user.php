<?php
if($username == "") { $username = $_COOKIE['username']; };
if(substr_count($username, "guest") > 0) { 
	$username = str_replace("guest", "", $username);
	$guest = 1;
} else {
	$guest = 0;
};
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if(mysql_num_rows($result) > 0) {
	$row = mysql_fetch_object($result);
	$username = $row->username;
	$name = $row->firstname." ".$row->lastname;
		$firstname = $row->firstname;
		$lastname = $row->lastname;
	$city = $row->city;
	$state = $row->state;
	$admin = $row->admin;
	$usernameid = $row->id;
	$userpublic = $row->public;
	$publicfoldername = $row->publicfoldername;
} else {
	if($public_process == "Y") {
		$username = "";
	} else {
		die ("<p>Username does not exist... there must be a problem.</p>");
	};
};
?>
