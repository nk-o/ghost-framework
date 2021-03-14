<?php
/**
 * Plugin Name:   Kirki Customizer Framework
 * Plugin URI:    https://kirki.org
 * Description:   The Ultimate WordPress Customizer Framework
 * Author:        Ari Stathopoulos (@aristath)
 * Author URI:    https://aristath.github.io
 * Version:       3.0.44
 * Text Domain:   kirki
 * Requires WP:   4.9
 * Requires PHP:  5.3
 * GitHub Plugin URI: aristath/kirki
 * GitHub Plugin URI: https://github.com/aristath/kirki
 * Code Change by nkDev
 *
 * @package   Kirki
 * @category  Core
 * @author    Ari Stathopoulos (@aristath)
 * @copyright Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license   https://opensource.org/licenses/MIT
 * @since     1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// No need to proceed if Ghost_Framework_Kirki already exists.
if ( class_exists( 'Ghost_Framework_Kirki' ) ) {
    return;
}

// Include the autoloader.
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-kirki-autoload.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude

new Ghost_Framework_Kirki_Autoload();

if ( ! defined( 'KIRKI_PLUGIN_FILE' ) ) {
    define( 'KIRKI_PLUGIN_FILE', __FILE__ );
}

// Make sure the path is properly set.
Ghost_Framework_Kirki::$path = Ghost_Framework::get_path() . '/inc/kirki'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
Ghost_Framework_Kirki_Init::set_url();

new Ghost_Framework_Kirki_Controls();

if ( ! function_exists( 'Ghost_Framework_Kirki' ) ) {
    /**
     * Returns an instance of the Ghost_Framework_Kirki object.
     */
    function ghost_framework_kirki() {
        $kirki = Ghost_Framework_Kirki_Toolkit::get_instance();
        return $kirki;
    }
}

// Start Ghost_Framework_Kirki.
global $kirki;
$kirki = ghost_framework_kirki();

// Instantiate the modules.
$kirki->modules = new Ghost_Framework_Kirki_Modules();

Ghost_Framework_Kirki::$url = Ghost_Framework::get_url() . '/inc/kirki/';

// Instantiate classes.
new Ghost_Framework_Kirki();

// Include the ariColor library.
require_once wp_normalize_path( dirname( __FILE__ ) . '/lib/class-aricolor.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude

// Add an empty config for global fields.
Ghost_Framework_Kirki::add_config( '' );

// Assets for Customizer.
function ghost_framework_kirki_styles() {
    global $wp_customize;

    if ( isset( $wp_customize ) ) {
        wp_enqueue_style( 'ghost-framework-kirki', Ghost_Framework_Kirki::$url . 'assets/css/customizer.min.css', array(), '@@theme_version' );
    }
}

add_action( 'admin_enqueue_scripts', 'ghost_framework_kirki_styles' );
