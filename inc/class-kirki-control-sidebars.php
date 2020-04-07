<?php
/**
 * Sidebars selector custom control for Kirki.
 *
 * @package @@theme_name/kirki-sidebars-control
 */

/**
 * The custom control class
 *
 * @param array $controls - Kirki controls.
 * @return array
 */
function ghost_framework_customizer_register_control_sidebars( $controls ) {
    if ( ! class_exists( 'Ghost_Framework_Kirki_Control_Base' ) ) {
        return $controls;
    }

    /**
     * Ghost_Framework_Kirki_Sidebars_Control class
     */
    class Ghost_Framework_Kirki_Sidebars_Control extends Ghost_Framework_Kirki_Control_Base {
        /**
         * Render control type name
         *
         * @var string
         */
        public $type = 'sidebars';

        /**
         * Render control
         *
         * @return void
         */
        public function render_content() {
            $sidebar_options = $GLOBALS['wp_registered_sidebars'];
            $input_id         = '_customize-input-' . $this->id;
            ?>
            <?php if ( $this->label ) : ?>
                <label class="customize-control-title" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $this->label ); ?></label>
            <?php endif; ?>
            <?php if ( $this->description ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php endif; ?>
            <select <?php $this->link(); ?> id="<?php echo esc_attr( $input_id ); ?>">
                <option value="" <?php echo selected( $this->value(), '', false ); ?>><?php echo esc_html__( '-- Select sidebar --', '@@text_domain' ); ?></option>
                <?php foreach ( $sidebar_options as $option ) : ?>
                    <option value="<?php echo esc_attr( $option['id'] ); ?>" <?php echo selected( $this->value(), $option['id'], false ); ?>><?php echo esc_html( $option['name'] ); ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }

    // Register our custom control with Kirki.
    $controls['sidebars'] = 'Ghost_Framework_Kirki_Sidebars_Control';
    return $controls;
}

add_action( 'kirki_control_types', 'ghost_framework_customizer_register_control_sidebars' );
