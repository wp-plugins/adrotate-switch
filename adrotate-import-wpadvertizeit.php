<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_wpadvertizeit() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wpadvertizeit')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_wpadvertizeit_schedules'])) ? 1 : 0;

		$data = get_option('wpai_settings');

		foreach($data['blocks'] as $key => $value) {
			$new_id = $key + 1;
			// Format advert
			$advertdata['title'] = '[Imported] WP Advertize It banner '.$new_id;
			$advertdata['bannercode'] = esc_attr($value);
			$advertdata['thetime'] = $now;
			$advertdata['updated'] = $now;
			$advertdata['author'] = $current_user->user_login;
			$advertdata['imagetype'] = '';
			$advertdata['image'] = '';
			$advertdata['link'] = ''; // Deprecated
			$advertdata['tracker'] = 'N';
			$advertdata['mobile'] = 'N';
			$advertdata['tablet'] = 'N';
			$advertdata['responsive'] = 'N';
			$advertdata['type'] = 'active';
			$advertdata['weight'] = 6;
			$advertdata['sortorder'] = 0;
			$advertdata['budget'] = 0;
			$advertdata['crate'] = 0;
			$advertdata['irate'] = 0;
			$advertdata['cities'] = serialize(array());
			$advertdata['countries'] = serialize(array());
	
			$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
		    $ad_id = $wpdb->insert_id;
			$ads2schedule[] = $ad_id;
			
			unset($advertdata, $ad_id);
		}
	
		if($include_schedules == 1) {
			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Ad Injection schedule', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'dayimpressions' => 0));
			$schedule_id = $wpdb->insert_id;

			foreach($ads2schedule as $key => $ad_id) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
			}
		}
		unset($ads2schedule, $ad_id, $schedule_id);
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>