<?php
require_once 'functions.php';

if ( !current_user_can( 'manage_options' ) ) {
	return;
}

if (isset($_REQUEST['Clear'])){
	ca_clear_cache();
	?><div class="updated"><p><strong><?php _e('Cleared Cache.', 'clicky-analytics' ); ?></strong></p></div>  
	<?php
}
if(ca_safe_get('ca_hidden') == 'Y') {  
	//Form data sent  
	$sitekey = ca_safe_get('ca_sitekey');  
	update_option('ca_sitekey', sanitize_text_field($sitekey));  
	
	$siteid = ca_safe_get('ca_siteid');
	update_option('ca_siteid', sanitize_text_field($siteid));  
	
	$dashaccess = ca_safe_get('ca_access');  
	update_option('ca_access', $dashaccess);
	
	$ca_pgd = ca_safe_get('ca_pgd');
	update_option('ca_pgd', $ca_pgd);

	$ca_rd = ca_safe_get('ca_rd');
	update_option('ca_rd', $ca_rd);

	$ca_sd = ca_safe_get('ca_sd');
	update_option('ca_sd', $ca_sd);		
	
	$ca_map = ca_safe_get('ca_map');
	update_option('ca_map', $ca_map);
	
	$ca_traffic = ca_safe_get('ca_traffic');
	update_option('ca_traffic', $ca_traffic);		

	$ca_frontend = ca_safe_get('ca_frontend');
	update_option('ca_frontend', $ca_frontend);		
	
	$ca_cachetime = ca_safe_get('ca_cachetime');
	update_option('ca_cachetime', $ca_cachetime);
	
	$ca_tracking = ca_safe_get('ca_tracking');
	update_option('ca_tracking', $ca_tracking);		

	$ca_tracking_type = ca_safe_get('ca_tracking_type');
	update_option('ca_tracking_type', $ca_tracking_type);			
	
	$ca_track_username = ca_safe_get('ca_track_username');
	update_option('ca_track_username', $ca_track_username);
		
	$ca_track_email = ca_safe_get('ca_track_email');
	update_option('ca_track_email', $ca_track_email);
	
	$ca_track_youtube = ca_safe_get('ca_track_youtube');
	update_option('ca_track_youtube', $ca_track_youtube);	
	
	$ca_track_html5 = ca_safe_get('ca_track_html5');
	update_option('ca_track_html5', $ca_track_html5);	
	if (!isset($_REQUEST['Clear'])){
		?>  
		<div class="updated"><p><strong><?php _e('Options saved.', 'clicky-analytics'); ?></strong></p></div>  
		<?php
	}	
}
	
if(!get_option('ca_access')){
	update_option('ca_access', "manage_options");	
}

$sitekey = get_option('ca_sitekey');  
$siteid = get_option('ca_siteid');  
$dashaccess = get_option('ca_access'); 
$ca_pgd = get_option('ca_pgd');
$ca_rd = get_option('ca_rd');
$ca_sd = get_option('ca_sd');
$ca_map = get_option('ca_map');
$ca_traffic = get_option('ca_traffic');
$ca_frontend = get_option('ca_frontend');
$ca_cachetime = get_option('ca_cachetime');
$ca_tracking = get_option('ca_tracking');
$ca_tracking_type = get_option('ca_tracking_type');
$ca_track_username = get_option('ca_track_username');
$ca_track_email = get_option('ca_track_email');
$ca_track_youtube = get_option('ca_track_youtube');
$ca_track_html5 = get_option('ca_track_html5');

if ( is_rtl() ) {
	$float_main="right";
	$float_note="left";
}else{
	$float_main="left";
	$float_note="right";	
}

