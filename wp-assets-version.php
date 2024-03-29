<?php
/**
 * Plugin Name: Assets Version
 * Description: Helps with versioning of CSS and JS files.
 * Plugin URI: https://github.com/innocode-digital/wp-assets-version
 * Version: 1.1.0
 * Author: Innocode
 * Author URI: https://innocode.com
 * Tested up to: 5.8
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

use Innocode\AssetsVersion;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

$GLOBALS['innocode_assets_version'] = new AssetsVersion\Plugin();
$GLOBALS['innocode_assets_version']->run();

if ( ! function_exists( 'innocode_assets_version' ) ) {
    /**
     * @return string|null
     */
    function innocode_assets_version() : ?string {
        /**
         * @var AssetsVersion\Plugin $innocode_assets_version
         */
        global $innocode_assets_version;

        return $innocode_assets_version->get_version()();
    }
}
