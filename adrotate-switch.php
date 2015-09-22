<?php
/*
Plugin Name: AdRotate Switch
Plugin URI: https://ajdg.solutions/?pk_campaign=adrotateswitch-pluginpage
Author: Arnan de Gans of AJdG Solutions
Author URI: https://ajdg.solutions/?pk_campaign=adrotateswitch-pluginpage
Description: Easily migrate your data from other advertising plugins to AdRotate or AdRotate Pro.
Version: 1.3
License: GPLv3
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

if(is_admin()) {
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-mba.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-ubm.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-sam.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-wp125.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-wppas.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-adking.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-bannerman.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-adinjection.php');
	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-import-wpadvertizeit.php');

	add_action('admin_menu', 'adrotateswitch_dashboard');
	add_action("admin_print_styles", 'adrotateswitch_dashboard_styles');
	/*--- Internal redirects ------------------------------------*/
	if(isset($_POST['adrotateswitch_import_mba'])) add_action('init', 'adrotateswitch_import_mba');
	if(isset($_POST['adrotateswitch_import_ubm'])) add_action('init', 'adrotateswitch_import_ubm');
	if(isset($_POST['adrotateswitch_import_sam'])) add_action('init', 'adrotateswitch_import_sam');
	if(isset($_POST['adrotateswitch_import_wp125'])) add_action('init', 'adrotateswitch_import_wp125');
	if(isset($_POST['adrotateswitch_import_wppas'])) add_action('init', 'adrotateswitch_import_wppas');
	if(isset($_POST['adrotateswitch_import_adking'])) add_action('init', 'adrotateswitch_import_adking');
	if(isset($_POST['adrotateswitch_import_bannerman'])) add_action('init', 'adrotateswitch_import_bannerman');
	if(isset($_POST['adrotateswitch_import_adinjection'])) add_action('init', 'adrotateswitch_import_adinjection');
	if(isset($_POST['adrotateswitch_import_wpadvertizeit'])) add_action('init', 'adrotateswitch_import_wpadvertizeit');
}

/* Add dashboard */
function adrotateswitch_dashboard() {
	add_management_page('AdRotate Switch', 'AdRotate Switch', 'manage_options', 'adrotate-switch', 'adrotateswitch_main');
}