?>  
<div class="wrap">
<div style="width:70%;float:<?php echo $float_main; ?>;">  
    <?php echo "<h2>" . __( 'Clicky Analytics Settings', 'clicky-analytics' ) . "</h2>"; ?>  
        <form name="ca_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="ca_hidden" value="Y">
		<?php echo "<h3>". __( 'Clicky Analytics API', 'clicky-analytics' )."</h3>"; ?>  
        <?php echo "<i>".__("You should watch this", 'clicky-analytics')." <a href='http://deconf.com/clicky-analytics-dashboard-wordpress/' target='_blank'>". __("Step by step video tutorial")."</a> ".__("to learn how to properly setup this plugin", 'clicky-analytics').". ".__("If you don't have a Clicky Account, you can", 'clicky-analytics')." <a href='http://clicky.com/66508224' target='_blank'>". __("create one here")."</a>.</i>";?>
		<p><?php echo "<b>".__("Site ID:", 'clicky-analytics')." </b>"; ?><input type="text" name="ca_siteid" value="<?php echo $siteid; ?>" size="60"></p>  
		<p><?php echo "<b>".__("Site Key:", 'clicky-analytics')." </b>"; ?><input type="text" name="ca_sitekey" value="<?php echo $sitekey; ?>" size="58"></p>  
		</p>  
		<?php echo "<h3>" . __( 'Access Level', 'clicky-analytics' ). "</h3>";?>
		<p><?php _e("View Access Level: ", 'clicky-analytics' ); ?>
		<select id="ca_access" name="ca_access">
			<option value="manage_options" <?php if (($dashaccess=="manage_options") OR (!$dashaccess)) echo "selected='yes'"; echo ">".__("Administrators", 'clicky-analytics');?></option>
			<option value="edit_pages" <?php if ($dashaccess=="edit_pages") echo "selected='yes'"; echo ">".__("Editors", 'clicky-analytics');?></option>
			<option value="publish_posts" <?php if ($dashaccess=="publish_posts") echo "selected='yes'"; echo ">".__("Authors", 'clicky-analytics');?></option>
			<option value="edit_posts" <?php if ($dashaccess=="edit_posts") echo "selected='yes'"; echo ">".__("Contributors", 'clicky-analytics');?></option>
		</select></p>
		<?php echo "<h3>" . __( 'Frontend Settings', 'clicky-analytics' ). "</h3>";?>
		<p><input name="ca_frontend" type="checkbox" id="ca_frontend" value="1"<?php if (get_option('ca_frontend')) echo " checked='checked'"; ?>  /><?php _e(" show page visits and top searches in frontend (after each article)", 'clicky-analytics' ); ?></p>
		<?php echo "<h3>" . __( 'Backend Settings', 'clicky-analytics' ). "</h3>";?>
		<p><input name="ca_pgd" type="checkbox" id="ca_pgd" value="1"<?php if (get_option('ca_pgd')) echo " checked='checked'"; ?>  /><?php _e(" show top pages", 'clicky-analytics' ); ?></p>
		<p><input name="ca_rd" type="checkbox" id="ca_rd" value="1"<?php if (get_option('ca_rd')) echo " checked='checked'"; ?>  /><?php _e(" show top referrers", 'clicky-analytics' ); ?></p>		
		<p><input name="ca_sd" type="checkbox" id="ca_sd" value="1"<?php if (get_option('ca_sd')) echo " checked='checked'"; ?>  /><?php _e(" show top searches", 'clicky-analytics' ); ?></p>		
		<?php echo "<h3>" . __( 'Cache Settings', 'clicky-analytics' ). "</h3>";?>
		<p><?php _e("Cache Time: ", 'clicky-analytics' ); ?>
		<select id="ca_cachetime" name="ca_cachetime">
			<option value="900" <?php if ($ca_cachetime=="900") echo "selected='yes'"; echo ">".__("15 minutes", 'clicky-analytics');?></option>
			<option value="1800" <?php if (($ca_cachetime=="1800") OR (!$ca_cachetime)) echo "selected='yes'"; echo ">".__("30 minutes", 'clicky-analytics');?></option>
			<option value="3600" <?php if ($ca_cachetime=="3600") echo "selected='yes'"; echo ">".__("1 hour", 'clicky-analytics');?></option>
		</select></p>

		<?php echo "<h3>" . __( 'Clicky Analytics Tracking', 'clicky-analytics' ). "</h3>";?>

		<p><?php _e("Enable Tracking: ", 'clicky-analytics' ); ?>
		<select id="ca_tracking" name="ca_tracking">
			<option value="1" <?php if (($ca_tracking=="1") OR (!$ca_tracking)) echo "selected='yes'"; echo ">".__("Enabled", 'clicky-analytics');?></option>
			<option value="2" <?php if ($ca_tracking=="2") echo "selected='yes'"; echo ">".__("Disabled", 'clicky-analytics');?></option>
		</select></p>

		<p><input name="ca_track_username" type="checkbox" id="ca_track_username" value="1"<?php if (get_option('ca_track_username')) echo " checked='checked'"; ?>  /><?php _e(" track usernames", 'clicky-analytics' ); ?></p>				
		<p><input name="ca_track_email" type="checkbox" id="ca_track_email" value="1"<?php if (get_option('ca_track_email')) echo " checked='checked'"; ?>  /><?php _e(" track emails", 'clicky-analytics' ); ?></p>
		<p><input name="ca_track_youtube" type="checkbox" id="ca_track_youtube" value="1"<?php if (get_option('ca_track_youtube')) echo " checked='checked'"; ?>  /><?php _e(" track Youtube videos", 'clicky-analytics' ); ?></p>
		<p><input name="ca_track_html5" type="checkbox" id="ca_track_html5" value="1"<?php if (get_option('ca_track_html5')) echo " checked='checked'"; ?>  /><?php _e(" track HTML5 videos", 'clicky-analytics' ); ?></p>
		<p class="submit">  
        <input type="submit" name="Submit" class="button button-primary" value="<?php _e('Update Options', 'clicky-analytics' ) ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" name="Clear" class="button button-primary" value="<?php _e('Clear Cache', 'clicky-analytics' ) ?>" />
        </p>  
    </form>  
