<?php
/**
 * Automatic field-dependencies scripts calculation for Ghost_Framework_Kirki controls.
 *
 * @package     Ghost_Framework_Kirki
 * @category    Modules
 * @author      Ari Stathopoulos (@aristath)
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds styles to the customizer.
 */
class Ghost_Framework_Kirki_Modules_Field_Dependencies {

    /**
     * The object instance.
     *
     * @static
     * @access private
     * @since 3.0.0
     * @var object
     */
    private static $instance;

    /**
     * Constructor.
     *
     * @access protected
     * @since 3.0.0
     */
    protected function __construct() {
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'field_dependencies' ) );
    }

    /**
     * Gets an instance of this object.
     * Prevents duplicate instances which avoid artefacts and improves performance.
     *
     * @static
     * @access public
     * @since 3.0.0
     * @return object
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Enqueues the field-dependencies script
     * and adds variables to it using the wp_localize_script function.
     * The rest is handled via JS.
     *
     * @access public
     * @return void
     */
    public function field_dependencies() {
        wp_enqueue_script( 'kirki-field-dependencies', trailingslashit( Ghost_Framework_Kirki::$url ) . 'modules/field-dependencies/field-dependencies.min.js', array( 'jquery', 'customize-base', 'customize-controls' ), '@@theme_version', true );
    }
}