/* Show dashboard */
function adrotateswitch_main() {
	$status = 0;
	if(isset($_GET['s'])) $status = esc_attr($_GET['s']);
	?>
	<div class="wrap">
		<h2>AdRotate Switch</h2>

		<?php if($status == 1) {
		echo '<div class="updated" style="padding:12px;">Your data hase been imported into AdRotate. You can manage your adverts from the <a href="'.admin_url('/admin.php?page=adrotate-ads&view=manage').'">adrotate dashboard</a> now.<br /><br /><strong>Next steps:</strong><br />- If you have imported all your ads through AdRotate Switch you can uninstall or disable this plugin.<br />- Once you have verified all your ads and compatible data have been imported into AdRotate correctly you can remove or disable your previous advertising plugin.</div>';
		}

		if(!adrotateswitch_adrotate_is_active()) { 
		echo '<div class="error" style="padding:12px;">AdRotate (Pro) is not active or installed! AdRotate (Pro) must be active for AdRotate Switch to work!</div>';
		} 
		?>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
		
				<div id="postbox-container-1" class="postbox-container" style="width:50%;">
					<div class="meta-box-sortables">
						
						<h3>Max Banner Ads PRO v2.1.3</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_mba','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_mba_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_mba_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_groups" value="1" /> Import zones into groups</label><br />
									<label for="adrotateswitch_import_mba_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_schedules" value="1" /> Import expiry dates into schedules (Recommended)</label><br />
									<label for="adrotateswitch_import_mba_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_stats" value="1" /> Import clicks and impressions into a stats record</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_mba" value="Import Max Banner Ads" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>				
								</form>
								<p><strong>Notes:</strong> No location/placement data migrated. Zones are migrated to groups with default settings. AdCode generated on the fly based on your banners. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>Ad King PRO v1.9.15</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_adking','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_adking_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adking_adverts" checked="1" disabled="1" /> Import Image, Text or AdSense banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_adking_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adking_schedules" value="1" /> Import expiry dates into schedules (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adking" value="Import Ad King PRO" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>				
								</form>
								<p><strong>Notes:</strong> HTML5 and Flash banners are not imported. Banner Zones are not compatible. AdCode will be generated on the fly based on your settings. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>WP Pro Ad System v4.6.9</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wppas','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_wppas_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_wppas_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_groups" value="1" /> Import zones into groups</label><br />
									<label for="adrotateswitch_import_wppas_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_schedules" value="1" /> Assign a default schedule (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wppas" value="Import WP Pro Ad System" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>				
								</form>
								<p><strong>Caution:</strong> This plugin has a complex set of options. AdRotate makes a best effort but some adverts/groups may not work without some tweaks!</p>
								<p><strong>Notes:</strong> Adzones imported into groups. AdCode may be generated on the fly based on your settings. Tablet/Phone advert variations are imported into seperate adverts. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>WP Advertize It v0.7.3</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wpadvertizeit','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_wpadvertizeit_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wpadvertizeit_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_wpadvertizeit_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wpadvertizeit_schedules" value="1" /> Assign a default schedule (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wpadvertizeit" value="Import WP Advertize It" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Notes:</strong> Placement data not compatible. AdCode will be imported as-is. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>Simple Ads Manager v2.4.90</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_sam','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_sam_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_sam_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_sam_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_sam_groups" value="1" /> Import Places and Blocks into groups</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_sam" value="Import Simple Ads Manager" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Notes:</strong> Ads Blocks are converted to groups in Block mode, no ads linked. AdCode will be generated on the fly based on your settings. Groups are converted where possible and compatible settings migrated. Each advert is assigned a schedule.</p>
							</div>
						</div>
		
					</div>
				</div>
		
				<div id="postbox-container-2" class="postbox-container" style="width:50%;">
					<div class="meta-box-sortables">
		
						<h3>Ad Injection v1.2.0.19</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_adinjection','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_adinjection_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adinjection_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_adinjection_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adinjection_schedules" value="1" /> Assign a default schedule (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adinjection" value="Import Ad Injection" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Notes:</strong> All configured ad codes migrated. AdCode will be generated on the fly based on your settings. Placement information not compatible. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>wp125 v1.5.3 (Plugin defunct)</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wp125','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_wp125_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_wp125_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_schedules" value="1" /> Import expiry dates into schedules (Recommended)</label><br />
									<label for="adrotateswitch_import_wp125_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_stats" value="1" /> Import clicks into a stats record</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wp125" value="Import wp125" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Caution:</strong> This plugin is tested to have errors on modern WordPress 4.2+ which may affect your import.</p>
								<p><strong>Notes:</strong> Slots not migrated. AdCode will be generated on the fly based on your banners. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>BannerMan v0.2.4</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_bannerman','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_bannerman_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_bannerman_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_bannerman_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_bannerman_schedules" value="1" /> Assign a default schedule (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_bannerman" value="Import BannerMan" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Notes:</strong> A group with relevant settings will be generated to accomodate all ads. AdCode will be imported as-is. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>Useful Banner Manager v1.5</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_ubm','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_ubm_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_ubm_adverts" checked="1" disabled="1" /> Import banners into adverts (required)</label><br />
									<label for="adrotateswitch_import_ubm_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_ubm_schedules" value="1" /> Import expiry dates into schedules (Recommended)</label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_ubm" value="Import Useful Banner Manager" class="button-primary" />&nbsp;&nbsp;&nbsp;<em>Click only once!</em></p>
								</form>
								<p><strong>Notes:</strong> AdCode generated on the fly based on your banners. Most settings are converted into HTML for use in AdCode. If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.</p>
							</div>
						</div>
		
						<h3>Help improve AdRotate Switch</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<strong>Know of a plugin to add?</strong>
								<p>Know of a plugin that can be added here? Is your current plugin not listed? Maybe I can add it.
								<a href="https://ajdg.solutions/contact/?pk_campaign=adrotateswitch" target="_blank">Get in touch</a>!</p>
								<strong>Something wrong?</strong>
								<p>Module outdated or import not working? <a href="https://ajdg.solutions/contact/?pk_campaign=adrotateswitch" target="_blank">Let me know</a>!</p>
							</div>
						</div>
		
					</div>
				</div>
		
			</div>
		
			<div class="clear"></div>
			<table class="widefat" style="margin-top: .5em">
			
			<thead>
			<tr valign="top">
				<th colspan="2">Did AdRotate Switch help?</th>
				<th width="35%">Don't have AdRotate Pro?</th>
				<th colspan="2" width="20%"><center>Brought to you by</center></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
			<td><center><a href="https://ajdg.solutions/products/adrotate-for-wordpress/?pk_campaign=adrotateswitch-credits" title="AdRotate Switch"><img src="<?php echo plugins_url('/images/adrotate-logo-60x60.png', __FILE__); ?>" alt="adrotate-logo-60x60" width="60" height="60" /></a></center></td>
			
			<td>If you found AdRotate Switch useful while you migrated your adverts, please <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch?rate=5#postform" target="_blank"><strong>rate</strong></a> and <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch" target="_blank"><strong>review</strong></a> the plugin on WordPress.org. Thank you!</strong></td>

			<td>Use discount code <strong>getadrotatepro</strong> for 10% off on any AdRotate Pro license! <a href="https://ajdg.solutions/products/adrotate-for-wordpress/?pk_campaign=adrotateswitch-credits" target="_blank"><strong>Buy now</strong></a>.<br />Thank you for your purchase and support!</td>
			
			<td><center><a href="https://ajdg.solutions/?pk_campaign=adrotateswitch-credits" title="Arnan de Gans"><img src="<?php echo plugins_url('/images/arnan-jungle.jpg', __FILE__); ?>" alt="Arnan de Gans" width="60" height="60" align="left" class="adrotate-photo" /></a><a href="http://www.floatingcoconut.net?pk_campaign=adrotateswitch-credits" target="_blank">Arnan de Gans</a><br />from<br /><a href="https://ajdg.solutions?pk_campaign=adrotateswitch-credits" target="_blank">AJdG Solutions</a></center></td></td>
			</tr>
			</tbody>
			
			</table>
			<center><small>AdRotate<sup>&reg;</sup> is a registered trademark.</small></center>
		</div>

	</div>
	<?php
}

/* Check if AdRotate is active */
function adrotateswitch_adrotate_is_active() {
	if(function_exists('adrotate_dashboard')) {
		return true;
	} else {
		return false;
	}
}

/* Load Dashboard styles */
function adrotateswitch_dashboard_styles() {
	wp_enqueue_style('adrotateswitch-admin-stylesheet', plugins_url('dashboard.css', __FILE__));
}
?>