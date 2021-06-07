(function ($) {
  'use strict';

  $(document).ready(function() {

    console.log("The Playing with jQuery script has been loaded");

    // Creating the new elements just a div and a button.
    $('#block-bartik-page-title').append("<input type='button' id='getting-bacon' class='btn-bacon' value='Bacon' />");
    $('#block-bartik-page-title').append("<div id='bacon-text'></div>");

    // Hide the block by now.
    $("#bacon-text").hide();

    // Adding a click event to the former button.
    $('#getting-bacon').click(function () {

      // Hidding the block for the next load.
      $("#bacon-text").hide();

      // Getting values in JSON format.
      $.getJSON('https://baconipsum.com/api/?callback=?',
        {'type': 'meat-and-filler', 'start-with-lorem': '1', 'paras': '4'},
        function (baconTexts) {

        // We are inside the callback function for success in JSON request.
          if (baconTexts && baconTexts.length > 0) {

            $("#bacon-text").html('');

            // Loop into the received items.
            for (var i = 0; i < baconTexts.length; i++) {

              // Creating the naming keys.
              var bacon_id = "bacontext_" + i;
              var new_bacon = "<p" + " id='" + bacon_id + "'" + ">" + baconTexts[i] + "</p>";

              // Add the new element to the div mother.
              $("#bacon-text").append(new_bacon);
            }

            // Show the div mother show in a progressive way.
            $("#bacon-text").slideDown(7000, function(){
              console.log("New bacon has been loaded");
            });
          }
        });
    });
  });

})(jQuery);
