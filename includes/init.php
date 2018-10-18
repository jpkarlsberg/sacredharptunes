<?php
require("constants.php");
require(SITE_ROOT_HTML_PATH.SITE_INCLUDES_PATH."password_protect.php"); /* password protection. do not (re)move this line of code!! */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php 
require("head.php");
require("naming_functions.php");
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die ("Unable to connect!");
mysql_select_db(DB_NAME) or die( "Unable to select database!");
require("user.php");

if($page == "Settings" || $page == "Add" || ($page == "Administration" && $admintools == "pubpage")) { /* TinyMCE Check */
?>
<script type="text/javascript" src="/includes/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme: "advanced",
	width: "570px"
});
</script>

<?php }; /* END TinyMCE Check */
if($page == "Settings" || $page == "Add") { /* Object Toggle JavaScript Check */
	if($page == "Settings") { /* Query for Object Toggle */
		$query = "SELECT * FROM tunes WHERE public = 0 AND tunesmithid = ".$usernameid." ORDER BY rank, name";
		$result = mysql_query($query) or die("<p>Error in query: $query. ".mysql_error()."</p>");
		if(mysql_num_rows($result) > 0) {
			$selecttune = "<option>Select a Tune</option>";
			while($row = mysql_fetch_object($result)) {
				$queryfiles = "SELECT fileext FROM uploadedfiles WHERE (fileext = 'pdf' OR fileext = 'mp3' OR fileext = 'mid') AND tuneid = ".$row->id."";
				$resultfiles = mysql_query($queryfiles) or die("<p>Error in query: $queryfiles. ".mysql_error()."</p>");
				if(mysql_num_rows($resultfiles) > 0) {
					$selecttune .= "<option value=\"".$row->id."\">".readable_tunename($row->name,"",$row->meter)."</option>";
				};
		
			};
			$selecttune = str_replace("'", "\'", $selecttune);
		};
	}; /* END Query for Object Toggle */
?>
<script type="text/javascript">
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
  var selecttunes = '<?php echo $selecttune; ?>';
  function toggle(obj) {
	var el = document.getElementById(obj);
	el.style.display = (el.style.display != 'none' ? 'none' : '' );
  }
  var Dom = {
	get: function(el) {
	  if (typeof el === 'string') {
		return document.getElementById(el);
	  } else {
		return el;
	  }
	},
	add: function(el, dest) {
	  var el = this.get(el);
	  var dest = this.get(dest);
	  dest.appendChild(el);
	},
	remove: function(el) {
	  var el = this.get(el);
	  el.parentNode.removeChild(el);
	}
  };
  var Event = {
	add: function() {
	  if (window.addEventListener) {
		return function(el, type, fn) {
		  Dom.get(el).addEventListener(type, fn, false);
		};
	  } else if (window.attachEvent) {
		return function(el, type, fn) {
		  var f = function() {
			fn.call(Dom.get(el), window.event);
		  };
		  Dom.get(el).attachEvent('on' + type, f);
		};
	  }
	}()
  };
<?php if($page == "Settings") { /* Event for Particular Pages */ ?>
  Event.add(window, 'load', function() {
	var i = 0;
	Event.add('addatune', 'click', function() {
	  var el = document.createElement('p');
	  el.innerHTML = '<select name="tunetoadd[]">' + selecttunes + '</select> <label class="space" for="tunetoadd[]">Add this tune</label>'; ++i;
//	  el.innerHTML = 'Remove This Element (' + ++i + ')';
	  Dom.add(el, 'addtunes');
	});
  });
<?php } elseif($page == "Add") { ?>
  Event.add(window, 'load', function() {
	var i = 0;
	Event.add('addafile', 'click', function() {
	  var el = document.createElement('div');
	  el.innerHTML = '<label for="uploadedfile">Upload a PDF, INDD, MIDI, MUS, or MYR file:</label> <input type="hidden" name="uploadedfilecounter[]" value="1" /><input type="file" name="uploadedfile[]" id="uploadedfile[]" />'; ++i;
//	  el.innerHTML = 'Remove This Element (' + ++i + ')';
	  Dom.add(el, 'extrafiles');
	});
	Event.add('addamp3file', 'click', function() {
	  var el = document.createElement('div');
	  el.innerHTML = '<p><label for="uploadedmp3">Upload a MP3/M4A file:</p><div><label for="uploadedmp3">MP3/M4A File:</label><input type="hidden" name="uploadedmp3counter[]" value="1" /> <input size="5" type="file" name="uploadedmp3[]" id="uploadedmp3[]" /><label class="sameline" for="uploadedmp3title">MP3/M4A title:</label> <input type="text" size="30" name="uploadedmp3title[]" id="uploadedmp3title[]" maxlength="60" /></div><div><label for="uploadedmp3description">MP3/M4A description:</label><br /><textarea name="uploadedmp3description[]" id="uploadedmp3description[]" cols="65" rows="2"></textarea></div>'; ++i;
//	  el.innerHTML = 'Remove This Element (' + ++i + ')';
	  Dom.add(el, 'extramp3files');
	});
  });
<?php }; /* END Event for Particular Page */ ?>
</script>
<?php }; /* END Object Toggle JavaScript Check */ ?>
</head>

<body<?php if($userpublic != 1) { ?> onload="toggle('publictunes');"<?php };
if($page == "Home Page" || $page == "Statistics") { /* Wide Page Check */ echo " class=\"wide\""; }; /* END Wide Page Check */ ?>>

<div id="<?php if($page == "Delete") { /* Page Type Check */ echo "pagetask"; } else { echo "page"; }; /* END Page Type Check */ ?>">
<div id="header">
<h1><?php echo SITE_NAME_SHORT_PRIVATE; ?> <span class="em">by <?php echo $name; ?></span></h1>
<?php /* put any alert or maintenance announcement here */ ?>
</div>
	<div id="main">
<?php
if($page != "Delete") { /* Don't Display Menu for Some Pages */ require("menu.php"); };
?>
	<div id="content">
<?php if($page != "Delete") { /* Don't Display Navbar for Some Pages */  require("navbar.php"); }; ?>
