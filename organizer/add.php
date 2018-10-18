<?php $page = "Add";
require("../includes/init.php");

/* Start Page-Specific Code */
if($_POST['submit'] == "Edit") {
	$queryedit = "SELECT * FROM tunes WHERE id = ".$_POST['id']." LIMIT 1";
	$resultedit = mysql_query($queryedit) or die("<p>Error in query: $queryedit. ".mysql_error()."</p>");
	if (mysql_num_rows($resultedit) > 0) {
		while($rowedit = mysql_fetch_object($resultedit)) {
			$id = $rowedit->id;
			$tunename = stripslashes($rowedit->name);
			$nameslug = $rowedit->nameslug;
			$year = $rowedit->year;
			$additionalyear = $rowedit->additionalyear;
			$textsource = stripslashes($rowedit->textsource);
			$textyear = $rowedit->textyear;
			$meter = $rowedit->meter;
			$modeoftime = $rowedit->modeoftime;
			$tunekey = $rowedit->tunekey;
			$threeliner = $rowedit->threeliner;
			$type = stripslashes($rowedit->type);
			$text = stripslashes($rowedit->text);
			$description = stripslashes($rowedit->description);
			$publicdescription = stripslashes($rowedit->publicdescription);
			echo "<h2>Edit ".readable_tunename($rowedit->name,"",$rowedit->meter)."</h2>\n";
		};
	} else {
		$tunename = "";
		$year = "";
		$additionalyear = "";
		$textsource = "";
		$textyear = "";
		$meter = "";
		$modeoftime = "";
		$tunekey = "";
		$threeliner = "";
		$type = "";
		$text = "";
		$description = "";
		echo "<h2>Edit Tune</h2>\n";
	};
} else {
?>
	<h2>Add Tune</h2>
		<p class="subtle">All fields except for the text, description, public description, and file upload fields are required.</p>
<?php
};
?>
		<form enctype="multipart/form-data" action="add_process.php" method="post">
		<?php
if($_POST['tunename'] && $_POST['tunename'] != "Tune Name" && $clear != 1) {
?>
			<div><label class="fixedsize" for="tunename">Tune Name:</label> <input type="text" size="30" name="tunename" id="tunename" maxlength="45" value="<?php echo stripslashes($_POST['tunename']); ?>" />
<?php
} else {
?>
			<div><label class="fixedsize" for="tunename">Tune Name:</label> <input type="text" size="30" name="tunename" id="tunename" maxlength="45" <?php if($tunename != "") { echo "value=\"".$tunename."\" "; }; ?>/>
<?php
}
		?>
			<label class="sameline" for="year">Year:</label> <input type="text" size="4" name="year" id="year" maxlength="4" <?php if($year != "") { echo "value=\"".$year."\" "; }; ?>/>
			<label for="additionalyear">to</label> <input type="text" size="4" name="additionalyear" id="additionalyear" maxlength="4" <?php if($additionalyear != "" && $additionalyear != "0") { echo "value=\"".$additionalyear."\" "; }; ?>/></div>
			<div><label class="fixedsize" for="textsource">Text Source:</label> <input type="text" size="30" name="textsource" id="textsource" maxlength="45" <?php if($textsource != "") { echo "value=\"".$textsource."\" "; }; ?>/>
			<label class="sameline" for="textyear">Text Year:</label> <input type="text" size="4" name="textyear" id="textyear" maxlength="12" <?php if($textyear != "") { echo "value=\"".$textyear."\" "; }; ?>/></div>
			<div><label class="fixedsize" for="type">Type of Tune:</label> <input type="type" size="30" name="type" id="type" maxlength="20" <?php if($type != "") { echo "value=\"".$type."\" "; }; ?>/></div>
			<div><label for="meter">Meter:</label> <input type="text" size="5" name="meter" id="meter" maxlength="10" <?php if($meter != "") { echo "value=\"".$meter."\" "; }; ?>/>
			<label class="sameline" for="meter">Mode of Time:</label>
			<select name="modeoftime" id="modeoftime">
				<option<?php if($modeoftime == "4/4") { echo " selected=\"selected\""; }; ?>>4/4</option>
				<option<?php if($modeoftime == "2/4") { echo " selected=\"selected\""; }; ?>>2/4</option>
				<option<?php if($modeoftime == "2/2") { echo " selected=\"selected\""; }; ?>>2/2</option>
				<option<?php if($modeoftime == "3/4") { echo " selected=\"selected\""; }; ?>>3/4</option>
				<option<?php if($modeoftime == "3/2") { echo " selected=\"selected\""; }; ?>>3/2</option>
				<option<?php if($modeoftime == "6/4") { echo " selected=\"selected\""; }; ?>>6/4</option>
				<option<?php if($modeoftime == "6/8") { echo " selected=\"selected\""; }; ?>>6/8</option>
				<option value="mul"<?php if($modeoftime == "mul") { echo " selected=\"selected\""; }; ?>>Mult.</option>
			</select>
			<label class="sameline" for="key">Key:</label>
			<select name="key" id="key">
				<option value="A"<?php if(substr($tunekey, 0, 1) == "A") { echo " selected=\"selected\""; }; ?>>A</option>
				<option value="B"<?php if(substr($tunekey, 0, 1) == "B" && substr($tunekey, 1, 1) != "b") { echo " selected=\"selected\""; }; ?>>B</option>
				<option value="Bb"<?php if(substr($tunekey, 0, 2) == "Bb") { echo " selected=\"selected\""; }; ?>>B&#9837;</option>
				<option value="C"<?php if(substr($tunekey, 0, 1) == "C" && substr($tunekey, 1, 1) != "#") { echo " selected=\"selected\""; }; ?>>C</option>
				<option value="C#"<?php if(substr($tunekey, 0, 2) == "C#") { echo " selected=\"selected\""; }; ?>>C&#9839;</option>
				<option value="D"<?php if(substr($tunekey, 0, 1) == "D") { echo " selected=\"selected\""; }; ?>>D</option>
				<option value="E"<?php if(substr($tunekey, 0, 1) == "E" && substr($tunekey, 1, 1) != "b") { echo " selected=\"selected\""; }; ?>>E</option>
				<option value="Eb"<?php if(substr($tunekey, 0, 2) == "Eb") { echo " selected=\"selected\""; }; ?>>E&#9837;</option>
				<option value="F"<?php if(substr($tunekey, 0, 1) == "F" && substr($tunekey, 1, 1) != "#") { echo " selected=\"selected\""; }; ?>>F</option>
				<option value="F#"<?php if(substr($tunekey, 0, 2) == "F#") { echo " selected=\"selected\""; }; ?>>F&#9839;</option>
				<option value="G"<?php if(substr($tunekey, 0, 1) == "G") { echo " selected=\"selected\""; }; ?>>G</option>
			</select>
			<select name="majorminor" id="majorminor">
				<option value="M"<?php if(substr($tunekey, -1) == "M") { echo " selected=\"selected\""; }; ?>>Major</option>
				<option value="m"<?php if(substr($tunekey, -1) == "m") { echo " selected=\"selected\""; }; ?>>Minor</option>
			</select>
			<span class="sameline"><input type="checkbox" name="threeliner" <?php if($threeliner == 1) { echo " checked=\"checked\""; }; ?>/></span> <label for="threeliner">3-Liner?</label></div>
			<div><label for="text">Text:</label><br /><textarea name="text" id="text" cols="65" rows="4"><?php if($text != "") { echo $text; }; ?></textarea></div>
			<div><label for="publicdescription">Public Description <span class="subtle">(displayed for public tunes only)</span>:</label><br /><textarea name="publicdescription" id="publicdescription" cols="65" rows="2"><?php if($publicdescription != "") { echo $publicdescription; }; ?></textarea></div>
