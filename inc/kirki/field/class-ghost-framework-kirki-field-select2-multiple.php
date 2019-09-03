<?php
/**
 * Override field methods
 *
 * @package     Ghost_Framework_Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license     https://opensource.org/licenses/MIT
 * @since       2.2.7
 */

/**
 * This is nothing more than an alias for the Ghost_Framework_Kirki_Field_Select class.
 * In older versions of Ghost_Framework_Kirki there was a separate 'select2' field.
 * This exists here just for compatibility purposes.
 */
class Ghost_Framework_Kirki_Field_Select2_Multiple extends Ghost_Framework_Kirki_Field_Select {

    /**
     * Sets the $multiple
     *
     * @access protected
     */
    protected function set_multiple() {
        $this->multiple = 999;
    }
}
