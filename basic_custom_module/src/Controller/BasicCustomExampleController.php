<?php

namespace Drupal\basic_custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the Basic Custom module example.
 */
class BasicCustomExampleController extends ControllerBase {

  /**
   * Hello World controller method.
   *
   * @return array
   *   Return just an array with a piece of markup to render in screen.
   */
  public function helloWorld() {

    return [
      '#markup' => $this->t('Hello World, I am just a basic custom example.'),
    ];
  }

}

