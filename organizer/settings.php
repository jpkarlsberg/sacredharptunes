<?php $page = "Settings";
require("../includes/init.php");

/* Start Page-Specific Code */
	$querysettings = "SELECT * FROM users WHERE id = ".$usernameid." LIMIT 1";
	$resultsettings = mysql_query($querysettings) or die("<p>Error in query: $querysettings. ".mysql_error()."</p>");
	if (mysql_num_rows($resultsettings) > 0) {
		$rowsettings = mysql_fetch_object($resultsettings);
		$description = $rowsettings->description;
	} else {

	};
?>
	<h2>Personal Settings</h2>
	<?php if($action == "personalsettings") { echo "<p class=\"success\">You have updated your personal settings.</p>\n\n"; }; ?>
	<form action="settings_process.php" method="post">
		<input type="hidden" name="usernameid" value="<?php echo $usernameid; ?>" />
		<input type="hidden" name="username" value="<?php echo $username; ?>" />
		<div><label class="fixedsize" for="tunesmithlogin">Your Login:</label> <?php echo $username; ?><input type="hidden" name="tunesmithlogin" value="<?php echo $username; ?>" />
		<div><label class="fixedsize" for="tunesmithname">Your Name:</label> <input type="text" size="15" name="tunesmithfirst" id="tunesmithfirst" maxlength="40" value="<?php echo $firstname; ?>" /> <input type="text" size="25" name="tunesmithlast" id="tunesmithlast" maxlength="40" value="<?php echo $lastname; ?>" /></div>
		<div><label class="fixedsize" for="city">Your City:</label> <input type="text" size="20" name="city" id="city" maxlength="40" value="<?php echo $city; ?>" /></div>
		<div><label class="fixedsize" for="state">Your State:</label> <select name="state">