<?php if($_POST['submit'] == "Edit") {
	$query_notes = "SELECT * FROM notes WHERE tuneid = ".$_POST['id']." ORDER BY createdate, modifydate";
	$result_notes = mysql_query($query_notes) or die("<p>Error in query: $query_notes. ".mysql_error()."</p>");
	if(mysql_num_rows($result_notes) > 0) {
		echo "<div>Notes:\n\n";
		while($row_n = mysql_fetch_object($result_notes)) {
			echo "<br />Note written <span class=\"success\">".date("M j, Y", strtotime($row_n->createdate))."</span>: \"".neat_text($row_n->note, "first line", 70)."\" [Delete: <input name=\"delete_".$row_n->id."\" type=\"checkbox\" />] <span class=\"linklike\" onclick=\"javascript:toggle_visibility(".$row_n->id.")\">Edit &raquo;</span>";
			echo "\n\n<span class=\"notetextareas\" id=\"".$row_n->id."\"><textarea name=\"note_".$row_n->id."\" id=\"editnotes[]\" cols=\"65\" rows=\"4\">".stripslashes($row_n->note)."</textarea></span>\n\n";
			echo "<input type=\"hidden\" name=\"editnotescounter[]\" value=\"1\" />\n";
			echo "<input type=\"hidden\" name=\"editnotesid[]\" value=\"".$row_n->id."\" />\n";
		}
		echo "</div>\n\n";
	}
};

?>
			<div><label for="description">Add Note:</label><br /><textarea name="description" id="description" cols="65" rows="4"></textarea></div>
			<h3 class="clear">Add <?php if($_POST['submit'] == "Edit") { echo "Edit or Delete"; }; ?> Tune Files</h3>
