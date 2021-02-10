<?php
/**
 * Aspect ratio custom control for Kirki.
 *
 * @package @@theme_name/kirki-aspect-ratio-control
 */

/**
 * The custom control class
 *
 * @param array $controls - Kirki controls.
 * @return array
 */
function ghost_framework_customizer_register_control_aspect_ratio( $controls ) {
    if ( ! class_exists( 'Ghost_Framework_Kirki_Control_Base' ) ) {
        return $controls;
    }

    /**
     * Ghost_Framework_Kirki_Control_Aspect_Ratio class
     */
    class Ghost_Framework_Kirki_Control_Aspect_Ratio extends Ghost_Framework_Kirki_Control_Base {
        /**
         * The control type.
         *
         * @var string
         */
        public $type = 'aspect-ratio';

        /**
         * Render the control's content.
         * Verbatim copy from WP_Customize_Control->render_content.
         */
        protected function render_content() {
            $input_id       = '_customize-input-' . $this->id;
            $description_id = '_customize-description-' . $this->id;
            ?>
            <div class="customize-control-kirki-aspect-ratio">
                <label class="customize-control-title" for="<?php echo esc_attr( $input_id ); ?>-select"><?php echo esc_html( $this->label ); ?></label>
                <?php if ( ! empty( $this->description ) ) : ?>
                    <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
                <?php endif; ?>

                <select
                    id="<?php echo esc_attr( $input_id ); ?>-select"
                    class="customize-control-kirki-aspect-ratio-select"
                >
                    <option value="auto" <?php selected( $this->value() === 'auto' ); ?>><?php echo esc_attr__( 'Auto', '@@text_domain' ); ?></option>
                    <option value="16:9" <?php selected( $this->value() === '16:9' ); ?>><?php echo esc_attr__( 'Wide 16:9', '@@text_domain' ); ?></option>
                    <option value="21:9" <?php selected( $this->value() === '21:9' ); ?>><?php echo esc_attr__( 'Ultra Wide 21:9', '@@text_domain' ); ?></option>
                    <option value="4:3" <?php selected( $this->value() === '4:3' ); ?>><?php echo esc_attr__( 'TV 4:3', '@@text_domain' ); ?></option>
                    <option value="3:2" <?php selected( $this->value() === '3:2' ); ?>><?php echo esc_attr__( 'Classic Film 3:2', '@@text_domain' ); ?></option>
                    <option value="custom"><?php echo esc_attr__( 'Custom', '@@text_domain' ); ?></option>
                </select>

                <div class="customize-control-kirki-aspect-ratio-custom customize-control-kirki-aspect-ratio-hide">
                    <div>
                        <input
                            class="customize-control-kirki-aspect-ratio-custom-width"
                            placeholder="<?php echo esc_attr__( 'Width', '@@text_domain' ); ?>"
                            type="number"
                            min="0"
                        />
                    </div>
                    <div class="customize-control-kirki-aspect-ratio-custom-delimiter">:</div>
                    <div>
                        <input
                            class="customize-control-kirki-aspect-ratio-custom-height"
                            placeholder="<?php echo esc_attr__( 'Height', '@@text_domain' ); ?>"
                            type="number"
                            min="0"
                        />
                    </div>
                </div>

                <input
                    class="customize-control-kirki-aspect-ratio-hide customize-control-kirki-aspect-ratio-value"
                    id="<?php echo esc_attr( $input_id ); ?>"
                    <?php echo ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : ''; ?>
                    type="text"
                    value="<?php echo esc_attr( $this->value() ); ?>"
                    <?php $this->link(); ?>
                />
            </div>
            <?php
        }
    }

    // Register our custom control with Kirki.
    $controls['aspect-ratio']     = 'Ghost_Framework_Kirki_Control_Aspect_Ratio';

    return $controls;
}

add_action( 'kirki_control_types', 'ghost_framework_customizer_register_control_aspect_ratio' );

/**
 * Assets for Customizer.
 *
 * @return void
 */
function ghost_framework_customizer_aspect_ratio_assets() {
    wp_enqueue_style( 'ghost-framework-kirki-aspect-ratio-control', Ghost_Framework::get_url() . '/assets/css/customizer-aspect-ratio-control.min.css', array(), '@@theme_version' );
    wp_enqueue_script( 'ghost-framework-kirki-aspect-ratio-control', Ghost_Framework::get_url() . '/assets/js/customizer-aspect-ratio-control.min.js', array( 'jquery', 'customize-controls' ), '@@theme_version', true );
}

add_action( 'customize_controls_enqueue_scripts', 'ghost_framework_customizer_aspect_ratio_assets' );
