<?php
/**
 * Ghost Backend Menu Walker
 *
 * We're separating this class from the plugin file because Walker_Nav_Menu
 * is only loaded on the wp-admin/nav-menus.php page.
 * Thanks for idea Dzikri Aziz <kvcrvt@gmail.com> and his project wp-menu-icons: https://github.com/kucrut/wp-menu-icons/
 *
 * @package @@theme_name/ghost-menu
 */

// phpcs:disable

/**
 * Class Ghost_Framework_Backend_Menu
 */
class Ghost_Framework_Backend_Menu {
    /**
     * Menu data.
     *
     * @var array
     */
    public $data = array(
        'mega_menu_class' => 'ghost-mega-menu',
    );

    /**
     * Ghost_Framework_Backend_Menu constructor.
     *
     * @param boolean|array $data - additional data.
     */
    public function __construct( $data = false ) {
        global $wp_version;

        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'wp_nav_menu_item_custom_fields' ), 10, 4 );
        add_filter( 'wp_setup_nav_menu_item', array( $this, 'wp_setup_nav_menu_item' ) );
        add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 10, 3 );

        if ( $data ) {
            $this->data = array_merge(
                $this->data,
                $data
            );
        }

        // Add frontend menu classes.
        add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 4 );

        /**
         * Add custom action for WordPress < 5.4.
         * Since WordPress 5.4 this action already available
         * https://make.wordpress.org/core/2020/02/25/wordpress-5-4-introduces-new-hooks-to-add-custom-fields-to-menu-items/
         */
        if ( version_compare( $wp_version, '5.4.0', '<' ) ) {
            add_filter( 'wp_edit_nav_menu_walker', array( $this, 'wp_edit_nav_menu_walker_fallback' ), 10, 2 );
        }
    }

    /**
     * Add mega menu checkbox.
     *
     * @param int    $id - menu id.
     * @param object $item - menu item.
     * @param int    $depth - depth of menu item.
     * @param array  $args additional menu arguments.
     */
    public function wp_nav_menu_item_custom_fields( $id, $item, $depth, $args ) {
        ?>
        <p class="field-link-ghost-mega-menu description">
            <label for="edit-menu-item-ghost-mega-menu-<?php echo esc_attr( $item->ID ); ?>">
                <input type="hidden" name="menu-item-ghost-mega-menu[<?php echo esc_attr( $item->ID ); ?>]" id="edit-menu-item-ghost-mega-menu-<?php echo esc_attr( $item->ID ); ?>_hidden" value="off">
                <input type="checkbox" id="edit-menu-item-ghost-mega-menu-<?php echo esc_attr( $item->ID ); ?>" value="on" name="menu-item-ghost-mega-menu[<?php echo esc_attr( $item->ID ); ?>]" <?php checked( $item->ghost_mega_menu, 'on' ); ?> />
                <?php echo esc_html__( 'Mega Menu', '@@text_domain' ); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Set menu object parameter with mega menu value.
     *
     * @param object $item - menu item.
     * @return object
     */
    public function wp_setup_nav_menu_item( $item ) {
        $item->ghost_mega_menu = get_post_meta( $item->ID, '_menu_item_ghost_mega_menu', true );
        return $item;
    }

    /**
     * Update menu post meta data.
     *
     * @param int   $menu_id - menu id.
     * @param int   $menu_item_id - menu item id.
     * @param array $args additional menu arguments.
     */
    public function wp_update_nav_menu_item( $menu_id, $menu_item_id, $args ) {
        // Check if element is properly sent.
        if ( isset( $_REQUEST['menu-item-ghost-mega-menu'] ) && is_array( $_REQUEST['menu-item-ghost-mega-menu'] ) ) {
            $mega = isset( $_REQUEST['menu-item-ghost-mega-menu'][ $menu_item_id ] ) ? $_REQUEST['menu-item-ghost-mega-menu'][ $menu_item_id ] : false;
            update_post_meta( $menu_item_id, '_menu_item_ghost_mega_menu', $mega );
        }
    }

    /**
     * Add mega menu classes to items.
     *
     * @param array $classes - menu id.
     * @param class $item - menu item id.
     * @param class $args - menu item args.
     * @param int   $depth - menu item depth.
     */
    public function nav_menu_css_class( $classes, $item, $args, $depth ) {
        if ( isset( $item->ID ) & 0 === $depth ) {
            $ghost_mega_menu = 'on' === get_post_meta( $item->ID, '_menu_item_ghost_mega_menu', true );

            if ( $ghost_mega_menu ) {
                $classes[] = $this->data['mega_menu_class'];
            }
        }

        return $classes;
    }

    /**
     * Add custom action for WordPress < 5.4.
     * Since WordPress 5.4 this action already available
     * https://make.wordpress.org/core/2020/02/25/wordpress-5-4-introduces-new-hooks-to-add-custom-fields-to-menu-items/
     *
     * @param object $walker - menu walker.
     * @param int    $menu_id - menu id.
     * @return string
     */
    public function wp_edit_nav_menu_walker_fallback( $walker, $menu_id ) {
        return 'Ghost_Framework_Backend_Menu_Walker_Fallback';
    }
}

/**
 * Extend Walker_Nav_Menu class to override default menu.
 * extends from Walker_Nav_Menu, because Walker_Nav_Menu_Edit don't available.
 */
