<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_sam() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_sam')) {
		$include_groups = (isset($_POST['adrotateswitch_import_sam_groups'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$blocks = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_blocks` ORDER BY `id` ASC;");
			foreach($blocks as $block) {
				$margin = explode(" ", $block->b_margin);
				$blockdata['name'] = '[Imported] '.esc_attr($block->name);
				$blockdata['modus'] = 2;
				$blockdata['fallback'] = 0;
				$blockdata['sortorder'] = 0;
				$blockdata['cat'] = '';
				$blockdata['cat_loc'] = '';
				$blockdata['cat_par'] = '';
				$blockdata['page'] = '';
				$blockdata['page_loc'] = '';
				$blockdata['page_par'] = '';
				$blockdata['mobile'] = 0;
				$blockdata['geo'] = 0;
				$blockdata['wrapper_before'] = '';
				$blockdata['wrapper_after'] = '';
				$blockdata['align'] = 1;
				$blockdata['gridrows'] = $block->b_lines;
				$blockdata['gridcolumns'] = $block->b_cols;
				$blockdata['admargin'] = rtrim($margin[0], 'px');
				$blockdata['admargin_bottom'] = rtrim($margin[2], 'px');
				$blockdata['admargin_left'] = rtrim($margin[3], 'px');
				$blockdata['admargin_right'] = rtrim($margin[1], 'px');
				$blockdata['adwidth'] = 'auto';
				$blockdata['adheight'] = 'auto';
				$blockdata['adspeed'] = 6000;

				$wpdb->insert($wpdb->prefix."adrotate_groups", $blockdata);
			}

			$groups = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_places` ORDER BY `id` ASC;");
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
				$groupdata['mobile'] = 0;
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
				$groupdata['adwidth'] = ($group->place_custom_width > 0) ? $group->place_custom_width : 'auto';
				$groupdata['adheight'] = ($group->place_custom_height > 0) ? $group->place_custom_height : 'auto';
				$groupdata['adspeed'] = 6000;

				$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
				$group2advert[esc_attr($group->ID)] = $wpdb->insert_id;
			}
		}
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_ads` ORDER BY `id` ASC;");
		foreach($adverts as $advert) {
			if($advert->code_mode == 1) {
				$adcode = esc_attr($advert->ad_code);
				$imagetype = '';
				$image = '';
			} else {				
				$adcode = '<a href="'.esc_attr($advert->ad_target).'" target="_blank"><img src="%image%" /></a>';
				$imagetype = 'field';
				$image = esc_attr($advert->ad_img);
			}

			// Enabled or Disabled
			if($advert->ad_weight > 0) {
				$status = 'active';
			} else {
				$status = 'disabled';
			}

			// Convert Weight
			$weight = 6;
			if($advert->ad_weight == 1) $weight = 2;
			if($advert->ad_weight == 3) $weight = 4;
			if($advert->ad_weight == 5) $weight = 6;
			if($advert->ad_weight == 7) $weight = 8;
			if($advert->ad_weight == 9) $weight = 10;

			// Format advert
			$advertdata['title'] = '[Imported] '.esc_attr($advert->name);
			$advertdata['bannercode'] = $adcode;
			$advertdata['thetime'] = $now;
			$advertdata['updated'] = $now;
			$advertdata['author'] = $current_user->user_login;
			$advertdata['imagetype'] = $imagetype;
			$advertdata['image'] = $image;
			$advertdata['link'] = ''; // Deprecated
			$advertdata['tracker'] = 'Y';
			$advertdata['mobile'] = 'N';
			$advertdata['tablet'] = 'N';
			$advertdata['responsive'] = 'N';
			$advertdata['type'] = $status;
			$advertdata['weight'] = $weight;
			$advertdata['sortorder'] = 0;
			$advertdata['budget'] = $advert->per_month;
			$advertdata['crate'] = $advert->cpc;
			$advertdata['irate'] = $advert->cpm;
			$advertdata['cities'] = serialize(array());
			$advertdata['countries'] = serialize(array());
	
			$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
		    $ad_id = $wpdb->insert_id;
	
			$start_date = adrotate_now();
			$end_date = $start_date + 7257600;
			list($syear, $smonth, $sday) = explode('-', esc_attr($advert->ad_start_date));
			if($syear > 0 AND $smonth > 0 AND $sday > 0) $start_date = gmmktime(0, 0, 0, $smonth, $sday, $syear);
			list($eyear, $emonth, $eday) = explode('-', esc_attr($advert->ad_end_date));
			if($eyear > 0 AND $emonth > 0 AND $eday > 0) $end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);

			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'dayimpressions' => 0));
			$schedule_id = $wpdb->insert_id;
			$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));

			if($include_groups == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group2advert[esc_attr($advert->pid)], 'user' => 0, 'schedule' => 0));
			}
	
			unset($advertdata, $adcode, $status, $weight, $ad_id, $syear, $smonth, $sday, $eyear, $emonth, $eday, $start_date, $end_date, $schedule_id);
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>