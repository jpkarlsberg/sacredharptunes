<title><?php echo SITE_NAME_SHORT; if($page == "com") { echo " by ".$bywhat; }; ?></title>
<link rel="stylesheet" type="text/css" href="/includes/style.css">
<?php if($page == "com" and $com != NULL) { ?>
<script src="<?php echo SITE_URL.SITE_INCLUDES_PATH; ?>wimpy/wimpy_button_bridge.js" type="text/javascript"></script>
<script language="javascript">
// Here we are over-writing the variables locally to use the smaller buttons. 
wimpyButtonImagePlay	= "<?php echo SITE_URL.SITE_INCLUDES_PATH; ?>wimpy/b_play_sh.png";
wimpyButtonImagePause	= "<?php echo SITE_URL.SITE_INCLUDES_PATH; ?>wimpy/b_pause_sh.png";
</script>
<?php } ?>