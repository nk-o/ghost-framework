<?php
/**
 * Ghost - nK Themes Framework
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework
 */
class Ghost_Framework {
    /**
     * Framework path.
     *
     * @var string
     */
    private static $framework_path = '';

    /**
     * Framework url.
     *
     * @var string
     */
    private static $framework_url = '';

    /**
     * Registered options array.
     *
     * @var array
     */
    private static $registered_options = array();

    /**
     * Registered options array.
     *
     * @var array
     */
    private static $typography = array();

    /**
     * Body class.
     *
     * @var string
     */
    private static $body_class = '';

    /**
     * Admin body class.
     *
     * @var string
     */
    private static $admin_body_class = '';

    /**
     * TGMPA plugins.
     *
     * @var array
     */
    private static $tgmpa_plugins = array();

    /**
     * TGMPA config.
     *
     * @var array
     */
    private static $tgmpa_config = array();

    /**
     * SCSS files available for compilation.
     *
     * @var array
     */
    private static $scss_files = array();

    /**
     * Ghost_Framework constructor.
     *
     * @param array $args - arguments for framework.
     */
    public function __construct( $args = array() ) {
        if ( isset( $args['path'] ) ) {
            self::$framework_path = $args['path'];
            self::$framework_url = $args['url'];

            // Kirki Fallback Class.
            $classes_include = array(
                'kirki/kirki.php',
                'class-kirki-control-aspect-ratio.php',
                'class-kirki-control-sidebars.php',
                'class-kirki-control-collapsible.php',
                'class-fonts.php',
                'class-typography.php',
                'class-brand-svg.php',
                'class-night-mode.php',
                'class-breakpoints.php',
                'class-templates.php',
            );
            foreach ( $classes_include as $inc ) {
                require_once self::$framework_path . '/inc/' . $inc;
            }

            // init.
            self::init();
        }
    }

    /**
     * Check Theme active.
     *
     * @return bool
     */
    public static function is_theme_active() {
        return ( function_exists( 'nk_theme' ) && ( nk_theme()->theme_dashboard()->is_envato_hosted || nk_theme()->theme_dashboard()->activation()->active ) );
    }

    /**
     * Get framework path.
     */
    public static function get_path() {
        return self::$framework_path;
    }

    /**
     * Get framework url.
     */
    public static function get_url() {
        return self::$framework_url;
    }

    /**
     * Init framework
     */
    public static function init() {
        // add body classes.
        add_filter( 'body_class', array( __CLASS__, 'filter_body_class' ) );
        add_filter( 'admin_body_class', array( __CLASS__, 'filter_admin_body_class' ) );

        // admin styles.
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

        // add registered Kirki options.
        add_action( 'after_setup_theme', array( __CLASS__, 'maybe_add_kirki_options' ), 9 );

        // add registered Typography.
        add_filter( 'gkt_custom_typography', array( __CLASS__, 'registered_typography' ) );
        if ( ! class_exists( 'GhostKit' ) && ! class_exists( 'GhostKit_Typography' ) ) {
            add_filter( 'ghost_framework_typography', array( __CLASS__, 'registered_typography' ) );
        }
        // register tgmpa.
        add_action( 'tgmpa_register', array( __CLASS__, 'action_tgmpa_register' ) );

        // compile scss.
        add_action( 'customize_preview_init', array( __CLASS__, 'maybe_compile_scss' ) );
        add_action( 'customize_save_after', array( __CLASS__, 'maybe_compile_scss' ) );

        // Init templates loader.
        add_action( 'init', 'Ghost_Framework_Templates::init' );

        // Visual Composer as theme.
        add_action( 'vc_before_init', array( __CLASS__, 'action_vc_set_as_theme' ) );

        // Revolution Slider as theme.
        add_action( 'init', array( __CLASS__, 'action_rev_set_as_theme' ) );
    }

    /**
     * Admin assets.
     */
    public static function admin_enqueue_scripts() {
        wp_enqueue_style( 'ghost-framework-admin', self::$framework_url . '/assets/css/admin.min.css', array(), '@@theme_version' );
    }

    /**
     * Add TGMPA plugins and config
     *
     * @param array      $plugins - plugins data.
     * @param array|bool $config - tgmpa config.
     */
    public static function add_tgmpa( $plugins, $config = false ) {
        require_once self::$framework_path . '/lib/class-tgm-plugin-activation.php';

        if ( ! $config ) {
            $config = array(
                'domain'       => '@@text_domain',
                'default_path' => '',
                'has_notices'  => true,
                'message'      => '',
            );
        }

        self::$tgmpa_plugins = $plugins;
        self::$tgmpa_config = $config;
    }

