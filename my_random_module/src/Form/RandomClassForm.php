<?php

namespace Drupal\my_random_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Component\Utility\EmailValidatorInterface;

/**
 * Class RandomClassForm.
 */
class RandomClassForm extends FormBase {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Component\Utility\EmailValidatorInterface definition.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Constructs a new RandomClassForm object.
   */
  public function __construct(
    Connection $database,
    AccountProxyInterface $current_user,
    EmailValidatorInterface $email_validator
  ) {
    $this->database = $database;
    $this->current_user = $current_user;
    $this->email_validator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('current_user'),
      $container->get('email.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'default_random_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Build the base query.
    $query = $this->database->select('comment_field_data', 'c')
      ->fields('c')
      ->condition('c.uid', $this->current_user->id(), '=');

    // Get the number of registers.
    $query_counter = $query->countQuery();
    $result = $query_counter->execute();
    $count = $result->fetchField();

    // Get the Content Types and its keys.
    $options = node_type_get_names();
    $defaults = array_keys($options);

    // Building the form.
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#value' => $this->current_user->getDisplayName(),
      '#description' => $this->t('User Name'),
      '#maxlength' => 64,
    ];
    $form['id_user'] = [
      '#type' => 'number',
      '#value' => $this->current_user->id(),
      '#title' => $this->t('User ID'),
      '#description' => $this->t('User ID'),
      '#maxlength' => 64,
      '#weight' => '1',
    ];
    $form['number_comments'] = [
      '#type' => 'number',
      '#value' => $count,
      '#title' => $this->t('Number of Comments'),
      '#description' => $this->t('Number of Comments'),
      '#weight' => '3',
    ];
    $form['types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content Types'),
      '#description' => $this->t('Select Content Types'),
      '#options' =>   $options,
      '#default_value' => $defaults,
      '#weight' => '4',
    ];

    // Testing if the user is logged or not.
    if(!$this->current_user->isAuthenticated()) {
      $form['help'] = [
        '#type' => 'item',
        '#title' => $this->t('Please, read the conditions.'),
        '#markup' => $this->t('<strong>Only registered users can send info.</strong>'),
        '#weight' => 5,
      ];

    }else {
      $form['email'] = [
        '#type' => 'email',
        '#value' => $this->current_user->getEmail(),
        '#title' => $this->t('Email'),
        '#description' => $this->t('User email'),
        '#weight' => '2',
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#weight' => 5,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get the email value from the field.
    $mail = $form_state->getValue('email');

    // Test the format of the email.
    if(!$this->email_validator->isValid($mail)) {
      $form_state->setErrorByName('email', $this->t('The %email is not valid email.',
        ['%email' => $mail]));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