<option value="">Select State</option>
<option <?php if ($state == "AL") { echo "selected=\"selected\""; }; ?>value ="AL">Alabama</option>
<option <?php if ($state == "AK") { echo "selected=\"selected\""; }; ?>value ="AK">Alaska</option>
<option <?php if ($state == "AZ") { echo "selected=\"selected\""; }; ?>value ="AZ">Arizona</option>
<option <?php if ($state == "AR") { echo "selected=\"selected\""; }; ?>value ="AR">Arkansas</option>
<option <?php if ($state == "CA") { echo "selected=\"selected\""; }; ?>value ="CA">California</option>
<option <?php if ($state == "CO") { echo "selected=\"selected\""; }; ?>value ="CO">Colorado</option>
<option <?php if ($state == "CT") { echo "selected=\"selected\""; }; ?>value ="CT">Connecticut</option>
<option <?php if ($state == "DE") { echo "selected=\"selected\""; }; ?>value ="DE">Delaware</option>
<option <?php if ($state == "FL") { echo "selected=\"selected\""; }; ?>value ="FL">Florida</option>
<option <?php if ($state == "GA") { echo "selected=\"selected\""; }; ?>value ="GA">Georgia</option>
<option <?php if ($state == "HI") { echo "selected=\"selected\""; }; ?>value ="HI">Hawaii</option>
<option <?php if ($state == "ID") { echo "selected=\"selected\""; }; ?>value ="ID">Idaho</option>
<option <?php if ($state == "IL") { echo "selected=\"selected\""; }; ?>value ="IL">Illinois</option>
<option <?php if ($state == "IN") { echo "selected=\"selected\""; }; ?>value ="IN">Indiana</option>
<option <?php if ($state == "IA") { echo "selected=\"selected\""; }; ?>value ="IA">Iowa</option>
<option <?php if ($state == "KS") { echo "selected=\"selected\""; }; ?>value ="KS">Kansas</option>
<option <?php if ($state == "KY") { echo "selected=\"selected\""; }; ?>value ="KY">Kentucky</option>
<option <?php if ($state == "LA") { echo "selected=\"selected\""; }; ?>value ="LA">Louisiana</option>
<option <?php if ($state == "ME") { echo "selected=\"selected\""; }; ?>value ="ME">Maine</option>
<option <?php if ($state == "MD") { echo "selected=\"selected\""; }; ?>value ="MD">Maryland</option>
<option <?php if ($state == "MA") { echo "selected=\"selected\""; }; ?>value ="MA">Massachusetts</option>
<option <?php if ($state == "MI") { echo "selected=\"selected\""; }; ?>value ="MI">Michigan</option>
<option <?php if ($state == "MN") { echo "selected=\"selected\""; }; ?>value ="MN">Minnesota</option>
<option <?php if ($state == "MS") { echo "selected=\"selected\""; }; ?>value ="MS">Mississippi</option>
<option <?php if ($state == "MO") { echo "selected=\"selected\""; }; ?>value ="MO">Missouri</option>
<option <?php if ($state == "MT") { echo "selected=\"selected\""; }; ?>value ="MT">Montana</option>
<option <?php if ($state == "NE") { echo "selected=\"selected\""; }; ?>value ="NE">Nebraska</option>
<option <?php if ($state == "NV") { echo "selected=\"selected\""; }; ?>value ="NV">Nevada</option>
<option <?php if ($state == "NH") { echo "selected=\"selected\""; }; ?>value ="NH">New Hampshire</option>
<option <?php if ($state == "NJ") { echo "selected=\"selected\""; }; ?>value ="NJ">New Jersey</option>
<option <?php if ($state == "NM") { echo "selected=\"selected\""; }; ?>value ="NM">New Mexico</option>
<option <?php if ($state == "NY") { echo "selected=\"selected\""; }; ?>value ="NY">New York</option>
<option <?php if ($state == "NC") { echo "selected=\"selected\""; }; ?>value ="NC">North Carolina</option>
<option <?php if ($state == "ND") { echo "selected=\"selected\""; }; ?>value ="ND">North Dakota</option>
<option <?php if ($state == "OH") { echo "selected=\"selected\""; }; ?>value ="OH">Ohio</option>
<option <?php if ($state == "OK") { echo "selected=\"selected\""; }; ?>value ="OK">Oklahoma</option>
<option <?php if ($state == "OR") { echo "selected=\"selected\""; }; ?>value ="OR">Oregon</option>
<option <?php if ($state == "PA") { echo "selected=\"selected\""; }; ?>value ="PA">Pennsylvania</option>
<option <?php if ($state == "RI") { echo "selected=\"selected\""; }; ?>value ="RI">Rhode Island</option>
<option <?php if ($state == "SC") { echo "selected=\"selected\""; }; ?>value ="SC">South Carolina</option>
<option <?php if ($state == "SD") { echo "selected=\"selected\""; }; ?>value ="SD">South Dakota</option>
<option <?php if ($state == "TN") { echo "selected=\"selected\""; }; ?>value ="TN">Tennessee</option>
<option <?php if ($state == "TX") { echo "selected=\"selected\""; }; ?>value ="TX">Texas</option>
<option <?php if ($state == "UT") { echo "selected=\"selected\""; }; ?>value ="UT">Utah</option>
<option <?php if ($state == "VT") { echo "selected=\"selected\""; }; ?>value ="VT">Vermont</option>
<option <?php if ($state == "VA") { echo "selected=\"selected\""; }; ?>value ="VA">Virginia</option>
<option <?php if ($state == "WA") { echo "selected=\"selected\""; }; ?>value ="WA">Washington</option>
<option <?php if ($state == "WV") { echo "selected=\"selected\""; }; ?>value ="WV">West Virginia</option>
<option <?php if ($state == "WI") { echo "selected=\"selected\""; }; ?>value ="WI">Wisconsin</option>
<option <?php if ($state == "WY") { echo "selected=\"selected\""; }; ?>value ="WY">Wyoming</option>
</select></div>
		<?php if($ue == 1) { ?><label class="space alert">sorry, the username you selected is already in use.</label><?php }; ?></div>
		<div><label for="description">Biography/Description:</label>
		<textarea name="description" id="description" rows="4" cols="65"><?php echo stripslashes($description); ?></textarea> </div>
		<div><input type="submit" name="submit" id="submit" value="Update Personal Settings" /></div>
	</form>
	<h2 class="section">Public Tunes</h2>
	<?php if($action == "publictunes") { echo "<p class=\"success\">You have updated your public tunes.</p>\n\n"; }; ?>
	<?php if($errpfn && is_numeric($errpfn)) {
		$query = "SELECT error FROM errors WHERE id = $errpfn LIMIT 1";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			$row = mysql_fetch_object($result);
			echo "<p class=\"alert\">".$row->error."</p>\n\n";
		};
	}; ?>
	<?php /* make sure there are tunes with files to make public... */
