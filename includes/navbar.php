		<ul id="navbar">
			<li class="first"><a href="<?php echo SITE_URL.SITE_LOGIN_PATH; ?>?logout=1">Logout</a></li><?php
			if($admin >= 1 && $guest == 0) { echo "<li><a href=\"admin.php\">Administration</a></li>"; };
			if($userpublic == 1) { ?><li><a target="_blank" href="<?php echo SITE_URL.$publicfoldername."/"; ?>">View Public Tunes</a></li><?php }; ?>
		</ul>
