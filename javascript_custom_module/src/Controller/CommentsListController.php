<?php

namespace Drupal\javascript_custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the JavaScript Custom module example.
 */
class CommentsListController extends ControllerBase {

  protected $current_user;
  protected $connection;

  public function __construct( AccountInterface $current_user,
                               Connection $connection) {
    $this->current_user = $current_user;
    $this->connection = $connection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('database')
    );
  }
  /**
   * Getting List Controller method.
   *
   * @return array
   *   Return just an array with a piece of markup to render in screen.
   */
  public function gettingList() {

    // This is the array for the render system ready to print.
    $final_array = [];

    // Set the first element only a initial welcome message.
    $final_array['welcome_message'] = [
      '#type' => 'item',
      '#markup' => $this->t('Hello World, I am just a text.'),
      '#prefix' => '<div id="salute">',
      '#suffix' => '</div>',
      '#attached' => [
        'library' => [
          'javascript_custom_module/js_hello_world_console',
        ],
      ],
    ];

    // We're adding the new resources to the same welcome element.
    $final_array['welcome_message']['#attached']['library'][] = 'javascript_custom_module/js_hello_world_advanced';
    $final_array['welcome_message']['#attached']['drupalSettings']['data']['name'] = $this->current_user->getDisplayName();
    $final_array['welcome_message']['#attached']['drupalSettings']['data']['mail'] = $this->current_user->getEmail();
    $final_array['welcome_message']['#attached']['library'][] = 'javascript_custom_module/js_custom_dialog_window';

    // Build a dynamic select query for database.
    $query = $this->connection->select('comment_field_data', 'a');

    // Add methods to the query object.
    $query->join('comment', 'b', 'a.cid = b.cid');
    $query->fields('a', ['cid', 'entity_id', 'subject', 'uid']);
    $query->fields('b', ['uuid']);
    $query->condition('a.uid', $this->current_user->id());
    $query->orderBy('cid', 'DESC');

    // Executes the select query.
    $result = $query->execute();

    // Go Through the results.
    $rows = [];
    foreach($result as $record) {
      $rows[] = [
        $record->cid,
        $record->entity_id,
        $record->subject,
        $record->uid,
        $record->uuid,
      ];
    }

    // Build the final table ready to render.
    $final_array['comment_table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => [
        $this->t('Comment ID'),
        $this->t('Entity ID'),
        $this->t('Subject'),
        $this->t('User ID'),
        $this->t('Universal Unique ID'),
      ],
    ];

    return $final_array;
  }

}

