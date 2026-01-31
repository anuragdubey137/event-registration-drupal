<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class EventListController extends ControllerBase {

  public function list() {
    // Database connection
    $connection = Database::getConnection();

    // Query events
    $query = $connection->select('event_registration_event', 'e')
      ->fields('e')
      ->orderBy('created', 'DESC');

    $events = $query->execute()->fetchAll();

    // Build table rows
    $rows = [];
    foreach ($events as $event) {
      $rows[] = [
        $event->id,
        $event->event_name,
        $event->event_category,
        $event->event_date,
        $event->registration_start,
        $event->registration_end,
      ];
    }

    // Table header
    $header = [
      'ID',
      'Event Name',
      'Category',
      'Event Date',
      'Registration Start',
      'Registration End',
    ];

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No events found'),
    ];
  }
}