    /**
     * Add Mega menu checkbox in nav editor.
     *
     * @param boolean|array $data - additional data.
     */
    public static function add_mega_menu( $data = false ) {
        require_once self::$framework_path . '/inc/class-menu-backend-walker.php';

        new Ghost_Framework_Backend_Menu( $data );
    }

    /**
     * Action for tgmpa_register
     */
    public static function action_tgmpa_register() {
        if ( function_exists( 'tgmpa' ) && self::$tgmpa_plugins && count( self::$tgmpa_plugins ) ) {
            tgmpa( self::$tgmpa_plugins, self::$tgmpa_config );
        }
    }

    /**
     * Add Typography
     *
     * @param array $args - typography data.
     */
    public static function add_typography( $args ) {
        self::$typography[] = $args;
    }

    /**
     * Typography registration in the GhostKit plugin and by default in the ghost_framework_typography filter.
     *
     * @param array $typography - typography array.
     * @return array
     */
    public static function registered_typography( $typography ) {
        if ( ! empty( self::$typography ) && is_array( self::$typography ) ) {
            foreach ( self::$typography as $typography_item ) {
                $typography = array_replace_recursive( $typography, $typography_item );
            }
        }

        return $typography;
    }

    /**
     * Add registered Kirki options
     */
    public static function maybe_add_kirki_options() {
        if ( ! empty( self::$registered_options ) ) {
            foreach ( self::$registered_options as $option ) {
                // add field.
                Ghost_Framework_Kirki::add_field( '@@text_domain', $option );
            }
        }
    }

    /**
     * Add options field
     *
     * @param array $args - field data.
     */
    public static function add_field( $args ) {
        if ( isset( $args ) && is_array( $args ) ) {
            self::$registered_options[ $args['settings'] ] = $args;
        }
    }

    /**
     * Add options config
     *
     * @param array $args - config data.
     */
    public static function add_config( $args ) {
        Ghost_Framework_Kirki::add_config( '@@text_domain', $args );
    }

    /**
     * Add options panel
     *
     * @param string $name - panel name.
     * @param array  $args - panel data.
     */
    public static function add_panel( $name, $args ) {
        Ghost_Framework_Kirki::add_panel( $name, $args );
    }

    /**
     * Add options section
     *
     * @param string $name - section name.
     * @param array  $args - section data.
     */
    public static function add_section( $name, $args ) {
        Ghost_Framework_Kirki::add_section( $name, $args );
    }

    /**
     * Get option value
     *
     * @param string $name - option name.
     *
     * @return mixed
     */
    public static function get_option( $name ) {
        $value = Ghost_Framework_Kirki::get_option( '@@theme_name', $name );

        if ( 'on' === $value ) {
            $value = true;
        } elseif ( 'off' === $value ) {
            $value = false;
        }

        return $value;
    }

    /**
     * Get theme mod
     * Work with theme options and ACF custom fields
     *
     * @param string $name - option name.
     * @param bool   $use_meta - use metaboxes.
     * @param int    $post_id - post ID metaboxes.
     * @param string $meta_name - metabox name.
     *
     * @return null
     */
    public static function get_theme_mod( $name = null, $use_meta = false, $post_id = null, $meta_name = null ) {
        $value = null;

        // try to get value from meta box.
        if ( $use_meta ) {
            $value = self::get_metabox( $meta_name ? $meta_name : $name, $post_id );
        }

        // get value from options.
        if ( ( null === $value || 'default' === $value ) ) {
            $value = self::get_option( $name );
        }

        return $value;
    }

    /**
     * Get ACF metabox.
     *
     * @param string $name - acf name.
     * @param int    $post_id - post id.
     *
     * @return null
     */
    public static function get_metabox( $name = null, $post_id = null ) {
        $value = null;

        // get Post ID of Shop Page.
        if (
            function_exists( 'is_shop' ) && is_shop() ||
            function_exists( 'is_product_category' ) && is_product_category() ||
            function_exists( 'is_product_tag' ) && is_product_tag()
        ) {
            if ( empty( $post_id ) ) {
                $post_id = get_option( 'woocommerce_shop_page_id' );
            }
        }

        if ( null === $post_id ) {
            $post_id = get_the_ID();
        }

        // there is no posts with ID 1, only on the main archive we can see this.
        if ( ! $post_id || 1 === $post_id ) {
            return $value;
        }

        // try to get value from meta box.
        // lazyblocks.
        if ( function_exists( 'get_lzb_meta' ) ) {
            $value = get_lzb_meta( $name, $post_id );

            // acf.
        } else if ( function_exists( 'get_field' ) ) {
            $value = get_field( $name, $post_id );

            // wp meta.
        } else {
            $value = get_post_meta( $post_id, $name, true );
        }

        return $value;
    }

