<?php
/**
 * Templates
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework_Templates
 */
class Ghost_Framework_Templates {
    /**
     * All available templates.
     *
     * @var array
     */
    public static $registered_templates = array();

    /**
     * Add template.
     *
     * @param string  $hook_name - hook name.
     * @param string  $template_path - template path.
     * @param integer $priority - priority.
     */
    public static function add_template( $hook_name, $template_path, $priority = 10 ) {
        if ( ! isset( self::$registered_templates[ $hook_name ] ) ) {
            self::$registered_templates[ $hook_name ] = array();
        }

        self::$registered_templates[ $hook_name ][ $template_path ] = $priority;
    }

    /**
     * Remove template.
     *
     * @param string $hook_name - hook name.
     * @param string $template_path - template path.
     */
    public static function remove_template( $hook_name, $template_path = '' ) {
        if ( ! isset( self::$registered_templates[ $hook_name ] ) ) {
            return;
        }

        // Remove selected template.
        if ( $template_path && isset( self::$registered_templates[ $hook_name ][ $template_path ] ) ) {
            unset( self::$registered_templates[ $hook_name ][ $template_path ] );
        }

        // Remove all available templates, if template path was not specified.
        if ( ! $template_path ) {
            unset( self::$registered_templates[ $hook_name ] );
        }

        // Clean templates array if empty.
        if ( empty( self::$registered_templates[ $hook_name ] ) ) {
            unset( self::$registered_templates[ $hook_name ] );
        }
    }

    /**
     * Register all available hooks templates.
     */
    public static function init() {
        if ( empty( self::$registered_templates ) ) {
            return;
        }

        foreach ( self::$registered_templates as $hook_name => $data ) {
            add_action( $hook_name, 'Ghost_Framework_Templates::include_template' );
        }
    }

    /**
     * Include template.
     */
    public static function include_template() {
        if ( empty( self::$registered_templates ) ) {
            return;
        }

        $hook_name = current_action();

        if ( ! isset( self::$registered_templates[ $hook_name ] ) || empty( self::$registered_templates[ $hook_name ] ) ) {
            return;
        }

        $templates = self::$registered_templates[ $hook_name ];

        // Sort by priority.
        asort( $templates );

        foreach ( $templates as $template_path => $priority ) {
            get_template_part( $template_path );
        }
    }
}
