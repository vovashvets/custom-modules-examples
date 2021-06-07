(function ($, Drupal) {
    'use strict';

    $( document ).ready(function () {
        console.log( "ready!" );

        $("#regexp_checker_button").click(() => {
             let element = $("#regexp_checker_final_result");

            // Add info over the HTML element.
            element.html("<p> <strong>Update-> Will be processed the Regular Expression: </strong></p>");
            // For testing.
            console.log("Update-> Will be processed the Regular Expression: ");
        });

    });

})(jQuery, Drupal);
