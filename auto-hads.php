<?php
/*
  Plugin Name: Auto hads
  Plugin URI: http://kadrealestate.com/
  Description: Use to get news or products from other websites on the internet
  Author: huynhduy1985
  Author URI: https://www.facebook.com/huynhduy1985
  Version: 1.0.2
  License: GPLv2 or later
  Text Domain: autohads
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2018 hadwebs, Inc.
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'AUTOHADS_VERSION', '1.0.0' );
if (!defined('AUTO_HADS_PLUGIN_URL')) {
    define('AUTO_HADS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('AUTO_HADS_PLUGIN_DIR')) {
    define('AUTO_HADS_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('AUTO_HADS_PLUGIN_DIR_FILE')) {
    define('AUTO_HADS_PLUGIN_DIR_FILE', __FILE__);
}
// Include the main AutoHads class.
if ( ! class_exists( 'AutoHads' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-autohads.php';
}
/**
 * Main instance of AutoHads.
 *
 * Returns the main instance of autohads to prevent the need to use globals.
 *
 * @since  1.0
 * @return AutoHads
 */
function auto_hads() {
	return AutoHads::instance();
}
// Global for backwards compatibility.
$GLOBALS['autohads'] = auto_hads();