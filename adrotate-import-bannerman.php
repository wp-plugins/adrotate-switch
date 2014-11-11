<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2014 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a trademark (pending registration) of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_bannerman() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_bannerman')) {
		$data = maybe_unserialize(get_option('bannerman'));

		// Convert interfal
		if($data['refresh'] == 30) $data['refresh'] = 35;
		if($data['refresh'] == 40) $data['refresh'] = 45;
		if($data['refresh'] == 50) $data['refresh'] = 45;

		// Determine loation
		if($data['display'] == 'none') $data['display'] = 0;
		if($data['display'] == 'top') $data['display'] = 1;
		if($data['display'] == 'bottom') $data['display'] = 2;

		// List all pages
		$pages = get_pages(array('sort_column' => 'ID', 'sort_order' => 'asc'));
		$page_list = '';	
		if(!empty($pages)) {
			foreach($pages as $page) {
				$page_list .= $page_list.','.$page->ID;
			}
		}

		$groupdata['name'] = '[Imported] BannerMan';
		$groupdata['modus'] = ($data['refresh'] > 0) ? 1 : 0;
		$groupdata['fallback'] = 0;
		$groupdata['sortorder'] = 0;
		$groupdata['cat'] = '';
		$groupdata['cat_loc'] = '';
		$groupdata['cat_par'] = '';
		$groupdata['page'] = $page_list;
		$groupdata['page_loc'] = $data['display'];
		$groupdata['page_par'] = '';
		$groupdata['geo'] = 0;
		$groupdata['wrapper_before'] = '<center>';
		$groupdata['wrapper_after'] = '</center>';
		$groupdata['gridrows'] = 2;
		$groupdata['gridcolumns'] = 2;
		$groupdata['admargin'] = 0;
		$groupdata['admargin_bottom'] = 0;
		$groupdata['admargin_left'] = 0;
		$groupdata['admargin_right'] = 0;
		$groupdata['adwidth'] = 125;
		$groupdata['adheight'] = 125;
		$groupdata['adspeed'] = ($data['refresh'] > 0) ? $data['refresh']*1000 : 6000;

		$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
		$group_id = $wpdb->insert_id;

		foreach($data['banners'] as $key => $value) {
			$new_id = $key + 1;
			// Format advert
			$advertdata['title'] = '[Imported] BannerMan banner '.$new_id;
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

			$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group_id, 'block' => 0, 'user' => 0, 'schedule' => 0));
			
			unset($advertdata, $ad_id, $groupdata, $group_id, $schedule_id);
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>