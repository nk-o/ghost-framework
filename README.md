# Ghost Framework

## Initialize (see functions.php)

```php
require_once get_template_directory() . '/framework/ghost.php';
new Ghost_Framework( array(
    'path' => get_template_directory() . '/framework',
    'url' => get_template_directory_uri() . '/framework',
) );
```

## Add TGMPA plugins

```php
Ghost_Framework::add_tgmpa( array(
    array(
        'name'       => 'WooCommerce',
        'slug'       => 'woocommerce',
        'required'   => false,
    ),
) );
```

## Customizer Options

```php
Ghost_Framework::add_config( array(
    'capability'  => 'edit_theme_options',
    'option_type' => 'theme_mod',
) );

// Panel.
Ghost_Framework::add_panel(
    'panel_name',
    array(
        'title'    => esc_html__( 'Panel Title', '@@text_domain' ),
        'icon'     => 'dashicons-heart',
    )
);

// Section.
Ghost_Framework::add_section(
    'section_name',
    array(
        'title'    => esc_html__( 'Section Title', '@@text_domain' ),
        'panel'    => 'panel_name',
        'priority' => 20,
    )
);

// Toggle Field.
Ghost_Framework::add_field( array(
    'type'        => 'toggle',
    'settings'    => 'toggle_option_name',
    'label'       => esc_html__( 'Toggle Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
    'default'     => 'on',
) );

// Image Field.
Ghost_Framework::add_field( array(
    'type'        => 'image',
    'settings'    => 'image_option_name',
    'label'       => esc_html__( 'Image Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
) );

// Color Field.
Ghost_Framework::add_field( array(
    'type'        => 'color',
    'settings'    => 'color_option_name',
    'label'       => esc_html__( 'Color Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
) );

// Number Slider Field.
Ghost_Framework::add_field( array(
    'type'        => 'slider',
    'settings'    => 'slider_option_name',
    'label'       => esc_html__( 'Number Slider Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
    'default'     => 42,
    'choices'     => array(
        'min'  => 0,
        'max'  => 100,
        'step' => 1,
    ),
) );

// Number Field.
Ghost_Framework::add_field( array(
    'type'        => 'number',
    'settings'    => 'number_option_name',
    'label'       => esc_html__( 'Number Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
    'default'     => 42,
    'choices'     => array(
        'min'  => 0,
        'max'  => 80,
        'step' => 1,
    ),
) );

// Select Field.
Ghost_Framework::add_field( array(
    'type'        => 'select',
    'settings'    => 'select_option_name',
    'label'       => esc_html__( 'Select Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
    'default'     => 'option-1',
    'choices'     => array(
        'option-1' => esc_html__( 'Option 1', '@@text_domain' ),
        'option-2' => esc_html__( 'Option 2', '@@text_domain' ),
        'option-3' => esc_html__( 'Option 3', '@@text_domain' ),
        'option-4' => esc_html__( 'Option 4', '@@text_domain' ),
    ),
) );

// Text Field.
Ghost_Framework::add_field( array(
    'type'        => 'text',
    'settings'    => 'text_option_name',
    'label'       => esc_html__( 'Text Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
    'default'     => 'Test Value',
) );

// Upload Field.
Ghost_Framework::add_field( array(
    'type'        => 'upload',
    'settings'    => 'upload_option_name',
    'label'       => esc_html__( 'Upload Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
) );

// Editor Field.
Ghost_Framework::add_field( array(
    'type'        => 'editor',
    'settings'    => 'editor_option_name',
    'label'       => esc_html__( 'Editor Field', '@@text_domain' ),
    'description' => esc_html__( 'Description', '@@text_domain' ),
    'section'     => 'section_name',
) );

// Repeater Field.
Ghost_Framework::add_field( array(
    'type'         => 'repeater',
    'settings'     => 'repeater_option_name',
    'label'        => esc_html__( 'Repeater Field', '@@text_domain' ),
    'description'  => esc_html__( 'Description', '@@text_domain' ),
    'section'      => 'section_name',
    'priority'     => 10,
    'row_label'    => array(
        'type'  => 'text',
        'value' => esc_attr__( 'Example', '@@text_domain' ),
    ),
    'button_label' => esc_attr__( '"Add new" Example (optional) ', '@@text_domain' ),
    'default'      => array(
        array(
            'url'    => '#',
            'target' => '_self',
        ),
        array(
            'url'    => '#',
            'target' => '_self',
        ),
    ),
    'fields'       => array(
        'url'    => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Link URL', '@@text_domain' ),
            'description' => esc_attr__( 'This will be the link URL', '@@text_domain' ),
            'default'     => '',
        ),
        'target' => array(
            'type'    => 'select',
            'label'   => esc_html__( 'Target', '@@text_domain' ),
            'default' => '_blank',
            'choices' => array(
                '_blank'  => esc_attr__( 'Blank', '@@text_domain' ),
                '_self'   => esc_attr__( 'Self', '@@text_domain' ),
                '_parent' => esc_attr__( 'Parent', '@@text_domain' ),
                '_top'    => esc_attr__( 'Top', '@@text_domain' ),
            ),
        ),
    ),
) );

$option_value = Ghost_Framework::get_theme_mod( $name, $use_acf, $post_id, $acf_name );
$metabox_value = Ghost_Framework::get_metabox( $name, $post_id );
```

