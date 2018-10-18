<?php
if($_POST['submit'] == "Delete") {
	header( 'Location: delete.php?id='.$id );
}

/* connect to db */
require("../includes/constants.php");
require("../includes/naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");

/* initialize S3 object for file-related processes */
require("../includes/S3.php");
$s3 = new s3(AWS_ACCESS_KEY, AWS_SECRET_KEY);

/* prepare form data and add to db */
$name = empty($_POST['tunename']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a name for the new tune.</p>") : mysql_escape_string($_POST['tunename']);
$year = empty($_POST['year']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a year for the new tune.</p>") : mysql_escape_string($_POST['year']);
$additionalyear = mysql_escape_string($_POST['additionalyear']);
$textsource = empty($_POST['textsource']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a text source for the new tune.</p>") : mysql_escape_string($_POST['textsource']);
$textyear = empty($_POST['textyear']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a year of publication for the new tune's text.</p>") : mysql_escape_string($_POST['textyear']);
$meter = mysql_escape_string($_POST['meter']);
$modeoftime = empty($_POST['modeoftime']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a mode of time for the new tune.</p>") : mysql_escape_string($_POST['modeoftime']);
$tunekey = $_POST['key'].$_POST['majorminor'];
if($_POST['threeliner'] == "on") { $threeliner = 1; } else { $threeliner = 0; };
$type = empty($_POST['type']) ? die ("<p><strong class=\"alert\">Error:</strong> Please enter a type for the new tune.</p>") : mysql_escape_string($_POST['type']);
$text = mysql_escape_string($_POST['text']);
$description = mysql_escape_string($_POST['description']);
$publicdescription = mysql_escape_string($_POST['publicdescription']);
$tunesmithid = $_POST['tunesmithid'];
$username = $_POST['username'];
$id = $_POST['id'];
$oldname = $_POST['oldname'];
$nameslug = $_POST['nameslug'];

if($_POST['submit'] == "Add Tune") {
	$nameslug = generate_slug($name, $tunesmithid);
	$query = "INSERT INTO tunes (name, nameslug, year, additionalyear, textsource, textyear, rank, meter, modeoftime, tunekey, threeliner, type, text, publicdescription, tunesmithid, dateadded, useradded) VALUES ('$name', '$nameslug', '$year', '$additionalyear', '$textsource', '$textyear', '0', '$meter', '$modeoftime', '$tunekey', '$threeliner', '$type', '$text', '$publicdescription', '$tunesmithid', '".date("Y-m-d H:i:s")."', '$username')";
	$action = "add tune";
} elseif($_POST['submit'] == "Edit Tune") {
	$query = "SELECT name, publicdescription, type, text FROM tunes WHERE id = '$id'";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	$row_old = mysql_fetch_object($result);
	if($name != $row_old->name) { /* record edit in activity table and generate new slug. */
		$nameslug = generate_slug($name, $tunesmithid);
		$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'edit tune name', '".date("Y-m-d H:i:s")."')";
		$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
	};
	if($_POST['publicdescription'] != $row_old->publicdescription) {
		if($row_old->publicdescription == "") { $spec_action = "add public description"; } else { $spec_action = "edit public description"; }; 
		$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', '$spec_action', '".date("Y-m-d H:i:s")."')";
		$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
	};
	if($type != $row_old->type) {
		$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'edit type', '".date("Y-m-d H:i:s")."')";
		$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
	};
	if($_POST['text'] != $row_old->text) {
		$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'edit text', '".date("Y-m-d H:i:s")."')";
		$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
	};

	$query_n = "SELECT * FROM notes WHERE tuneid = '$id'";
	$result_n = mysql_query($query_n) or die("<p class=\"alert\">Error in query: $query_n. ".mysql_error()."</p>");
	if(mysql_num_rows($result_n) > 0) {
		while($row_n = mysql_fetch_object($result_n)) {
			if($_POST['delete_'.$row_n->id]) {
				$query_nu = "DELETE FROM notes WHERE id = '".$row_n->id."'";
				$result_nu = mysql_query($query_nu) or die("<p class=\"alert\">Error in query: $query_nu. ".mysql_error()."</p>");
				$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'delete note', '".date("Y-m-d H:i:s")."')";
				$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
			} elseif($_POST['note_'.$row_n->id] != $row_n->note) {
				$query_nu = "UPDATE notes SET note = '".mysql_escape_string($_POST['note_'.$row_n->id])."', modifydate = '".date("Y-m-d H:i:s")."' WHERE id = '".$row_n->id."'";
				$result_nu = mysql_query($query_nu) or die("<p class=\"alert\">Error in query: $query_nu. ".mysql_error()."</p>");
				$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$id', '$tunesmithid', 'edit note', '".date("Y-m-d H:i:s")."')";
				$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
			}
		}
	}
	
	$query = "UPDATE tunes SET name = '$name', nameslug = '$nameslug', year = '$year', additionalyear = '$additionalyear', textsource = '$textsource', textyear = '$textyear', meter = '$meter', modeoftime = '$modeoftime', tunekey = '$tunekey', threeliner = '$threeliner', type = '$type', text = '$text', publicdescription = '$publicdescription', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$id'";
	$tuneid = $id;
	$action = "edit tune";
};
$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
if($_POST['submit'] == "Add Tune") { // must happen after the result
	$tuneid = mysql_insert_id();
}

$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', '$action', '".date("Y-m-d H:i:s")."')";
$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");

/* Notes */

if($description != "") {
	$query = "INSERT INTO notes (tuneid, tunesmithid, note, createdate) VALUES ('$tuneid', '$tunesmithid', '$description', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	$query_a = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'add note', '".date("Y-m-d H:i:s")."')";
	$result_a = mysql_query($query_a) or die("<p class=\"alert\">Error in query: $query_a. ".mysql_error()."</p>");
}

/* delete PDF, INDD, MIDI, MP3, and MUS/MYR files */

if($_POST['submit'] == "Edit Tune" && $_POST['deletefile'] && is_array($_POST['deletefile'])) {
  for($i = 0; $i < sizeof($_POST['deletefile']); $i ++) {
	if(is_numeric($_POST['deletefile'][$i])) {
	  $query = "SELECT filename, fileext FROM uploadedfiles WHERE id = '".$_POST['deletefile'][$i]."' LIMIT 1";
	  $result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	  if (mysql_num_rows($result) > 0) {
	    $row = mysql_fetch_object($result);
		$query = "DELETE FROM uploadedfiles WHERE id = '".$_POST['deletefile'][$i]."'";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'delete ".$row->fileext."', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
/*
		$myFile = "files/".$row->filename;
		unlink($myFile);
*/
		if($s3->deleteObject(S3_BUCKET, $row->filename)) {
		  if($filedeletesuccess == "") { $filedeletesuccess = "&filedel=".$row->fileext; } else { $filedeletesuccess .= ", ".$row->fileext; }
		} else {
		  if($filedeletefailure == "") { $filedeletefailure = "&filedelfailure=6"; } else { $filedeletefailure .= ",6"; }; // generic error
		}
	  } else {
		if($filedeletefailure == "") { $filedeletefailure = "&filedelfailure=6"; } else { $filedeletefailure .= ",6"; }; // generic error
	  };
	};
  };
};

/* edit MP3/M4A files */

for($i = 0; $i < sizeof($_POST['editmp3counter']); $i ++) {
  $query = "SELECT id FROM uploadedfiles WHERE id = '".$_POST['editmp3id'][$i]."' AND (fileext = 'mp3' OR fileext = 'm4a') LIMIT 1";
  $result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
  if(mysql_num_rows($result) > 0) {
	$row = mysql_fetch_object($result);
	$mp3id = $_POST['editmp3id'][$i];
	$mp3title = mysql_escape_string($_POST['editmp3title'][$i]);
	$mp3description = mysql_escape_string($_POST['editmp3description'][$i]);
	$query = "UPDATE uploadedfiles SET title = '$mp3title', description = '$mp3description', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$mp3id'";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'edit ".$row->fileext."', '".date("Y-m-d H:i:s")."')";
	$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
  };
};

