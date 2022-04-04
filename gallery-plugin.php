<?php
/**
 * Plugin Name:       Gallery plugin
 * Description:       Test plugin
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            Vadim
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gallery-plugin
 * Domain Path:       /languages
 */

define( 'PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_SLUG', 'gallery-plugin' );

require_once __DIR__ . '/inc/class-gallery-core.php';
new GalleyCore();
?>