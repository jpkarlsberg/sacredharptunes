<?php
function neat_text ($text, $style = NULL, $abbrevlength = 50) {
	$textbr = nl2br(stripslashes($text));
	if($style == "first line") {
		$strip_html_arr = array("<p>", "</p>", "<li>", "</li>", "<ul>", "</ul>", "<ol>", "</ol>", "<blockquote>", "</blockquote>");
		$textbr = str_replace($strip_html_arr, "", $textbr);
		$pos = strpos($textbr, "<br />");
		if(($pos === false || $pos > $abbrevlength) && strlen($text) > $abbrevlength) {
			$extra = " &hellip;";
			$pos = $abbrevlength - 20;
		} elseif($pos === false || $pos > $abbrevlength) {
			$pos = strlen($text);
		};
		$neat_text = substr($textbr, 0, $pos).$extra;
	} else {
		$neat_text = $textbr;
	};
	return $neat_text;
}

function filename ($username, $tuneid, $fileext, $filenum = NULL) {
	$filename = $username."_".$tuneid;
	if($fileext == ("mp3" || "m4a") && $filenum) {
		$filename .= "_".$filenum;
	};
	$filename .= ".".$fileext;
	return $filename;
}

function formatted_mp3player ($username, $tuneid, $fileid, $fileext, $linktextformat, $filenum = NULL, $action = "echo", $title = 0, $filecount = 0) {
	$filename = filename($username, $tuneid, $fileext, $filenum);
	if($title != 0) {
		$titletext = "title=\"".mp3_filetitle($fileid)."\" ";
	} else {
		$titletext = "";
	}
	/*
	* echotitle is full player,
	* echo (default) is small direct link - steve-brett
	 */
	if ($action == "echotitle") {
		$displaytext = mp3_filetitle($fileid);
		$formatted_mp3player = "<audio height=\"25\" width=\"150\" controls> \n\t <source src=\"" . SITE_FILES_URL . $filename . "\" type=\"audio/mpeg\" data-title=\"$displaytext\"> \n\t <!-- fallback for non supporting browsers goes here --> \n\t <p><a ".$titletext." href=\"".SITE_FILES_URL.$filename."\" download> $fileext </a></p>\n\t</audio>\n\t<p>" . $displaytext . "</p>";
	} else {
		// Just give a link for now
		$displaytext = $fileext;
		$formatted_mp3player = "<a ".$titletext." href=\"" . SITE_FILES_URL . $filename . "\" download>" . $displaytext . "</a>";
	}
	//$formatted_mp3player = "<a ".$titletext."href=\"javascript:;\" onClick=\"wimpyButtonPlayPause('".SITE_FILES_URL.$filename."')\">".$displaytext."</a>";
	//$formatted_mp3player = "<img ".$titletext."src=\"".SITE_URL.SITE_INCLUDES_PATH."wimpy/b_play_sh.png\" onClick=\"wimpyButtonPlayPause('".SITE_FILES_URL.$filename."')\">";
	//$formatted_mp3player = "<object type=\"application/x-shockwave-flash\" data=\"http://sacredharptunes.com/includes/button/musicplayer.swf?&song_url=".SITE_FILES_URL.$filename."&b_bgcolor=DDDDFF&b_fgcolor=7979a9&\" width=\"17\" height=\"17\"> <param name=\"movie\" value=\"http://sacredharptunes.com/includes/button/musicplayer.swf?&song_url=".SITE_FILES_URL.$filename."&b_bgcolor=DDDDFF&b_fgcolor=7979a9&\" /> <img src=\"noflash.gif\" width=\"17\" height=\"17\" alt=\"\" /> </object>";
	if($action == "echo" || $action == "echotitle") {
		echo $formatted_mp3player;
	};
}

function formatted_filename ($username, $tuneid, $fileid, $fileext, $linktextformat, $filenum = NULL, $action = "echo", $title = 0, $filecount = 0) {
	$filename = filename($username, $tuneid, $fileext, $filenum);
	if($fileext == "mid") { $fileext = "midi"; } // show midi, not mid, to the user
	if($fileext == "pdf") { $google_viewer_text = PDF_VIEWER_CODE; $target_text = TARGET_CODE; } else { $google_viewer_text = ""; $target_text = ""; };
	$linktext = str_replace("EXT", strtolower($fileext), str_replace("ext", $fileext, $linktextformat));
	$linktext = stripslashes(str_replace("mp3title", mp3_filetitle($fileid), $linktext));
	if($title != 0) {
		$titletext = "title=\"".mp3_filetitle($fileid)."\" ";
	} else {
		$titletext = "";
	}
	if($filecount == 1) {
		$formatted_filename = "<a ".$titletext."href=\"".SITE_URL."index_process.php?action=downloadcount&id=".$fileid."\"".$target_text.">".$linktext."</a>";
	} else {
		$formatted_filename = "<a ".$titletext."href=\"".$google_viewer_text.SITE_FILES_URL.$filename."\"".$target_text.">".$linktext."</a>";
	}
	if($action == "return") {
		return $formatted_filename;
	} else {
		echo $formatted_filename;
	};
}

function mp3_filetitle ($fileid, $case = "upper") {
	$query = "SELECT title, filenumber, fileext FROM uploadedfiles WHERE id = '$fileid' LIMIT 1";
	$result = mysql_query($query) or die("<p>There was an error with the previous operation. Error in query: $query. ".mysql_error()."</p>");
	$row = mysql_fetch_object($result);
	if($row->title != "") {
		$mp3title = stripslashes($row->title);
	} else {
		if($case == "upper") { $fileext = strtoupper($row->fileext); } else { $fileext = $row->fileext; };
		$mp3title = $fileext." ".$row->filenumber;
	}
	return $mp3title;
}