/* upload PDF, INDD, MIDI, and MUS/MYR files */
$pdfs = 0; $indds = 0; $muss = 0; $mids = 0;
for($i = 0; $i < sizeof($_POST['uploadedfilecounter']); $i ++) {
  if($_FILES['uploadedfile']['name'][$i]) { //make sure there's actually a file in this file input
  //echo $_FILES['uploadedfile']['type'][$i]." ".substr($_FILES['uploadedfile']['name'][$i], -4); exit();
	if($_FILES['uploadedfile']['type'][$i] == "application/pdf" // make sure its of the appropriate filetype
	|| $_FILES['uploadedfile']['type'][$i] == "application/x-indesign"
	||($_FILES['uploadedfile']['type'][$i] == "application/octet-stream" && substr($_FILES['uploadedfile']['name'][$i], -4) == "indd")
	|| $_FILES['uploadedfile']['type'][$i] == "application/x-myriad-music"
	||($_FILES['uploadedfile']['type'][$i] == "application/octet-stream" && substr($_FILES['uploadedfile']['name'][$i], -3) == "myr")
	|| $_FILES['uploadedfile']['type'][$i] == "audio/mid"
	|| $_FILES['uploadedfile']['type'][$i] == "audio/midi") {
	  if($_FILES['uploadedfile']['type'][$i] == "application/pdf") { $pdfs++; $thisfile = "PDF"; } // increment counter of number of files of this type uploaded
	  elseif($_FILES['uploadedfile']['type'][$i] == "application/x-indesign" || ($_FILES['uploadedfile']['type'][$i] == "application/octet-stream" && substr($_FILES['uploadedfile']['name'][$i], -4) == "indd")) { $indds++; $thisfile = "INDD"; }
	  elseif($_FILES['uploadedfile']['type'][$i] == "application/x-myriad-music") { $muss++; $thisfile = "MUS"; }
	  elseif($_FILES['uploadedfile']['type'][$i] == "application/octet-stream" && substr($_FILES['uploadedfile']['name'][$i], -3) == "myr") { $muss++; $thisfile = "MYR"; }
	  elseif($_FILES['uploadedfile']['type'][$i] == "audio/mid") { $mids++; $thisfile = "MIDI"; };
  	  if(($thisfile == "PDF" && $pdfs > 1) || ($thisfile == "INDD" && $indds > 1) || ($thisfile == "MUS" && $muss > 1) || ($thisfile == "MIDI" && $mids > 1)) { // make sure only one of each type is uploaded
		if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=3"; } else { $fileuploadfailure .= ",3"; }; // only one of filetype permitted error
	  } else {

		$fileext = find_exts($_FILES['uploadedfile']['name'][$i]); 
	    $filetype = $_FILES['uploadedfile']['type'][$i];
	    $filesize = $_FILES['uploadedfile']['size'][$i];
		$filename = $username."_".$tuneid.".".$fileext;
		if($s3->putObject($s3->inputFile($_FILES['uploadedfile']['tmp_name'][$i], false), S3_BUCKET, $filename, S3::ACL_PUBLIC_READ, array(), array("Content-Type" => $filetype))) {
		  $query = "SELECT * FROM uploadedfiles WHERE tuneid = '$tuneid' AND fileext = '$fileext'";
		  $result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		  $alreadythere = 0;
		  if (mysql_num_rows($result) > 0) {
		    while($row = mysql_fetch_object($result)) {
			  $alreadythere = 1;
			  $id = $row->id;
			  $queryfileupdate = "UPDATE uploadedfiles SET filesize = '$filesize', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$tuneid'";
			  $resultfileupdate = mysql_query($queryfileupdate) or die("<p class=\"alert\">Error in query: $queryfileupdate. ".mysql_error()."</p>");
			  $queryactivityfileupdate = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'edit ".$row->fileext."', '".date("Y-m-d H:i:s")."')";
			  $resultactivityfileupdate = mysql_query($queryactivityfileupdate) or die("<p class=\"alert\">Error in query: $queryactivityfileupdate. ".mysql_error()."</p>");
		  	};
		  } else {
			$query = "INSERT INTO uploadedfiles (tuneid, filename, filetype, fileext, filesize, tunesmithid, dateadded, useradded) VALUES ('$tuneid', '$filename', '$filetype', '$fileext', '$filesize', '$tunesmithid', '".date("Y-m-d H:i:s")."', '$username')";
			$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
			$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'add $fileext', '".date("Y-m-d H:i:s")."')";
			$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		  };
		  if($fileuploadsuccess == "") { $fileuploadsuccess = "&fileupload=".$thisfile; } else { $fileuploadsuccess .= ", ".$thisfile; }
 	    } else {
		  if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=1"; } else { $fileuploadfailure .= ",1"; }; // generic error
		}

/*
	    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'][$i], $target_path)) {
		  $query = "SELECT * FROM uploadedfiles WHERE tuneid = '$tuneid' AND fileext = '$fileext'";
		  $result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		  $alreadythere = 0;
		  if (mysql_num_rows($result) > 0) {
		    while($row = mysql_fetch_object($result)) {
			  $alreadythere = 1;
			  $id = $row->id;
			  $queryfileupdate = "UPDATE uploadedfiles SET filesize = '$filesize', dateupdated = '".date("Y-m-d H:i:s")."', userupdated = '$username' WHERE id = '$tuneid'";
			  $resultfileupdate = mysql_query($queryfileupdate) or die("<p class=\"alert\">Error in query: $queryfileupdate. ".mysql_error()."</p>");
			  $queryactivityfileupdate = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'edit ".$row->fileext."', '".date("Y-m-d H:i:s")."')";
			  $resultactivityfileupdate = mysql_query($queryactivityfileupdate) or die("<p class=\"alert\">Error in query: $queryactivityfileupdate. ".mysql_error()."</p>");
		  	};
		  } else {
			$query = "INSERT INTO uploadedfiles (tuneid, filename, filetype, fileext, filesize, tunesmithid, dateadded, useradded) VALUES ('$tuneid', '$filename', '$filetype', '$fileext', '$filesize', '$tunesmithid', '".date("Y-m-d H:i:s")."', '$username')";
			$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
			$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'add $fileext', '".date("Y-m-d H:i:s")."')";
			$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		  };
		  if($fileuploadsuccess == "") { $fileuploadsuccess = "&fileupload=".$thisfile; } else { $fileuploadsuccess .= ", ".$thisfile; }
 	    } else {
		  if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=1"; } else { $fileuploadfailure .= ",1"; }; // generic error
		};
*/
	  };
	} else {
	  if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=2"; } else { $fileuploadfailure .= ",2"; }; // wrong filetype error
	};
  };
};

