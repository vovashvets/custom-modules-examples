<?php

namespace Drupal\managing_activities\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Component\Utility\EmailValidatorInterface;

/**
 * Class ManagingActivitiesRegisterForm implements the Managing Activities Register Form.
 *
 * @package Drupal\managing_activities\Form
 * @access public
 * @see \Drupal\Core\Form\FormBase
 */
class ManagingActivitiesRegisterForm extends FormBase {

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
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */

  protected $entityTypeManager;

  /**
   * Drupal\Component\Datetime\TimeInterface definition.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $timeManager;

  /**
   * Drupal\Core\Mail\MailManagerInterface definition.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * String definition.
   *
   * @var string
   */
  protected $keyApplication;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerManager;

  /**
   * Drupal\Core\Messenger\MessengerInterface.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messageManager;

  /**
   * Constructs a new ManagingActivitiesRegisterForm object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   * @param \Drupal\Component\Utility\EmailValidatorInterface $email_validator
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entitytype_manager
   * @param \Drupal\Component\Datetime\TimeInterface $time_manager
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_manager
   * @param \Drupal\Core\Messenger\MessengerInterface $message_manager
   */
  public function __construct(
  AccountProxyInterface $current_user,
  EmailValidatorInterface $email_validator,
  EntityTypeManagerInterface $entitytype_manager,
  TimeInterface $time_manager,
  MailManagerInterface $mail_manager,
  LoggerChannelFactoryInterface $logger_manager,
  MessengerInterface $message_manager) {
    $this->currentUser = $current_user;
    $this->emailValidator = $email_validator;
    $this->entityTypeManager = $entitytype_manager;
    $this->timeManager = $time_manager;
    $this->mailManager = $mail_manager;
    $this->loggerManager = $logger_manager->get($this->config('managing_activities.settings')->get('module_name'));
    $this->messageManager = $message_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('email.validator'),
      $container->get('entity_type.manager'),
      $container->get('datetime.time'),
      $container->get('plugin.manager.mail'),
      $container->get('logger.factory'),
      $container->get('messenger')
    );
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'managing_activities_register';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Processing the taxonomy terms ready for a select list.
    $vid = $this->config('managing_activities.settings')->get('taxonomy_events_name');
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid, 0, NULL, FALSE);
    $terms_options = [];
    foreach ($terms as $position => $term) {
      $terms_options[$term->tid] = $term->name;
    }

    // Building the form.
    $form['#prefix'] = '<div id="register_form_wrapper">';
    $form['#suffix'] = '</div>';

    $form['managing_activities_register_about'] = [
      '#type' => 'item',
      '#markup' => $this->t('We will process your request and respond by mail as soon as possible.'),
      '#prefix' => '<div id="register_form_about">',
      '#sufix' => '</div>',
    ];

    $form['managing_activities_register_event'] = [
      '#type' => 'select',
      '#title' => $this->t('Pick your event'),
      '#description' => $this->t('Select the event where you want to apply.'),
      '#options' => $terms_options,
      '#weight' => '0',
      '#prefix' => '<div id="register_form_event">',
      '#sufix' => '</div>',
    ];

    $form['managing_activities_register_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#attributes' => [
        'placeholder' => t('John'),
      ],
      '#description' => $this->t('Set your First Name.'),
      '#maxlength' => 30,
      '#size' => 30,
      '#weight' => '1',
      '#required' => TRUE,
      '#prefix' => '<div id="register_form_name">',
      '#suffix' => '</div>',
    ];

    $form['managing_activities_register_lastname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#attributes' => [
        'placeholder' => t('Doe'),
      ],
      '#description' => $this->t('Set your Last Name.'),
      '#maxlength' => 30,
      '#size' => 30,
      '#weight' => '2',
      '#required' => TRUE,
      '#prefix' => '<div id="register_form_lastname">',
      '#suffix' => '</div>',
    ];

    $form['managing_activities_register_email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-Mail'),
      '#attributes' => [
        'placeholder' => t('johndoe@mail.com'),
      ],
      '#description' => $this->t('Set your e-mail.'),
      '#maxlength' => 30,
      '#size' => 30,
      '#weight' => '3',
      '#required' => TRUE,
      '#prefix' => '<div id="register_form_email">',
      '#suffix' => '</div>',
    ];

    $form['managing_activities_register_identification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Indentification Number'),
      '#attributes' => [
        'placeholder' => t('12345678A'),
      ],
      '#description' => $this->t('Set your Identification Number or DNI for the Spanish State.'),
      '#maxlength' => 30,
      '#size' => 30,
      '#weight' => '4',
      '#required' => TRUE,
      '#prefix' => '<div id="register_form_identification">',
      '#suffix' => '</div>',
    ];

    $form['managing_activities_register_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of birth'),
      '#description' => $this->t('Set your date of birth.'),
      '#weight' => '5',
      '#required' => TRUE,
      '#prefix' => '<div id="register_form_date">',
      '#suffix' => '</div>',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Request'),
      '#button_type' => 'primary',
      '#prefix' => '<div id="register_form_submit">',
      '#suffix' => '</div>',
    ];
    // We're going to add the custom JavaScript related library.
    $form['#attached']['library'][] = 'managing_activities/getting_feedback';
  

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (($this->timeManager->getCurrentTime()) < (strtotime('+18 years', strtotime($form_state->getValue('managing_activities_register_date'))))) {
      $form_state->setErrorByName('managing_activities_register_date', $this->t("The age can't be under 18."));
    } 
    if (!($this->emailValidator->isValid($form_state->getValue('managing_activities_register_email')))) {
      $form_state->setErrorByName('managing_activities_register_email', $this->t("The email is not valid."));
    }
    if (!$this->checkSpanishDNI($form_state->getValue('managing_activities_register_identification'))) {
      $form_state->setErrorByName('managing_activities_register_identification', $this->t("Your Identification key is not like a Spanish DNI, neither a NIF or a NIE."));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Creates the new Application node.
    $this->createNewApplications($form_state);
    // Loads a feedback window for users in screen.
    $this->setFeedback($form_state);
    // Send a e-mail notification to the applications manager.
    $this->launchNotification();
  }

  /**
   * Loads a feedback in screen about the new application.
   */
  public function setFeedback(FormStateInterface $form_state) {
    $this->messageManager->addMessage(t('Your application has been processed.'), 'status');
  }

  /**
   * Launchs a new notification for admins using mail.
   */
  public function launchNotification() {
    $module = $this->config('managing_activities.settings')->get('module_name');
    $key = 'register_application';
    $to = $this->config('managing_activities.settings')->get('notice_mail');
    $langcode = $this->currentUser->getPreferredLangcode();
    $params['message'] = $this->config('managing_activities.settings')->get('notice_mail_message');
    $params['key_application'] = $this->keyApplication;

    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params);

    if ($result['result'] !== TRUE) {
      $this->loggerManager->error(t('There was a problem sending a new email with key: @key'), ['@key' => $this->keyApplication]);
    }
    else {
      $this->loggerManager->notice(t('A new email was sending to the Applications Manager with key: @key.'), ['@key' => $this->keyApplication]);
    }

  }

  /**
   * Create new node with type Application.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object with all the values registered from form.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNewApplications(FormStateInterface $form_state) {

    // An application will be in Pending status by default.
    $term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['name' => 'Pending']);

    // Creates a new node for the Application type.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $this->keyApplication = $form_state->getValue('managing_activities_register_identification') .
    '-' . $form_state->getValue('managing_activities_register_name') .
    '-' . $form_state->getValue('managing_activities_register_lastname');

    $node = $node_storage->create([
      'type' => 'application',
      'langcode' => 'en',
      'created' => $this->timeManager->getRequestTime(),
      'changed' => $this->timeManager->getRequestTime(),
      'uid' => 1,
      'title' => $this->keyApplication,
      'field_application_status' => key($term),
      'field_date_of_birth' => $form_state->getValue('managing_activities_register_date'),
      'field_email' => $form_state->getValue('managing_activities_register_email'),
      'field_event_id' => $form_state->getValue('managing_activities_register_event'),
      'field_identification_number' => $form_state->getValue('managing_activities_register_identification'),
      'field_first_name' => $form_state->getValue('managing_activities_register_name'),
      'field_last_name' => $form_state->getValue('managing_activities_register_lastname'),
    ]);
    $node->save();
  }

  /**
   * Check the format of a string as a Spanish DNI, NIF or NIE.
   *
   * @param string $identificator
   *   String for the identification.
   *
   * @return bool
   *   Return if the identificator has a DNI/NIF/NIE form.
   */
  public function checkSpanishDni($identificator) {
    if (strlen($identificator) != 9 || preg_match('/^([XYZ]?)([0-9]{7,8})([A-Z])$/i', $identificator, $matches) !== 1) {
      return FALSE;
    }
    $map = 'TRWAGMYFPDXBNJZSQVHLCKE';
    list(, $nieLetter, $number, $letter) = $matches;

    if ($nieLetter == 'Y') {
      $number = '1' . $number;
    }
    elseif ($nieLetter == 'Z') {
      $number = '2' . $number;
    }

    return strtoupper($letter) === $map[((int) $number) % 23];
  }

}
