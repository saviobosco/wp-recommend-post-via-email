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


class DGRVE
{
    /**
     * Used mainly to register the ajax actions
     * Constructor
     */
    public function __construct()
    {
        add_action("wp_ajax_dgrve", array(&$this, "sendToFriend"));
        add_action("wp_ajax_nopriv_dgrve", array(&$this, "sendToFriend"));
    }

    /**
     * Return the "recommendation count" for the recommended post
     *
     * @param $post_id
     * @return int
     */
    public static function getReccomendationNumber($post_id)
    {
        $count = get_post_meta($post_id, "recommendation_count", true);
        if($count == "" || (!isset($count) && empty($count) ))
        {
            $count = 0;
        }
        return (int)$count;
    }

    /**
     * Increments the post meta "recommendation_count" for the recommended post
     *
     * @param $post_id
     * @return bool
     */
    public static function addRecommendationNumber($post_id)
    {
        if(false != get_post_status( $post_id ) )
        {
            $count = get_post_meta($post_id, "recommendation_count", true);
            if(empty($count) || $count == "")
            {
                $count = 1;
            }else
            {
                $count = (int)$count + 1;
            }
            $result = update_post_meta($post_id, "reccomendation_count", $count);
            return $result;

        }
        return false;
    }


    /**
     * Output recommend to friend button and initialize the popup used for
     * gathering the friend's email address.
     *
     * Additional arguements can be passed to replace the form title, and other
     * text used, also the current post id. If no post id provided it will try
     * to get the post via get_post() function.
     *
     * @param null $args
     * @param null $post_id
     * @return string
     */
    public static function recommendToFriend( $args = null, $post_id = null )
    {

        if(null === $post_id )
        {
            $__post = get_post();
        }else
        {
            if(false === get_post_status( $post_id ))
            {
                $__post = get_post();
            }
            else
            {
                $__post = get_post($post_id);
            }
        }

        //Get modal popup content
        $modal_content = isset($args['content']) ? $args['content'] : __("Enter your friends email to recommend this post to your friend.", "dgrve");
        $email_content = isset($args['emailmessage']) ? $args['emailmessage'] : __("Hello! One of your friends sent you the link to this page. To read our article please go to the link below!", 'dgrve');
        $button_content = isset($args['btnname']) ? $args['btnname'] : __("Recommend", 'dgvrve');
        $title_content = isset($args['title']) ? $args['title'] : __("Recommend to Friend", 'dgrve');
        $nonce = wp_create_nonce("dgrve_nonce");
        $postid = $__post->ID;

        //Get the link
        $post_link = get_permalink($__post->ID);


        //Generate popup id
        $popupID = "modal-" . $__post->ID;


        $output = '<a data-remodal-target="' . $popupID . '">' . $button_content . '</a>';
        $output .=  '<div class="remodal" data-remodal-id="' . $popupID . '">';
        $output .=  '<button data-remodal-action="close" class="remodal-close"></button>';
        $output .=  '<h1>' . $title_content . '</h1>';
        $output .=  '<p>';
        $output .=  $modal_content;
        $output .=  '</p>';
        $output .=  '<input type="text" name="dgrve_email" class="dgrveemail" placeholder="Enter friend email address">';
        $output .=  '<input type="hidden" name="dgrve_postid" class="dgrvepostid" value="' . $postid . '"  />';
        $output .=  '<p class="dgrvestatus" style="display:none"></p>';
        $output .=  '<button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>';
        $output .=  '<button data-nonce="' . $nonce . '" class="recommend-friend remodal-confirm">' . $button_content . '</button>';
        $output .=  '</div>';

        return $output;
    }

    /**
     * This function is called with Ajax to process the request
     */
    public function sendToFriend() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dgrve_nonce")) {
            $result['type'] = "error";
            $result = json_encode($result);
            echo $result;
            exit("No naughty business please");
        }

        $email = $_REQUEST['dgrve_email'];
        $post_id = $_REQUEST['dgrve_postid'];

        $emailsent = false;
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            //wp_mail()
            $emailsent = true;

            if($emailsent)
            {
                self::addRecommendationNumber($post_id);
            }

        }
        if($emailsent === false) {
            $result['type'] = "error";
        }
        else {
            $result['type'] = "success";
            $result['recommendationCount'] = self::getReccomendationNumber($post_id);
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
        die();
    }
}

// Create new instance to intialize the plugin
new DGRVE();