<?php

namespace Drupal\javascript_custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the JavaScript Custom module example.
 */
class UnsplashServiceController extends ControllerBase {

  public function gettingUnsplash() {
    // This is the array for the render system ready to print.
    $info_array = [];

    // Set the first element only a initial welcome message.
    $info_array['initial_message'] = [
      '#type' => 'item',
      '#markup' => $this->t('Unsplash service connection.'),
      '#prefix' => '<div id="unsplash">',
      '#suffix' => '</div>',
      '#attached' => [
        'library' => [
          'javascript_custom_module/js_getting_unsplash_items',
        ],
      ],
    ];

    return $info_array;
  }

}
