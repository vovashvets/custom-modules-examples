<?php

namespace Drupal\javascript_custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the JavaScript Custom module example.
 */
class TestingBehaviorsController extends ControllerBase {

  public function executingBehaviors() {
    // This is the array for the render system ready to print.
    $behaviors_array = [];

    // Set the first element only a initial welcome message.
    $behaviors_array['initial_message'] = [
      '#type' => 'item',
      '#markup' => $this->t('Testing from JavaScript custom library.'),
      '#prefix' => '<div id="behaviors_section">',
      '#suffix' => '</div>',
      '#attached' => [
        'library' => [
          'javascript_custom_module/js_testing_drupal_behaviors',
        ],
      ],
    ];

    return $behaviors_array;
  }

}
