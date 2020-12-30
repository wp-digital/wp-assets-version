<?php
/**
 * Plugin Name: Assets Version.
 * Description: Helps with versioning of CSS and JS files.
 * Plugin URI: https://github.com/innocode-digital/wp-assets-version
 * Version: 0.0.1
 * Author: Innocode
 * Author URI: https://innocode.com
 * Tested up to: 5.6
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

use Innocode\AssetsVersion;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

$innocode_assets_version = new AssetsVersion\Plugin();
$innocode_assets_version->run();
