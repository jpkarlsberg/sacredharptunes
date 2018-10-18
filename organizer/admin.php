<?php $page = "Administration";
require("../includes/init.php");

/* Start Page-Specific Code */
$admintools = $_GET["admintools"];
?>
		<ul class="inlinelist">
			<li class="first"><a href="?admintools=user">User Administration</a></li>
			<li><a href="?admintools=pubpage">Public Page Administration</a></li>
		</ul>
		<h2>Administration</h2>
<?php
if ($admintools == "pubpage") {
	echo "<h3>Edit Page Content</h3>\n\n";
} else {
	echo "<h3>User Tools</h3>\n\n";
};
if($action) {
	if($action == "initialize") {
		echo "<p class=\"success\">You have sucessfully created a new user account for ".$modname.".</p>\n\n";
	} elseif($action == "sent email") {
		echo "<p class=\"success\">You have sucessfully sent a new user message to ".$modname.".</p>\n\n";
	} elseif($action == "reset") {
		echo "<p class=\"success\">You have successfully reset the password for ".$modname.".</p>\n\n";
	} elseif($action == "addadmin") {
		echo "<p class=\"success\">You have successfully given ".$modname." administrative privileges.</p>\n\n";
	} elseif($action == "subtractadmin") {
		echo "<p class=\"success\">You have successfully removed administrative privileges for ".$modname.".</p>\n\n";
	} elseif($action == "email") {
		echo "<p class=\"success\">You have successfully sent an e-mail to ".$modname.".</p>\n\n";
	} elseif($action == "delete") {
		echo "<p class=\"success\">You have successfully deleted the user account for ".$modname.".</p>\n\n";
	} elseif($action == "trashdeletedusers") {
		echo "<p class=\"success\">You have successfully trashed the user accounts, tunes, notes, and uploaded files for ".$modname." deleted users.</p>\n\n";
	} elseif($action == "canceldelete") {
		echo "<p class=\"alert\">You cancelled deletion of the user account for ".$modname.".</p>\n\n";
	} elseif($action == "editpagecontent") {
		echo "<p class=\"success\">You have edited page content blocks.</p>\n\n";
	} elseif($action == "noaction") {
		echo "<p class=\"alert\">No action was taken.</p>\n\n";
	};
} elseif($error && is_numeric($error)) {
	$query = "SELECT * FROM errors WHERE id = $error LIMIT 1";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if(mysql_num_rows($result) > 0) {
		$row = mysql_fetch_object($result);
		echo "<p class=\"alert\">".$row->error."</p>\n\n";
	}	
};