function readable_tunename ($name, $nameadditional, $meter, $fileext = NULL, $style = NULL) {
	if($nameadditional != "") { $nameadditional = " ".$nameadditional; };
	if($style == "strong" || $style == "em") {
		$readable = "<".$style.">".stripslashes($name.$nameadditional)."</".$style.">.";
	} else {
		$readable = stripslashes($name.$nameadditional).".";
	}
	if($meter != "" && $meter != "none") {
		$readable .= " ".$meter;
		if(substr($meter, -1) != ".") {
			$readable .= ".";
		};
	};
	$readable .= $fileext;
	return $readable;
}

function readable_key ($keyfromdb, $action = "echo", $keymm = "both") {
	if($keymm == "both"){
		$readable = substr($keyfromdb, 0, 1);
		if(substr($keyfromdb, 1, 1) == "b") {
			$readable .= "&#9837;";
		} elseif(substr($keyfromdb, 1, 1) == "#" or substr($keyfromdb, 1, 1) == "s") {
			$readable .= "&#9839;";
		};
		if(substr($keyfromdb, -1) == "m") {
			$readable .= " minor ";
		} else {
			$readable .= " major ";
		};
	} elseif($keymm == "mm") {
		if(substr($keyfromdb, -1) == "m") {
			$readable .= "minor ";
		} else {
			$readable .= "major ";
		};
	};
	if($action == "return") {
		return $readable;
	} else {
		echo $readable;
	};
}

function find_exts ($filename) { /* from about.com */
	$filename = strtolower($filename) ;
	$exts = split("[/\\.]", $filename) ;
	$n = count($exts)-1;
	$exts = $exts[$n];
	return $exts;
}

$nwords = array(    "zero", "first", "second", "third", "fourth", "fifth", "sixth", "seventh",
                     "eighth", "ninth", "tenth", "eleventh", "twelveth", "thirteenth",
                     "fourteenth", "fifteenth", "sixteenth", "seventeenth", "eighteenth",
                     "nineteenth", "twenty", 30 => "thirty", 40 => "forty",
                     50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
                     90 => "ninety" );

function int_to_words($x)
{
     global $nwords;
     if(!is_numeric($x))
     {
         $w = '#';
     }else if(fmod($x, 1) != 0)
     {
         $w = '#';
     }else{
         if($x < 0)
         {
             $w = 'minus ';
             $x = -$x;
         }else{
             $w = '';
         }
         if($x < 21)
         {
             $w .= $nwords[$x];
         }else if($x < 100)
         {
             $w .= $nwords[10 * floor($x/10)];
             $r = fmod($x, 10);
             if($r > 0)
             {
                 $w .= '-'. $nwords[$r];
             }
         } else if($x < 1000)
         {
             $w .= $nwords[floor($x/100)] .' hundred';
             $r = fmod($x, 100);
             if($r > 0)
             {
                 $w .= ' and '. int_to_words($r);
             }
         } else if($x < 1000000)
         {
             $w .= int_to_words(floor($x/1000)) .' thousand';
             $r = fmod($x, 1000);
             if($r > 0)
             {
                 $w .= ' ';
                 if($r < 100)
                 {
                     $w .= 'and ';
                 }
                 $w .= int_to_words($r);
             }
         } else {
             $w .= int_to_words(floor($x/1000000)) .' million';
             $r = fmod($x, 1000000);
             if($r > 0)
             {
                 $w .= ' ';
                 if($r < 100)
                 {
                     $word .= 'and ';
                 }
                 $w .= int_to_words($r);
             }
         }
     }
     return $w;
}

function url_safe_characters($string, $char, $replacement = NULL) {
	$safe_string = str_replace($replacement, $char, $string);
	return $safe_string;
}

function str_to_slug($string) {
	$slug = strtolower(preg_replace('/[^A-Za-z0-9()]/', '-', preg_replace('/[^A-Za-z0-9 -]/', '', trim($string))));
	return $slug;
}

function generate_slug($tunename, $tunesmithid) {
	$slug = str_to_slug($tunename);
	$q_slug = "SELECT * FROM ".TBL_TUNES." WHERE tunesmithid = '".$tunesmithid."' AND nameslug = '".$slug."'";
	$r_slug = mysql_query($q_slug) or die("<p>Error in query: $q_slug. ".mysql_error()."</p>");
	if(mysql_num_rows($r_slug) == 0) {
		return $slug;
	} else {
		for($int = 2; ; $int ++) {
			$slug_appended = $slug."-".$int;
			$q_slug = "SELECT * FROM ".TBL_TUNES." WHERE tunesmithid = '".$tunesmithid."' AND nameslug = '".$slug_appended."'";
			$r_slug = mysql_query($q_slug) or die("<p>Error in query: $q_slug. ".mysql_error()."</p>");
			if(mysql_num_rows($r_slug) == 0) { break; };
		};
		return $slug_appended;
	};
}

function relative_date($time) {
    $today = strtotime(date('M j, Y'));
    $reldays = ($time - $today)/86400;
    if ($reldays >= 0 && $reldays < 1) {
        return 'today';
    } else if ($reldays >= 1 && $reldays < 2) {
        return 'tomorrow';
    } else if ($reldays >= -1 && $reldays < 0) {
        return 'yesterday';
    }
    if (abs($reldays) < 7) {
        if ($reldays > 0) {
            $reldays = floor($reldays);
            return 'in ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
        } else {
            $reldays = abs(floor($reldays));
            return $reldays . ' day'  . ($reldays != 1 ? 's' : '') . ' ago';
        }
    }
    if (abs($reldays) < 182) {
        return date('D, M j',$time ? $time : time());
    } else {
        return date('D, M j, Y',$time ? $time : time());
    }
}
?>