class Ghost_Framework_Backend_Menu_Walker_Fallback extends Walker_Nav_Menu {
    /**
     * Start the level output.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth Depth of menu item. Used for padding.
     * @param array  $args additional menu arguments.
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
    }

    /**
     * Start the level output.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth Depth of menu item. Used for padding.
     * @param array  $args additional menu arguments.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
    }

    /**
     * Start the element output.
     *
     * @global int $_wp_nav_menu_max_depth
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int    $depth Depth of menu item. Used for padding.
     * @param array  $args additional menu arguments.
     * @param int    $id element id.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = $item->ID;
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = false;
        if ( 'taxonomy' === $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) ) {
                $original_title = false;
            }
        } elseif ( 'post_type' === $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = get_the_title( $original_object->ID );
        } elseif ( 'post_type_archive' === $item->type ) {
            $original_object = get_post_type_object( $item->object );
            if ( $original_object ) {
                $original_title = $original_object->labels->archives;
            }
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id === $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( esc_html( '%s (Invalid)' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' === $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( esc_html( '%s (Pending)' ), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if ( 0 == $depth ) {
            $submenu_text = 'style="display: none;"';
        }

        ?>
    <li id="menu-item-<?php echo esc_attr( $item_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
        <div class="menu-item-bar">
            <div class="menu-item-handle">
                <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
                        class="is-submenu" <?php echo wp_kses_post( $submenu_text ); ?>><?php esc_html_e( 'sub item', '@@text_domain' ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html($item->type_label); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-up" aria-label="<?php esc_attr_e('Move up', '@@text_domain') ?>">&#8593;</a>
							|
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-down" aria-label="<?php esc_attr_e('Move down', '@@text_domain') ?>">&#8595;</a>
						</span>
						<a class="item-edit" id="edit-<?php echo esc_attr( $item_id ); ?>" href="<?php
                        echo (isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? admin_url('nav-menus.php') : add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id)));
                        ?>" aria-label="<?php esc_attr_e('Edit menu item', '@@text_domain'); ?>"><?php esc_html_e('Edit', '@@text_domain'); ?></a>
					</span>
            </div>
        </div>

        <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo esc_attr( $item_id ); ?>">
            <?php if ('custom' == $item->type) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>">
                        <?php esc_html_e('URL', '@@text_domain'); ?><br/>
                        <input type="text" id="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>"
                               class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr( $item_id ); ?>]"
                               value="<?php echo esc_attr($item->url); ?>"/>
                    </label>
                </p>
            <?php endif; ?>
            <p class="description description-wide">
                <label for="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e('Navigation Label', '@@text_domain'); ?><br/>
                    <input type="text" id="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr($item->title); ?>"/>
                </label>
            </p>
            <p class="field-title-attribute field-attr-title description description-wide">
                <label for="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e('Title Attribute', '@@text_domain'); ?><br/>
                    <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat edit-menu-item-attr-title"
                           name="menu-item-attr-title[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr($item->post_excerpt); ?>"/>
                </label>
            </p>
            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>" value="_blank"
                           name="menu-item-target[<?php echo esc_attr( $item_id ); ?>]"<?php checked($item->target, '_blank'); ?> />
                    <?php esc_html_e('Open link in a new tab', '@@text_domain'); ?>
                </label>
            </p>
            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e('CSS Classes (optional)', '@@text_domain'); ?><br/>
                    <input type="text" id="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr(implode(' ', $item->classes)); ?>"/>
                </label>
            </p>
            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e('Link Relationship (XFN)', '@@text_domain'); ?><br/>
                    <input type="text" id="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr($item->xfn); ?>"/>
                </label>
            </p>
            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e('Description', '@@text_domain'); ?><br/>
                    <textarea id="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>"
                              class="widefat edit-menu-item-description" rows="3" cols="20"
                              name="menu-item-description[<?php echo esc_attr( $item_id ); ?>]"><?php echo esc_html($item->description); // textarea_escaped ?></textarea>
                    <span
                        class="description"><?php esc_html_e('The description will be displayed in the menu if the current theme supports it.', '@@text_domain'); ?></span>
                </label>
            </p>

            <?php
            // add custom fields
            do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args, $id );
            ?>

            <fieldset class="field-move hide-if-no-js description description-wide">
                <span class="field-move-visual-label" aria-hidden="true"><?php esc_html_e('Move', '@@text_domain'); ?></span>
                <button type="button" class="button-link menus-move menus-move-up"
                        data-dir="up"><?php esc_html_e('Up one', '@@text_domain'); ?></button>
                <button type="button" class="button-link menus-move menus-move-down"
                        data-dir="down"><?php esc_html_e('Down one', '@@text_domain'); ?></button>
                <button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
                <button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
                <button type="button" class="button-link menus-move menus-move-top"
                        data-dir="top"><?php esc_html_e('To the top', '@@text_domain'); ?></button>
            </fieldset>

            <div class="menu-item-actions description-wide submitbox">
                <?php if ('custom' != $item->type && $original_title !== false) : ?>
                    <p class="link-to-original">
                        <?php printf('Original: %s', '<a href="' . esc_attr($item->url) . '">' . esc_html($original_title) . '</a>'); ?>
                    </p>
                <?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr( $item_id ); ?>" href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        admin_url('nav-menus.php')
                    ),
                    'delete-menu_item_' . $item_id
                ); ?>"><?php esc_html_e('Remove', '@@text_domain'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a
                    class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr( $item_id ); ?>"
                    href="<?php echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), admin_url('nav-menus.php')));
                    ?>#menu-item-settings-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e('Cancel', '@@text_domain'); ?></a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr( $item_id ); ?>"/>
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr($item->object_id); ?>"/>
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr($item->object); ?>"/>
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr($item->menu_item_parent); ?>"/>
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr($item->menu_order); ?>"/>
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr( $item_id ); ?>]"
                   value="<?php echo esc_attr($item->type); ?>"/>
        </div><!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }
}
