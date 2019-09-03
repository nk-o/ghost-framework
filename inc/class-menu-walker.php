<?php
/**
 * Ghost Menu Walker.
 *
 * @package @@theme_name/ghost-menu
 */

/**
 * Class Ghost_Framework_Menu
 */
class Ghost_Framework_Menu_Walker extends Walker_Nav_Menu {
    /**
     * Ghost atts.
     *
     * @var array
     */
    public $ghost_atts = array(
        'menu_atts'     => array(),
        'sub_menu_atts' => array(),
        'start_lvl' => '<ul{$attributes}>',
        'start_el'      => '<li{$attributes}>',
        'start_link'        => '{$before}<a{$attributes}>',
        'title_link'            => '{$before}{$title}{$after}',
        'end_link'          => '</a>{$after}',
        'end_el'        => '</li>',
        'end_lvl'   => '</ul>',
    );

    /**
     * Ghost classes.
     *
     * @var array
     */
    public $ghost_classes = array(
        'item'                 => 'ghost_menu__item',
        'item_id'              => 'ghost_menu__item--{$id}',
        'item_parent'          => 'ghost_menu__item--parent',
        'item_active'          => 'ghost_menu__item--active',
        'item_active_parent'   => 'ghost_menu__item--parent--active',
        'item_active_ancestor' => 'ghost_menu__item--ancestor--active',
        'mega_menu'            => 'ghost_menu__mega-menu',
        'sub_menu'             => 'ghost_menu__sub-menu',
        'sub_menu_depth'       => 'ghost_menu__sub-menu--{$depth}',
        'sub_menu_item'        => 'ghost_menu__sub-menu__item',
        'sub_menu_depth_item'  => 'ghost_menu__sub-menu--{$depth}__item',
    );

    /**
     * Ghost_Framework_Menu_Walker constructor.
     *
     * @param array $atts - custom user attributes.
     */
    public function __construct( $atts = array() ) {
        $this->ghost_atts = array_merge( $this->ghost_atts, (array) $atts );
        unset( $this->ghost_atts['classes'] );

        $this->ghost_classes = array_merge( $this->ghost_classes, isset( $atts['classes'] ) ? $atts['classes'] : array() );
    }

    /**
     * Starts the list before the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::start_lvl()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl( &$output, $depth = 1, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );

        // Ghost classes.
        $classes = array(
            $this->ghost_classes['sub_menu'],
        );

        // Default classes.
        $classes[] = 'sub-menu';

        /**
         * Filters the CSS class(es) applied to a menu list element.
         *
         * @since 4.8.0
         *
         * @param array    $classes The CSS classes that are applied to the menu `<ul>` element.
         * @param stdClass $args    An object of `wp_nav_menu()` arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = ( $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '' );

        // <ul class..>
        $output .= "{$n}{$indent}" . str_replace( '{$attributes}', $class_names, $this->ghost_atts['start_lvl'] ) . "{$n}";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::end_lvl()
     *
     * @param string         $output Used to append additional content (passed by reference).
     * @param int            $depth  Depth of menu item. Used for padding.
     * @param stdClass|array $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );

        // </ul>
        $output .= "{$indent}" . $this->ghost_atts['end_lvl'] . "{$n}";
    }

    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string         $output Used to append additional content (passed by reference).
     * @param WP_Post        $item   Menu item data object.
     * @param int            $depth  Depth of menu item. Used for padding.
     * @param stdClass|array $args   An object of wp_nav_menu() arguments.
     * @param int            $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        // Ghost classes.
        $classes = array_merge(
            $classes, array(
                'item_class'            => 0 === $depth ? $this->ghost_classes['item'] : '',
                'parent_class'          => isset( $args->walker->has_children ) && $args->walker->has_children ? $this->ghost_classes['item_parent'] : '',
                'active_page_class'     => $item->current ? $this->ghost_classes['item_active'] : '',
                'active_parent_class'   => $item->current_item_parent ? $this->ghost_classes['item_active_parent'] : '',
                'active_ancestor_class' => $item->current_item_ancestor ? $this->ghost_classes['item_active_ancestor'] : '',
                'submenu_class'         => $depth >= 1 ? $this->ghost_classes['sub_menu_item'] : '',
                'submenu_depth_class'   => $depth >= 1 ? str_replace( '{$depth}', $depth, $this->ghost_classes['sub_menu_depth_item'] ) : '',
                'item_id_class'         => str_replace( '{$id}', $item->ID, $this->ghost_classes['item_id'] ),
                'user_class'            => isset( $classes[0] ) && '' !== $classes[0] ? '__item--' . $classes[0] : '',
                'mega_menu'             => isset( $item->ghost_mega_menu ) && 'on' === $item->ghost_mega_menu ? $this->ghost_classes['mega_menu'] : '',
            )
        );

        // Default classes.
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        // <li id... class..>
        $output .= "{$indent}" . str_replace( '{$attributes}', $id . $class_names, $this->ghost_atts['start_el'] );

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        // {$before}<a attributes...>
        $item_start_output = str_replace( '{$before}', isset( $args->before ) ? $args->before : '', $this->ghost_atts['start_link'] );
        $item_start_output = str_replace( '{$attributes}', $attributes, $item_start_output );

        // {$before}{$title}{$after}
        $item_title_output = str_replace( '{$before}', isset( $args->link_before ) ? $args->link_before : '', $this->ghost_atts['title_link'] );
        $item_title_output = str_replace( '{$after}', isset( $args->link_after ) ? $args->link_after : '', $item_title_output );
        $item_title_output = str_replace( '{$title}', $title, $item_title_output );

        // </a>{$after}
        $item_end_output = str_replace( '{$after}', isset( $args->after ) ? $args->after : '', $this->ghost_atts['end_link'] );

        $item_output = $item_start_output . $item_title_output . $item_end_output;

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     *
     * @see Walker::end_el()
     *
     * @param string         $output Used to append additional content (passed by reference).
     * @param WP_Post        $item   Page data object. Not used.
     * @param int            $depth  Depth of page. Not Used.
     * @param stdClass|array $args   An object of wp_nav_menu() arguments.
     */
    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        // </li>
        $output .= $this->ghost_atts['end_el'] . "{$n}";
    }
}
