(function (parameter) {
  'use strict';

  // Get the HTML element by it ID.
  let element = document.getElementById("salute");
  console.log(element);

  // Add to the HTML the new string using the parameter.
  element.innerHTML += "Salute, " + parameter +". ";

  // Creating and adding a line for the HTML element.
  var hr = document.createElement("hr");
  console.log(hr);
  element.prepend(hr);

})('Dear User');