/* upload MP3/M4A files */

$mp3s = 0; 
for($i = 0; $i < sizeof($_POST['uploadedmp3counter']); $i ++) {
  if($_FILES['uploadedmp3']['name'][$i]) { //make sure there's actually a file in this mp3/m4a file input
	if(find_exts($_FILES['uploadedmp3']['name'][$i]) == "mp3"
    || find_exts($_FILES['uploadedmp3']['name'][$i]) == "m4a") { // make sure file is an mp3/m4a <--- DOESN'T SEEM TO BE WORKING FOR LARGER FILES??
	  $query = "SELECT max(filenumber) as maxfilenumber FROM uploadedfiles WHERE tuneid = '$tuneid' AND (fileext = 'mp3' OR fileext = 'm4a')";
	  $result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
	  $row = mysql_fetch_object($result);
	  $filenumber = $row->maxfilenumber + 1; // figure out what number mp3/m4a upload this is for this tune
	  $fileext = find_exts($_FILES['uploadedmp3']['name'][$i]); 
	  $filetype = $_FILES['uploadedmp3']['type'][$i];
	  $filesize = $_FILES['uploadedmp3']['size'][$i];
	  $filename = $username."_".$tuneid."_".$filenumber.".".$fileext;
	  $mp3title = mysql_escape_string($_POST['uploadedmp3title'][$i]);
	  $mp3description = mysql_escape_string($_POST['uploadedmp3description'][$i]);
	  if($s3->putObject($s3->inputFile($_FILES['uploadedmp3']['tmp_name'][$i], false), S3_BUCKET, $filename, S3::ACL_PUBLIC_READ)) {
		$query = "INSERT INTO uploadedfiles (tuneid, filename, filenumber, filetype, fileext, filesize, tunesmithid, title, description, dateadded, useradded) VALUES ('$tuneid', '$filename', '$filenumber', '$filetype', '$fileext', '$filesize', '$tunesmithid', '$mp3title', '$mp3description', '".date("Y-m-d H:i:s")."', '$username')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		$query = "INSERT INTO activity (tuneid, tunesmithid, action, actiondate) VALUES ('$tuneid', '$tunesmithid', 'add ".$fileext."', '".date("Y-m-d H:i:s")."')";
		$result = mysql_query($query) or die("<p class=\"alert\">Error in query: $query. ".mysql_error()."</p>");
		if($mp3title == "") { $filetoreport = strtoupper($fileext)."_".$filenumber; } else { $filetoreport = $mp3title. " (".strtoupper($fileext).")"; };
		if($fileuploadsuccess == "") { $fileuploadsuccess = "&fileupload=".$filetoreport; } else { $fileuploadsuccess .= ", ".$filetoreport; }
 	  } else {
		if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=4"; } else { $fileuploadfailure .= ",4"; }; // generic error
	  };
	} else {
	  if($fileuploadfailure == "") { $fileuploadfailure = "&filefailure=5"; } else { $fileuploadfailure .= ",5"; }; // wrong filetype error
	};
  };
};

/* redirect to add.php */
if($_POST['submit'] == "Edit Tune") { $action = "updated"; } else { $action = "added"; };
$action = "?action=".$action;
header( 'Location: index.php'.$action.'&tunename='.stripslashes($name).$filedeletesuccess.$filedeletefailure.$fileuploadsuccess.$fileuploadfailure );
$clear = 1;
// free result set memory
?>