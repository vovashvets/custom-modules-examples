<?php

namespace Drupal\regexp_checker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RegExpCheckerTestForm.
 */
class RegExpCheckerTestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'regexp_checker_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Building the form.
    $form['regexp_checker_about'] = [
      '#type' => 'item',
      '#markup' => $this->t('You can use this form page for testing Regular Expressions.'),
    ];

    $form['regexp_checker_pattern'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Regular Expression'),
      '#description' => $this->t('Put here your Regular Expression.'),
      '#cols' => 30,
      '#rows' => 15,
      '#wysiwyg' => FALSE,
      '#prefix' => '<div id="regexp_checker_pattern">',
      '#suffix' => '</div>',
    ];

    $form['regexp_checker_input'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Input'),
      '#description' => $this->t('Introduce your string or value for testing.'),
      '#cols' => 30,
      '#rows' => 15,
      '#wysiwyg' => FALSE,
      '#prefix' => '<div id="regexp_checker_input">',
      '#suffix' => '</div>',
    ];

    $form['regexp_checker_result'] = [
      '#type' => 'item',
      '#markup' => $this->t('Results zone.'),
      '#prefix' => '<div id="regexp_checker_final_result">',
      '#suffix' => '</div>',
    ];

    // $form['regexp_checker_result']['#attached']['drupalSettings']['data']['confirm'] = 'Travelling';
    $form['regexp_checker_action'] = [
      '#type' => 'button',
      '#value' => $this->t('Check'),
      '#weight' => 5,
      '#prefix' => '<div id="regexp_checker_button">',
      '#suffix' => '</div>',
      '#attached' => [
        'library' => [
          'regexp_checker/regexp_checker.showing_results',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
