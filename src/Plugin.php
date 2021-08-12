<?php

namespace Innocode\AssetsVersion;

use WP_Dependencies;
use WP_Scripts;

/**
 * Class Plugin
 * @package Innocode\AssetsVersion
 */
final class Plugin
{
    const OPTION = 'innocode_assets_version';

    /**
     * @var Version
     */
    private $version;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        $this->version = new Version( Plugin::OPTION );
    }

    public function run()
    {
        add_filter( 'script_loader_src', [ $this, 'add_script_ver_query_arg' ], 10, 2 );
        add_filter( 'style_loader_src', [ $this, 'add_style_ver_query_arg' ], 10, 2 );

        add_action( 'plugins_loaded', [ $this, 'add_flush_cache_actions' ] );
    }

    /**
     * @return Version
     */
    public function get_version() : Version
    {
        return $this->version;
    }

    /**
     * @param string      $src
     * @param string|null $handle
     * @return string
     */
    public function add_script_ver_query_arg( string $src, ?string $handle ) : string
    {
        global $wp_scripts;

        return $this->add_ver_query_arg( $src, $wp_scripts, $handle );
    }

    /**
     * @param string      $src
     * @param string|null $handle
     * @return string
     */
    public function add_style_ver_query_arg( string $src, ?string $handle ) : string
    {
        global $wp_styles;

        return $this->add_ver_query_arg( $src, $wp_styles, $handle );
    }

    /**
     * @param string $src
     * @param WP_Dependencies $dependencies
     * @param string|null $handle
     * @return string
     */
    public function add_ver_query_arg( string $src, WP_Dependencies $dependencies, ?string $handle ) : string
    {
        if ( null === $handle || ! isset( $dependencies->registered[ $handle ] ) ) {
            return $src;
        }

        $type = $dependencies instanceof WP_Scripts ? 'script' : 'style';

        if ( ! apply_filters( 'innocode_assets_version_allow_default', false, $type ) ) {
            return $src;
        }

        $dependency = $dependencies->registered[ $handle ];

        if ( null !== $dependency->ver ) {
            return $src;
        }

        $ver = $this->get_version()();

        if ( ! $ver ) {
            return $src;
        }

        return add_query_arg( 'ver', $ver, $src );
    }

    public function add_flush_cache_actions()
    {
        $bump_version = [ $this->get_version(), 'bump' ];

        if ( function_exists( 'flush_cache_add_button' ) ) {
            flush_cache_add_button(
                __( 'Assets cache', 'innocode-assets-version' ),
                $bump_version
            );
        }

        if ( function_exists( 'flush_cache_add_sites_action_link' ) ) {
            flush_cache_add_sites_action_link(
                __( 'Assets cache', 'innocode-assets-version' ),
                $bump_version
            );
        }
    }
}
