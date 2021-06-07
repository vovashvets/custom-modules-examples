(function () {
  'use strict';

  // Put here your custom JavaScript code.

  // First creating and initialising the new element.
  let new_tag = document.createElement("P");
  new_tag.setAttribute("id", "my_p");
  new_tag.innerHTML = "Hello World from a custom Dialog Window.";

  // Then we'll creating a new modal window.
  Drupal.dialog(new_tag, {
    title: 'Custom Dialog Window',
    buttons: [{
      text: 'Change colour',
      click: function() {
        let change_colour = document.getElementById("my_p");
        change_colour.style.backgroundColor = "red";
      }
      }, {
      text: 'Get the salute',
      click: function() {
        let my_section = document.getElementById('salute');
        document.getElementById("my_p").append(my_section);
      }
    }],
    classes: {
      "ui-dialog": "highlight"
    }
  }).showModal();

})();