## Extended Kirki fields

### Collapsible Control

```php
Ghost_Framework::add_field(
    array(
        'type'     => 'collapsible',
        'settings' => 'navigation_search_collapse',
        'label'    => esc_html__( 'Search', '@@text_domain' ),
        'section'  => 'navigation',
    )
);

// Optional Collapse End.
Ghost_Framework::add_field(
    array(
        'type'     => 'collapsible_end',
        'settings' => 'navigation_search_collapse_end',
        'section'  => 'navigation',
    )
);
```

### Aspect Ratio Control

```php
Ghost_Framework::add_field(
    array(
        'type'       => 'aspect-ratio',
        'settings'   => 'aspect_ratio',
        'label'      => esc_html__( 'Aspect Ratio', '@@text_domain' ),
        'section'    => 'post',
        'default'    => '4:3',
    )
);
```

### Sidebars Selector Control

```php
Ghost_Framework::add_field(
    array(
        'type'       => 'sidebars',
        'settings'   => 'footer_widget_1_sidebar',
        'label'      => esc_html__( 'Select Sidebar For 1 Col', '@@text_domain' ),
        'section'    => 'footer',
        'default'    => 'sidebar-footer-1',
    )
);
```

## Add custom Typography

```php
Ghost_Framework::add_typography(
    array(
        'body' => array(
            'label' => esc_html__( 'Body', '@@text_domain' ),
            'defaults' => array(
                'font-family-category' => 'google-fonts',
                'font-family' => 'Abel',
                'font-size' => '14px',
                'font-weight' => '',
                'line-height' => '',
                'letter-spacing' => '',
            ),
            'output' => array(
                array(
                    'selectors' => 'body',
                ),
                array(
                    'selectors' => '#editor .editor-styles-wrapper, .editor-styles-wrapper p',
                    'editor' => true,
                ),
            ),
        ),
    )
);
```

## Add custom SCSS compilation

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

## Templates

We can include template parts of the theme using hooks.

### Add Template

```php
Ghost_Framework::add_template( 'ghost_site_before', '/template-parts/site-general/skip-links', 5 );
```

### Remove Template

```php
Ghost_Framework::remove_template( 'ghost_site_before', '/template-parts/site-general/skip-links' );

// OR to remove all templates from the selected hook.
Ghost_Framework::remove_template( 'ghost_site_before' );
```

## Night Mode

Enable night mode, that will add script for switching mode automatically.

```php
Ghost_Framework::night_mode( array(
    'default'           => 'auto',
    'use_local_storage' => true,
    'night_class'       => 'ghost-night-mode',
    'switching_class'   => 'ghost-night-mode-switching',
    'toggle_selector'   => '.ghost-night-mode-toggle',
) );
```

## Custom Breakpoints

Change default breakpoints used in Ghost Kit and in Visual Portfolio plugins.

```php
Ghost_Framework::breakpoints( array(
    'xs' => 320,
    'sm' => 576,
    'md' => 768,
    'lg' => 992,
    'xl' => 1200,
) );
```

Note: `xs` used in Visual Portfolio only.

## Brand SVG

### Get brand SVG string

```php
$icon = Ghost_Framework::brand_svg( 'facebook' );
```

### Get brand name string

```php
$name = Ghost_Framework::brand_svg_name( 'facebook' );
// returns 'Facebook'
```

### Check if brand exists

```php
if ( Ghost_Framework::brand_svg_exists( 'facebook' ) ) {
    ...
}
```

### Print brand SVG icon

Auto:

```php
Ghost_Framework::brand_svg_e( 'facebook' );
```

Manual:

```php
if ( Ghost_Framework::brand_svg_exists( 'facebook' ) ) {
    $icon = Ghost_Framework::brand_svg( 'facebook' );
    echo wp_kses( $icon, Ghost_Framework::brand_svg_kses() );
}
```

### Get all available brands

```php
$brands = Ghost_Framework::brand_svg_all();
```

## Add theme dashboard

```php
Ghost_Framework::add_theme_dashboard( $data );
```

## Add menu

Uses menu args like in this function <https://developer.wordpress.org/reference/functions/wp_nav_menu/> + additional attributes for walker.

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

## Add Mega menu checkbox in nav settings

```php
Ghost_Framework::add_mega_menu();
```

## Add classes on &lt;body&gt; tag

```php
Ghost_Framework::add_body_class( 'custom classes' );
Ghost_Framework::add_body_class( 'custom-class-2' );
```

## Add classes on Admin &lt;body&gt; tag

```php
Ghost_Framework::add_admin_body_class( 'custom classes' );
Ghost_Framework::add_admin_body_class( 'custom-class-2' );
```

## Print posts navigation

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

## Get image data by ID or URL

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

## Get &lt;img&gt; tag string by ID or URL

```php
Ghost_Framework::get_image( $id, $size, $icon, $attr );
```

## Get array with available image sizes (plus full size)

```php
Ghost_Framework::get_image_sizes();
```
