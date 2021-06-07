(function ($, Drupal) {

  'use strict';
  Drupal.behaviors.getting_unsplash_items = {
    attach: function(context, settings) {
      $(context).find("#unsplash").once('unsplashtest').each(function() {
        $(this).click(function () {
          console.log('it works!');
        });

        // Loading initial message.
        $(this).append('<p>Welcome to the Unsplash consumption Test.</p>')

        // Adding the buttons through jQuery.
        $("#unsplash", context).append("<button type='button' id='load_button'>Load Images</button>" );
        $("#unsplash", context).append("<button type='button' id='clean_button'>Clean Board</button>" );

        // Adding an event listener to the new buttons using jQuery.
        $('#load_button').click(function() {

        // In case of click we will clean the written former message.
        $("#message", context).remove();

          // In case of click we will call to the prompt window.
          processingKeywords();
        });

        $('#clean_button').click(function() {

          // In case of click we will clean the written former message.
          $("#message", context).remove();
          // And we will remove the entire image board too.
          $("#image-board").remove();
        });

        // Loading basic values Access Key and End Point.
        const unsplash_key = 'YOUR-APP-CODE';
        const end_point = 'https://api.unsplash.com/search/photos';

        // Show values in console and screen.
        console.log(unsplash_key);
        console.log(end_point);

       function processingKeywords(){
        let message = '';
        let option = prompt("Please write a keyword for search: ", "boat");

        if(option == null || option == ""){

          // Null option without response.
          message = "Sorry but the keyword is empty.";

          // Render in screen the message from the prompt.
          $("#unsplash", context).append("<br/><p id='message'>" + message + "</p>");
        }else {

          // Valid answer launches request.
          message = "Ok, we're searching..." + option;

          // Render in screen the message from the prompt.
          $("#unsplash", context).append("<br/><p id='message'>" + message + "</p>");

          // Launching external request with some delay with arrow format.
          setTimeout(() => {
            gettingImages(option);

            }, 4000);

          }

        }

        async function gettingImages(keyword){

          //  Building the petition.
          let response = await fetch(end_point + '?query=' + keyword + '&client_id=' + unsplash_key);

          // Processing the results.
          let json_response = await response.json();
          // Getting an array with URLs to images.
          let images_list = await json_response.results;

          // Calling the createImages method.
          creatingImages(images_list);

        }

        function creatingImages(images_list) {

         // If a former image board exists we will delete it.
          $("#image-board", context).remove();

         // Creating a new image board as frame for the images.
         $("#unsplash", context).append("<section id='image-board'> </section>");

          // We will add some CSS classes for styling the image board.
          $("#image-board").addClass("images-frame");


          // Now we will set the received images inside the new board.
          for(let i = 0; i < images_list.length; i++){
            const image = document.createElement('img');
            image.src = images_list[i].urls.thumb;
            document.getElementById('image-board').appendChild(image);
          }

          // When finished we will put a border for the image board.
          $(".images-frame").css({'background-color': '#babb8f', 'border': '5px solid #1E1E1E'});

        }
      });
    }
  };

})(jQuery, Drupal);


