<?php

/*
Plugin Name: ADS PRO – Affiliate Program Add-on
Plugin URI: http://bsapro.scripteo.info
Description: Premium Multi-Purpose WordPress Ad Plugin, Create Incredible Good Ad Spaces!
Author: Scripteo
Author URI: http://codecanyon.net/user/scripteo
Version: 1.0.4
License: GPL2
*/

// Require files
if ( file_exists(ABSPATH . '/wp-content/plugins/ap-plugin-scripteo/lib/BSA_PRO_Model.php') && file_exists(ABSPATH . '/wp-content/plugins/bsa-pro-ap-scripteo/lib/functions.php') ) {
	require_once ABSPATH . '/wp-content/plugins/ap-plugin-scripteo/lib/BSA_PRO_Model.php';
	require_once ABSPATH . '/wp-content/plugins/bsa-pro-ap-scripteo/lib/functions.php';
}

class BuySellAdsProAffiliate
{
	private $plugin_id = 'bsa_pro_ap_plugin';
	private $plugin_version = '1.0.4';
	private $model;

	function __construct() {
		if ( class_exists('BSA_PRO_Model') ) {
			$this->model = new BSA_PRO_Model();
			register_activation_hook(__FILE__, array($this, 'onActivate'));
			register_uninstall_hook(__FILE__, array('BuySellAdsProAffiliate', 'onUninstall'));
		}
	}

	static function onUninstall()
	{
		$ver_opt = 'bsa_pro_ap_plugin'.'_version';

		// Delete version number
		delete_option($ver_opt);
	}

	function onActivate() {
		$opt = 'bsa_pro_ap_plugin';
		$ver_opt = $opt.'_version';
		$installed_version = get_option($ver_opt);

		if ( floatval(get_option('bsa_pro_plugin_version')) < 4.42 || !is_plugin_active( 'ap-plugin-scripteo/bsa-pro.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This Add-on requires <strong>Ads Pro 4.42</strong> or higher version. Download it <strong><a href="http://tinyurl.com/Ads-Pro-WordPress">here</a></strong>.' );
		}

		if($installed_version == NULL) {

			// Update plugin version
			update_option($ver_opt, $this->plugin_version);

		} else {

			switch(version_compare($installed_version, $this->plugin_version)) {

				case 0;
					// if installed plugin is the same
//					update_option($ver_opt, $this->plugin_version);
					break;

				case 1;
					// if installed plugin is newer
					update_option($ver_opt, $this->plugin_version);
					break;

				case -1;
					// if installed plugin is older
					update_option($ver_opt, $this->plugin_version);
					break;
			}
		}
	}
}

function BSA_PRO_AP_add_stylesheet_and_script()
{
	$rtl = (get_option('bsa_pro_plugin_rtl_support') == 'yes') ? 'rtl-' : null;
	wp_register_style('buy_sell_ads_pro_ap_main_stylesheet', plugins_url('frontend/css/'.$rtl.'style.css', __FILE__));
	wp_enqueue_style('buy_sell_ads_pro_ap_main_stylesheet');
}
add_action('wp_enqueue_scripts', 'BSA_PRO_AP_add_stylesheet_and_script');

$BSA_PRO_AP_Plugin = new BuySellAdsProAffiliate();
