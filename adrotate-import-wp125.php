<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2014 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a trademark (pending registration) of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_wp125() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wp125')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_wp125_schedules'])) ? 1 : 0;
		$include_stats = (isset($_POST['adrotateswitch_import_wp125_stats'])) ? 1 : 0;
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mban_banner` ORDER BY `id` ASC;");
		foreach($adverts as $advert) {
			// Use jQuery Clicktracker?
			if($adrotate_config['clicktracking'] == 'Y') {
				$target_url = esc_attr($advert->target);
				$track_link = '';
			} else {
				$target_url = '%link%';
				$track_link = esc_attr($advert->target);
			}				
			$adcode = '<a href="'.$target_url.'"'.$new_window.'><img src="%image%" /></a>';
	
			// Enabled or Disabled
			if($advert->status == 1) {
				$status = 'active';
			} else {
				$status = 'disabled';
			}

			// Format advert
			$advertdata['title'] = '[Imported] '.esc_attr($advert->name);
			$advertdata['bannercode'] = $adcode;
			$advertdata['thetime'] = $now;
			$advertdata['updated'] = $now;
			$advertdata['author'] = $current_user->user_login;
			$advertdata['imagetype'] = 'field';
			$advertdata['image'] = esc_attr($advert->image_url);
			$advertdata['link'] = $track_link;
			$advertdata['tracker'] = 'Y';
			$advertdata['responsive'] = 'N';
			$advertdata['type'] = $status;
			$advertdata['weight'] = 6;
			$advertdata['sortorder'] = 0;
			$advertdata['cbudget'] = 0;
			$advertdata['ibudget'] = 0;
			$advertdata['crate'] = 0;
			$advertdata['irate'] = 0;
			$advertdata['cities'] = array();
			$advertdata['countries'] = array();
	
			$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
		    $ad_id = $wpdb->insert_id;
	
			if($include_schedules == 1) {
				list($smonth, $sday, $syear) = explode('/', esc_attr($advert->start_date));
				$start_date = gmmktime(0, 0, 0, $smonth, $sday, $syear);
				list($emonth, $eday, $eyear) = explode('/', esc_attr($advert->end_date));
				$end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);
				
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'hourimpressions' => 0));
				$schedule_id = $wpdb->insert_id;
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'block' => 0, 'user' => 0, 'schedule' => $schedule_id));
			}
	
			if($include_stats == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_stats', array('ad' => $ad_id, 'group' => 0, 'block' => 0, 'thetime' => $now, 'clicks' => esc_attr($advert->clicks), 'impressions' => 0));
			}
			
			unset($advertdata, $adcode, $new_window, $track_link, $target_url, $status, $ad_id, $schedule_id);
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>