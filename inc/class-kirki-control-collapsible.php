<?php
/**
 * Collapsible custom control for Kirki.
 *
 * @package @@theme_name/kirki-collapsible-control
 */

/**
 * The custom control class
 *
 * @param array $controls - Kirki controls.
 * @return array
 */
function ghost_framework_customizer_register_control_collapsible( $controls ) {
    if ( ! class_exists( 'Ghost_Framework_Kirki_Control_Base' ) ) {
        return $controls;
    }

    /**
     * Ghost_Framework_Kirki_Control_Collapsible class
     */
    class Ghost_Framework_Kirki_Control_Collapsible extends Ghost_Framework_Kirki_Control_Base {
        /**
         * The control type.
         *
         * @var string
         */
        public $type = 'collapsible';

        /**
         * Whitelisting the "expanded" argument.
         *
         * @var array
         */
        public $expanded = array();

        /**
         * Refresh the parameters passed to the JavaScript via JSON.
         *
         * @see WP_Customize_Control::to_json()
         */
        public function to_json() {
            // Get the basics from the parent class.
            parent::to_json();

            // Collapsed.
            $this->json['expanded'] = $this->expanded;
        }

        /**
         * Render the control's content.
         * Verbatim copy from WP_Customize_Control->render_content.
         */
        protected function render_content() {
            ?>
            <label class="customize-control-kirki-collapsible <?php echo esc_attr( $this->expanded ? 'customize-control-kirki-collapsible-expanded' : '' ); ?>">
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
                <?php endif; ?>
            </label>
            <?php
        }
    }

    /**
     * Ghost_Framework_Kirki_Control_Collapsible_End class
     */
    class Ghost_Framework_Kirki_Control_Collapsible_End extends Ghost_Framework_Kirki_Control_Base {
        /**
         * The control type.
         *
         * @var string
         */
        public $type = 'collapsible_end';

        /**
         * Render the control's content.
         * Verbatim copy from WP_Customize_Control->render_content.
         */
        protected function render_content() {
            ?>
            <label class="customize-control-kirki-collapsible-end"></label>
            <?php
        }
    }

    // Register our custom control with Kirki.
    $controls['collapsible']     = 'Ghost_Framework_Kirki_Control_Collapsible';
    $controls['collapsible_end'] = 'Ghost_Framework_Kirki_Control_Collapsible_End';

    return $controls;
}

add_action( 'kirki_control_types', 'ghost_framework_customizer_register_control_collapsible' );

/**
 * Assets for Customizer.
 *
 * @return void
 */
function ghost_framework_customizer_collapsible_assets() {
    wp_enqueue_style( 'ghost-framework-kirki-collapsible-control', Ghost_Framework::get_url() . '/assets/css/customizer-collapsible-control.min.css', array(), '@@theme_version' );
    wp_enqueue_script( 'ghost-framework-kirki-collapsible-control', Ghost_Framework::get_url() . '/assets/js/customizer-collapsible-control.min.js', array( 'jquery', 'customize-controls' ), '@@theme_version', true );
}

add_action( 'customize_controls_enqueue_scripts', 'ghost_framework_customizer_collapsible_assets' );
