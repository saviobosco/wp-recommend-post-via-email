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
 * Verify Email
 * @param email
 * @returns {boolean}
 */
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}

/**
 * jQuery Code goes here
 */
jQuery(document).ready(function(){

    //initalize remodal
    var inst = jQuery('[data-remodal-id~=modal]').remodal();

    //handle email ajax recommendaiton
    var emailSent = false;
    jQuery(".recommend-friend").on("click", function(){

        var self = jQuery(this);

        if( ! emailSent )
        {
            var email = self.siblings(".dgrveemail").val();
            var nonce = self.attr("data-nonce");
            var postid = self.siblings(".dgrvepostid").val();

            if( ! validateEmail(email)){
                self.siblings(".dgrveemail").addClass('error');
                return;
            }else{
                self.siblings(".dgrveemail").removeClass('error');
            }

            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : DGRVE.ajaxurl,
                data : {
                    action: "dgrve",
                    dgrve_email : email,
                    dgrve_postid : postid,
                    nonce: nonce
                },
                success: function(response) {
                    if(response.type == "success") {
                        emailSent = true;
                        self.attr("disabled", emailSent);
                        self.siblings(".dgrvestatus").text("Thanks for your recommendation. Your friend will receive your recommendation on his email");
                        self.siblings(".dgrvestatus").fadeIn();
                    }
                    else {
                        alert("Error sending email. Please try again");
                    }
                }
            })
        }

    });


})