    /**
     * Enqueue SCSS (build + enqueue if possible)
     *
     * @param string $handle - style name.
     * @param string $src - SCSS php file src.
     * @param array  $deps - dependencies.
     * @param bool   $ver - version.
     * @param string $media - media.
     * @param string $src_fallback - css file src in case if scss compilation failed.
     */
    public static function enqueue_scss( $handle, $src = '', $deps = array(), $ver = false, $media = 'all', $src_fallback = '' ) {
        $use_fallback = ! $src ||
                        ! function_exists( 'nk_theme' ) ||
                        ( ! nk_theme()->theme_dashboard()->is_envato_hosted && ! nk_theme()->theme_dashboard()->activation()->active ) ||
                        version_compare( phpversion(), '5.4', '<' );

        if ( ! $use_fallback ) {
            $compiled_filename = basename( $src, '.php' ) . '.min.css';

            self::$scss_files[ $handle ] = array(
                'handle' => $handle,
                'src' => $src,
                'deps' => $deps,
                'ver' => $ver,
                'media' => $media,
                'compiled_filename' => $compiled_filename,
            );

            // try to compile scss.
            self::maybe_compile_scss();

            // if scss compiled - use it.
            if ( nk_theme()->get_compiled_css_url( $compiled_filename ) ) {
                $src = nk_theme()->get_compiled_css_url( $compiled_filename );
                $ver = nk_theme()->get_compiled_css_version( $compiled_filename );
            } else {
                $use_fallback = true;
            }
        }

        if ( $use_fallback ) {
            $src = $src_fallback;
        }

        // enqueue.
        if ( $handle && $src ) {
            wp_enqueue_style( $handle, $src, $deps, $ver, $media );
        }
    }

    /**
     * Compile SCSS.
     */
    public static function maybe_compile_scss() {
        $dont_compile = ! function_exists( 'nk_theme' ) ||
                        ( ! nk_theme()->theme_dashboard()->is_envato_hosted && ! nk_theme()->theme_dashboard()->activation()->active ) ||
                        version_compare( phpversion(), '5.4', '<' ) ||
                        empty( self::$scss_files );

        if ( $dont_compile ) {
            return;
        }

        // check all available scss files.
        foreach ( self::$scss_files as $scss ) {
            ob_start();
            if ( file_exists( $scss['src'] ) ) {
                include $scss['src'];
            }
            $scss_content = ob_get_contents();
            ob_end_clean();

            if ( $scss_content ) {
                nk_theme()->scss( $scss['compiled_filename'], dirname( $scss['src'] ), $scss_content );
            }
        }
    }

    /**
     * Enable Night Mode.
     */
    public static function night_mode( $attributes = array() ) {
        Ghost_Framework_Night_Mode::init( $attributes );
    }

    /**
     * Custom Breakpoints for Ghost Kit and Visual Portfolio plugins.
     */
    public static function breakpoints( $attributes = array() ) {
        Ghost_Framework_Breakpoints::init( $attributes );
    }

    /**
     * Get Brand SVG icon.
     *
     * @param String $name - brand name.
     * @param Array  $data - svg icon data.
     *
     * @return String
     */
    public static function brand_svg( $name, $data = array() ) {
        return Ghost_Framework_Brand_Svg::get( $name, $data );
    }

    /**
     * Print Brand SVG icon.
     *
     * @param String $name - brand name.
     * @param Array  $data - svg icon data.
     *
     * @return String
     */
    public static function brand_svg_e( $name, $data = array() ) {
        return Ghost_Framework_Brand_Svg::get_e( $name, $data );
    }

    /**
     * Get Brand SVG name.
     *
     * @param String $name - brand name.
     *
     * @return String
     */
    public static function brand_svg_name( $name ) {
        return Ghost_Framework_Brand_Svg::get_name( $name );
    }

    /**
     * Check if Brand SVG exists.
     *
     * @param String $name - brand name.
     *
     * @return Boolean
     */
    public static function brand_svg_exists( $name ) {
        return Ghost_Framework_Brand_Svg::exists( $name );
    }

    /**
     * Get Brand SVG kses data.
     *
     * @return Array
     */
    public static function brand_svg_kses() {
        return Ghost_Framework_Brand_Svg::kses();
    }

    /**
     * Get Brand SVG all available brands.
     *
     * @param Boolean $get_svg - get SVG and insert it inside array.
     * @param Array   $svg_data - svg data.
     *
     * @return Array
     */
    public static function brand_svg_all( $get_svg = false, $svg_data = array() ) {
        return Ghost_Framework_Brand_Svg::get_all_brands( $get_svg, $svg_data );
    }

    /**
     * Add theme dashboard.
     *
     * @param array $data - dashboard data.
     */
    public static function add_theme_dashboard( $data ) {
        if ( ! function_exists( 'nk_theme' ) || ! method_exists( nk_theme(), 'theme_dashboard' ) ) {
            return;
        }

        nk_theme()->theme_dashboard( $data );
    }

