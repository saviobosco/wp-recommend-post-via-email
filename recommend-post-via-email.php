<?php
/**
 * Plugin Name: Recommend Post Via Email
 * Plugin URI: https://github.com/gdarko/recommend-post-via-email
 * Description: Easily recommend a post to a friend via email.
 * Version: 1.0.0
 * Author: Darko Gjorgjijoski
 * Author URI: http://darkog.com
 * License: GPL V2
 *
 * Copyright (C) 2015  Darko Gjorgjijoski
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

class DGRecommendPostViaEmail {

    /**
     * Singleton instance
     * @var null
     */
    private static $instance = null;

    /**
     * Keep The plugin path
     * @var string
     */
    private $plugin_path;

    /**
     * Plugin Url Path
     * @var string
     */
    private $plugin_url;

    /**
     * Text Domain for further translatons
     * @var string
     */
    private $text_domain = 'dgrve';

    /**
     * @var DG_CheckWpHeaderAndFooter
     */
    private $wphfchecker;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        // If an instance hasn't been created and set to $instance create an instance and set it to $instance.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
     */
    private function __construct() {
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url  = plugin_dir_url( __FILE__ );

        load_plugin_textdomain( $this->text_domain, false, $this->plugin_path . '\lang' );

        $this->includes();

        add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );

        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

        $this->run_plugin();
    }

    /**
     * Get plugin url path
     * @return string
     */
    public function get_plugin_url() {
        return $this->plugin_url;
    }

    /**
     * Get plugin path
     * @return string
     */
    public function get_plugin_path() {
        return $this->plugin_path;
    }

    /**
     * Place code that runs at plugin activation here.
     */
    public function activation() {

    }

    /**
     * Place code that runs at plugin deactivation here.
     */
    public function deactivation() {

    }

    /**
     * Enqueue and register JavaScript files here.
     */
    public function register_scripts() {

        if( ! is_admin() )
        {
            wp_enqueue_script( 'dgrpe-customjs', $this->plugin_url . '/assets/js/custom.js', array(), '1.0.0', true );
            wp_enqueue_script( 'dgrpe-remmodal-js', $this->plugin_url . '/assets/js/remodal/remodal.min.js', array(), '1.0.0', true );
            wp_localize_script( 'dgrpe-customjs', 'DGRVE', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'my_voter_script' );
        }

    }

    /**
     * Enqueue and register CSS files here.
     */
    public function register_styles() {
        if( !is_admin() )
        {
            wp_enqueue_style( 'dgrpe-remodal-css', $this->plugin_url . '/assets/js/remodal/remodal.css' );
            wp_enqueue_style( 'dgrpe-remodaltheme-css', $this->plugin_url . '/assets/js/remodal/remodal-default-theme.css' );
            wp_enqueue_style( 'dgrpe-customcss', $this->plugin_url . '/assets/css/custom.css' );
        }
    }

    /**
     * Include scripts
     */
    private function includes()
    {
        require($this->plugin_path . "/includes/class-check-wpheader-footer.php");
        require($this->plugin_path . "/includes/class-recommendation-via-email.php");
        require($this->plugin_path . "/includes/class-shortcodes.php");
    }

    /**
     * Place code for your plugin's functionality here.
     */
    private function run_plugin() {

        //Initialize Checker to check existence of wp_head and wp_footer tags
        $this->wphfchecker = new DG_CheckWpHeaderAndFooter();
    }
}

//Singleton Instance Plugin
DGRecommendPostViaEmail::get_instance();
