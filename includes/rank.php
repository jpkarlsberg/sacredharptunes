<?php
if($_POST['submitrank'] == "Rank") { /*rank change function */
	if($_POST['rank'] <= 0) {
		?><p class="alert">Your attempt to assign a new rank to "<?php echo stripslashes($_POST['name']); ?>" has failed because the rank (<?php echo $_POST['rank']; ?>) is too low.</p><?php
	} else {
		$queryid = "SELECT id, rank FROM tunes WHERE id=".$_POST['id']." LIMIT 1";
		$resultid = mysql_query($queryid) or die("<p>Error in query: $queryid. ".mysql_error()."</p>");
		if(mysql_num_rows($resultid) > 0) {
			while($rowid = mysql_fetch_object($resultid)) {
				if($rowid->rank == 0) {
					$queryrank = "SELECT id, rank FROM tunes WHERE rank=".$_POST['rank']." AND tunesmithid = ".$usernameid." LIMIT 1";
					$resultrank = mysql_query($queryrank) or die("<p>Error in query: $queryrank. ".mysql_error()."</p>");
					if(mysql_num_rows($resultrank) > 0) {
						$queryrankfromzero = "SELECT id, rank FROM tunes WHERE rank >=".$_POST['rank']." AND tunesmithid = ".$usernameid;
						$resultrankfromzero = mysql_query($queryrankfromzero) or die("<p>Error in query: $queryrankfromzero. ".mysql_error()."</p>");
						if(mysql_num_rows($resultrankfromzero) > 0) {
							while($rowrankfromzero = mysql_fetch_object($resultrankfromzero)) {
								$newrank = $rowrankfromzero->rank + 1;
								$queryhighermassupdate = "UPDATE tunes SET rank=".$newrank." WHERE id=".$rowrankfromzero->id;
								$resulthighermassupdate = mysql_query($queryhighermassupdate) or die("<p>Error in query: $queryhighermassupdate. ".mysql_error()."</p>");
							};
						};
						$queryupdate = "UPDATE tunes SET rank=".$_POST['rank']." WHERE id=".$_POST['id'];
						$resultupdate = mysql_query($queryupdate) or die("<p>Error in query: $queryupdate. ".mysql_error()."</p>");
						?><p class="success">You successfully gave "<?php echo stripslashes($_POST['name']); ?>" the rank <?php echo $_POST['rank']; ?>.</p><?php
					} else {
						$queryranklessone = "SELECT id, rank FROM tunes WHERE rank=".($_POST['rank'] - 1)." AND tunesmithid = ".$usernameid." LIMIT 1";
						$resultranklessone = mysql_query($queryranklessone) or die("<p>Error in query: $queryranklessone. ".mysql_error()."</p>");
						if(mysql_num_rows($resultranklessone) > 0) {
							$queryupdate = "UPDATE tunes SET rank=".$_POST['rank']." WHERE id=".$_POST['id'];
							$resultupdate = mysql_query($queryupdate) or die("<p>Error in query: $queryupdate. ".mysql_error()."</p>");
							?><p class="success">You successfully gave "<?php echo stripslashes($_POST['name']); ?>" the rank <?php echo $_POST['rank']; ?>.</p><?php
						} else {
							?><p class="alert">Your attempt to assign a new rank to "<?php echo stripslashes($_POST['name']); ?>" has failed because the rank (<?php echo $_POST['rank']; ?>) is too high.</p><?php
						};
					};
				} else { /* rank > 0 */
					$queryrank = "SELECT id, rank FROM tunes WHERE rank=".$_POST['rank']." AND tunesmithid = ".$usernameid." LIMIT 1";
					$resultrank = mysql_query($queryrank) or die("<p>Error in query: $queryrank. ".mysql_error()."</p>");
					if(mysql_num_rows($resultrank) > 0) {
						if($rowid->rank == $_POST['rank']) {
							?><p class="alert">Your attempt to assign a new rank to "<?php echo stripslashes($_POST['name']); ?>" has failed because the tune already has the rank (<?php echo $_POST['rank']; ?>) you selected.</p><?php
						} elseif($rowid->rank > $_POST['rank']) {
							$queryhigher = "SELECT id, rank FROM tunes WHERE rank >= ".$_POST['rank']." AND rank < ".$rowid->rank." AND tunesmithid = ".$usernameid;
							$resulthigher = mysql_query($queryhigher) or die("<p>Error in query: $queryhigher. ".mysql_error()."</p>");
							if (mysql_num_rows($resulthigher) > 0) {
								while($rowhigher = mysql_fetch_object($resulthigher)) {
									$newrank = $rowhigher->rank + 1;
									$queryhighermassupdate = "UPDATE tunes SET rank=".$newrank." WHERE id=".$rowhigher->id;
									$resulthighermassupdate = mysql_query($queryhighermassupdate) or die("<p>Error in query: $queryhighermassupdate. ".mysql_error()."</p>");
								};
							} else {
								echo "<p> NO RESULTS</p>";
							};
							$queryupdate = "UPDATE tunes SET rank=".$_POST['rank']." WHERE id=".$_POST['id'];
							$resultupdate = mysql_query($queryupdate) or die("<p>Error in query: $queryupdate. ".mysql_error()."</p>");
							?><p class="success">You successfully gave "<?php echo stripslashes($_POST['name']); ?>" the rank <?php echo $_POST['rank']; ?>.</p><?php
						} else { /* rowid->rank < post-rank */
							$querylower = "SELECT id, rank FROM tunes WHERE rank > ".$rowid->rank." AND rank <= ".$_POST['rank']." AND tunesmithid = ".$usernameid;
							$resultlower = mysql_query($querylower) or die("<p>Error in query: $querylower. ".mysql_error()."</p>");
							if (mysql_num_rows($resultlower) > 0) {
								while($rowlower = mysql_fetch_object($resultlower)) {
									$newrank = $rowlower->rank - 1;
									$querylowermassupdate = "UPDATE tunes SET rank=".$newrank." WHERE id=".$rowlower->id;
									$resultlowermassupdate = mysql_query($querylowermassupdate) or die("<p>Error in query: $querylowermassupdate. ".mysql_error()."</p>");
								};
							};
							$queryupdate = "UPDATE tunes SET rank=".$_POST['rank']." WHERE id=".$_POST['id'];
							$resultupdate = mysql_query($queryupdate) or die("<p>Error in query: $queryupdate. ".mysql_error()."</p>");
							?><p class="success">You successfully gave "<?php echo stripslashes(stripslashes($_POST['name'])); ?>" the rank <?php echo $_POST['rank']; ?>.</p><?php
						};
					} else {
						?><p class="alert">Your attempt to assign a new rank to "<?php echo stripslashes($_POST['name']); ?>" has failed because the rank (<?php echo $_POST['rank']; ?>) is too high.</p><?php
					};
				};
			};
		};
	};
};
?>