<?php
/**
 * Override field methods
 *
 * @package    Ghost_Framework_Kirki
 * @subpackage Controls
 * @copyright  Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since      3.0.36
 */

/**
 * Field overrides.
 */
class Ghost_Framework_Kirki_Field_Dropdown_Pages extends Ghost_Framework_Kirki_Field_Select {

    /**
     * Sets the default value.
     *
     * @access protected
     * @since 3.0.0
     */
    protected function set_choices() {
        $all_pages = get_pages();
        foreach ( $all_pages as $page ) {
            $this->choices[ $page->ID ] = $page->post_title;
        }
    }
}
