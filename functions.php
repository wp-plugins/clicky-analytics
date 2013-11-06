<?php

	function file_get_contents_clicky($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
	
	function ca_tracking_code(){
		global $current_user;
		
		$custom_tracking="";

		get_currentuserinfo();
		
		if ($current_user->user_login){
		
			if ((get_option('ca_track_username')) AND ((get_option('ca_track_email')))){
		
			$custom_tracking="<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    username: '".$current_user->user_login."',
    email: '". $current_user->user_email."'
  };
</script>";

			}
			else if (get_option('ca_track_username')){
		
			$custom_tracking="<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    username: '".$current_user->user_login."'
  };
</script>";
			}
			else if (get_option('ca_track_email')){
			$custom_tracking="<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    email: '". $current_user->user_email."'
  };
</script>";
			}
		}
		
		$main_tracking="<script type=\"text/javascript\">
var clicky_site_ids = clicky_site_ids || [];
clicky_site_ids.push(".get_option('ca_siteid').");
(function() {
  var s = document.createElement('script');
  s.type = 'text/javascript';
  s.async = true;
  s.src = '//static.getclicky.com/js';
  ( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
})();
</script>";		
		$video_tracking="";
		if (get_option('ca_track_youtube')){
			$video_tracking.="<script src='//static.getclicky.com/inc/javascript/video/youtube.js'></script>";
		}
		if (get_option('ca_track_html5')){
			$video_tracking.='<script src="//static.getclicky.com/inc/javascript/video/html.js"></script>';
		}		
		$tracking=$custom_tracking.$main_tracking.$video_tracking;	
		
		return $tracking;	

	}
	
	function ca_validation($item){
	
		return str_replace(array("'",'"'),'',$item);
	
	}
	
	function ca_clear_cache(){
		global $wpdb;
		$sqlquery=$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_clicky%%'");
		$sqlquery=$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_clicky%%'");
	}
	
	function ca_safe_get($key) {
		if (array_key_exists($key, $_POST)) {
			return $_POST[$key];
		}
		return false;
	}

// Get Top Pages
	function ca_top_pages($api_url, $siteid, $sitekey, $from){

		$metric = 'type=pages';
		try{
			$serial='clicky_qr3'.$from;
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&limit=30&output=php";
				//echo $url;
				$result = unserialize(file_get_contents($url));
				set_transient( $serial, $result, get_option('ca_cachetime') );
				//echo "QR3-Refresh";
			}else{
				$result = $transient;
				//echo "QR3-Cache";
			}	
		}  
			catch(exception $e) {
			return "<br />ERROR LOG:<br /><br />".$e; 
		}
		$i=0;
		foreach( $result as $item ) {
			foreach( $item as $date => $item1 ) {
				if (!$item1) {
					return;
				}			
				foreach( $item1 as $item2) {
					$goores[$i][0]=ca_validation($item2['title']);
					$goores[$i][1]=ca_validation($item2['value']);
					$i++;
						
				}	
			}
		}
		$j=0;
		$ca_statsdata="";
		for ($j=0;$j<=$i-1;$j++){
		
			$ca_statsdata.="['".$goores[$j][0]."',".$goores[$j][1]."],";

		}
		return rtrim($ca_statsdata,',');
	}
	
// Get Top referrers
	function ca_top_referrers($api_url, $siteid, $sitekey, $from){

		$metric = 'type=links-domains';
		try{
			$serial='clicky_qr4'.$from;
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&limit=30&output=php";
				//echo $url;
				$result = unserialize(file_get_contents($url));
				set_transient( $serial, $result, get_option('ca_cachetime') );
				//echo "QR4-Refresh";
			}else{
				$result = $transient;
				//echo "QR4-Cache";
			}	
		}  
			catch(exception $e) {
			return "<br />ERROR LOG:<br /><br />".$e; 
		}
		$i=0;
		foreach( $result as $item ) {
			foreach( $item as $date => $item1 ) {
				if (!$item1) {
					return;
				}			
				foreach( $item1 as $item2) {
					$goores[$i][0]=ca_validation($item2['title']);
					$goores[$i][1]=ca_validation($item2['value']);
					$i++;
						
				}	
			}
		}
		$j=0;
		$ca_statsdata="";
		for ($j=0;$j<=$i-1;$j++){
		
			$ca_statsdata.="['".$goores[$j][0]."',".$goores[$j][1]."],";

		}

		return rtrim($ca_statsdata,',');		
	}
// Get Top searches
	function ca_top_searches($api_url, $siteid, $sitekey, $from){

		$metric = 'type=searches';
		try{
			$serial='clicky_qr5'.$from;
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&limit=30&output=php";
				//echo $url;
				$result = unserialize(file_get_contents($url));
				set_transient( $serial, $result, get_option('ca_cachetime') );
				//echo "QR4-Refresh";
			}else{
				$result = $transient;
				//echo "QR4-Cache";
			}	
		}  
			catch(exception $e) {
			return "<br />ERROR LOG:<br /><br />".$e; 
		}
		$i=0;
		foreach( $result as $item ) {
			foreach( $item as $date => $item1 ) {
				if (!$item1) {
					return;
				}			
				foreach( $item1 as $item2) {
					$goores[$i][0]=ca_validation($item2['title']);
					$goores[$i][1]=ca_validation($item2['value']);
					$i++;
						
				}	
			}
		}
		$j=0;
		$ca_statsdata="";
		for ($j=0;$j<=$i-1;$j++){
		
			$ca_statsdata.="['".$goores[$j][0]."',".$goores[$j][1]."],";

		}
		return rtrim($ca_statsdata,',');
	}
?>