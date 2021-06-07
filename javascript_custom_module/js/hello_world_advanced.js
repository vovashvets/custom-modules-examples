(function () {
  'use strict';

  // Recovering the user name from drupalSettings
  let element = document.getElementById("salute");
  let user_name = drupalSettings.data.name;
  let user_mail = drupalSettings.data.mail;

  // Add to the HTML the new string using the parameter.
  element.innerHTML += "<br>" + "Update-> You are the user: " + user_name +
                       " with mail: " + user_mail;


  // Asking for the localStorage parameter value if exists.
  let visit_value = localStorage.getItem('visit_number');
  console.log("LocalStorage - current value: " + visit_value);

  // Same but for the sessionStorage.
  let session_value = sessionStorage.getItem('session_number');
  console.log("SessionStorage - current value: " + session_value);

  // Testing the localStorage visit value.
  if(visit_value === null) {

  // If null we'll create the initial value.
    localStorage.setItem('visit_number', 1);
    console.log("LocalStorage: " +localStorage.getItem('visit_number'));

  }else {

  // If not null we'll increment the current value.
    localStorage.setItem('visit_number', ++visit_value);
    console.log("LocalStorage: " + localStorage.getItem('visit_number'));
  }

  // Same for sessionStorage.
  if(session_value === null) {

  // If null we'll create the initial value.
    sessionStorage.setItem('session_number', 1);
    console.log("Session: " + sessionStorage.getItem('session_number'));

  }else {

  // If not null we'll increment the current value.
    sessionStorage.setItem('session_number', ++session_value);
    console.log("Session: " + sessionStorage.getItem('session_number'));
  }

  // Add to the HTML the counter value.
  element.innerHTML += "<br>" + "Total count of visits: " +
                                localStorage.getItem('visit_number');
  element.innerHTML += "<br>" + "Total count of visits during this session: " +
    sessionStorage.getItem('session_number');

})();
