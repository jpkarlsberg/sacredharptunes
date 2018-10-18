<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php $page = "init";
require("../includes/constants.php");
require("../includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
require("../includes/head.php"); ?>
</head>

<body>
<div id="pagelogin">
	<div id="header"><h1><?php echo SITE_NAME_PRIVATE; ?></h1></div>
	<div id="main">
	<div id="content">
<?php
if ($_GET[action]) {
	$action = $_GET[action];
}
if ($_GET[name]) {
	$name = $_GET[name];
}
if($action == "setupwithguest" || $action == "setup") {
	echo "<p><strong>Congratulations ".$name."</strong>, you have successfully set up your account with the ".SITE_NAME_PRIVATE.". To use your account you will need to log in with the username that was assigned to you and the password you selected on the previous page.</p>\n\n";
	if($action == "setupwithguest") { 
		echo "<p>You have also set up a guest login for your account.</p>\n\n";
	};
	//echo "<p>This information has been e-mailed to you.</p>\n\n"; // no it hasn't! set this up.
	echo "<p>Now you can <a href=\"".SITE_URL.SITE_LOGIN_PATH."\">begin using your account &raquo;</a></p>\n\n";
} elseif($action == "changepass") {
	echo "<p>You have successfully set your password. You may now <a href=\"".SITE_URL.SITE_LOGIN_PATH."\">log in</a>.</p>\n\n";
} elseif($action == "pass") {
	$query = "SELECT * FROM users WHERE id = '$id' AND ispasswordreset = 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)) { 
			if($errors) { /* error messages */
				echo "<p class=\"alert\">";
				$err_arr = explode(",", $errors);
				foreach ($err_arr as $err_val) {
					$query = "SELECT error FROM errors WHERE id = $err_val LIMIT 1";
					$result = mysql_query($query) or die("<p>There was an error with the previous operation. Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) {
						$row_err = mysql_fetch_object($result);
						echo $row_err->error."<br />\n";
					} else {
						echo "There was an error with the previous operation.<br />";
					};
				};
				echo "</p>\n\n";
			};
		
		?>
<p>Welcome <?php echo $row->firstname." ".$row->lastname; ?>.<br />You may set your password by typing it into the fields below:</p>
<form action="initialize_process.php" method="post">
	<input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="username" id="username" value="<?php echo $row->username; ?>" />
	<div><label for="password">Password:</label> <input type="password" name="password" id="password" maxlength="20" size="15" /></div>
	<div><label for="passwordcheck">Password:</label> <input type="password" name="passwordcheck" id="passwordcheck" maxlength="20" size="15" /> <span class="subtle">(enter again)</span></div>
	<div><input type="submit" name="submit" id="submit" class="submit" value="Set Password" /></div>
</form>
<?php	};
	} else {
		echo "<p class=\"alert\">Sorry. Unable to set password for this user at this time.</p>
		<p>Please <a href=\"index.php\">log in</a>, or <a href=\"mailto:".ADMIN_EMAIL."\">contact ".ADMIN_NICKNAME."</a> for assistance.</p>\n\n";
	};
} elseif($name) { // initialization form
$query = "SELECT id, email, password, ispasswordreset FROM users WHERE username = '".$name."'";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if(mysql_num_rows($result) > 0) {
	while($row = mysql_fetch_object($result)) {
		if($row->password == "" && $row->ispasswordreset == 0) {
			if($errors) { /* error messages */
				echo "<p class=\"alert\">";
				$err_arr = explode(",", $errors);
				foreach ($err_arr as $err_val) {
					$query = "SELECT error FROM errors WHERE id = $err_val LIMIT 1";
					$result = mysql_query($query) or die("<p>There was an error with the previous operation. Error in query: $query. ".mysql_error()."</p>");
					if(mysql_num_rows($result) > 0) {
						$row_err = mysql_fetch_object($result);
						echo $row_err->error."<br />\n";
					} else {
						echo "There was an error with the previous operation.<br />";
					};
				};
				echo "</p>\n\n";
			};
?>
		<p><strong>Welcome to the <?php echo SITE_NAME_PRIVATE; ?>.</strong> You can use this web site to rank, categorize, and otherwise keep track of your tunes. You can also use the <?php echo SITE_ABBR; ?> to upload PDF, MIDI, MP3, and Melody/Harmony Assistant files so that you can access them anywhere you go off of the Internet.</p>
		<p>To begin using the <?php echo SITE_ABBR; ?>, please fill out the form below:</p>
		<form action="initialize_process.php" method="post">
			<input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="username" id="username" value="<?php echo $name; ?>" />
			<input type="hidden" name="email" id="email" value="<?php echo $row->email; ?>" />
			<fieldset>
				<legend>Contact Information</legend>
				<div><label class="fixedsize" for="fullname">First Name:</label> <input type="text" name="firstname" id="firstname" maxlength="40" size="30" value="<?php if($fn) { echo $fn; }; ?>" /></div>
				<div><label class="fixedsize" for="lastname">Last Name:</label> <input type="text" name="lastname" id="lastname" maxlength="40" size="30" value="<?php if($ln) { echo $ln; }; ?>" /></div>
				<div><label class="fixedsize" for="email">E-mail:</label> <input type="text" name="email" id="email" maxlength="50" size="30" value="<?php echo $row->email; ?>" /></div>
			</fieldset>
			<fieldset>
				<legend>Login Information</legend>
				<div><label for="username">Username:</label> <span class="space"><?php echo $name; ?></span></div>
				<div><label for="password">Password:</label> <input type="password" name="password" id="password" maxlength="20" size="15" /></div>
				<div><label for="passwordcheck">Password:</label> <input type="password" name="passwordcheck" id="passwordcheck" maxlength="20" size="15" /> <span class="subtle">(enter again)</span></div>
				<div><label for="guestpassword">Guest Password:</label> <input type="password" name="guestpassword" id="guestpassword" maxlength="20" size="15" /> <span class="subtle">(optional)</span></div>
				<div><label for="guestpasswordcheck">Guest Password:</label> <input type="password" name="guestpasswordcheck" id="guestpasswordcheck" maxlength="20" size="15" /> <span class="subtle">(enter again)</span></div>
			</fieldset>
			<div><input type="submit" name="submit" id="submit" class="submit" value="Set up Account" /></div>
		</form>
<?php } else { ?>
		<p class="alert">Your have already initialized your account.</p>
		<p>Please <a href="index.php">log in</a>, or <a href="mailto:<?php echo ADMIN_EMAIL; ?>">contact <?php echo ADMIN_NICKNAME; ?></a> for assistance.</p>
<?php }; }; } else { ?>
		<p>Your username is invalid.<br />
		Please <a href="mailto:<?php echo ADMIN_EMAIL; ?>">contact <?php echo ADMIN_NICKNAME; ?></a> for assistance.</p>
<?php
};
// free result set memory
mysql_free_result($result);
};
?>

<?php mysql_close($connection); ?>
	</div>
	</div>
<?php require("../includes/footer.php"); ?>
</div>
</body>
</html>
