<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_mba() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_mba')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_mba_schedules'])) ? 1 : 0;
		$include_groups = (isset($_POST['adrotateswitch_import_mba_groups'])) ? 1 : 0;
		$include_stats = (isset($_POST['adrotateswitch_import_mba_stats'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$groups = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mban_zone` ORDER BY `id` ASC;");
			foreach($groups as $group) {
				$groupdata['name'] = '[Imported] '.esc_attr($group->name);
				$groupdata['modus'] = 0;
				$groupdata['fallback'] = 0;
				$groupdata['sortorder'] = 0;
				$groupdata['cat'] = '';
				$groupdata['cat_loc'] = '';
				$groupdata['cat_par'] = '';
				$groupdata['page'] = '';
				$groupdata['page_loc'] = '';
				$groupdata['page_par'] = '';
				$groupdata['geo'] = 0;
				$groupdata['wrapper_before'] = '';
				$groupdata['wrapper_after'] = '';
				$groupdata['align'] = 1;
				$groupdata['gridrows'] = 2;
				$groupdata['gridcolumns'] = 2;
				$groupdata['admargin'] = 0;
				$groupdata['admargin_bottom'] = 0;
				$groupdata['admargin_left'] = 0;
				$groupdata['admargin_right'] = 0;
				$groupdata['adwidth'] = 125;
				$groupdata['adheight'] = 125;
				$groupdata['adspeed'] = 6000;

				$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
				$group2zone[esc_attr($group->id)] = $wpdb->insert_id;
			}
		}
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mban_banner` ORDER BY `id` ASC;");
		foreach($adverts as $advert) {
			if(strlen($advert->text_ad_code) > 0) {
				$adcode = esc_attr($advert->text_ad_code);
			} else {
				// Open in new window?
				if($advert->in_new_win == 1) {
					$new_window = ' target="_blank"';
				} else {
					$new_window = '';
				}
				
				$adcode = '<a href="'.esc_attr($advert->link).'"'.$new_window.'><img src="%image%" /></a>';
			}

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
			$advertdata['image'] = esc_attr($advert->url);
			$advertdata['link'] = ''; // Deprecated
			$advertdata['tracker'] = 'Y';
			$advertdata['responsive'] = 'N';
			$advertdata['type'] = $status;
			$advertdata['weight'] = 6;
			$advertdata['sortorder'] = 0;
			$advertdata['cbudget'] = 0;
			$advertdata['ibudget'] = 0;
			$advertdata['crate'] = 0;
			$advertdata['irate'] = 0;
			$advertdata['cities'] = serialize(array());
			$advertdata['countries'] = serialize(array());
	
			$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
		    $ad_id = $wpdb->insert_id;
	
			if($include_schedules == 1) {
				list($eyear, $emonth, $eday) = explode('-', esc_attr($advert->expiry_date));
				$end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);

				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'dayimpressions' => 0));
				$schedule_id = $wpdb->insert_id;
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
			}
			
			if($include_groups == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group2zone[esc_attr($advert->zoneid)], 'user' => 0, 'schedule' => 0));
			}
	
			if($include_stats == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_stats', array('ad' => $ad_id, 'group' => 0, 'thetime' => $now, 'clicks' => esc_attr($advert->clicks), 'impressions' => esc_attr($advert->impressions)));
			}
			
			unset($advertdata, $adcode, $new_window, $track_link, $target_url, $status, $ad_id, $schedule_id);
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>