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
        'is_default_night' => false,
        'night_class'      => 'ghost-night-mode',
        'switching_class'  => 'ghost-night-mode-switching',
        'toggle_selector'  => '.ghost-night-mode-toggle',
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
    }

    /**
     * Enqueue assets.
     */
    public static function wp_enqueue_scripts() {
        wp_enqueue_script( 'ghost-framework-night-mode', Ghost_Framework::get_url() . '/assets/js/night-mode.min.js', array( 'jquery' ), '@@theme_version', false );
        wp_localize_script( 'ghost-framework-night-mode', 'ghostFrameworkNightMode', self::$attributes );
    }
}
