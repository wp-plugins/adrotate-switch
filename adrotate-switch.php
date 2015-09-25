<?php
/*
Plugin Name: AdRotate Switch
Plugin URI: https://ajdg.solutions/?pk_campaign=adrotateswitch-pluginpage
Author: Arnan de Gans of AJdG Solutions
Author URI: https://ajdg.solutions/?pk_campaign=adrotateswitch-pluginpage
Description: Easily migrate your data from compatible advertising plugins to AdRotate or AdRotate Pro.
Text Domain: adrotate-switch
Domain Path: /languages/
Version: 1.3.1
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

	load_plugin_textdomain('adrotate-switch', false, basename( dirname( __FILE__ ) ) . '/language' );
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
	add_management_page(__('AdRotate Switch', 'adrotate-switch'), __('AdRotate Switch', 'adrotate-switch'), 'manage_options', 'adrotate-switch', 'adrotateswitch_main');
}

/* Show dashboard */
function adrotateswitch_main() {
	$status = 0;
	if(isset($_GET['s'])) $status = esc_attr($_GET['s']);
	?>
	<div class="wrap">
		<h2><?php _e('AdRotate Switch', 'adrotate-switch'); ?></h2>

		<?php if($status == 1) { ?>
			<div class="updated" style="padding:12px;"><?php _e('Your data hase been imported into AdRotate. You can manage your adverts from the', 'adrotate-switch'); ?> <a href="'.admin_url('/admin.php?page=adrotate-ads&view=manage').'">AdRotate <?php _e('dashboard', 'adrotate-switch'); ?></a> <?php _e('now.', 'adrotate-switch'); ?><br /><br /><strong><?php _e('Next steps:', 'adrotate-switch'); ?></strong><br />- <?php _e('If you have imported all your ads through AdRotate Switch you can uninstall or disable this plugin.', 'adrotate-switch'); ?><br />- <?php _e('Once you have verified all your ads and compatible data have been imported into AdRotate correctly you can remove or disable your previous advertising plugin.', 'adrotate-switch'); ?></div>
		<?php } ?>

		<?php if(!adrotateswitch_adrotate_is_active()) { ?>
			<div class="error" style="padding:12px;"><?php _e('AdRotate (Pro) is not active or installed! AdRotate (Pro) must be active for AdRotate Switch to work!', 'adrotate-switch'); ?></div>
		<?php } ?>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
		
				<div id="postbox-container-1" class="postbox-container" style="width:50%;">
					<div class="meta-box-sortables">
						
						<h3>Max Banner Ads PRO v2.1.3</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_mba','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_mba_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_mba_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_groups" value="1" /> <?php _e('Import zones into groups', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_mba_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_mba_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_mba_stats" value="1" /> <?php _e('Import clicks and impressions into a stats record', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_mba" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('No location/placement data migrated.', 'adrotate-switch'); ?> <?php _e('Zones are migrated to groups with default settings.', 'adrotate-switch'); ?> <?php _e('AdCode generated on the fly based on your banners.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>Ad King PRO v1.9.15</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_adking','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_adking_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adking_adverts" checked="1" disabled="1" /> <?php _e('Import Image, Text or AdSense banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_adking_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adking_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adking" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('HTML5 and Flash banners are not imported.', 'adrotate-switch'); ?> <?php _e('Banner Zones are not compatible.', 'adrotate-switch'); ?> <?php _e('AdCode will be generated on the fly based on your settings.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>WP Pro Ad System v4.6.9</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wppas','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_wppas_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_wppas_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_groups" value="1" /> <?php _e('Import zones into groups', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_wppas_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wppas_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wppas" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
								</form>
								<p><strong><?php _e('Caution:', 'adrotate-switch'); ?></strong> <?php _e('This plugin has a complex set of options.', 'adrotate-switch'); ?> <?php _e('AdRotate makes a best effort but some adverts/groups may not work without some tweaks!', 'adrotate-switch'); ?></p>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('Adzones imported into groups.', 'adrotate-switch'); ?> <?php _e('AdCode may be generated on the fly based on your settings.', 'adrotate-switch'); ?> <?php _e('Tablet/Phone advert variations are imported into seperate adverts.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>WP Advertize It v0.7.3</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wpadvertizeit','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_wpadvertizeit_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wpadvertizeit_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_wpadvertizeit_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wpadvertizeit_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wpadvertizeit" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('Placement data not compatible.', 'adrotate-switch'); ?> <?php _e('AdCode will be imported as-is.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>Simple Ads Manager v2.4.90</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_sam','adrotateswitch_nonce'); ?>					
									<p><label for="adrotateswitch_import_sam_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_sam_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_sam_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_sam_groups" value="1" /> <?php _e('Import Places and Blocks into groups', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_sam" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('Ads Blocks are converted to groups in Block mode, no ads linked.', 'adrotate-switch'); ?> <?php _e('AdCode will be generated on the fly based on your settings.', 'adrotate-switch'); ?> <?php _e('Groups are converted where possible and compatible settings migrated.', 'adrotate-switch'); ?> <?php _e('Each advert is assigned a schedule.', 'adrotate-switch'); ?></p>
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
									<p><label for="adrotateswitch_import_adinjection_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adinjection_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_adinjection_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adinjection_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adinjection" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('All configured ad codes migrated.', 'adrotate-switch'); ?> <?php _e('AdCode will be generated on the fly based on your settings.', 'adrotate-switch'); ?> <?php _e('Placement information not compatible.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>wp125 v1.5.3 (<?php _e('Plugin defunct', 'adrotate-switch'); ?>)</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_wp125','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_wp125_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_wp125_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_wp125_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_wp125_stats" value="1" /> <?php _e('Import clicks into a stats record', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wp125" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Caution:', 'adrotate-switch'); ?></strong> <?php _e('This plugin is tested to have errors on modern WordPress 4.2+ which may affect your import.', 'adrotate-switch'); ?></p>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('Slots not migrated.', 'adrotate-switch'); ?> <?php _e('AdCode will be generated on the fly.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>BannerMan v0.2.4</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_bannerman','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_bannerman_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_bannerman_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_bannerman_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_bannerman_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_bannerman" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('A group with relevant settings will be generated to accomodate all ads.', 'adrotate-switch'); ?> <?php _e('AdCode will be imported as-is.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3>Useful Banner Manager v1.5</h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<form method="post" action="admin.php?page=adrotate-switch">
									<?php wp_nonce_field('adrotateswitch_import_ubm','adrotateswitch_nonce'); ?>
									<p><label for="adrotateswitch_import_ubm_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_ubm_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
									<label for="adrotateswitch_import_ubm_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_ubm_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label></p>
									<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_ubm" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
								</form>
								<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> <?php _e('AdCode generated on the fly.', 'adrotate-switch'); ?> <?php _e('Most settings are converted into HTML for use in AdCode.', 'adrotate-switch'); ?> <?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
						<h3><?php _e('Help improve AdRotate Switch', 'adrotate-switch'); ?></h3>
						<div class="postbox-adrotate">
							<div class="inside">
								<strong><?php _e('Know of a plugin to add?', 'adrotate-switch'); ?></strong>
								<p><?php _e('Know of a plugin that can be added here? Is your current plugin not listed? Maybe I can add it.', 'adrotate-switch'); ?>
								<a href="https://ajdg.solutions/contact/?pk_campaign=adrotateswitch" target="_blank"><?php _e('Get in touch', 'adrotate-switch'); ?></a>!</p>
								<strong><?php _e('Something wrong?', 'adrotate-switch'); ?></strong>
								<p><?php _e('Module outdated or import not working?', 'adrotate-switch'); ?> <a href="https://ajdg.solutions/contact/?pk_campaign=adrotateswitch" target="_blank"><?php _e('Let me know', 'adrotate-switch'); ?></a>! <?php _e('Please use ENGLISH only!', 'adrotate-switch'); ?></p>
							</div>
						</div>
		
					</div>
				</div>
		
			</div>
		
			<div class="clear"></div>
			<table class="widefat" style="margin-top: .5em">
			
			<thead>
			<tr valign="top">
				<th colspan="2"><?php _e('Did AdRotate Switch help?', 'adrotate-switch'); ?></th>
				<th width="35%"><?php _e('Don\'t have AdRotate Pro?', 'adrotate-switch'); ?></th>
				<th colspan="2" width="20%"><center><?php _e('Brought to you by', 'adrotate-switch'); ?></center></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
			<td><center><a href="https://ajdg.solutions/products/adrotate-for-wordpress/?pk_campaign=adrotateswitch-credits" title="AdRotate Switch"><img src="<?php echo plugins_url('/images/adrotate-logo-60x60.png', __FILE__); ?>" alt="adrotate-logo-60x60" width="60" height="60" /></a></center></td>
			
			<td><?php _e('If you found AdRotate Switch useful while you migrated your adverts, please', 'adrotate-switch'); ?> <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch?rate=5#postform" target="_blank"><strong><?php _e('rate', 'adrotate-switch'); ?></strong></a> <?php _e('and', 'adrotate-switch'); ?> <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch" target="_blank"><strong><?php _e('review', 'adrotate-switch'); ?></strong></a> <?php _e('the plugin on WordPress.org. Thank you!', 'adrotate-switch'); ?></strong></td>

			<td><?php _e('Use discount code', 'adrotate-switch'); ?> <strong>getadrotatepro</strong> <?php _e('for 10% off on any AdRotate Pro license!', 'adrotate-switch'); ?> <a href="https://ajdg.solutions/products/adrotate-for-wordpress/?pk_campaign=adrotateswitch-credits" target="_blank"><strong><?php _e('Buy now', 'adrotate-switch'); ?></strong></a>.<br /><?php _e('Thank you for your purchase and support!', 'adrotate-switch'); ?></td>
			
			<td><center><a href="https://ajdg.solutions/?pk_campaign=adrotateswitch-credits" title="Arnan de Gans"><img src="<?php echo plugins_url('/images/arnan-jungle.jpg', __FILE__); ?>" alt="Arnan de Gans" width="60" height="60" align="left" class="adrotate-photo" /></a><a href="http://www.floatingcoconut.net?pk_campaign=adrotateswitch-credits" target="_blank">Arnan de Gans</a><br /><?php _e('from', 'adrotate-switch'); ?><br /><a href="https://ajdg.solutions?pk_campaign=adrotateswitch-credits" target="_blank">AJdG Solutions</a></center></td></td>
			</tr>
			</tbody>
			
			</table>
			<center><small><?php _e('AdRotate<sup>&reg;</sup> is a registered trademark.', 'adrotate-switch'); ?></small></center>
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