<?php $page = "Delete";
require("../includes/init.php");

/* Start Page-Specific Code */
if($_POST['submit'] == "Delete") {
	$idid = $_POST['id'];
	$userid = $_POST['userid'];
} else {
	$idid = $id;
}
if($idid) {
	$queryedit = "SELECT * FROM tunes WHERE id = ".$idid." LIMIT 1";
	$resultedit = mysql_query($queryedit) or die("<p>Error in query: $queryedit. ".mysql_error()."</p>");
	if (mysql_num_rows($resultedit) > 0) {
		$rowedit = mysql_fetch_object($resultedit);
		$id = $rowedit->id;
		$tunename = stripslashes($rowedit->name);
	};
} elseif($userid) {
	$queryedit = "SELECT * FROM users WHERE id = ".$userid." LIMIT 1";
	$resultedit = mysql_query($queryedit) or die("<p>Error in query: $queryedit. ".mysql_error()."</p>");
	if (mysql_num_rows($resultedit) > 0) {
		$rowedit = mysql_fetch_object($resultedit);
		$id = $rowedit->id;
		if($rowedit->firstname || $rowedit->lastname) { 
			$username = stripslashes($rowedit->firstname)." ".stripslashes($rowedit->lastname);
		} else {
			$username = $rowedit->username;
		};
	};
}
?>
	<h2>Delete <?php if($idid) { ?>Tune<?php } else { ?>User<?php }; ?></h2>
		<p class="alert">Are you sure you want to delete <?php if($idid) { echo $tunename; } else { echo $username; }; ?>?</p>
		<h2 class="forms"><form action="delete_process.php" method="post">
			<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="deletetype" id="deletetype" value="<?php if($idid) { ?>tune<?php } else { ?>user<?php }; ?>" />
			<input type="submit" name="submit" id="submit" value="Yes, Delete" />
		</form><form action="<?php if($idid) { ?>index.php<?php } else { ?>admin_process.php<?php }; ?>" method="post">
			<input type="submit" name="submit" id="submit" value="No, Cancel" /><?php if($username) { ?>
			<input type="hidden" name="username" id="username" value="<?php echo $username; ?>" />
		<?php }; ?></form></h2>

<?php require("../includes/shutdown.php"); ?>