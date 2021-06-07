<?php
 
namespace Drupal\visibility_conditions\Plugin\Condition;
 
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a condition for articles marked as selected.
 *
 * This condition evaluates to TRUE when in a node context, and the node is 
 * Article content type and the article was marked as selected article in a field.
 *
 * @Condition(
 *   id = "selected_article",
 *   label = @Translation("Selected Article"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("node"))
 *   }
 * )
 * 
 */
 class SelectedArticle extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Creates a new SelectedArticle instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // This default value will mark the block as hidden.
      return ['show' => 0] + parent::defaultConfiguration();
    }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Build a checkbox to expose the new condition.
    $form['show'] = [
      '#title' => $this->t('Display only in Selected Articles'),
      '#type' => 'checkbox',
      // Is using the previous config value as the default.
      '#default_value' => $this->configuration['show'],
      '#description' => $this->t('If this box is checked, this block will only be shown for Selected Articles.'),
      ];
     
      return parent::buildConfigurationForm($form, $form_state);
    }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Save the selected value to configuration.
    $this->configuration['show'] = $form_state->getValue('show'); 
      parent::submitConfigurationForm($form, $form_state);
    }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // We have to check three options: 
    // 1- Condition enabled.
    // 2- Condition enabled and negated.
    // 3- Condition not enabled.
    if ($this->configuration['show']) {
      // Check if the 'negate condition' checkbox was enabled.
      if ($this->isNegated()) {
        // The condition is enabled and negated.
        return $this->t('The block will be shown in all Articles except the Selected.');
      } else {
        // The condition is only enabled.
        return $this->t('The block will be shown only in Selected Articles.');
      }
    }
      // The condition is not enabled.
      return $this->t('The block will be shown in all Articles.');
    }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    // First ensure that doesn't disable other blocks aren't using it.
    if (empty($this->configuration['show']) && !$this->isNegated()) {
      return TRUE;
    }
    // Then review if the node Article has setted the Selected Article field.
    $node = $this->getContextValue('node');

    if (($node->getType() == "article") && ($node->hasField('field_selected_article_check')) && ($node->field_selected_article_check->value)) {
      return TRUE;
    }
      // Finally if not exist marked value in Selected Article field.
      return FALSE;
    }
 }

