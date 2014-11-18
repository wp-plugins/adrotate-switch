<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2014 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a trademark (pending registration) of Arnan de Gans.

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

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wpadvertizeit')) {
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
			$advertdata['link'] = '';
			$advertdata['tracker'] = 'N';
			$advertdata['responsive'] = 'N';
			$advertdata['type'] = 'active';
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

			$end_date = $now + 2592000;

			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'hourimpressions' => 0));
			$schedule_id = $wpdb->insert_id;
			$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'block' => 0, 'user' => 0, 'schedule' => $schedule_id));
			
			unset($advertdata, $ad_id, $schedule_id);
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>