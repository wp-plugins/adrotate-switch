<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_wppas() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wppas')) {
		$include_groups = (isset($_POST['adrotateswitch_import_wppas_groups'])) ? 1 : 0;
		$include_schedules = (isset($_POST['adrotateswitch_import_wppas_schedules'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$groups = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'adzones' AND `post_status` = 'publish' ORDER BY `id` ASC;");
			foreach($groups as $group) {
				$meta_group = get_post_meta($group->ID);
				
				// Advert sizing
				if(strlen($meta_advert['_adzone_size'][0] > 0)) {
					list($group_width, $group_height) = explode("x", $meta_advert['_adzone_size'][0]);
				} else {
					$group_width = $group_height = 125;
				}

				// Modus
				if($meta_advert['_adzone_grid_horizontal'][0] > 0 AND $meta_advert['_adzone_grid_vertical'][0] > 0) {
					$modus = 2;
					$rows = $meta_advert['_adzone_grid_horizontal'][0];
					$columns = $meta_advert['_adzone_grid_vertical'][0];
				} else {
					$modus = 0;
					$rows = $columns = 2;
				}
				
				// Rotation
				if($meta_advert['_adzone_rotation_time'][0] == 1) {
					$rotation = $meta_advert['_adzone_rotation_time'][0] * 1000;
				} else {
					$rotation = 6000;
				}

				// Centering
				if($meta_advert['_adzone_center'][0] == 1) {
					$center = 3;
				} else {
					$center = 0;
				}
				
				$groupdata['name'] = '[Imported] '.esc_attr($group->post_title);
				$groupdata['modus'] = $modus;
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
				$groupdata['align'] = $center;
				$groupdata['gridrows'] = $rows;
				$groupdata['gridcolumns'] = $columns;
				$groupdata['admargin'] = 0;
				$groupdata['admargin_bottom'] = 0;
				$groupdata['admargin_left'] = 0;
				$groupdata['admargin_right'] = 0;
				$groupdata['adwidth'] = $group_width;
				$groupdata['adheight'] = $group_height;
				$groupdata['adspeed'] = $rotation;

				$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
				$group2zone[esc_attr($group->ID)] = $wpdb->insert_id;
				
				unset($groupdata, $group_width, $group_height, $modus, $rows, $columns, $rotation, $center);
			}
		}

		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'banners' ORDER BY `id` ASC;");
		foreach($adverts as $advert) {
			$meta_advert = get_post_meta($advert->ID);

			// Open in new window?
			if($meta_advert['_banner_target'][0] == '_blank') {
				$new_window = ' target="_blank"';
			} else {
				$new_window = '';
			}
	
			// Enabled or Disabled
			if($advert->post_status == 'publish') {
				$status = 'active';
			} else {
				$status = 'disabled';
			}
	
			// Format advert (Desktop)
			$desktop_image = $desktop_imagetype = $desktop_adcode = '';
			if(strlen($meta_advert['_banner_html'][0]) > 0) {
				$desktop_adcode = esc_attr($meta_advert['_banner_html'][0]);
				$desktop_image = '';
				$desktop_imagetype = '';
			} else {
				$desktop_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'>%image%</a>';
				$desktop_image = $meta_advert['_banner_url'][0];
				$desktop_imagetype = 'field';
			}
	
			$desktop_advertdata['title'] = '[Imported] '.$advert->post_title.' (#'.$advert->ID.')';
			$desktop_advertdata['bannercode'] = $desktop_adcode;
			$desktop_advertdata['thetime'] = $now;
			$desktop_advertdata['updated'] = $now;
			$desktop_advertdata['author'] = $current_user->user_login;
			$desktop_advertdata['imagetype'] = $desktop_imagetype;
			$desktop_advertdata['image'] = $desktop_image;
			$desktop_advertdata['link'] = ''; // Deprecated
			$desktop_advertdata['tracker'] = 'Y';
			$desktop_advertdata['mobile'] = 'N';
			$desktop_advertdata['tablet'] = 'N';
			$desktop_advertdata['responsive'] = 'N';
			$desktop_advertdata['type'] = $status;
			$desktop_advertdata['weight'] = 6;
			$desktop_advertdata['sortorder'] = 0;
			$desktop_advertdata['budget'] = 0;
			$desktop_advertdata['crate'] = 0;
			$desktop_advertdata['irate'] = 0;
			$desktop_advertdata['cities'] = serialize(array());
			$desktop_advertdata['countries'] = serialize(array());
	
			$wpdb->insert($wpdb->prefix."adrotate", $desktop_advertdata);
		    $desktop_ad_id = $wpdb->insert_id;
			$ads2schedule[] = $desktop_ad_id;
		    unset($desktop_image, $desktop_imagetype, $desktop_adcode, $desktop_advertdata);


			$tablet_ad_id = $phone_ad_id = 0;
			// Format advert (Tablet)
			if($meta_advert['_banner_html_tablet_portrait'][0] OR $meta_advert['_banner_url_tablet_portrait'][0]) {
				$tablet_image = $tablet_imagetype = $tablet_adcode = '';
				if(strlen($meta_advert['_banner_html_tablet_portrait'][0]) > 0) {
					$tablet_adcode = esc_attr($meta_advert['_banner_html_tablet_portrait'][0]);
					$tablet_image = '';
					$tablet_imagetype = '';
				} else {
					$tablet_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'>%image%</a>';
					$tablet_image = $meta_advert['_banner_url_tablet_portrait'][0];
					$tablet_imagetype = 'field';
				}
		
				$tablet_advertdata['title'] = '[Imported] '.$advert->post_title.' (Tablet #'.$advert->ID.')';
				$tablet_advertdata['bannercode'] = $tablet_adcode;
				$tablet_advertdata['thetime'] = $now;
				$tablet_advertdata['updated'] = $now;
				$tablet_advertdata['author'] = $current_user->user_login;
				$tablet_advertdata['imagetype'] = $tablet_imagetype;
				$tablet_advertdata['image'] = $tablet_image;
				$tablet_advertdata['link'] = ''; // Deprecated
				$tablet_advertdata['tracker'] = 'Y';
				$tablet_advertdata['mobile'] = 'N';
				$tablet_advertdata['tablet'] = 'Y';
				$tablet_advertdata['responsive'] = 'N';
				$tablet_advertdata['type'] = $status;
				$tablet_advertdata['weight'] = 6;
				$tablet_advertdata['sortorder'] = 0;
				$tablet_advertdata['budget'] = 0;
				$tablet_advertdata['crate'] = 0;
				$tablet_advertdata['irate'] = 0;
				$tablet_advertdata['cities'] = serialize(array());
				$tablet_advertdata['countries'] = serialize(array());
		
				$wpdb->insert($wpdb->prefix."adrotate", $tablet_advertdata);
			    $tablet_ad_id = $wpdb->insert_id;
				$ads2schedule[] = $tablet_ad_id;
			    unset($tablet_image, $tablet_imagetype, $tablet_adcode, $tablet_advertdata);
			}

			// Format advert (Smartphone)
			if($meta_advert['_banner_html_phone_portrait'][0] OR $meta_advert['_banner_url_phone_portrait'][0]) {
				$phone_image = $phone_imagetype = $phone_adcode = '';
				if(strlen($meta_advert['_banner_html_phone_portrait'][0]) > 0) {
					$phone_adcode = esc_attr($meta_advert['_banner_html_phone_portrait'][0]);
					$phone_image = '';
					$phone_imagetype = '';
				} else {
					$phone_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'>%image%</a>';
					$phone_image = $meta_advert['_banner_url_phone_portrait'][0];
					$phone_imagetype = 'field';
				}
		
				$phone_advertdata['title'] = '[Imported] '.$advert->post_title.' (Mobile #'.$advert->ID.')';
				$phone_advertdata['bannercode'] = $phone_adcode;
				$phone_advertdata['thetime'] = $now;
				$phone_advertdata['updated'] = $now;
				$phone_advertdata['author'] = $current_user->user_login;
				$phone_advertdata['imagetype'] = $phone_imagetype;
				$phone_advertdata['image'] = $phone_image;
				$phone_advertdata['link'] = ''; // Deprecated
				$phone_advertdata['tracker'] = 'Y';
				$phone_advertdata['mobile'] = 'Y';
				$phone_advertdata['tablet'] = 'N';
				$phone_advertdata['responsive'] = 'N';
				$phone_advertdata['type'] = $status;
				$phone_advertdata['weight'] = 6;
				$phone_advertdata['sortorder'] = 0;
				$phone_advertdata['budget'] = 0;
				$phone_advertdata['crate'] = 0;
				$phone_advertdata['irate'] = 0;
				$phone_advertdata['cities'] = serialize(array());
				$phone_advertdata['countries'] = serialize(array());
		
				$wpdb->insert($wpdb->prefix."adrotate", $phone_advertdata);
			    $phone_ad_id = $wpdb->insert_id;
				$ads2schedule[] = $tablet_ad_id;
			    unset($phone_image, $phone_imagetype, $phone_adcode, $phone_advertdata);
			}

			$adzones = maybe_unserialize($meta_advert['_linked_adzones'][0]);
			if($include_groups == 1 AND is_array($adzones)) {
				foreach($adzones as $key => $adzone) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $desktop_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
					if($tablet_ad_id > 0) {
						$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $tablet_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
					}
					if($phone_ad_id > 0) {
						$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $phone_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
					}
					
					// Enable mobile support in group
					$wpdb->update($wpdb->prefix.'adrotate_groups', array('mobile' => 1), array('id' => $group2zone[esc_attr($adzone)]));
				}
			}
				
			unset($meta_advert, $new_window, $status, $adzones);
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