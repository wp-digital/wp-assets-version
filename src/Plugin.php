<?php

namespace Innocode\AssetsVersion;

use _WP_Dependency;
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
        add_filter( 'innocode_assets_version_allow_dependency', [ $this, 'can_use_dependency' ], 10, 3 );

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

        if ( ! isset( $wp_scripts ) ) {
            return $src;
        }

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

        if ( ! isset( $wp_styles ) ) {
            return $src;
        }

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
        if (
            ! apply_filters( 'innocode_assets_version_allow_default', false ) ||
            null === $handle ||
            ! isset( $dependencies->registered[ $handle ] )
        ) {
            return $src;
        }

        /**
         * @var _WP_Dependency $dependency
         */
        $dependency = $dependencies->registered[ $handle ];
        $type = $dependencies instanceof WP_Scripts ? 'script' : 'style';

        if ( ! apply_filters( 'innocode_assets_version_allow_dependency', true, $type, $dependency ) ) {
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

    /**
     * @param bool $allow
     * @param string $type - One of [ 'script', 'style' ].
     * @param _WP_Dependency $dependency
     * @return bool
     */
    public function can_use_dependency( bool $allow, string $type, _WP_Dependency $dependency ) : bool
    {
        return null === $dependency->ver;
    }
}
