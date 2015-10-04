<?php

/**
 *
 */
function catch_get_request()
{
    if(isset($_GET['welcome']) && (int)$_GET['welcome'] == 1)
    {

    }


}
add_action('init', 'catch_get_request');


/**
 * Adds plugin html to the footer
 */
function footer_add_popup()
{

}
add_action('wp_footer','footer_add_popup' );

