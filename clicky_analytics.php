<?php
/* 
Plugin Name: Clicky Analytics
Plugin URI: http://www.deconf.com
Description: This plugin will display Clicky Analytics data and statistics into Admin Dashboard. 
Author: Deconf.com
Version: 1.2.1
Author URI: http://www.deconf.com
*/  

function ca_admin() {  
    include('clicky_analytics_admin.php');  
} 
	
function ca_admin_actions() {
	if (current_user_can('manage_options')) {  
		add_options_page(__("Clicky Analytics",'clicky-analytics'), __("Clicky Analytics",'clicky-analytics'), "manage_options", "Clicky_Analytics_Dashboard", "ca_admin");
	}
}  

function ca_init() {
  	load_plugin_textdomain( 'clicky-analytics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function ca_admin_enqueue_scripts() {
	wp_register_style( 'clicky_analytics', plugins_url('clicky_analytics.css', __FILE__) );
	wp_enqueue_style( 'clicky_analytics' );
}

function ca_enqueue_scripts() {
	if (get_option('ca_track_youtube') AND !wp_script_is('jquery')) {
		wp_enqueue_script('jquery');
	}	
}

function ca_setup() {
	if (current_user_can(get_option('ca_access'))) {
		wp_add_dashboard_widget(
			'clicky-analytics-widget',
			__("Clicky Analytics",'clicky-analytics'),
			'ca_content',
			$control_callback = null
		);
	}
}

function ca_dash_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=Clicky_Analytics_Dashboard">'.__("Settings",'ca-dash').'</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function ca_tracking($head) {

	$ca_traking=get_option('ca_tracking');

	if ($ca_traking){
		require_once 'functions.php';
		echo ca_tracking_code();
	}
	
}

$plugin = plugin_basename(__FILE__);
add_filter('the_content', 'ca_front_content');  
add_action('wp_dashboard_setup', 'ca_setup');
add_action('admin_menu', 'ca_admin_actions'); 
add_action('admin_enqueue_scripts', 'ca_admin_enqueue_scripts');
add_action('wp_enqueue_scripts', 'ca_enqueue_scripts');
add_action('plugins_loaded', 'ca_init');
add_action('wp_footer', 'ca_tracking');
add_filter("plugin_action_links_$plugin", 'ca_dash_settings_link' );