$query = "SELECT id FROM uploadedfiles WHERE tunesmithid = '".$usernameid."' AND (fileext = 'pdf' OR fileext = 'mp3')";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if (mysql_num_rows($result) > 0) {
	?>
	<form action="settings_process.php" method="post">
		<input type="hidden" name="usernameid" id="usernameid" value="<?php echo $usernameid; ?>" />
		<div><input type="checkbox" name="public" id="public" onclick="toggle('publictunes');" <?php if($userpublic == 1) { ?>checked="checked" <?php }; ?>/><label class="space" for="public">Would you like to share some of your tunes publicly?</label></div>
		<div><label for="publicfoldername">Public tunes address: http://sacredharptunes.com/</label>
			<input size="15" maxlength="120" type="text" name="publicfoldername" value="<?php echo $publicfoldername; ?>" /> /<br />
			<span class="subtle">(May contain only lower-case letters, numbers, and hyphens)</span></div>
		<div id="publictunes">
<?php
$query = "SELECT * FROM tunes WHERE tunesmithid = $usernameid AND public = 1 ORDER BY rank, name";
$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
if(mysql_num_rows($result) > 0) {
	echo "<table>\n";
	echo "<thead><tr><td>Added Public Tunes</td><td class=\"nudge\">MP3s</td><td class=\"nudge\">MIDIs</td><td class=\"nudge\">PDFs</td><td class=\"nudge\">Remove</td></tr></thead>\n";
	while($row = mysql_fetch_object($result)) {
		echo "<input type=\"hidden\" name=\"publictune[]\" value=\"".$row->id."\" />\n";
		echo "<tr><td valign=\"center\">".readable_tunename($row->name,"",$row->meter)."</td>\n";
		$querymp3 = "SELECT id, public, title, filenumber, rank FROM uploadedfiles WHERE fileext = 'mp3' AND tuneid = '".$row->id."' ORDER BY public DESC, rank, filenumber";
		$resultmp3 = mysql_query($querymp3) or die("<p>Error in query: $querymp3. ".mysql_error()."</p>");
		if(mysql_num_rows($resultmp3) > 0) {
			echo "<td valign=\"center\">\n";
			while($mp3 = mysql_fetch_object($resultmp3)) {
				if($mp3->public == 1) {
					echo "<input type=\"text\" class=\"tiny\" name=\"mp3rank[]\" value=\"".$mp3->rank."\" />";
				} else {
					echo "<input type=\"text\" class=\"tiny\" disabled=\"disabled\" />"
						."<input type=\"hidden\" name=\"mp3rank[]\" value=\"0\" />";
				};
				echo "<input type=\"checkbox\" name=\"mp3[]\" value=\"".$row->id.",".$mp3->id."\" ";
				if($mp3->public == 1) { echo " checked=\"checked\""; };
				echo "/><label for=\"mp3[]\">".mp3_filetitle($mp3->id)."</label><br />";
			};
			echo "</td>\n";
		} else {
			echo "<td><input type=\"hidden\" name=\"mp3[]\" value=\"nofile\" /></td>\n";
		};
		$querymid = "SELECT id, public FROM uploadedfiles WHERE fileext = 'mid' AND tuneid = ".$row->id." LIMIT 1";
		$resultmid = mysql_query($querymid) or die("<p>Error in query: $querymid. ".mysql_error()."</p>");
		if(mysql_num_rows($resultmid) > 0) {
			while($rowmid = mysql_fetch_object($resultmid)) {
				echo "<td valign=\"center\" class=\"center\" style=\"white-space: nowrap;\"><input type=\"checkbox\" name=\"mid[]\" value=\"".$row->id."\" ";
				if($rowmid->public == 1) { echo " checked=\"checked\""; };
				echo "/><label for=\"mid[]\">mid</label></td>\n";
			};
		} else {
			echo "<td><input type=\"hidden\" name=\"mid[]\" value=\"nofile\" /></td>\n";
		};
		$querypdf = "SELECT id, public FROM uploadedfiles WHERE fileext = 'pdf' AND tuneid = ".$row->id." LIMIT 1";
		$resultpdf = mysql_query($querypdf) or die("<p>Error in query: $querypdf. ".mysql_error()."</p>");
		if(mysql_num_rows($resultpdf) > 0) {
			while($rowpdf = mysql_fetch_object($resultpdf)) {
				echo "<td valign=\"center\" class=\"center\" style=\"white-space: nowrap;\"><input type=\"checkbox\" name=\"pdf[]\" value=\"".$row->id."\" ";
				if($rowpdf->public == 1) { echo " checked=\"checked\""; };
				echo "/><label for=\"pdf[]\">pdf</label></td>\n";
			};
		} else {
			echo "<td><input type=\"hidden\" name=\"pdf[]\" value=\"nofile\" /></td>\n";
		};
		echo "<td valign=\"center\" class=\"center\"><input type=\"checkbox\" name=\"remove[]\" value=\"".$row->id."\"><label for=\"remove[]\">X</label></td></tr>\n\n";
	};
	echo "</table>\n\n";
};
?>
			<p>Click on the "Add a public tune" link to choose from a list of your tunes with PDF, MP3, or MIDI files.</p>
			<div id="addtunes"></div>
			<p class="linklike" id="addatune">Add a public tune &raquo;</p></div>
			<div><input type="submit" name="submit" id="submit" value="Update Public Tunes Settings" /></div>
		</form>
<?php } else { ?>
	<p class="alert">No tunes with files that can be made public in database.</p>
<?php }; ?>

<?php require("../includes/shutdown.php"); ?>