    /**
     * Add class on the body tag
     *
     * @param string $class - class string.
     */
    public static function add_body_class( $class ) {
        self::$body_class .= ' ' . $class;
    }

    /**
     * Body class filter
     *
     * @param array $classes - body classes.
     *
     * @return array
     */
    public static function filter_body_class( $classes ) {
        if ( self::$body_class ) {
            $body_classes = explode( ' ', self::$body_class );

            foreach ( $body_classes as $class ) {
                $classes[] = $class;
            }
        }
        return $classes;
    }

    /**
     * Add admin class on the body tag
     *
     * @param string $class - class string.
     */
    public static function add_admin_body_class( $class ) {
        self::$admin_body_class .= ' ' . $class;
    }

    /**
     * Admin body class filter
     *
     * @param string $classes - body classes.
     *
     * @return string
     */
    public static function filter_admin_body_class( $classes ) {
        if ( self::$admin_body_class ) {
            $classes .= self::$admin_body_class;
        }
        return $classes;
    }

    /**
     * Add template.
     *
     * @param string  $hook_name - hook name.
     * @param string  $template_path - template path.
     * @param integer $priority - priority.
     */
    public static function add_template( $hook_name, $template_path, $priority = 10 ) {
        Ghost_Framework_Templates::add_template( $hook_name, $template_path, $priority );
    }

    /**
     * Remove template.
     *
     * @param string $hook_name - hook name.
     * @param string $template_path - template path.
     */
    public static function remove_template( $hook_name, $template_path = '' ) {
        Ghost_Framework_Templates::remove_template( $hook_name, $template_path );
    }

    /**
     * Get attachment data.
     *
     * @param int|string $attachment_id - attaachment ID or URL to image.
     * @param string     $size - attachment size.
     *
     * @return array|bool
     */
    public static function get_attachment( $attachment_id, $size = 'full' ) {
        // is url.
        if (
            is_string( $attachment_id ) &&
            (
                filter_var( $attachment_id, FILTER_VALIDATE_URL ) ||

                // Simple check for slashes in relative URL.
                strpos( $attachment_id, '/' ) !== false
            )
        ) {
            $path_to_image = $attachment_id;

            // @codingStandardsIgnoreLine
            $attachment_id = attachment_url_to_postid( $attachment_id );
            if ( is_numeric( $attachment_id ) && 0 === $attachment_id ) {
                return array(
                    'alt' => null,
                    'caption' => null,
                    'description' => null,
                    'href' => null,
                    'src' => $path_to_image,
                    'title' => null,
                    'width' => null,
                    'height' => null,
                );
            }
        }

        // is numeric.
        if ( is_numeric( $attachment_id ) && 0 !== $attachment_id ) {
            $attachment = get_post( $attachment_id );

            if ( is_object( $attachment ) ) {
                $attachment_src = array();

                if ( isset( $size ) ) {
                    $attachment_src = wp_get_attachment_image_src( $attachment_id, $size );
                }

                return array(
                    'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
                    'caption' => $attachment->post_excerpt,
                    'description' => $attachment->post_content,
                    'href' => get_permalink( $attachment->ID ),
                    'src' => isset( $attachment_src[0] ) ? $attachment_src[0] : $attachment->guid,
                    'title' => $attachment->post_title,
                    'width' => isset( $attachment_src[1] ) ? $attachment_src[1] : false,
                    'height' => isset( $attachment_src[2] ) ? $attachment_src[2] : false,
                );
            }
        }

        return false;
    }

    /**
     * Get <img> tag by ID or URL
     *
     * @param int|string $attachment_id - attaachment ID or URL to image.
     * @param string     $size - attachment size.
     * @param bool       $icon - icon.
     * @param array      $attr - attributes array.
     *
     * @return null|string
     */
    public static function get_image( $attachment_id, $size = 'thumbnail', $icon = false, $attr = array() ) {
        $image_id = $attachment_id;

        if (
            is_string( $attachment_id ) &&
            (
                filter_var( $attachment_id, FILTER_VALIDATE_URL ) ||

                // Simple check for slashes in relative URL.
                strpos( $attachment_id, '/' ) !== false
            )
        ) {
            // @codingStandardsIgnoreLine
            $image_id = attachment_url_to_postid( $attachment_id );

            if ( is_numeric( $image_id ) && 0 === $image_id ) {
                $alt = '';
                $return = '<img src="' . esc_url( $attachment_id ) . '"';
                if ( isset( $attr ) && ! empty( $attr ) && is_array( $attr ) ) {
                    foreach ( $attr as $key => $attribute ) {
                        $return .= ' ' . esc_attr( $key ) . '="' . esc_attr( $attribute ) . '"';
                    }
                }
                $return .= ' alt="' . esc_attr( $alt ) . '" />';
                return $return;
            }
        }

        if ( is_numeric( $image_id ) && 0 !== $image_id ) {
            return wp_get_attachment_image( $image_id, $size, $icon, $attr );
        }

        return null;
    }

