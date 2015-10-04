<?php
/**
 * Copyright (C) 2015  Darko Gjorgjijoski
 * http://darkog.com/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * Used to register plugin shortcodes
 * Class DGRVE_Shortcodes
 */
class DGRVE_Shortcodes{

    public function __construct()
    {
        // Initialize shortcode
        add_shortcode('email_recommend', array(&$this, 'output_recommendation_button'));
    }

    public function output_recommendation_button($atts)
    {
        $a = shortcode_atts( array(
            'postid' => '1'
        ), $atts );

        global $post;

        if($post->post_type != "page"){

        }

    }

}

new DGRVE_Shortcodes();

?>