<?php
/**
 * Automatic preset scripts calculation for Ghost_Framework_Kirki controls.
 *
 * @package     Ghost_Framework_Kirki
 * @category    Modules
 * @author      Ari Stathopoulos (@aristath)
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since       3.0.26
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds styles to the customizer.
 */
class Ghost_Framework_Kirki_Modules_Preset {

    /**
     * The object instance.
     *
     * @static
     * @access private
     * @since 3.0.26
     * @var object
     */
    private static $instance;

    /**
     * Constructor.
     *
     * @access protected
     * @since 3.0.26
     */
    protected function __construct() {
        add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_controls_print_footer_scripts' ) );
    }

    /**
     * Gets an instance of this object.
     * Prevents duplicate instances which avoid artefacts and improves performance.
     *
     * @static
     * @access public
     * @since 3.0.26
     * @return object
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Enqueue scripts.
     *
     * @access public
     * @since 3.0.26
     */
    public function customize_controls_print_footer_scripts() {
        wp_enqueue_script( 'kirki-preset', trailingslashit( Ghost_Framework_Kirki::$url ) . 'modules/preset/preset.min.js', array( 'jquery' ), '@@theme_version', false );
    }
}