    /**
     * Get image sizes array
     *
     * @return array
     */
    public static function get_image_sizes() {
        // @codingStandardsIgnoreLine
        $sizes = get_intermediate_image_sizes();
        $result = array(
            'full' => 'full',
        );

        foreach ( $sizes as $k => $name ) {
            $result[ $name ] = $name;
        }

        return $result;
    }

    /**
     * Print nav menu with Ghost_Framework_Menu_Walker
     *
     * @param array $args - example https://github.com/nk-o/wp-starter-theme#add-menu .
     */
    public static function print_nav_menu( $args = array() ) {
        require_once self::$framework_path . '/inc/class-menu-walker.php';

        // add classes to default args from 'classes'.
        if ( isset( $args['classes'] ) ) {
            if ( isset( $args['classes']['menu'] ) ) {
                $args['menu_class'] = $args['classes']['menu'];
            }
            if ( isset( $args['classes']['menu_container'] ) ) {
                $args['container_class'] = $args['classes']['menu_container'];
            }
        }

        // add walker.
        $args = array_merge(
            $args, array(
                'walker' => new Ghost_Framework_Menu_Walker( $args ),
            )
        );

        wp_nav_menu( $args );
    }

    /**
     * Universal generate Breadcrumbs
     *
     * @param array $args - Set of initial parameters for the generation of breadcrumbs.
     * @return string
     */
    public static function get_breadcrumbs( $args = array(
        'home' => false, // text for the 'Home' link.
        'category' => 'Archive by Category "%s"', // text for a category page.
        'tax' => 'Archive for "%s"', // text for a taxonomy page.
        'search' => 'Search Results for "%s" Query', // text for a search results page.
        'tag' => 'Posts Tagged "%s"', // text for a tag page.
        'author' => 'Articles Posted by %s', // text for an author page.
        '404' => 'Error 404', // text for the 404 page.
        'show_current' => 1, // 1 - show current post/page title in breadcrumbs, 0 - don't show
        'show_on_home' => 1, // 1 - show breadcrumbs on the homepage, 0 - don't show.
        'delimiter' => '', // delimiter between crumbs.
        'before' => '<li><span>', // tag before the current crumb.
        'after' => '</span></li>', // tag after the current crumb.
        'classes' => '', // list classes.
        'show_as_title' => false,
    ) ) {
        global $post;
        $post_type = get_post_type();
        $result = '';
        $home_link = esc_url( home_url( '/' ) );
        $link_before = '<li>';
        $link_after = '</li>';
        $link_attr = ' rel="v:url" property="v:title"';
        $link = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
        $classes = $args['classes'] ? ' class="' . esc_attr( $args['classes'] ) . '"' : '';
        if ( is_home() || is_front_page() ) {
            if ( 1 == $args['show_on_home'] && $args['home'] ) {
                $result .= '<ul' . $classes . '><li><a href="' . $home_link . '">' . $args['home'] . '</a></li></ul>';
            }
        } else {
            $result .= '<ul' . $classes . '>';
            if ( function_exists( 'is_bbpress' ) && is_bbpress() ) { // supported bbpres breadcrumbs.
                $current_before = '<span>';
                $current_after = '</span>';
                if ( $args['show_as_title'] ) {
                    $current_before = '<span><h1>';
                    $current_after = '</h1></span>';
                }
                $result .= bbp_get_breadcrumb(
                    array(
                        'home_text' => $args['home'],
                        'before' => '',
                        'after'  => '',
                        'sep' => $args['delimiter'],
                        'sep_before' => '',
                        'sep_after'  => '',
                        'crumb_before' => $link_before,
                        'crumb_after'  => $link_after,
                        'current_before' => $current_before,
                        'current_after'  => $current_after,
                    )
                );
            } elseif ( 'product' == $post_type && function_exists( 'woocommerce_breadcrumb' ) ) {
                // is Woocommerce.
                $args = wp_parse_args(
                    array(
                        'delimiter' => $args['delimiter'],
                        'before' => $args['before'],
                        'wrap_before' => '',
                        'wrap_after'  => '',
                        'after'       => $args['after'],
                        'home'        => $args['home'],
                        'show_as_title' => $args['show_as_title'],
                    )
                );
                $breadcrumbs = new WC_Breadcrumb();
                if ( ! empty( $args['home'] ) ) {
                    $breadcrumbs->add_crumb( $args['home'], apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) );
                }
                $args['breadcrumb'] = $breadcrumbs->generate();
                if ( ! empty( $args['breadcrumb'] ) ) {
                    $result .= $args['wrap_before'];
                    foreach ( $args['breadcrumb'] as $key => $crumb ) {
                        $result .= $args['before'];
                        // @codingStandardsIgnoreLine
                        if ( ! empty( $crumb[1] ) && sizeof( $args['breadcrumb'] ) !== $key + 1 ) {
                            $result .= '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
                        } else {
                            if ( $args['show_as_title'] ) {
                                $result .= '<h1>';
                            }
                            $result .= esc_html( $crumb[0] );
                            if ( $args['show_as_title'] ) {
                                $result .= '</h1>';
                            }
                        }

                        $result .= $args['after'];
                        // @codingStandardsIgnoreLine
                        if ( sizeof( $args['breadcrumb'] ) !== $key + 1 ) {
                            $result .= $args['delimiter'];
                        }
                    }
                    $result .= $args['wrap_after'];
                }
            } else {
                if ( $args['show_as_title'] ) {
                    $args['before'] .= '<h1>';
                    $args['after'] = '</h1>' . $args['after'];
                }
                $sub_result = '';
                if ( get_query_var( 'paged' ) ) {
                    $sub_result .= ' (';
                    // @codingStandardsIgnoreLine
                    $sub_result .= esc_html__( 'Page', '@@text_domain' ) . ' ' . get_query_var( 'paged' );
                    $sub_result .= ')';
                }
                if ( $args['home'] ) {
                    $result .= sprintf( $link, $home_link, $args['home'] ) . $args['delimiter'];
                }
                if ( is_category() ) {
                    $this_cat = get_category( get_query_var( 'cat' ), false );
                    if ( is_object( $this_cat ) && ! isset( $this_cat->errors ) && 0 != $this_cat->parent ) {
                        $cats = get_category_parents( $this_cat->parent, true, $args['delimiter'] );
                        $cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
                        $cats = str_replace( '</a>', '</a>' . $link_after, $cats );
                        $result .= $cats;
                    }
                    $result .= $args['before'] . sprintf( esc_html( $args['category'] ), single_cat_title( '', false ) ) . $sub_result . $args['after'];
                } elseif ( is_tax() ) {
                    $this_cat = get_category( get_query_var( 'cat' ), false );
                    if ( is_object( $this_cat ) && ! isset( $this_cat->errors ) && 0 != $this_cat->parent ) {
                        $cats = get_category_parents( $this_cat->parent, true, $args['delimiter'] );
                        $cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
                        $cats = str_replace( '</a>', '</a>' . $link_after, $cats );
                        $result .= $cats;
                    }
                    $result .= $args['before'] . sprintf( esc_html( $args['tax'] ), single_cat_title( '', false ) ) . $sub_result . $args['after'];
                } elseif ( is_search() ) {
                    $result .= $args['before'] . sprintf( esc_html( $args['search'] ), get_search_query() ) . $sub_result . $args['after'];
                } elseif ( is_day() ) {
                    $result .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $args['delimiter'];
                    $result .= sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $args['delimiter'];
                    $result .= $args['before'] . esc_html__( 'Day', '@@text_domain' ) . '&nbsp;' . get_the_time( 'd' ) . $sub_result . $args['after'];
                } elseif ( is_month() ) {
                    $result .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $sub_result . $args['delimiter'];
                    $result .= $args['before'] . get_the_time( 'F' ) . $args['after'];
                } elseif ( is_year() ) {
                    $result .= $args['before'] . esc_html__( 'Year', '@@text_domain' ) . '&nbsp;' . get_the_time( 'Y' ) . $sub_result . $args['after'];
                } elseif ( is_single() && ! is_attachment() ) {
                    if ( 'post' != get_post_type() ) {
                        $post_type = get_post_type_object( get_post_type() );
                        $slug = $post_type->rewrite;
                        $home_link = rtrim( $home_link, '/' );
                        switch ( $post_type->name ) {
                            case 'team':
                            case 'game':
                            case 'player':
                            case 'match':
                            case 'tournament':
                                $label = $post_type->label;
                                $slug['slug'] = $post_type->has_archive;
                                break;
                            case 'download':
                                $label = $post_type->label;
                                break;
                            default:
                                $label = $post_type->labels->singular_name;
                        }

                        $result .= sprintf( $link, $home_link . '/' . $slug['slug'] . '/', $label );
                        if ( 1 == $args['show_current'] ) {
                            $result .= $args['delimiter'] . $args['before'] . get_the_title() . $sub_result . $args['after'];
                        }
                    } else {
                        $cat = get_the_category();
                        $cat = $cat[0];
                        $cats = get_category_parents( $cat, true, $args['delimiter'] );
                        if ( 0 == $args['show_current'] ) {
                            $cats = preg_replace( '#^(.+)' . $args['delimiter'] . '$#', '$1', $cats );
                        }
                        $cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
                        $cats = str_replace( '</a>', '</a>' . $link_after, $cats );
                        $result .= $cats;
                        if ( 1 == $args['show_current'] ) {
                            $title = get_the_title();
                            /**
                             * This Reg used to finding first container and add hide class if title is empty.
                             */
                            if ( empty( $title ) ) {
                                $matches = array();
                                preg_match( '/^<\w+>/', $args['before'], $matches );
                                if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
                                    $preg_container = $matches[0];
                                    $preg_future = str_replace( '>', ' class="d-none">', $preg_container );
                                    if ( isset( $preg_future ) && ! empty( $preg_future ) ) {
                                        $args['before'] = str_replace( $matches[0], $preg_future, $args['before'] );
                                    }
                                }
                            }
                            $result .= $args['before'] . $title . $sub_result . $args['after'];
                        }
                    }
                } elseif ( ! is_single() && ! is_page() && 'post' != get_post_type() && ! is_404() ) {
                    $post_type = get_post_type_object( get_post_type() );
                    $label = false;
                    if ( is_object( $post_type ) &&
                        ( is_post_type_archive( 'team' ) ||
                            is_post_type_archive( 'game' ) ||
                            is_post_type_archive( 'player' ) ||
                            is_post_type_archive( 'match' ) ||
                            is_post_type_archive( 'tournament' ) )
                    ) {
                        $label = $post_type->labels->name;
                    } elseif ( is_object( $post_type ) ) {
                        $label = $post_type->labels->singular_name;
                    }
                    if ( is_object( $post_type ) && $label ) {
                        $result .= $args['before'] . $label . $sub_result . $args['after'];
                    } else {
                        $result .= $args['before'] . esc_html( $args['404'] ) . $sub_result . $args['after'];
                    }
                } elseif ( is_attachment() ) {
                    $parent = get_post( $post->post_parent );
                    $cat = get_the_category( $parent->ID );
                    if ( ! empty( $cat ) && is_array( $cat ) ) {
                        $cat = $cat[0];
                        $cats = get_category_parents( $cat, true, $args['delimiter'] );
                        $cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
                        $cats = str_replace( '</a>', '</a>' . $link_after, $cats );
                        $result .= $cats;
                    }
                    $result .= sprintf( $link, get_permalink( $parent ), $parent->post_title );
                    if ( 1 == $args['show_current'] ) {
                        $result .= $args['delimiter'] . $args['before'] . get_the_title() . $args['after'];
                    }
                } elseif ( is_page() && ! $post->post_parent ) {
                    if ( 1 == $args['show_current'] ) {
                        $result .= $args['before'] . get_the_title() . $sub_result . $args['after'];
                    }
                } elseif ( is_page() && $post->post_parent ) {
                    $parent_id = $post->post_parent;
                    $breadcrumbs = array();
                    while ( $parent_id ) {
                        $page = get_page( $parent_id );
                        $breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
                        $parent_id = $page->post_parent;
                    }
                    $breadcrumbs = array_reverse( $breadcrumbs );
                    $breadcrumbs_count = count( $breadcrumbs );
                    for ( $i = 0; $i < $breadcrumbs_count; $i++ ) {
                        $result .= $breadcrumbs[ $i ];
                        if ( ( count( $breadcrumbs ) - 1 ) != $i ) {
                            $result .= $args['delimiter'];
                        }
                    }
                    if ( 1 == $args['show_current'] ) {
                        $result .= $args['delimiter'] . $args['before'] . get_the_title() . $sub_result . $args['after'];
                    }
                } elseif ( is_tag() ) {
                    $result .= $args['before'] . sprintf( esc_html( $args['tag'] ), single_tag_title( '', false ) ) . $sub_result . $args['after'];
                } elseif ( is_author() ) {
                    global $author;
                    $userdata = get_userdata( $author );
                    $result .= $args['before'] . sprintf( esc_html( $args['author'] ), $userdata->display_name ) . $sub_result . $args['after'];
                } elseif ( is_archive() ) {
                    $result .= $args['before'] . the_archive_title() . $sub_result . $args['after'];
                } elseif ( is_404() ) {
                    $result .= $args['before'] . esc_html( $args['404'] ) . $args['after'];
                }
            }
            $result .= '</ul>';
        }
        return $result;
    }

    /**
     * Get posts pagination.
     *
     * @param array  $args - additional options.
     * @param object $query - custom posts query.
     */
    public static function posts_pagination( $args, $query = null ) {
        if ( null == $query ) {
            $query_name = isset( $GLOBALS['ghost_query'] ) ? 'ghost_query' : 'wp_query';

            // Don't print empty markup if there's only one page.
            if ( $GLOBALS[ $query_name ]->max_num_pages < 1 ) {
                return;
            }

            $query = $GLOBALS[ $query_name ];
        }

        $args = array_merge(
            array(
                'templates' => array(
                    'wrap' => '
                        <div class="ghost-pagination">
                            <ul>
                                {$pagination_items}
                            </ul>
                        </div>
                    ',
                    'item' => '<li class="ghost-pagination-item">{$link}</li>',
                ),
                'classes' => array(
                    'item'          => 'ghost-pagination-item',
                    'item_previous' => 'ghost-pagination-item-prev',
                    'item_next'     => 'ghost-pagination-item-next',
                    'item_current'  => 'ghost-pagination-item-current',
                    'item_dots'     => 'ghost-pagination-item-dots',
                ),

                // args for paginate_links() function.
                'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                'format'    => '',
                'add_args'  => '',
                'current'   => max( 1, get_query_var( 'page' ), get_query_var( 'paged' ) ),
                'total'     => $query->max_num_pages,
                'prev_text' => esc_html__( 'Previous', '@@text_domain' ),
                'next_text' => esc_html__( 'Next', '@@text_domain' ),
                'type'      => 'array',
                'end_size'  => 3,
                'mid_size'  => 3,
            ), $args
        );

        $page_links = paginate_links( apply_filters( 'ghost_framework_pagination_args', $args ) );

        if ( ! is_array( $page_links ) ) {
            return;
        }

        $pagination_items = '';
        $classes = $args['classes'];
        $templates = $args['templates'];

        foreach ( $page_links as $cur ) {
            if ( false !== strpos( $cur, 'class="page-numbers current"' ) ) {
                $cur = str_replace( 'class="page-numbers current"', 'class="' . $classes['item_current'] . '"', $cur );
            } else if ( false !== strpos( $cur, 'class=\'page-numbers current\'' ) ) {
                $cur = str_replace( 'class=\'page-numbers current\'', 'class="' . $classes['item_current'] . '"', $cur );
            } else if ( false !== strpos( $cur, 'class="next page-numbers"' ) ) {
                $cur = str_replace( 'class="next page-numbers"', 'class="' . $classes['item_next'] . '"', $cur );
            } else if ( false !== strpos( $cur, 'class="prev page-numbers"' ) ) {
                $cur = str_replace( 'class="prev page-numbers"', 'class="' . $classes['item_previous'] . '"', $cur );
            } else if ( false !== strpos( $cur, 'class="page-numbers dots"' ) ) {
                $cur = str_replace( 'class="page-numbers dots"', 'class="' . $classes['item_dots'] . '"', $cur );
            } else {
                $cur = str_replace( 'class="page-numbers"', 'class="' . $classes['item'] . '"', $cur );
                $cur = str_replace( 'class=\'page-numbers\'', 'class="' . $classes['item'] . '"', $cur );
            }

            $pagination_items .= str_replace( '{$link}', $cur, $templates['item'] );

        }

        echo wp_kses_post( str_replace( '{$pagination_items}', $pagination_items, $templates['wrap'] ) );
    }

    /**
     * Gets a list of related posts for the current post.
     *
     * @param int   $post_id - current post ID.
     * @param int   $related_count - number of post records.
     * @param array $args - custom database arguments.
     * @return array|WP_Query
     */
    public static function get_related_posts( $post_id, $related_count, $args = array() ) {
        $args = wp_parse_args(
            (array) $args, array(
                'orderby' => 'RAND(' . $post_id . ')',
                'return'  => 'query',
            )
        );

        $related_args = array(
            'post_type'      => get_post_type( $post_id ),
            'posts_per_page' => $related_count,
            'post_status'    => 'publish',
            'post__not_in'   => array( $post_id ),
            'orderby'        => $args['orderby'],
            'tax_query'      => array(),
        );

        $post       = get_post( $post_id );
        $taxonomies = get_object_taxonomies( $post, 'names' );

        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            // We skip the selection of the taxonomy of the language for the correct operation of the algorithm with the installed Polylang plugin.
            if ( empty( $terms ) || 'language' === $taxonomy ) {
                continue;
            }
            $term_list                   = wp_list_pluck( $terms, 'slug' );
            $related_args['tax_query'][] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $term_list,
            );
        }

        if ( count( $related_args['tax_query'] ) > 1 ) {
            $related_args['tax_query']['relation'] = 'OR';
        }

        if ( 'query' == $args['return'] ) {
            return new WP_Query( $related_args );
        } else {
            return $related_args;
        }
    }

    /**
     * Visual Composer as theme
     */
    public static function action_vc_set_as_theme() {
        if ( function_exists( 'vc_set_as_theme' ) ) {
            vc_set_as_theme();
        }
    }

    /**
     * Revolution Slider as theme
     */
    public static function action_rev_set_as_theme() {
        if ( function_exists( 'set_revslider_as_theme' ) ) {
            set_revslider_as_theme();
        }
    }
}
