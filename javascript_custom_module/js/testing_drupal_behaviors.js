(function ($) {
  'use strict';
  Drupal.behaviors.usingcontext_one = {
    attach: function(context, settings) {
      $("#behaviors_section").append('<p>Hello world by Behavior One</p>');    }
  };

  Drupal.behaviors.usingcontext_two = {
    attach: function(context, settings) {
      $(document).find("#behaviors_section").append('<p>Hello world by Behavior Two</p>');
    }
  };

  Drupal.behaviors.usingcontext_three = {
    attach: function(context, settings) {
      $(context).find('#behaviors_section').append('<p>Hello world by Behavior Three</p>');
    }
  };


  Drupal.behaviors.usingcontext_four = {
    attach: function(context, settings) {
      $('#behaviors_section').once('cacafuti').append('<p>Hello world by Behavior Four</p>');
    }
  };

  Drupal.behaviors.usingcontext_five = {
    attach: function(context, settings) {
      $(context).once('cacafuti2').find('#behaviors_section').append('<p>Hello world by Behavior Five</p>');
    }
  };

}(jQuery));


