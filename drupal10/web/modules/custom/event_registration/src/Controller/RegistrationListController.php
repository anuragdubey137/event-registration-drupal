<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for listing event registrations in admin.
 */
class RegistrationListController extends ControllerBase {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $database;

  /**
   * Constructs the controller.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Displays the list of event registrations.
   *
   * @return array
   *   Drupal render array for table.
   */
  public function list(): array {
    // Table headers.
    $header = [
      $this->t('Name'),
      $this->t('Email'),
      $this->t('Event Name'),
      $this->t('Category'),
      $this->t('Event Date'),
      $this->t('Registered On'),
    ];

    // Query: registrations joined with events.
    $query = $this->database->select('event_registration_registration', 'r');
    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');

    // Select fields from registration table.
    $query->fields('r', [
      'name',
      'email',
      'created',       // Use UNIX timestamp column
    ]);

    // Select fields from event table.
    $query->fields('e', [
      'event_name',
      'event_category',
      'event_date',
    ]);

    $query->orderBy('r.created', 'DESC');

    $results = $query->execute()->fetchAll();

    // Build table rows.
    $rows = [];
    foreach ($results as $row) {
      $rows[] = [
        $row->name,
        $row->email,
        $row->event_name,
        $row->event_category,
        $row->event_date,
        date('d M Y, h:i A', $row->created),
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found.'),
    ];
  }

}
