<?php
/**
 * Custom Breakpoints for Ghost Kit and Visual Portfolio plugins.
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework_Breakpoints
 */
class Ghost_Framework_Breakpoints {
    /**
     * Default attributes
     *
     * @var array
     */
    public static $default_attributes = array(
        'xs' => 320,
        'sm' => 576,
        'md' => 768,
        'lg' => 992,
        'xl' => 1200,
    );

    /**
     * Current attributes, will be used for overrides.
     *
     * @var array
     */
    public static $attributes = array();

    /**
     * Init filters.
     *
     * @param Array $attributes - additional attributes.
     *
     * @return String
     */
    public static function init( $attributes = array() ) {
        self::$attributes = array_merge(
            self::$default_attributes,
            $attributes
        );

        // Ghost Kit.
        // In this plugin we don't have an XS defined in this class, so breakpoints starts from 576 (SM in this class).
        add_filter( 'gkt_default_breakpoint_xs', array( __CLASS__, 'get_breakpoint_sm' ) );
        add_filter( 'gkt_default_breakpoint_sm', array( __CLASS__, 'get_breakpoint_md' ) );
        add_filter( 'gkt_default_breakpoint_md', array( __CLASS__, 'get_breakpoint_lg' ) );
        add_filter( 'gkt_default_breakpoint_lg', array( __CLASS__, 'get_breakpoint_xl' ) );

        // Visual Portfolio.
        add_filter( 'vpf_default_breakpoint_xs', array( __CLASS__, 'get_breakpoint_xs' ) );
        add_filter( 'vpf_default_breakpoint_sm', array( __CLASS__, 'get_breakpoint_sm' ) );
        add_filter( 'vpf_default_breakpoint_md', array( __CLASS__, 'get_breakpoint_md' ) );
        add_filter( 'vpf_default_breakpoint_lg', array( __CLASS__, 'get_breakpoint_lg' ) );
        add_filter( 'vpf_default_breakpoint_xl', array( __CLASS__, 'get_breakpoint_xl' ) );
    }

    /**
     * Change default XS breakpoint.
     *
     * @return int
     */
    public static function get_breakpoint_xs() {
        return self::$attributes['xs'];
    }

    /**
     * Change default SM breakpoint.
     *
     * @return int
     */
    public static function get_breakpoint_sm() {
        return self::$attributes['sm'];
    }

    /**
     * Change default MD breakpoint.
     *
     * @return int
     */
    public static function get_breakpoint_md() {
        return self::$attributes['md'];
    }

    /**
     * Change default LG breakpoint.
     *
     * @return int
     */
    public static function get_breakpoint_lg() {
        return self::$attributes['lg'];
    }

    /**
     * Change default XL breakpoint.
     *
     * @return int
     */
    public static function get_breakpoint_xl() {
        return self::$attributes['xl'];
    }
}
