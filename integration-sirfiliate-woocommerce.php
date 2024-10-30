<?php
/**
 * Plugin Name: Integration for SirFiliate with WooCommerce
 * Plugin URI: https://sirfiliate.com
 * Description: Connect your shop with Sir Filiate platform.
 * Version: 1.3.2
 * Author: Team WSA
 * Text Domain: integration-sirfiliate-woocommerce
 * Domain Path: /languages
 * Network: false
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * WC requires at least: 5.0
 * WC tested up to: 6.0
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


define(__NAMESPACE__ . '\PREFIX', 'srft');

define(__NAMESPACE__ . '\VERSION', '1.3.2');

define(__NAMESPACE__ . '\NAME', 'Integration for SirFiliate with WooCommerce');

define(__NAMESPACE__ . '\DIR_URL', untrailingslashit(plugin_dir_url(__FILE__)));

define(__NAMESPACE__ . '\DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

define(__NAMESPACE__ . '\DIR_NAME', plugin_basename(DIR_PATH));

define(__NAMESPACE__ . '\DIR_BASENAME', DIR_NAME . '/'.basename(__FILE__));

define(__NAMESPACE__ . '\SETTINGS_TAB_ID', 'sirfiliate');

define(__NAMESPACE__ . '\SETTINGS_TAB_NAME', 'Sir Filiate');

define(__NAMESPACE__ . '\SETTINGS_URL', admin_url('/admin.php?page=wc-settings&tab=' . SETTINGS_TAB_ID));

define(__NAMESPACE__ . '\DEBUG', get_option(PREFIX . '_debug') === 'yes' ? true:false);

define(__NAMESPACE__ . '\DEBUG_FILE', DIR_PATH . '/debug.log');


//include files
require_once DIR_PATH . '/vendor/autoload.php';

//init
Module_Core_Hook::init();