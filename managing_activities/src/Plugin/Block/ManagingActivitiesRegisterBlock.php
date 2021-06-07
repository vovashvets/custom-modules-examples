<?php

namespace Drupal\managing_activities\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a ManagingActivities Register block.
 *
 * @Block(
 *  id = "managing_activities_register_block",
 *  admin_label = @Translation("Managing Activities Register Block")
 * )
 */
class ManagingActivitiesRegisterBlock extends BlockBase implements
    ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Form\FormBuilderInterface definition.
   *
   * @var formBuilder
   */
  protected $formBuilder;

  /**
   * Constructs a new ManagingActivitiesRegisterBlock object.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @return ManagingActivitiesRegisterBlock
   */
  public static function create(ContainerInterface $container,
                                array $configuration,
                                $plugin_id,
                                $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * @inheritDoc
   */
  public function build() {

    $form = $this->formBuilder->getForm('Drupal\managing_activities\Form\ManagingActivitiesRegisterForm');
    return $form;
  }

}
