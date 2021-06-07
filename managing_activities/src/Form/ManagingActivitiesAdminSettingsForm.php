<?php

namespace Drupal\managing_activities\Form;

use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ManagingActivitiesAdminSettingsForm implements the Managing Activities Settings Form.
 *
 * @package Drupal\managing_activities\Form
 * @access public
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class ManagingActivitiesAdminSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'managing_activities.settings';

  /**
   * Drupal\Component\Utility\EmailValidator.
   *
   * @var emailValidator
   */
  protected  $emailValidator;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   */
  public function __construct(ConfigFactoryInterface $config_factory, EmailValidator $email_validator) {
    parent::__construct($config_factory);
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('email.validator')
    );
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'managing_activities_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * Build the Managing Activities Config form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Getting the config default values.
    // Always last values or initial by default if post install.
    $config = $this->config(static::SETTINGS);

    // Building the Form.
    $form['managing_activities_settings_about'] = [
      '#type' => 'item',
      '#markup' => $this->t('Add here the information about the different
                   configuration values for the Managing Activities Config Form.'),
    ];

    $form['managing_activities_settings_age'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Age for registration'),
      '#description' => $this->t('Add a minimum age for attendee registration.'),
      '#default_value' => $config->get('minimum_age'),
    ];

    $form['managing_activities_settings_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('Email direction for notices'),
      '#description' => $this->t('Add a email address for notices.'),
      '#default_value' => $config->get('notice_mail'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Check values for age and notifications mail.
    if ($form_state->getValue('managing_activities_settings_age') < 18) {
      $form_state->setErrorByName('managing_activities_settings_age', $this->t("The age can't be under 18."));
    }
    if (!($this->emailValidator->isValid($form_state->getValue('managing_activities_settings_mail')))){
      $form_state->setErrorByName('managing_activities_settings_mail', $this->t("The email is not valid."));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set the new values in the config object of the module.
    $this->config(static::SETTINGS)
      ->set('minimum_age', $form_state->getValue('managing_activities_settings_age'))
      ->set('notice_mail', $form_state->getValue('managing_activities_settings_mail'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
