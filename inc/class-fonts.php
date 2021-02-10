<?php
/**
 * Fonts for default typography
 *
 * @package @@theme_name/ghost
 */

/**
 * GhostKit_Fonts
 */
class Ghost_Framework_Fonts {
    /**
     * GhostKit_Fonts constructor.
     */
    public function __construct() {
        if ( ! class_exists( 'GhostKit' ) && ! class_exists( 'GhostKit_Typography' ) ) {
            add_filter( 'ghost_framework_fonts_list', array( $this, 'add_google_fonts' ) );
            add_filter( 'ghost_framework_fonts_list', array( $this, 'add_default_site_fonts' ), 9 );
    
            // enqueue fonts.
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_all_fonts_assets' ), 12 );
            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_all_fonts_assets' ), 12 );
        }
    }

    /**
     * Enqueue frontend & editor assets
     */
    public function enqueue_all_fonts_assets() {
        $fonts = $this->get_font_loader_list();

        if ( is_admin() || ! empty( $fonts ) ) {
            $families = array();

            foreach ( $fonts['google-fonts'] as $font => $font_data ) {
                $family = $font;

                if ( isset( $font_data['widths'] ) && ! empty( $font_data['widths'] ) ) {
                    $weights = array();

                    foreach ( $font_data['widths'] as $weight ) {
                        $weights[] = $weight;
                    }

                    $family .= ':' . implode( ',', $weights );
                }

                $families[] = $family;
            }

            $query_args = array(
                'family'  => implode( '|', $families ),
                'display' => 'swap',
            );

            wp_enqueue_style( 'ghost-framework-fonts-google', add_query_arg( $query_args, 'https://fonts.googleapis.com/css' ), array(), '@@theme_version' );
        }
    }

    /**
     * Create Font Loader List for webfont-loader.
     *
     * @return array - Font Loader List.
     */
    public function get_font_loader_list() {
        $fonts_list         = apply_filters( 'ghost_framework_fonts_list', array() );
        $default_typography = apply_filters( 'ghost_framework_typography', array() );
        $fonts              = array();
        $unique_fonts       = array();
        $webfont_list       = array();
        $additional_font_weights = array();

        // Default Typography.
        if ( ! empty( $default_typography ) ) {
            // Go through all defaults and prepare full fonts list.
            foreach ( $default_typography as $key => $typography ) {
                $result = array();

                // Default data.
                if (
                    isset( $typography['defaults']['font-family'] ) &&
                    ! empty( $typography['defaults']['font-family'] ) &&
                    isset( $typography['defaults']['font-family-category'] ) &&
                    ! empty( $typography['defaults']['font-family-category'] )
                ) {
                    $weight = '';
                    if ( isset( $typography['defaults']['font-weight'] ) && ! empty( $typography['defaults']['font-weight'] ) ) {
                        $weight = $typography['defaults']['font-weight'];
                    }

                    // TODO: add additional_font_weights option support for typography React component.
                    if ( isset( $typography['additional_font_weights'] ) && ! empty( $typography['additional_font_weights'] ) ) {
                        $additional_font_weights[ $key ] = $typography['additional_font_weights'];
                    }

                    $result = array(
                        'family'     => $typography['defaults']['font-family-category'],
                        'label'      => $typography['defaults']['font-family'],
                        'weight'     => $weight,
                        'typography' => $key,
                    );
                }

                // Save result.
                if ( isset( $result['family'] ) && $result['family'] && isset( $result['label'] ) && $result['label'] ) {
                    $fonts[ $key ] = $result;
                }
            }
        }

        // clear array to unique.
        $unique_fonts = array_map( 'unserialize', array_unique( array_map( 'serialize', $fonts ) ) );

        foreach ( $unique_fonts as $font ) {
            if ( isset( $font['family'] ) && ! empty( $font['family'] ) && array_key_exists( $font['family'], $fonts_list ) ) {
                foreach ( $fonts_list[ $font['family'] ]['fonts'] as $find_font ) {
                    if ( $font['label'] === $find_font['name'] ) {
                        $weights  = array();
                        $weight   = ( isset( $font['weight'] ) && ! empty( $font['weight'] ) ) ? $font['weight'] : '';
                        $widths   = ( isset( $find_font['widths'] ) && ! empty( $find_font['widths'] ) ) ? $find_font['widths'] : '';
                        $category = ( isset( $find_font['category'] ) && ! empty( $find_font['category'] ) ) ? $find_font['category'] : '';
                        $subsets  = ( isset( $find_font['subsets'] ) && ! empty( $find_font['subsets'] ) ) ? $find_font['subsets'] : '';

                        if ( isset( $additional_font_weights ) && ! empty( $additional_font_weights ) ) {
                            foreach ( $additional_font_weights as $key => $additional_font_weight ) {
                                if ( $key === $font['typography'] ) {
                                    $font_weights = $additional_font_weight;
                                }
                            }
                        }

                        if ( isset( $font_weights ) && ! empty( $font_weights ) && is_array( $font_weights ) ) {
                            $insert_weights = array();
                            if ( '' !== $weight ) {
                                $weight           = str_replace( 'i', '', $weight );
                                $insert_weights[] = $weight;
                                $insert_weights[] = $weight . 'i';
                            }
                            $insert_weights = array_merge( $insert_weights, $font_weights );
                            foreach ( $insert_weights as $insert_weight ) {
                                if ( array_search( $insert_weight, $widths, true ) !== false ) {
                                    $weights[] = $insert_weight;
                                }
                            }
                        } elseif ( '' !== $weight ) {
                            $weight = str_replace( 'i', '', $weight );

                            if ( '600' !== $weight &&
                                '700' !== $weight &&
                                '800' !== $weight &&
                                '900' !== $weight ) {

                                $insert_weights = array(
                                    $weight,
                                    $weight . 'i',
                                    '700',
                                    '700i',
                                );

                                foreach ( $insert_weights as $insert_weight ) {
                                    if ( array_search( $insert_weight, $widths, true ) !== false ) {
                                        $weights[] = $insert_weight;
                                    }
                                }
                            } else {
                                $insert_weights = array(
                                    $weight,
                                    $weight . 'i',
                                );

                                foreach ( $insert_weights as $insert_weight ) {
                                    if ( array_search( $insert_weight, $widths, true ) !== false ) {
                                        $weights[] = $insert_weight;
                                    }
                                }
                            }
                        } else {
                            $insert_weights = array(
                                '400',
                                '400i',
                                '700',
                                '700i',
                            );

                            foreach ( $insert_weights as $insert_weight ) {
                                if ( array_search( $insert_weight, $widths, true ) !== false ) {
                                    $weights[] = $insert_weight;
                                }
                            }
                        }

                        if (
                            isset( $webfont_list[ $font['family'] ][ $font['label'] ]['widths'] ) &&
                            ! empty( $webfont_list[ $font['family'] ][ $font['label'] ]['widths'] ) &&
                            is_array( $webfont_list[ $font['family'] ][ $font['label'] ]['widths'] )
                        ) {
                            $weights = array_values( array_unique( array_merge_recursive( $webfont_list[ $font['family'] ][ $font['label'] ]['widths'], $weights ) ) );
                        }

                        $webfont_list[ $font['family'] ][ $font['label'] ] = array(
                            'widths'   => $weights,
                            'category' => $category,
                            'subsets'  => $subsets,
                        );
                    }
                }
            }
        }

        return $webfont_list;
    }