</div>
<div class="note" style="float:<?php echo $float_note; ?>;text-align:<?php echo $float_main; ?>;"> 
		<center>
			<h3><?php _e("Setup Tutorial",'clicky-analytics') ?></h3>
			<a href="http://deconf.com/clicky-analytics-dashboard-wordpress/" target="_blank"><img src="../wp-content/plugins/clicky-analytics/img/video-tutorial.png" width="95%" /></a>
		</center>
		<center>
			<br /><h3><?php _e("Support Links",'clicky-analytics') ?></h3>
		</center>			
		<ul>
			<li><a href="http://deconf.com/clicky-analytics-dashboard-wordpress/" target="_blank"><?php _e("Clicky Analytics Plugin Official Page",'clicky-analytics') ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/clicky-analytics" target="_blank"><?php _e("Clicky Analytics Plugin Wordpress Support",'clicky-analytics') ?></a></li>
			<li><a href="http://forum.deconf.com/wordpress-plugins-f182/" target="_blank"><?php _e("Clicky Analytics Plugin on Deconf Forum",'clicky-analytics') ?></a></li>			
		</ul>
		<center>
			<br /><h3><?php _e("Useful Plugins",'clicky-analytics') ?></h3>
		</center>			
		<ul>
			<li><a href="http://deconf.com/youtube-analytics-dashboard-wordpress/" target="_blank"><?php _e("YouTube Analytics Dashboard",'clicky-analytics') ?></a></li>
			<li><a href="http://deconf.com/earnings-dashboard-google-adsense-wordpress/" target="_blank"><?php _e("Earnings Dashboard for Google Adsense™",'clicky-analytics') ?></a></li>
			<li><a href="http://deconf.com/google-analytics-dashboard-wordpress/" target="_blank"><?php _e("Google Analytics Dashboard",'clicky-analytics') ?></a></li>						
			<li><a href="http://wordpress.org/extend/plugins/follow-us-box/" target="_blank"><?php _e("Follow Us Box",'clicky-analytics') ?></a></li>			
		</ul>			
</div>
</div>