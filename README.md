## Ghost Framework

#### Initialize (see functions.php)
```php
require_once get_template_directory() . '/framework/ghost.php';
new Ghost_Framework( array(
    'path' => get_template_directory() . '/framework',
    'url' => get_template_directory_uri() . '/framework',
) );
```

#### Add TGMPA plugins
```php
Ghost_Framework::add_tgmpa( array(
    array(
        'name'       => 'WooCommerce',
        'slug'       => 'woocommerce',
        'required'   => false,
    ),
) );
```

#### Work with Kirki options (save default options in array)
```php
Ghost_Framework::add_config( array(
    'capability'  => 'edit_theme_options',
    'option_type' => 'theme_mod',
) );

Ghost_Framework::add_section( 'section_name', array(
    'title'    => esc_html__( 'Section Title', '@@text_domain' ),
    'priority' => 10,
) );

Ghost_Framework::add_panel( 'panel_name', array(
    'title'    => esc_html__( 'Panel Title', '@@text_domain' ),
    'icon'     => 'fa fa-bars',
    'priority' => 11,
) );

Ghost_Framework::add_field( array(
    'type'     => 'toggle',
    'settings' => 'option_name',
    'label'    => esc_html__( 'Label', '@@text_domain' ),
    'section'  => 'section_name',
    'default'  => 'on',
    'priority' => 10
) );

$option_value = Ghost_Framework::get_theme_mod( $name, $use_acf, $post_id, $acf_name );
$metabox_value = Ghost_Framework::get_metabox( $name, $post_id );
```

#### Extended Kirki fields
##### Sidebars Selector
```php
Ghost_Framework::add_field( array(
    'type'       => 'sidebars',
    'settings'   => 'footer_widget_1_sidebar',
    'label'      => esc_html__( 'Select Sidebar For 1 Col', '@@text_domain' ),
    'section'    => 'footer',
    'default'    => 'sidebar-footer-1',
) );
```

#### Add custom SCSS compilation
```php
// Starter Theme.
Ghost_Framework::enqueue_scss(
    '@@theme_name',
    get_template_directory() . '/assets/css/@@theme_name-custom.php',
    array(), '@@theme_version', 'all',
    get_template_directory_uri() . '/assets/css/@@theme_name.min.css'
);
```
In file `/assets/css/@@theme_name-custom.php` you can use options from customizer to define user colors.

Usage example see in `functions.php` file

#### Add theme dashboard
```php
Ghost_Framework::add_theme_dashboard( $data );
```

#### Add menu
Uses menu args like in this function https://developer.wordpress.org/reference/functions/wp_nav_menu/ + additional attributes for walker.
```php
Ghost_Framework::print_nav_menu( array(
    // default args.
    'theme_location' => 'top_menu',

    // ghost menu walker args.
    'start_lvl' => '<ul{$attributes}>',
    'start_el'      => '<li{$attributes}>',
    'start_link'        => '{$before}<a{$attributes}>',
    'title_link'            => '{$before}{$title}{$after}',
    'end_link'          => '</a>{$after}',
    'end_el'        => '</li>',
    'end_lvl'   => '</ul>',
    'classes' => array(
        'menu'                 => 'ghost_menu',
        'menu_container'       => 'ghost_menu__container',
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
    ),
) );
```

#### Add Mega menu checkbox in nav settings
```php
Ghost_Framework::add_mega_menu();
```

#### Add classes on &lt;body&gt; tag
```php
Ghost_Framework::add_body_class( 'custom classes' );
Ghost_Framework::add_body_class( 'custom-class-2' );
```

#### Add classes on Admin &lt;body&gt; tag
```php
Ghost_Framework::add_admin_body_class( 'custom classes' );
Ghost_Framework::add_admin_body_class( 'custom-class-2' );
```

#### Print posts navigation
```php
Ghost_Framework::posts_pagination( array(
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
    'prev_text' => esc_html__( 'Previous', '@@text_domain' ),
    'next_text' => esc_html__( 'Next', '@@text_domain' ),
) );
```

#### Get image data by ID or URL
```php
Ghost_Framework::get_attachment( $id, $size );
```

Return example:
```php
array(
    'alt' => '',
    'caption' => '',
    'description' => '',
    'href' => '',
    'src' => '',
    'title' => '',
    'width' => '',
    'height' => '',
)
```

#### Get &lt;img&gt; tag string by ID or URL
```php
Ghost_Framework::get_image( $id, $size, $icon, $attr );
```

#### Get array with available image sizes (plus full size)
```php
Ghost_Framework::get_image_sizes();
```
