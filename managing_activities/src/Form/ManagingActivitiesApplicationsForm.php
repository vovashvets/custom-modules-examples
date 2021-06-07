<?php

namespace Drupal\managing_activities\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Class ManagingActivitiesApplicationsForm implements the Managing Activities Applications Form.
 *
 * @package Drupal\managing_activities\Form
 * @access public
 * @see \Drupal\Core\Form\FormBase
 */
class ManagingActivitiesApplicationsForm extends FormBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */

  protected $entityTypeManager;

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
   * Drupal\Core\Mail\MailManagerInterface definition.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructs a new ManagingActivitiesApplicationsForm object.
   */
  public function __construct(
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entitytype_manager,
    LoggerChannelFactoryInterface $logger_manager,
    MessengerInterface $message_manager,
    MailManagerInterface $mail_manager) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entitytype_manager;
    $this->loggerManager = $logger_manager->get($this->config('managing_activities.settings')->get('module_name'));
    $this->messageManager = $message_manager;
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
      $container->get('messenger'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'managing_activities_applications';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Builds the header.
    $header = [
      'internal_node_id' => t('Node ID'),
      'request_key' => t('Request Key'),
      'name' => t('Name'),
      'lastname' => t('Lastname'),
      'identification' => t('Identification'),
      'date_of_birth' => t('Date of Birth'),
      'event' => t('Event'),
      'status' => t('Status'),
      'select_status' => t('Change Status'),
    ];

    // Gets the current existing Application nodes from database.
    $nodes = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'application']);

    // Builds the select status button options.
    $vid = $this->config('managing_activities.settings')->get('taxonomy_application_status');
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid, 0, NULL, FALSE);
    $terms_options = [];
    foreach ($terms as $position => $term) {
      $terms_options[$term->tid] = $term->name;
    }
    $terms_options[0] = '- No -';

    // Sets an initial message.
    $form['welcome'] = [
      '#type' => 'item',
      '#markup' => $this->t('Dear User: @user, Here you can change the status for applications.', ['@user' => $this->currentUser->getDisplayName()]),
      '#prefix' => '<div id="application_form_about">',
      '#sufix' => '</div>',
    ];

    // Builds the table.
    $form['table_applications'] = [
      '#type' => 'table',
      '#header' => $header,
      '#empty' => t('No applications found.'),
    ];

    // Gets all the node IDs using as index position.
    $index = array_keys($nodes);

    // Fills the rows and columns of the table.
    for ($i = 0; $i < count($nodes); $i++) {
      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Node ID'),
        '#title_display' => 'invisible',
        '#size' => 4,
        '#maxlenght' => 4,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->id(),
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Request Key'),
        '#title_display' => 'invisible',
        '#size' => 30,
        '#maxlenght' => 30,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->get('title')->first()->getValue(),
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#title_display' => 'invisible',
        '#size' => 20,
        '#maxlenght' => 20,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->get('field_first_name')->first()->getValue(),
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Last Name'),
        '#title_display' => 'invisible',
        '#size' => 20,
        '#maxlenght' => 20,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->get('field_last_name')->first()->getValue(),
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Identification'),
        '#title_display' => 'invisible',
        '#size' => 10,
        '#maxlenght' => 10,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->get('field_identification_number')->first()->getValue(),
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'date',
        '#title' => t('Date of Birth'),
        '#title_display' => 'invisible',
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $nodes[$index[$i]]->get('field_date_of_birth')->first()->getValue(),
      ];
      // Loads event name.
      $term_one_id = $nodes[$index[$i]]->get('field_event_id')->first()->getValue();
      $term_one = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_one_id['target_id']);
      $term_one_name = $term_one->label();

      // Loads application status.
      $term_two_id = $nodes[$index[$i]]->get('field_application_status')->first()->getValue();
      $term_two = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_two_id['target_id']);
      $term_two_name = $term_two->label();

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Event'),
        '#title_display' => 'invisible',
        '#size' => 20,
        '#maxlenght' => 20,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $term_one_name,
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'textfield',
        '#title' => t('Status'),
        '#title_display' => 'invisible',
        '#size' => 5,
        '#maxlenght' => 5,
        '#attributes' => [
          'readonly' => 'readonly',
          'disabled' => TRUE,
        ],
        '#default_value' => $term_two_name,
      ];

      $form['table_applications'][$i][] = [
        '#type' => 'select',
        '#title' => t('Status'),
        '#title_display' => 'invisible',
        '#options' => $terms_options,
        '#default_value' => 0,
      ];

    }
    $form['pager'] = [
      '#theme' => 'pager',
    ];
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
      '#button_type' => 'primary',
      '#prefix' => '<div id="applications_form_submit">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Gets the current number of Applications in processing from the table and report to the system.
    $items_values = $form_state->getValues()['table_applications'];
    $items_count = count($items_values);
    $this->messageManager->addMessage(t('Will be processed @count items.'), ['@count' => $items_count], 'status');
    $this->loggerManager->notice(t('Managing @count items from the Status Applications table'), ['@count' => $items_count]);

    // Loops over the items and checks differences between current status and changed status.
    $count_of_changes = 0;
    foreach ($items_values as $position => $item) {
      $changed_status = intval($item[8]);
      $current_status_name = $item[7];
      $vid = $this->config('managing_activities.settings')->get('taxonomy_application_status');
      $current_status_id = key($this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['name' => $current_status_name, 'vid' => $vid]));

      if (($current_status_id != $changed_status) && ($changed_status != 0)) {
        $getting_node = $this->entityTypeManager->getStorage('node')->load($item[0]);
        $getting_node->set("field_application_status", $changed_status);
        $getting_node->save();
        $count_of_changes++;

        // Getting the changed_status term name.
        $changed_status_name = ($this->entityTypeManager->getStorage('taxonomy_term')->load($changed_status))->getName();

        // Getting values for the email notifications.
        $key_notification = 'accepted_application';
        $key_message = 'accepted_mail_message';
        if ($changed_status_name == 'Denied') {
          $key_notification = 'denied_application';
          $key_message = 'denied_mail_message';
        }
        elseif ($changed_status_name == 'Pending') {
          $key_notification = 'pending_application';
          $key_message = 'pending_mail_message';
        }
        $key_email = $getting_node->get('field_email')->first()->getValue();

        $this->launchNotifications($key_notification, $key_email, $key_message);
      }
    }

    $this->messageManager->addMessage(t('@changed Nodes were processed for wich the status of the application was changed.', ['@changed' => $count_of_changes]));
    $this->loggerManager->notice(t('The Managing Activities module just changed the Application Status for @total Requests.'), ['@total' => $count_of_changes]);

  }

  /**
   * Launchs a new notification for assistants using mail.
   *
   * @param string $key_notification
   * @param string $key_email
   * @param string $key_message
   */
  public function launchNotifications(string $key_notification, string $key_email, string $key_message) {
    $module = $this->config('managing_activities.settings')->get('module_name');
    $key = $key_notification;
    $to = $key_email;
    $langcode = $this->currentUser->getPreferredLangcode();
    $params['message'] = $this->config('managing_activities.settings')->get($key_message);

    // Launchs the mail to the users.
    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params);

    if ($result['result'] !== TRUE) {
      $this->loggerManager->error(t('There was a problem sending a new email to an user'));
    }
    else {
      $this->loggerManager->notice(t('A new email was sending to an user'));
    }

  }

}