    /**
     * Add Default fonts list.
     *
     * @param array $fonts - fonts list.
     *
     * @return array
     */
    public function add_default_site_fonts( $fonts ) {
        $fonts['default'] = array(
            'name'  => esc_html__( 'Default Fonts Site', '@@text_domain' ),
            'fonts' => array(
                array(
                    'name'     => 'Default Site Font',
                    'widths'   => array(
                        '',
                        '400',
                        '700',
                    ),
                    'category' => 'sans-serif',
                ),
            ),
        );
        return $fonts;
    }

    /**
     * Add Google fonts list.
     *
     * @param array $fonts - fonts list.
     *
     * @return array
     */
    public function add_google_fonts( $fonts ) {
        $result = get_transient( 'ghost_framework_google_fonts_list' );

        if ( ! $result ) {
            $result = array();

            ob_start();
            include Ghost_Framework::get_path() . '/inc/google-fonts/webfonts.json';
            $fonts_json = ob_get_clean();

            $fonts_object = json_decode( $fonts_json, true );

            foreach ( $fonts_object['items'] as $font ) {
                $weights = array();
                foreach ( $font['variants'] as $variant ) {
                    $variant = str_replace( 'italic', 'i', $variant );

                    switch ( $variant ) {
                        case 'i':
                            $variant = '400i';
                            break;
                        case 'regular':
                            $variant = '400';
                            break;
                    }

                    $weights[] = $variant;
                }
                $result[] = array(
                    'name'     => $font['family'],
                    'widths'   => $weights,
                    'category' => $font['category'],
                    'subsets'  => $font['subsets'],
                );
            }

            if ( ! empty( $result ) ) {
                set_transient( 'ghost_framework_google_fonts_list', $result, DAY_IN_SECONDS );
            }
        }

        $fonts['google-fonts'] = array(
            'name'  => 'Google Fonts',
            'fonts' => $result,
        );

        return $fonts;
    }
}
new Ghost_Framework_Fonts();
