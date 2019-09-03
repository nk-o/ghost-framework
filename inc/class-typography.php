<?php
/**
 * Typography
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework_Typography
 */
class Ghost_Framework_Typography {
    /**
     * Ghost_Framework_Typography constructor.
     */
    public function __construct() {
        if ( ! class_exists( 'GhostKit' ) && ! class_exists( 'GhostKit_Typography' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_typography_assets' ), 100 );
            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_typography_assets' ), 100 );
        }
    }

    /**
     * Enqueue Typography assets to editor and front end.
     */
    public function enqueue_typography_assets() {
        $typography_css = $this->generate_typography_styles();
        $css = '';

        if ( isset( $typography_css ) && ! empty( $typography_css ) && is_array( $typography_css ) ) {
            if ( ! is_admin() && isset( $typography_css['front'] ) && ! empty( $typography_css['front'] ) ) {
                $css = $typography_css['front'];
            }
            if ( function_exists( 'register_block_type' ) && is_admin() && isset( $typography_css['editor'] ) && ! empty( $typography_css['editor'] ) ) {
                $css = $typography_css['editor'];
            }
        }
        wp_register_style( 'ghost-framework-typography', false );
        wp_enqueue_style( 'ghost-framework-typography' );
        wp_add_inline_style( 'ghost-framework-typography', $css );
    }

    /**
     * Generate Typography Styles.
     *
     * @return array - Typography Css.
     */
    public function generate_typography_styles() {
        $typography_prepeare_styles = array();
        $typography_css = array(
            'editor' => '',
            'front' => '',
        );
        $default_typography = apply_filters( 'ghost_framework_typography', array() );

        if ( $this->is_exist( $default_typography ) ) {

            foreach ( $default_typography as $key => $typography ) {
                if ( $this->is_exist( $typography['output'] ) ) {
                    $typography_prepeare_styles[ $key ] = array(
                        'style-properties' => $typography['defaults'],
                        'output' => $typography['output'],
                    );
                }
            }

            // Collect all the styles for further transfer to the inline file on the edit or front page.
            foreach ( $typography_prepeare_styles as $typography_prepeare_style ) {
                if ( ( $this->is_exist( $typography_prepeare_style['output'] ) && is_array( $typography_prepeare_style['output'] ) ) &&
                    ( $this->is_exist( $typography_prepeare_style['style-properties'], 'font-family' ) ||
                        $this->is_exist( $typography_prepeare_style['style-properties'], 'font-size' ) ||
                        $this->is_exist( $typography_prepeare_style['style-properties'], 'font-weight' ) ||
                        $this->is_exist( $typography_prepeare_style['style-properties'], 'line-height' ) ||
                        $this->is_exist( $typography_prepeare_style['style-properties'], 'letter-spacing' )
                    ) ) {
                    foreach ( $typography_prepeare_style['output'] as $output ) {
                        if ( $this->is_exist( $output['selectors'] ) ) {
                            $typography_styles = '';
                            $typography_styles .= $output['selectors'] . '{';

                            if ( $this->is_exist( $typography_prepeare_style['style-properties'], 'font-family' ) ) {
                                $typography_styles .= 'font-family: ' . esc_attr( $typography_prepeare_style['style-properties']['font-family'] ) . ';';
                            }
                            if ( $this->is_exist( $typography_prepeare_style['style-properties'], 'font-size' ) ) {
                                $typography_styles .= 'font-size: ' . esc_attr( $typography_prepeare_style['style-properties']['font-size'] ) . ';';
                            }
                            if ( $this->is_exist( $typography_prepeare_style['style-properties'], 'font-weight' ) ) {
                                $font_weight = $typography_prepeare_style['style-properties']['font-weight'];
                                if ( false !== strpos( $font_weight, 'i' ) ) {
                                    $font_weight = str_replace( 'i', '', $font_weight );
                                    $typography_styles .= 'font-style: italic;';
                                } else {
                                    $typography_styles .= 'font-style: normal;';
                                }
                                $typography_styles .= 'font-weight: ' . esc_attr( $font_weight ) . ';';
                            }
                            if ( $this->is_exist( $typography_prepeare_style['style-properties'], 'line-height' ) ) {
                                $typography_styles .= 'line-height: ' . esc_attr( $typography_prepeare_style['style-properties']['line-height'] ) . ';';
                            }
                            if ( $this->is_exist( $typography_prepeare_style['style-properties'], 'letter-spacing' ) ) {
                                $typography_styles .= 'letter-spacing: ' . esc_attr( $typography_prepeare_style['style-properties']['letter-spacing'] ) . ';';
                            }
                            $typography_styles .= '}';

                            if ( isset( $output['editor'] ) && true === $output['editor'] ) {
                                $typography_css['editor'] .= $typography_styles;
                            } else {
                                $typography_css['front'] .= $typography_styles;
                            }
                        }
                    }
                }
            }
        }

        return $typography_css;
    }

    /**
     * Check value on the existence and emptiness.
     *
     * @param void   $value - Checking value.
     * @param bool   $attribute - Checking attribute of Array Value.
     * @param string $mode - Full or isset for partial check.
     * @return bool  $value - True or false.
     */
    public function is_exist( $value, $attribute = false, $mode = 'full' ) {
        $check = false;
        if ( $attribute ) {
            if ( 'full' === $mode && isset( $value[ $attribute ] ) && ! empty( $value[ $attribute ] ) ) {
                $check = true;
            }
            if ( 'isset' === $mode && isset( $value[ $attribute ] ) ) {
                $check = true;
            }
        } else {
            if ( 'full' === $mode ) {
                $check = ( isset( $value ) && ! empty( $value ) ) ? true : false;
            }
            if ( 'isset' === $mode ) {
                $check = ( isset( $value ) ) ? true : false;
            }
        }
        return $check;
    }
}
new Ghost_Framework_Typography();