if ($admintools == "pubpage") {
	$query = "SELECT * FROM pagecontent ORDER BY page, priority";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if(mysql_num_rows($result) > 0) {
		echo "<form action=\"admin_process.php\" method=\"post\">\n";
		while($row = mysql_fetch_object($result)) {
			$query_p = "SELECT priority FROM pagecontent WHERE movable = 1 ORDER BY priority";
			$result_p = mysql_query($query_p) or die("<p>Error in query: $query_p. ".mysql_error()."</p>");
			if(mysql_num_rows($result_p) > 0) {
				$priority_box = "";
				while($row_p = mysql_fetch_object($result_p)) {
					$priority_box .= "<option";
					if($row_p->priority == $row->priority) {
						$priority_box .= " selected=\"selected\"";
					}
					$priority_box .= ">".$row_p->priority."</option>\n";
				}
			}
			if($row->page != $prevpage) {
				echo "<h4>Page: ".$row->page."</h4>\n\n";
				$prevpage = $row->page;
			};
			echo "<p>".$row->priority.". <span class=\"success\">".$row->contenttitle."</span>";
			if($row->movable == 0) {
				echo " <span class=\"alert sameline\">Priority May Not Be Changed</span>\n\n";
			} else {
				echo "<label class=\"sameline\" for=\"priority_".$row->id."\">Edit Priority:</label> <select name=\"priority_".$row->id."\">\n".$priority_box."</select>\n";
			};
			if($row->required == 0) {
				echo "</p>\n<textarea name=\"content_".$row->id."\">".$row->content."</textarea>\n\n";
			} else {
				echo " <span class=\"alert sameline\">Content Not Editable</span></p>\n\n";
			};
		}
		echo "<p><input type=\"submit\" name=\"submit\" value=\"Edit Page Content\" /></p>\n"
			."<input type=\"hidden\" name=\"admintools\" value=\"".$admintools."\" />\n"
			."</form>\n\n";
	} else {
		echo "<p>There are no page content blocks.</p>";
	}

} else { ?>
		<form action="admin_process.php" method="post">
			<input type="text" size="15" name="username" id="username" maxlength="20" value="New User Login" />
			<input type="text" size="25" name="email" id="email" maxlength="50" value="New User Email" />
			<input type="submit" name="submit" id="submit" value="Initialize User" />
		</form>

<?php
if($eo == "du") {
	$eo = "dateupdated DESC, ";
} else { $eo = ""; };

$query = "SELECT * FROM users WHERE active > 0 ORDER BY admin DESC, ".$eo."lastname, firstname";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");

if (mysql_num_rows($result) > 0) {
?>
	<table><thead>
		<tr>
			<td>#</td><td><a href="?eo=n">Name</a></td><td>Login</td><td class="center"><abbr title="Administrator?">A</abbr></td><td class="center"><abbr title="Public Tunes?">P</abbr></td><td class="center"><abbr title="Guest Password?">G</abbr></td><td class="center"><abbr title="Number of Tunes">T</abbr></td><td>Tools</td><td><a href="?eo=du">Logged In</a></td>
		</tr>
	</thead>
<?php
	$i = 1;
	while($row = mysql_fetch_object($result)) {
		if($row->guestpassword) { $gp = "Y"; $gp_class = " class=\"success center\""; } else { $gp = "N"; $gp_class = " class=\"center\""; };
		if($row->admin == 2) {
			$admin = "Y"; $superadmin = "Y"; $admin_class = " class=\"success center\"";
		} elseif($row->admin == 1) {
			$admin = "Y"; $superadmin = "N"; $admin_class = " class=\"success center\"";
		} else {
			$admin = "N"; $superadmin = "N"; $admin_class = " class=\"center\"";
		};
		if($row->public == 1) { $public = "Y"; $public_class = " class=\"success center\""; } else { $public = "N"; $public_class = " class=\"center\""; };
		if($row->lastname && $row->firstname) {
			$name_of_user = $row->firstname." ".$row->lastname;
			$readable_name = $row->firstname." ".$row->lastname;
			$name_class = " class=\"space\"";
			$query_tunes = "SELECT id FROM tunes WHERE tunesmithid = $row->id";
			$result_tunes = mysql_query($query_tunes) or die("<p>Error in query: $query_tunes. ".mysql_error()."</p>");
			$num_tunes = mysql_num_rows($result_tunes);
			mysql_free_result($result_tunes);
		} else {
			$name_of_user = "Not yet selected";
			$readable_name = $row->username;
			$name_class = " class=\"alert space\"";
			$num_tunes = "-";
		}
		echo "<tr>\n";
		echo "<td>".$i.".</td>\n";
		echo "<td".$name_class.">".$name_of_user."</td>\n";
		echo "<td>".$row->username."</td>\n";
		echo "<td".$admin_class.">".$admin."</td>\n";
		echo "<td".$public_class.">".$public."</td>\n";
		echo "<td".$gp_class.">".$gp."</td>\n";
		echo "<td class=\"center\">".$num_tunes."</td>\n";
/* Tools */?>
	<td>
<?php if($row->initialized == 0) { ?>
		<form action="admin_process.php" method="post">
			<input type="hidden" name="name" id="name" value="<?php echo $readable_name; ?>" />
			<input type="hidden" name="useremail" id="useremail" value="<?php echo $row->email; ?>" />
			<input type="submit" name="submit" id="submit" value="Send Initialization E-mail" />
		</form>
<?php } else { ?>
		<form action="admin_process.php" method="post">
			<input type="hidden" name="userid" id="userid" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="useremail" id="useremail" value="<?php echo $row->email; ?>" />
			<input type="hidden" name="name" id="name" value="<?php echo ucwords($readable_name); ?>" />
			<input type="submit" name="submit" id="submit" value="Reset Pass" />
		</form>
		<form action="admin_process.php" method="post">
			<input type="hidden" name="userid" id="userid" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="name" id="name" value="<?php echo ucwords($readable_name); ?>" />
			<?php if($admin == "Y") { ?>
			<input type="submit" name="submit" id="submit" value="&ndash; Admin" <?php if($superadmin == "Y") { echo " disabled=\"disabled\" "; }; ?>/>
			<?php } else { ?>
			<input type="submit" name="submit" id="submit" value="+ Admin" />
			<?php }; ?>
		</form>
		<form action="delete.php" method="post">
			<input type="hidden" name="userid" id="userid" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="name" id="name" value="<?php echo ucwords($readable_name); ?>" />
			<input type="submit" name="submit" id="submit" value="Delete" <?php if($superadmin == "Y") { echo " disabled=\"disabled\" "; }; ?>/>
		</form>
<?php }; ?>
	</td>
<?php
		if($row->datelogin) { $dateactive = $row->datelogin; } else { $dateactive = NULL; };
		echo "<td class=\"center\">".substr($dateactive, 0, 10)."</td>\n";
		echo "</tr>\n";
		$i ++;
	}
	
	$query = "SELECT * FROM users WHERE active = 0";
	$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
	if(mysql_num_rows($result) > 0) {
		echo "<tr class=\"subtle\"><td></td><td colspan=\"6\">There are ".mysql_num_rows($result)." Deleted Users</td><td colspan=\"2\">";
		echo "<form method=\"post\" action=\"admin_process.php\"><input class=\"error\" type=\"submit\" name=\"submit\" value=\"Trash Deleted Users\" /></form> (Cannot undo this action)</td></tr>\n\n";
	};
?>
	</table>
<?php
	$query_email = "SELECT id, username, firstname, lastname, email FROM users ORDER BY username";
	$result_email = mysql_query($query_email) or die("<p>Error in query: $query_email. ".mysql_error()."</p>");
	if(mysql_num_rows($result_email) > 0) {
?>
	<h3 class="verticalspace">Email a User</h3>
	<form action="admin_process.php" method="post">
		<div><label for="users">To:</label> <select name="users">
			<option>Select a User</option>
			<option value="all">All Users</option>
<?php
		while($row_email = mysql_fetch_object($result_email)) {
			if($row_email->firstname && $row_email->lastname) { $name_to_email = $row_email->firstname." ".$row_email->lastname; } else { $name_to_email = "\"".$row_email->username."\""; }
			echo "<option value=\"".$row_email->id."\">".$name_to_email."</option>\n";
		};
?>
		</select></div>
		<div><label for="emailsubject">Subject:</label> <input type="text" name="emailsubject" id="emailsubject" size="40" /></div>
		<div><textarea name="emailbody" id="emailbody" rows="10" cols="60"></textarea></div>
		<div><input type="submit" name="submit" id="submit" value="Send E-mail" /></div>
	</form>
<?php
	};
} else {
	echo "<p>There are no users.</p>";
}
};
// free result set memory
mysql_free_result($result);
?>

<?php require("../includes/shutdown.php"); ?>