<?php
/**
 * Night Mode
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework_Night_Mode
 */
class Ghost_Framework_Night_Mode {
    /**
     * Default attributes
     *
     * @var array
     */
    public static $default_attributes = array(
        'default'           => 'auto',
        'is_editor'         => false,
        'use_local_storage' => true,
        'night_class'       => 'ghost-night-mode',
        'switching_class'   => 'ghost-night-mode-switching',
        'toggle_selector'   => '.ghost-night-mode-toggle',
    );

    /**
     * Current attributes, will be used for script.
     *
     * @var array
     */
    public static $attributes = array();

    /**
     * Enable night mode.
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

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
        add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );

        // TODO: We can't currently fix the Rocket Loader conflict, because WordPress
        // does not allow us to add an attribute to localized scripts.
        // This code example is working for the main script, but it's localized data will be filtered by rocket loader and it fails.
        // add_filter( 'script_loader_tag', array( __CLASS__, 'rocket_loader_filter' ), 10, 2 );

        add_action( 'autoptimize_filter_js_exclude', array( __CLASS__, 'autoptimize_filter_js_exclude' ) );
    }

    /**
     * Enqueue assets.
     */
    public static function wp_enqueue_scripts() {
        wp_enqueue_script( 'ghost-framework-night-mode', Ghost_Framework::get_url() . '/assets/js/night-mode.min.js', array( 'jquery' ), '@@theme_version', false );
        wp_localize_script( 'ghost-framework-night-mode', 'ghostFrameworkNightMode', self::$attributes );
    }

    /**
     * Enqueue block editor assets.
     */
    public static function enqueue_block_editor_assets() {
        self::$attributes['is_editor'] = true;

        wp_enqueue_script( 'ghost-framework-night-mode', Ghost_Framework::get_url() . '/assets/js/night-mode.min.js', array( 'jquery', 'wp-element', 'wp-plugins', 'wp-data' ), '@@theme_version', false );
        wp_localize_script( 'ghost-framework-night-mode', 'ghostFrameworkNightMode', self::$attributes );
    }

    /**
     * Disable rocket loader from the night mode script, since we have to add
     * night mode classname to the <html> tag immediately.
     *
     * @param String $tag    - tag output.
     * @param String $handle - script handle.
     *
     * @return String
     */
    public static function rocket_loader_filter( $tag, $handle ) {
        if ( 'ghost-framework-night-mode' === $handle ) {
            $tag = str_replace( '<script', '<script data-cfasync="false"', $tag );
        }
    
        return $tag;
    }

    /**
     * Exclude night mode script from autoptimize.
     *
     * @param string|array $exclude_js - exclude JS files.
     *
     * @return string|array
     */
    public static function autoptimize_filter_js_exclude( $exclude_js ) {
        if ( is_string( $exclude_js ) ) {
            $exclude_js .= ( $exclude_js ? ',' : '' ) . 'ghost-framework-night-mode-js';
        }

        return $exclude_js;
    }
}