<?php
if($_POST['submit'] == "Edit") {
	$queryfilesedit = "SELECT * FROM uploadedfiles WHERE tuneid = ".$_POST['id']." AND (fileext != 'mp3' AND fileext != 'm4a') ORDER BY fileext";
	$resultfilesedit = mysql_query($queryfilesedit) or die("<p>Error in query: $queryedit. ".mysql_error()."</p>");
	if (mysql_num_rows($resultfilesedit) > 0) {
		echo "<p class=\"endofsection\">Edit or delete uploaded MIDI, MUS, MYR, or PDF files:";
		while($rowfilesedit = mysql_fetch_object($resultfilesedit)) {
			echo " [<span class=\"success\">".strtoupper($rowfilesedit->fileext)."</span> <label for=\"deletefile\">Delete:</label><input type=\"checkbox\" name=\"deletefile[]\" value=\"".$rowfilesedit->id."\" />]";
		};
		echo "</p>\n";
	};
};
if($_POST['submit'] == "Edit") {
	$queryfilesedit = "SELECT * FROM uploadedfiles WHERE tuneid = ".$_POST['id']." AND (fileext = 'mp3' OR fileext = 'm4a')";
	$resultfilesedit = mysql_query($queryfilesedit) or die("<p>Error in query: $queryedit. ".mysql_error()."</p>");
	if (mysql_num_rows($resultfilesedit) > 0) {
		echo "<p>Edit or delete uploaded MP3/M4A files:</p>";
		while($rowfilesedit = mysql_fetch_object($resultfilesedit)) {
			echo "<div><label for=\"editmp3title\">MP3/M4A File: <span class=\"success\">".$rowfilesedit->filename."</span><br />\n";
			echo "<input type=\"hidden\" name=\"editmp3counter[]\" value=\"1\" />\n";
			echo "<input type=\"hidden\" name=\"editmp3id[]\" value=\"".$rowfilesedit->id."\" />\n";
			echo "<label for=\"deletefile\">Delete:</label> <input type=\"checkbox\" name=\"deletefile[]\" value=\"".$rowfilesedit->id."\" />\n";
			echo "<label class=\"sameline\" for=\"editmp3title\">Edit MP3/M4A title:</label> <input type=\"text\" size=\"30\" name=\"editmp3title[]\" id=\"editmp3title[]\" maxlength=\"60\" value=\"".stripslashes($rowfilesedit->title)."\" /></div>\n";
			echo "<div><label for=\"editmp3description\">Edit MP3/M4A description:</label><br />\n<textarea name=\"editmp3description[]\" id=\"editmp3description[]\" cols=\"65\" rows=\"2\">".stripslashes($rowfilesedit->description)."</textarea></div>";
		};
	};
};
?>
			<p class="subtle">You may add a PDF, an INDD, a MIDI, and a MUS/MYR file, as well as multiple MP3/M4A files.<br />
			If you have already uploaded a PDF, INDD, MIDI, or MUS/MYR file, any newly uploaded file of the same format will overwrite the previously uploaded file.</p>
			<div><label for="uploadedfile">Upload a PDF, INDD, MIDI, MUS, or MYR file:</label> <input type="hidden" name="uploadedfilecounter[]" value="1" /><input type="file" name="uploadedfile[]" id="uploadedfile[]" /></div>
			<div id="extrafiles"></div>
			<p class="linklike" id="addafile">Add another PDF, INDD, MIDI, MUS, or MYR file &raquo;</p>

			<p><label for="uploadedmp3">Upload a MP3/M4A file:</label></p>
			<div><label for="uploadedmp3">MP3/M4A File:</label> <input type="hidden" name="uploadedmp3counter[]" value="1" /><input size="5" type="file" name="uploadedmp3[]" id="uploadedmp3[]" />
				<label class="sameline" for="uploadedmp3title">MP3/M4A title:</label> <input type="text" size="30" name="uploadedmp3title[]" id="uploadedmp3title[]" maxlength="60" /></div>
			<div><label for="uploadedmp3description">MP3/M4A description:</label><br /><textarea name="uploadedmp3description[]" id="uploadedmp3description[]" cols="65" rows="2"></textarea></div>
			<div id="extramp3files"></div>
			<p class="linklike" id="addamp3file">Add another MP3/M4A file &raquo;</p>
			<input type="hidden" name="process" id="process" value="1" />
			<input type="hidden" name="tunesmithid" id="tunesmithid" value="<?php echo $usernameid; ?>" />
			<input type="hidden" name="username" id="username" value="<?php echo $username; ?>" />
			<?php if($id != "") { ?> <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" /><?php }; ?>
			<?php if($tunename != "") { ?> <input type="hidden" name="oldname" id="oldname" value="<?php echo $tunename; ?>" /><?php }; ?>
			<?php if($nameslug != "") { ?><input type="hidden" name="nameslug" id="nameslug" value="<?php echo $nameslug; ?>" /><?php }; ?>
			<div><input type="submit" name="submit" id="submit" <?php if($_POST['submit'] == "Edit") { echo "value=\"Edit Tune\""; } else { echo "value=\"Add Tune\""; }; ?> /> <?php
			if($_POST['submit'] == "Edit") { ?><input type="submit" name="submit" id="submit" value="Delete" /><?php }; ?></div>
		</form>

<?php require("../includes/shutdown.php"); ?>