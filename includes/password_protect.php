<?php

###############################################################
# Page Password Protect 2.1
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
############################################################### 
#
# Usage:
# Set usernames / passwords below between SETTINGS START and SETTINGS END.
# Open it in browser with "help" parameter to get the code
# to add to all files being protected. 
#    Example: password_protect.php?help
# Include protection string which it gave you into every file that needs to be protected
#
###############################################################

/*
-------------------------------------------------------------------
SAMPLE if you only want to request login and password on login form.
Each row represents different user.

$LOGIN_INFORMATION = array(
  'zubrag' => 'root',
  'test' => 'testpass',
  'admin' => 'passwd'
);

--------------------------------------------------------------------
SAMPLE if you only want to request only password on login form.
Note: only passwords are listed

$LOGIN_INFORMATION = array(
  'root',
  'testpass',
  'passwd'
);

--------------------------------------------------------------------
*/

##################################################################
#  SETTINGS START
##################################################################

// Add login/password pairs below, like described above
// NOTE: all rows except last must have comma "," at the end of line
$LOGIN_INFORMATION = array();

$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
$query = "SELECT username, password, guestpassword FROM users WHERE password IS NOT NULL";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if(mysql_num_rows($result) > 0) {
	while($row = mysql_fetch_object($result)) {
		$LOGIN_INFORMATION[$row->username] = $row->password;
		if($row->guestpassword) {
			$LOGIN_INFORMATION[$row->username."guest"] = $row->guestpassword;
		};
	};
};
mysql_free_result($result);
mysql_close($connection);

// request login? true - show login and password boxes, false - password box only
define('USE_USERNAME', true);

##################################################################
#  SETTINGS END
##################################################################


///////////////////////////////////////////////////////
// do not change code below
///////////////////////////////////////////////////////

// show usage example
if(isset($_GET['help'])) {
  die('Include following code into every page you would like to protect, at the very beginning (first line):<br>&lt;?php include("' . __FILE__ . '"); ?&gt;');
}

// logout?
if(isset($_GET['logout'])) {
  setcookie("verify", ''); // clear password;
  setcookie("username", '');
  header( 'Location: index.php' ) ;
}

if(!function_exists('showLoginPasswordProtect')) {

// show login form
function showLoginPasswordProtect($error_msg) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php $page = "init"; $test = 1; require("head.php"); ?>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</head>

<body>
<div id="pagelogin">
	<div id="header"><h1><?php echo SITE_NAME_PRIVATE; ?></h1></div>
		<div id="main">
			<div id="content">
				<p>Public site: <a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME_PUBLIC; ?></a>.</p>
				<form method="post">
					<fieldset>
						<legend>Please Log in to Access this Page:</legend>
						<p class="alert"><?php echo $error_msg; ?></p>
						<?php if (USE_USERNAME) { echo '<div><label class="fixedsize" for="access_login">Login:</label> <input type="input" name="access_login" /></div><div><label class="fixedsize" for="access_password">Password:</label>'; } else { echo "<div>"; } ?>
						<input type="password" name="access_password" /></div>	
					</fieldset>
					<div><input type="submit" name="Submit" id="submit" value="Submit" /></div>
				</form>
			</div>
		</div>
		<?php require("footer.php"); ?>
	</div>
</div>
</body>
</html>

<?php
  // stop at this point
  die();
}
}

// user provided password
if (isset($_POST['access_password'])) {

  $login = isset($_POST['access_login']) ? $_POST['access_login'] : '';
  $pass = $_POST['access_password'];
  if (!USE_USERNAME && !in_array($pass, $LOGIN_INFORMATION)
  || (USE_USERNAME && ( !array_key_exists($login, $LOGIN_INFORMATION) || $LOGIN_INFORMATION[$login] != $pass ) ) 
  ) {
    showLoginPasswordProtect("Incorrect password.");
  }
  else {
    // set cookie if password was validated
    setcookie("verify", md5($pass));
	setcookie("username", $_POST['access_login']);
	$username = $login;
	$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
	mysql_select_db(DB_NAME) or die( "Unable to select database!");
	$query = "UPDATE users SET datelogin = '".date("Y-m-d H:i:s")."' WHERE username = '".$login."'";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
  }

}

else {

  // check if password cookie is set
  if (!isset($_COOKIE['verify'])) {
    showLoginPasswordProtect("");
  }

  // check if cookie is good
  $found = false;
  foreach($LOGIN_INFORMATION as $kay=>$val) {
    if ($_COOKIE['verify'] == md5($val)) {
      $found = true;
      break;
    }
  }
  if (!$found) {
    showLoginPasswordProtect("");
  }

}

?>
