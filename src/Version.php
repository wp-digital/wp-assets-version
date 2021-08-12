<?php

namespace Innocode\AssetsVersion;

/**
 * Class Version
 * @package Innocode\AssetsVersion
 */
class Version
{
    /**
     * @var string
     */
    protected $option;

    /**
     * Version constructor.
     * @param string $option
     */
    public function __construct( string $option )
    {
        $this->option = $option;
    }

    /**
     * @return string
     */
    public function get_option() : string
    {
        return $this->option;
    }

    public function bump()
    {
        update_option( $this->get_option(), static::generate() );
    }

    /**
     * @return string
     */
    public function __invoke() : string
    {
        return (string) get_option( $this->get_option(), '' );
    }

    /**
     * @return string
     */
    public static function generate() : string
    {
        return (string) apply_filters( 'innocode_assets_version', time() );
    }
}