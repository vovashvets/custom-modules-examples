(function ($) {
  'use strict';
  $(document).ready(function() {
    let submit = false;
    let name = false;
    let lastname = false;
    let mail = false;
    let identificator = false;
    let birthdate = false;
    let finally_full_filled = false;
        
    //$("#edit-submit").click(function (e) {
      $("#register_form_submit").click(function (e) {
        if(!finally_full_filled) {
              e.preventDefault();
              submit = true;
            if($('#edit-managing-activities-register-name').val() != '') {
                if( $('#warning_name').length ) {
                    $( "#warning_name" ).remove();
                }
                name = true;
            } else {
                if($("#warning_name").length === 0) {
                    $("#register_form_name").append('<p id="warning_name" style="color:red">Sorry but the name can\'t be empty.</p>');
                }
            }
            if ($('#edit-managing-activities-register-lastname').val() != '') {
                if( $('#warning_lastname').length ) {
                    $( "#warning_lastname" ).remove();
                }
                lastname = true;
            } else {
                if($("#warning_lastname").length === 0) {
                    $("#register_form_lastname").append('<p id="warning_lastname" style="color:red">Sorry but the lastname can\'t be empty.</p>');
                }
            }
            let email = $('#edit-managing-activities-register-email').val();
            // Check if email is valid.
            if((email != '') && (validateEmail(email))) {
                if( $('#warning_email').length ) {
                    $( "#warning_email" ).remove();
                }
                mail = true;
            } else {
                if($("#warning_email").length === 0) {
                    $("#register_form_email").append('<p id="warning_email" style="color:red">Sorry but the mail must be real.</p>');
                }
            }
            let dni = $('#edit-managing-activities-register-identification').val();
                // Check if the identification code is valid.
                if((dni != '') && (validateDNI(dni))) {
                    if( $('#warning_identification').length ) {
                        $( "#warning_identification" ).remove();
                    }
                    identificator = true;
                } else {
                    if ($("#warning_identification").length === 0) {
                        $("#register_form_identification").append('<p id="warning_identification" style="color:red">Sorry but your Spanish Identification Code seems wrong.</p>');
                    }
                }
            // Check if date of birth is valid.
             let date = $('#edit-managing-activities-register-date').val();
                
                if((date != '') && (validateDate(date)) ) {
                    if( $('#warning_date').length ) {
                        $( "#warning_date" ).remove();
                    }
                  birthdate = true;
                } else {
                    if ($("#warning_date").length === 0) {
                        $("#register_form_date").append('<p id="warning_date" style="color:red">Sorry but your date of birth seems wrong, You need be over 18 years old..</p>');
                    }
                }
                if(submit && name && lastname && mail && identificator && birthdate) {
                    window.alert("Thanks for show your interest! \nNow a new request for assistance will be processed in our systems. \nYou will be informed of your request by email. \nClose this message to confirm the request and conclude the process.");
                    finally_full_filled = true;
                    $("#edit-submit").trigger('click');
                }
           }
           
            }
        );
        function validateDate(date){
            let today = new Date(Date.now());
            let years_ago = new Date((new Date(today).getFullYear() - 18) + '-' + new Date(today).getMonth() + '-' + new Date(today).getDate());
            let reference = years_ago.getFullYear() +'-'+years_ago.getMonth() +'-'+years_ago.getDay();
            let result = reference > date;
            return result;
        }
        
        function validateEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
        
        function validateDNI(value) {
            let validChars = 'TRWAGMYFPDXBNJZSQVHLCKET';
            let nifRexp = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
            let nieRexp = /^[XYZ]{1}[0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
            let str = value.toString().toUpperCase();

            if (!nifRexp.test(str) && !nieRexp.test(str)) {
                return false;
            }

            let nie = str
                .replace(/^[X]/, '0')
                .replace(/^[Y]/, '1')
                .replace(/^[Z]/, '2');

            let letter = str.substr(-1);
            let charIndex = parseInt(nie.substr(0, 8)) % 23;

            if (validChars.charAt(charIndex) === letter) {
                return true;
            }
            return false;
        }
    });

})(jQuery);