function ca_front_content($content) {
	global $post;
	if (!current_user_can(get_option('ca_access')) OR !get_option('ca_frontend')) {
		return $content;
	}

	if(!is_feed() && !is_home() && !is_front_page()) {

		require_once 'functions.php';

		$api_url = "http://api.clicky.com/api/stats/4?";
		$siteid = get_option('ca_siteid');
		$sitekey = get_option('ca_sitekey');
		
		if(!get_option('ca_cachetime')){
			update_option('ca_cachetime', "900");	
		}

		$content.='<style>
		#ca_sdata td{
			line-height:1.5em;
			padding:2px;
			font-size:1em;
		}
		#ca_sdata{
			line-height:10px;
		}
		</style>';
		
		$page_url = $_SERVER["REQUEST_URI"];

		$post_id = $post->ID;
		$metric = 'type=pages';
		$from = "date=last-30-days"; 
		try{
			$serial='clicky_qr21'.$post_id;
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&daily=1"."&filter=".urlencode($page_url)."&output=php";
				//echo $url;
				$result = unserialize(file_get_contents_clicky($url));
				set_transient( $serial, $result, get_option('ca_cachetime') );
				//echo "QR21-Refresh";
			}else{
				$result = $transient;
				//echo "QR21-Cache";
			}	
		}  
			catch(exception $e) {
			return $content; 
		}
		$i=0;
		foreach( $result as $item ) {
			foreach( $item as $date => $item1 ) {
				if (!$item1) {
					return $content;
				}			
				$goores[$i][0]=$date;
				foreach( $item1 as $item2) {
					$goores[$i][1]=$item2['value'];
				}	
				$i++;
			}
		}
		$j=0;
		$ca_statsdata="";
		for ($j=$i-1;$j>=0;$j--){
		
			$ca_statsdata.="['".$goores[$j][0]."',".$goores[$j][1]."],";

		}
		
		$ca_statsdata = rtrim($ca_statsdata,',');
		
		$code='<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  google.load("visualization", "1", {packages:["corechart"]});

		  google.setOnLoadCallback(ca_callback);

		  function ca_callback(){
				ca_drawstats();
				if(typeof ca_drawsd == "function"){
					ca_drawsd();
				}				
		  }			  
		  
		  function ca_drawstats() {
			var data = google.visualization.arrayToDataTable(['."
			  ['".__("Date", 'clicky-analytics')."', 'Visitors'],"
			  .$ca_statsdata.
			"  
			]);

			var options = {
			  legend: {position: 'none'},
			  colors:['darkorange','#004411'],		  
			  pointSize: 3,
			  title: 'Visitors',
			  chartArea: {width: '80%', height: '50%'},
			  hAxis: { title: '".__("Date", 'clicky-analytics')."',  titleTextStyle: {color: 'black'}, showTextEvery: 5}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('ca_div'));
			chart.draw(data, options);
			
		  }
		";

		$metric = 'type=segmentation&segments=searches';
		$from = "date=last-30-days"; 
		try{
			$serial='clicky_qr22'.$post_id;
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&limit=30"."&href=".urlencode($page_url)."&output=php";
				//echo $url;
				$result = unserialize(file_get_contents_clicky($url));
				set_transient( $serial, $result, get_option('ca_cachetime') );
				//echo "QR21-Refresh";
			}else{
				$result = $transient;
				//echo "QR21-Cache";
			}	
		}  
			catch(exception $e) {
			return $content; 
		}
		$i=0;
		foreach( $result as $item ) {
			foreach( $item as $date => $item1 ) {
				if (!$item1) {
					return $content;
				}
				foreach( $item1 as $item2) {
					$truesearch=str_replace(array('[secure search]'),'',$item2['title']);
					if ($truesearch){	
						$goores[$i][0]=ca_validation($item2['title']);
						$goores[$i][1]=ca_validation($item2['value']);
						$i++;
					}	
				}	
			}
		}
		
		$j=0;
		$ca_organicdata="";
		for ($j=0;$j<=$i-1;$j++){
		
			$ca_organicdata.="['".$goores[$j][0]."',".$goores[$j][1]."],";

		}

		$ca_organicdata = rtrim($ca_organicdata,',');		
		
		if ($ca_organicdata){
			$code.='
					google.load("visualization", "1", {packages:["table"]})
					function ca_drawsd() {
					
					var datas = google.visualization.arrayToDataTable(['."
					  ['".__("Top Searches",'clicky-analytics')."', '".__("Visits",'clicky-analytics')."'],"
					  .$ca_organicdata.
					"  
					]);
					
					var options = {
						page: 'enable',
						pageSize: 6,
						width: '100%',
					};        
					
					var chart = new google.visualization.Table(document.getElementById('ca_sdata'));
					chart.draw(datas, options);
					
				  }";
		}
		$code.="</script>";		
		
		$content .= $code . '<div id="ca_div"></div><div id="ca_sdata" ></div>';

	}
	return $content;
}

