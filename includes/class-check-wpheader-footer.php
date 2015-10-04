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
 * This class is used to check if wp_head() and wp_footer() exists in the template,
 * additionally it check if those hooks are placed in right place
 *
 * Class DG_CheckWpHeaderAndFooter
 */
class DG_CheckWpHeaderAndFooter
{
    /**
     * True if wp_head() exists, false otherwise
     * @var bool
     */
    private $have_wp_head;

    /**
     * True if wp_footer() exists, false otherwise
     * @var bool
     */
    private $have_wp_footer;

    /**
     * True if wp_head() exists and its in the right place, false otherwise
     * @var bool
     */
    private $have_wp_head_proper_spoot;

    /**
     * True if wp_footer() exists and its in the right place, false otherwise
     * @var bool
     */
    private $have_wp_footer_proper_spot;


    public function __construct()
    {
        // Catch get requests
        add_action('init', array( &$this ,'test_head_footer_init') );

        // Hook in at admin_init to perform the check for wp_head and wp_footer
        add_action( 'admin_init', array( &$this ,'check_head_footer') );
        //$this->check_head_footer();
    }

    /**
     * Catch get requests from WordPress response and add_action to hook
     * <!--wp_head--> and <!--wp_footer --> for testing
     */
    public function test_head_footer_init()
    {
        // If test-head query var exists hook into wp_head
        if ( isset( $_GET['test-head'] ) )
            add_action( 'wp_head', array(&$this, 'test_head'), 99999 ); // Some obscene priority, make sure we run last

        // If test-footer query var exists hook into wp_footer
        if ( isset( $_GET['test-footer'] ) )
            add_action( 'wp_footer', array(&$this, 'test_footer'), 99999 ); // Some obscene priority, make sure we run last
    }

    /**
     * Text to hook in the wp_header() for testing
     */
    public function test_head() {
        echo '<!--wp_head-->';
    }

    /**
     * Text to hook in the wp_footer() for testing
     */
    public function test_footer() {
        echo '<!--wp_footer-->';
    }

    /**
     *  Check for the existence of the strings where wp_head and wp_footer should have been called from
     */
    public function check_head_footer()
    {
        // Build the url to call, NOTE: uses home_url and thus requires WordPress 3.0
        $url = add_query_arg( array( 'test-head' => '1', 'test-footer' => '1' ), home_url() );

        //var_dump($url);

        // Perform the HTTP GET ignoring SSL errors
        $response = wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );

        // Grab the response code and make sure the request was sucessful
        $code = (int) wp_remote_retrieve_response_code( $response );

        //var_dump($code);

        //$code = 0;

        if ( $code == 200 ) {

            global $head_footer_errors;

            $head_footer_errors = array();

            // Strip all tabs, line feeds, carriage returns and spaces
            $html = preg_replace( '/[ s]/', '', wp_remote_retrieve_body( $response ) );
            //$html = wp_remote_retrieve_body( $response );

            // Check to see if we found the existence of wp_head
            if ( ! strstr( $html, '<!--wp_head-->' ) ){
                $head_footer_errors['nohead'] = 'Is missing the call to <?php wp_head(); ?> which should appear directly before </head>';
                $this->have_wp_head = false;
            }else{
                $this->have_wp_head = true;
            }
            // Check to see if we found the existence of wp_footer
            if ( ! strstr( $html, '<!--wp_footer-->' ) ){
                $head_footer_errors['nofooter'] = 'Is missing the call to <?php wp_footer(); ?> which should appear directly before </body>';
                $this->have_wp_footer = false;
            }else{
                $this->have_wp_footer = true;
            }

            // Check to see if we found wp_head and if was located in the proper spot
            if ( ! strstr( $html, '<!--wp_head--></head>' ) && ! isset( $head_footer_errors['nohead'] ) ){
                $head_footer_errors[] = 'Has the call to <?php wp_head(); ?> but it is not called directly before </head>';
                $this->have_wp_head_proper_spoot = false;
            }else{
                $this->have_wp_head_proper_spoot = true;
            }
            // Check to see if we found wp_footer and if was located in the proper spot
            if ( ! strstr( $html, '<!--wp_footer--></body>' ) && ! isset( $head_footer_errors['nofooter'] ) )
            {
                $head_footer_errors[] = 'Has the call to <?php wp_footer(); ?> but it is not called directly before </body>';
                $this->have_wp_footer_proper_spot = false;
            }else{
                $this->have_wp_footer_proper_spot = true;
            }

            // If we found errors with the existence of wp_head or wp_footer hook into admin_notices to complain about it
            if ( ! empty( $head_footer_errors ) )
                add_action ( 'admin_notices', array(&$this, 'admin_notices_output') );
        }
    }

    /**
     * Output admin notices
     */
    public function admin_notices_output()
    {
        global $head_footer_errors;

        if(empty($head_footer_errors)) return;
        // If we made it here it is because there were errors, lets loop through and state them all
        echo '<div class="error"><p><strong>Your active theme:</strong></p><ul>';
        foreach ( $head_footer_errors as $error )
            echo '<li>' . esc_html( $error ) . '</li>';
        echo '</ul></div>';
    }

    /**
     * Check if wp_head() exists in the template
     * @return bool
     */
    public function wphead_exists()
    {
        return $this->have_wp_head;
    }

    /**
     * Check if wp_footer() exists in the template
     * @return bool
     */
    public function wpfooter_exists()
    {
        return $this->have_wp_footer;
    }

    /**
     * Check if the wp_head() exists in the template and is in a right place
     * @return bool
     */
    public function is_wphead_properlyset()
    {
        return $this->have_wp_head && $this->have_wp_head_proper_spoot;
    }

    /**
     * Check if the wp_footer() exists in the template and is in a right place
     * @return bool
     */
    public function is_wpfooter_properlyset()
    {
        return $this->have_wp_footer && $this->have_wp_footer_proper_spot;
    }
}
?>