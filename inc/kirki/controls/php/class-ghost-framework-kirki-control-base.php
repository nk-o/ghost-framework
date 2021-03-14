<?php
/**
 * Customizer Controls Base.
 * Code Changed.
 *
 * Extend this in other controls.
 *
 * @package     Ghost_Framework_Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since       3.0.12
 */

/**
 * A base for controls.
 */
class Ghost_Framework_Kirki_Control_Base extends WP_Customize_Control {

    /**
     * Used to automatically generate all CSS output.
     *
     * @access public
     * @var array
     */
    public $output = array();

    /**
     * Data type
     *
     * @access public
     * @var string
     */
    public $option_type = 'theme_mod';

    /**
     * Option name (if using options).
     *
     * @access public
     * @var string
     */
    public $option_name = false;

    /**
     * The kirki_config we're using for this control
     *
     * @access public
     * @var string
     */
    public $kirki_config = 'global';

    /**
     * Whitelisting the "required" argument.
     *
     * @since 3.0.17
     * @access public
     * @var array
     */
    public $required = array();

    /**
     * Whitelisting the "preset" argument.
     *
     * @since 3.0.26
     * @access public
     * @var array
     */
    public $preset = array();

    /**
     * Whitelisting the "css_vars" argument.
     *
     * @since 3.0.28
     * @access public
     * @var string
     */
    public $css_vars = '';

    /**
     * Extra script dependencies.
     *
     * @since 3.1.0
     * @return array
     */
    public function kirki_script_dependencies() {
        return array();
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @access public
     */
    public function enqueue() {

        // The Ghost_Framework_Kirki plugin URL.
        $kirki_url = trailingslashit( Ghost_Framework_Kirki::$url );

        // Enqueue ColorPicker.
        wp_enqueue_script( 'wp-color-picker-alpha', trailingslashit( Ghost_Framework_Kirki::$url ) . 'assets/vendor/wp-color-picker-alpha/wp-color-picker-alpha.js', array( 'wp-color-picker', 'wp-i18n' ), '@@theme_version', true );
        wp_enqueue_style( 'wp-color-picker' );

        // Enqueue selectWoo.
        wp_enqueue_script( 'select-woo', trailingslashit( Ghost_Framework_Kirki::$url ) . 'assets/vendor/selectWoo/js/selectWoo.full.js', array( 'jquery' ), '1.0.1', true );
        wp_enqueue_style( 'select-woo', trailingslashit( Ghost_Framework_Kirki::$url ) . 'assets/vendor/selectWoo/css/selectWoo.css', array(), '1.0.1' );
        wp_enqueue_style( 'kirki-select-woo', trailingslashit( Ghost_Framework_Kirki::$url ) . 'assets/vendor/selectWoo/kirki.css', array(), '@@theme_version' );

        // Enqueue the script.
        wp_enqueue_script(
            'kirki-script',
            "{$kirki_url}controls/js/script.min.js",
            array(
                'jquery',
                'customize-base',
                'wp-editor',
                'wp-color-picker-alpha',
                'select-woo',
                'jquery-ui-button',
                'jquery-ui-datepicker',
            ),
            '@@theme_version',
            false
        );

        wp_localize_script(
            'kirki-script',
            'kirkiL10n',
            array(
                'isScriptDebug'        => ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ),
                'noFileSelected'       => esc_html__( 'No File Selected', '@@text_domain' ),
                'remove'               => esc_html__( 'Remove', '@@text_domain' ),
                'default'              => esc_html__( 'Default', '@@text_domain' ),
                'selectFile'           => esc_html__( 'Select File', '@@text_domain' ),
                'standardFonts'        => esc_html__( 'Standard Fonts', '@@text_domain' ),
                'googleFonts'          => esc_html__( 'Google Fonts', '@@text_domain' ),
                'defaultCSSValues'     => esc_html__( 'CSS Defaults', '@@text_domain' ),
                'defaultBrowserFamily' => esc_html__( 'Default Browser Font-Family', '@@text_domain' ),
            )
        );

        // Enqueue the style.
        wp_enqueue_style(
            'kirki-styles',
            "{$kirki_url}controls/css/styles.min.css",
            array(),
            '@@theme_version'
        );
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @see WP_Customize_Control::to_json()
     */
    public function to_json() {

        // Get the basics from the parent class.
        parent::to_json();

        // Default value.
        $this->json['default'] = $this->setting->default;
        if ( isset( $this->default ) ) {
            $this->json['default'] = $this->default;
        }

        // Required.
        $this->json['required'] = $this->required;

        // Output.
        $this->json['output'] = $this->output;

        // Value.
        $this->json['value'] = $this->value();

        // Choices.
        $this->json['choices'] = $this->choices;

        // The link.
        $this->json['link'] = $this->get_link();

        // The ID.
        $this->json['id'] = $this->id;

        // Translation strings.
        $this->json['l10n'] = $this->l10n();

        // The ajaxurl in case we need it.
        $this->json['ajaxurl'] = admin_url( 'admin-ajax.php' );

        // Input attributes.
        $this->json['inputAttrs'] = '';
        foreach ( $this->input_attrs as $attr => $value ) {
            $this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
        }

        // The kirki-config.
        $this->json['kirkiConfig'] = $this->kirki_config;

        // The option-type.
        $this->json['kirkiOptionType'] = $this->option_type;

        // The option-name.
        $this->json['kirkiOptionName'] = $this->option_name;

        // The preset.
        $this->json['preset'] = $this->preset;

        // The CSS-Variables.
        $this->json['css-var'] = $this->css_vars;
    }

    /**
     * Render the control's content.
     *
     * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
     *
     * Control content can alternately be rendered in JS. See WP_Customize_Control::print_template().
     *
     * @since 3.4.0
     */
    protected function render_content() {}

    /**
     * An Underscore (JS) template for this control's content (but not its container).
     *
     * Class variables for this control class are available in the `data` JS object;
     * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
     *
     * @see WP_Customize_Control::print_template()
     *
     * @access protected
     */
    protected function content_template() {}

    /**
     * Returns an array of translation strings.
     *
     * @access protected
     * @since 3.0.0
     * @return array
     */
    protected function l10n() {
        return array();
    }
}
