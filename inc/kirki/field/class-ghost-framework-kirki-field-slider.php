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
 * Field overrides.
 */
class Ghost_Framework_Kirki_Field_Slider extends Ghost_Framework_Kirki_Field_Number {

    /**
     * Sets the control type.
     *
     * @access protected
     */
    protected function set_type() {
        $this->type = 'kirki-slider';
    }
}
