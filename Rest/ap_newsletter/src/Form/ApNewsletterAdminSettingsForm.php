<?php

namespace Drupal\ap_newsletter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ApNewsletterAdminSettingsForm implements the AP Newsletter Settings Form.
 *
 * @package Drupal\ap_newsletter\Form
 * @access public
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class ApNewsletterAdminSettingsForm extends ConfigFormBase {

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
    return 'ap_newsletter_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ap_newsletter.settings',
    ];
  }

  /**
   * Build the AP Newsletter Config form.
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
    $config = $this->config('ap_newsletter.settings');
    
    // Building the Form.
    $form['ap_newsletter_about'] = [
      '#type' => 'item',
      '#markup' => $this->t('Add here the values of your Mailchimp
                                   lightweight connection.'),
    ];

    $form['ap_newsletter_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mailchimp API KEY'),
      '#description' => $this->t('Fill the field with your Mailchimp API Key.'),
      '#default_value' => $config->get('api_key'),
    ];
    
    $form['ap_newsletter_list_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mailchimp List Name'),
      '#description' => $this->t('Fill the field with your selected Mailchimp List.'),
      '#default_value' => $config->get('list_name'),
    ];
    
    $form['ap_newsletter_tag_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mailchimp Tag name'),
      '#description' => $this->t('Fill the field with your Mailchimp Tag Name.'),
      '#default_value' => $config->get('tag_name'),
    ];

    $form['ap_newsletter_form_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Form ID'),
      '#description' => $this->t('Fill the field with your Form ID.'),
      '#default_value' => $config->get('form_id'),
    ];

    $form['ap_newsletter_field_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of the field to save'),
      '#description' => $this->t('Fill the field with your source field name key.'),
      '#default_value' => $config->get('field_key'),
    ];
    
    $form['ap_newsletter_field_firstname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field Name of user'),
      '#description' => $this->t('Mailchimp requires firstname to add a new contact. So if you have a field, set it.'),
      '#default_value' => $config->get('first_name'),
    ];

    $form['ap_newsletter_field_lastname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field Last Name of user'),
      '#description' => $this->t('Mailchimp requires lastname to add a new contact.'),
      '#default_value' => $config->get('last_name'),
    ];
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    // Set the new values in the config object of the module.
    $this->config('ap_newsletter.settings')
      ->set('api_key', $form_state->getValue('ap_newsletter_api_key'))
      ->set('list_name', $form_state->getValue('ap_newsletter_list_name'))
      ->set('tag_name', $form_state->getValue('ap_newsletter_tag_name'))
      ->set('form_id', $form_state->getValue('ap_newsletter_form_id'))
      ->set('field_key', $form_state->getValue('ap_newsletter_field_key'))
      ->set('first_name', $form_state->getValue('ap_newsletter_field_firstname'))
      ->set('last_name', $form_state->getValue('ap_newsletter_field_lastname'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