function ca_content() {
	
	require_once 'functions.php';
	
	if(!get_option('ca_cachetime')){
		update_option('ca_cachetime', "900");	
	}

	$api_url = "http://api.clicky.com/api/stats/4?";
	$siteid = get_option('ca_siteid');
	$sitekey = get_option('ca_sitekey');
	
	if ((!get_option('ca_siteid')) OR (!get_option('ca_sitekey'))){
		
		return "<div style='padding:20px;'>".__("Site ID or Site Key is missing",'clicky-analytics')."</div>";
		
	}	

	if (isset($_REQUEST['ca_query'])){
		$ca_query = $_REQUEST['ca_query'];
	}else{
		$ca_query = "visits";
	}	
	
	if (isset($_REQUEST['ca_period'])){
		$ca_period = $_REQUEST['ca_period'];
	}else{
		$ca_period = "last-30-days";
	}
	
	$from = "date=".$ca_period; 

	switch ($ca_query){

		case 'actions'	:	$title=__("Actions", 'clicky-analytics'); 
							$metric = "type=actions";
							break;
		case 'traffic-sources'	:	$title=__("Searches", 'clicky-analytics'); 
									$metric = "type=traffic-sources";
									break;

		case 'time-average'	:	$title=__("Time Average", 'clicky-analytics'); 
								$metric = "type=time-average";
								break;

		case 'bounce-rate'	:	$title=__("Bounce Rate", 'clicky-analytics'); 
								$metric = "type=bounce-rate";
								break;	
		
		default	:	$title=__("Visitors", 'clicky-analytics'); 
					$metric = "type=visitors";
	}
	
	try{
		$serial='clicky_qr1'.str_replace(array(',','-',date('Y')),"",$from.$metric);
		$transient = get_transient($serial);
		if ( empty( $transient ) ){
			$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metric."&daily=1"."&output=php";
			//echo $url;
			$result = unserialize(file_get_contents_clicky($url));
			set_transient( $serial, $result, get_option('ca_cachetime') );
			//echo "QR1-Refresh";
		}else{
			$result = $transient;
			//echo "QR1-Cache";
		}	
	}  
		catch(exception $e) {
		return "<br />ERROR LOG:<br /><br />".$e; 
	}
	$i=0;
	
	if (!is_array($result)){
		echo __("<br />ERROR LOG: Check your Site ID and Site Key!<br /><br />",'clicky-analytics');
		ca_clear_cache();
		return;
	}
	
	foreach( $result as $item ) {
		foreach( $item as $date => $item1 ) {
			if (!$item1) {
				return;
			}
			$goores[$i][0]=$date;
			foreach( $item1 as $item2) {
				if (isset($item2['title']) AND $item2['title']=="Searches")
					$goores[$i][1]=$item2['value'];
				else if (!isset($item2['title']))
					$goores[$i][1]=$item2['value'];	
			}	
			$i++;
		}
	}
	$j=0;
	$chart1_data="";
	for ($j=$i-1;$j>=0;$j--){
	
		$chart1_data.="['".$goores[$j][0]."',".$goores[$j][1]."],";

	}

	$chart1_data = rtrim($chart1_data,',');	
	
	$metrics = 'type=visitors,actions,visitors-online,traffic-sources,time-average,bounce-rate';
	
	try{
		$serial='clicky_qr2'.str_replace(array(',','-',date('Y')),"",$from);
		$transient = get_transient($serial);
		if ( empty( $transient ) ){
			$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metrics."&output=php";
			//echo $url;
			$result = unserialize(file_get_contents_clicky($url));
			set_transient( $serial, $result, get_option('ca_cachetime') );
			//echo "QR2-Refresh";
		}else{
			$result = $transient;
			//echo "QR2-Cache";
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
			$goores[$i][0]=$date;
			foreach( $item1 as $item2) {
				if (isset($item2['title']) AND $item2['title']=="Searches")
					$goores[$i][1]=$item2['value'];
				else if (!isset($item2['title']))
					$goores[$i][1]=$item2['value'];				
			}	
			$i++;
		}
	}

    $code='<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(ca_callback);

	  function ca_callback(){
			ca_drawstats();
			if(typeof ca_drawmap == "function"){
				ca_drawmap();
			}
			if(typeof ca_toppages == "function"){
				ca_toppages();
			}			
			if(typeof ca_topreferrers== "function"){
				ca_topreferrers();
			}
			if(typeof ca_searches == "function"){
				ca_searches();
			}
	  }		  
	  
      function ca_drawstats() {
        var data = google.visualization.arrayToDataTable(['."
          ['".__("Date", 'clicky-analytics')."', '".$title."'],"
		  .$chart1_data.
		"  
        ]);

        var options = {
		  legend: {position: 'none'},
		  colors:['darkorange','#004411'],		  
		  pointSize: 3,
          title: '".$title."',
		  chartArea: {width: '80%', height: '50%'},
          hAxis: { title: '".__("Date", 'clicky-analytics')."',  titleTextStyle: {color: 'black'}, showTextEvery: 5}
		};

        var chart = new google.visualization.AreaChart(document.getElementById('ca_div'));
		chart.draw(data, options);
		
      }";
	 
	if (get_option('ca_pgd')){
		$ca_toppages=ca_top_pages($api_url, $siteid, $sitekey, $from);
		if ($ca_toppages){
			$code.='
					google.load("visualization", "1", {packages:["table"]})
					function ca_toppages() {
					
					var datas = google.visualization.arrayToDataTable(['."
					  ['".__("Top Pages",'clicky-analytics')."', '".__("Visits",'clicky-analytics')."'],"
					  .$ca_toppages.
					"  
					]);
					
					var options = {
						page: 'enable',
						pageSize: 6,
						width: '100%',
					};        
					
					var chart = new google.visualization.Table(document.getElementById('ca_toppages'));
					chart.draw(datas, options);
					
				  }";
		}
	}

	if (get_option('ca_rd')){
		$ca_referrers=ca_top_referrers($api_url, $siteid, $sitekey, $from);
		//print_r($ca_referrers);
		if ($ca_referrers){
			$code.='
					google.load("visualization", "1", {packages:["table"]})
					function ca_topreferrers() {
					
					var datas = google.visualization.arrayToDataTable(['."
					  ['".__("Top Referrers",'clicky-analytics')."', '".__("Visits",'clicky-analytics')."'],"
					  .$ca_referrers.
					"  
					]);
					
					var options = {
						page: 'enable',
						pageSize: 6,
						width: '100%',
					};        
					
					var chart = new google.visualization.Table(document.getElementById('ca_referrers'));
					chart.draw(datas, options);
					
				  }";
		}
	}	

	if (get_option('ca_sd')){
		$ca_searches=ca_top_searches($api_url, $siteid, $sitekey, $from);
		//print_r($ca_searches);
		if ($ca_searches){
			$code.='
					google.load("visualization", "1", {packages:["table"]})
					function ca_searches() {
					
					var datas = google.visualization.arrayToDataTable(['."
					  ['".__("Top Searches",'clicky-analytics')."', '".__("Visits",'clicky-analytics')."'],"
					  .$ca_searches.
					"  
					]);
					
					var options = {
						page: 'enable',
						pageSize: 6,
						width: '100%',
					};        
					
					var chart = new google.visualization.Table(document.getElementById('ca_searches'));
					chart.draw(datas, options);
					
				  }";
		}
	}	
	
	
    $code.="</script>";
	
	$code.='
	<div id="clicky-dash">
	<center>
		<div id="ca_buttons_div">
		<center>
			<input class="clickybutton" type="button" value="'.__("Today", 'clicky-analytics').'" onClick="window.location=\'?ca_period=today&ca_query='.$ca_query.'\'" />
			<input class="clickybutton" type="button" value="'.__("Yesterday", 'clicky-analytics').'" onClick="window.location=\'?ca_period=yesterday&ca_query='.$ca_query.'\'" />
			<input class="clickybutton" type="button" value="'.__("Last 7 Days", 'clicky-analytics').'" onClick="window.location=\'?ca_period=last-7-days&ca_query='.$ca_query.'\'" />
			<input class="clickybutton" type="button" value="'.__("Last 14 Days", 'clicky-analytics').'" onClick="window.location=\'?ca_period=last-14-days&ca_query='.$ca_query.'\'" />
			<input class="clickybutton" type="button" value="'.__("Last 30 Days", 'clicky-analytics').'" onClick="window.location=\'?ca_period=last-30-days&ca_query='.$ca_query.'\'" />
		</center>
		</div>
		
		<div id="ca_div"></div>
		
		<div id="ca_details_div">
			<center>
			<table class="clickytable" cellpadding="4">
			<tr>
			<td width="24%">'.__("Online", 'clicky-analytics').':</td>
			<td width="12%" id="clickyonline" class="clickyvalue">'.$goores[2][1].'</td>
			<td width="24%">'.__("Visitors", 'clicky-analytics').':</td>
			<td width="12%" class="clickyvalue"><a href="?ca_query=visitors&ca_period='.$ca_period.'" class="clickytable">'.$goores[0][1].'</a></td>
			<td width="24%">'.__("Actions", 'clicky-analytics').':</td>
			<td width="12%" class="clickyvalue"><a href="?ca_query=actions&ca_period='.$ca_period.'" class="clickytable">'.$goores[1][1].'</a></td>
			</tr>
			<tr>
			<td width="24%">'.__("Searches", 'clicky-analytics').':</td>
			<td width="12%" class="clickyvalue"><a href="?ca_query=traffic-sources&ca_period='.$ca_period.'" class="clickytable">'.$goores[3][1].'</a></td>
			<td width="24%">'.__("Time AVG", 'clicky-analytics').':</td>
			<td width="12%" class="clickyvalue"><a href="?ca_query=time-average&ca_period='.$ca_period.'" class="clickytable">'.$goores[4][1].'</a></td>
			<td width="24%">'.__("Bounce Rate", 'clicky-analytics').':</td>
			<td width="12%" class="clickyvalue"><a href="?ca_query=bounce-rate&ca_period='.$ca_period.'" class="clickytable">'.$goores[5][1].'</a></td>
			</tr>
			</table>
			</center>		
		</div>
	</center>		
	</div>';
    $metrics = 'type=visitors-online';
	$url = $api_url."site_id=".$siteid."&sitekey=".$sitekey."&".$from."&".$metrics."&output=json";
	$code.='

	<script type="text/javascript">

	 function online_refresh(){
		jQuery.getJSON("'.$url.'&json_callback=?", function(data){
			if (data[0]["dates"][0]["items"][0]["value"]!==document.getElementById("clickyonline").innerHTML){
				jQuery("#clickyonline").fadeOut("slow");
				jQuery("#clickyonline").fadeOut(500);
				jQuery("#clickyonline").fadeOut("slow", function() {
					document.getElementById("clickyonline").innerHTML = data[0]["dates"][0]["items"][0]["value"];
				});
				jQuery("#clickyonline").fadeIn("slow");
				jQuery("#clickyonline").fadeIn(500);
				jQuery("#clickyonline").fadeIn("slow", function() {
				});
			};
		});
   };
   setInterval(online_refresh, 10000);
   </script>';

	$code.='</center>';
	if (get_option('ca_pgd'))
		$code .= '<br /><br /><div id="ca_toppages"></div>';
	if (get_option('ca_rd'))	
		$code .= '<div id="ca_referrers"></div>';
	if (get_option('ca_sd'))	
		$code .= '<div id="ca_searches"></div>';
	
	echo $code; 
   

}	
?>