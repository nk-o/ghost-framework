<?php
/**
 * The main Ghost_Framework_Kirki object
 *
 * @package     Ghost_Framework_Kirki
 * @category    Core
 * @author      Ari Stathopoulos (@aristath)
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Singleton class
 */
final class Ghost_Framework_Kirki_Toolkit {
    public $modules;

    /**
     * Holds the one, true instance of this object.
     *
     * @static
     * @access protected
     * @var object
     */
    protected static $instance = null;

    /**
     * Access the single instance of this class.
     *
     * @static
     * @access public
     * @return object Ghost_Framework_Kirki_Toolkit.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
