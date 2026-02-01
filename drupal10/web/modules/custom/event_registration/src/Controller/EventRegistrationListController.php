<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class EventRegistrationListController extends ControllerBase {

  /**
   * Display a table of all event registrations.
   */
  public function list() {
    $header = [
      'id' => $this->t('ID'),
      'event_name' => $this->t('Event Name'),
      'name' => $this->t('User Name'),
      'email' => $this->t('Email'),
      'registered_on' => $this->t('Registered On'),
    ];

    $connection = Database::getConnection();

    // 1️⃣ Fetch all events and build a map of event_id => event_name
    $events = $connection->select('event_registration_event', 'e')
      ->fields('e', ['id', 'event_name'])
      ->execute()
      ->fetchAllKeyed(0, 1); // id => event_name

    // 2️⃣ Fetch all registrations
    $registrations = $connection->select('event_registration_registration', 'r')
      ->fields('r', ['id', 'event_id', 'name', 'email', 'created'])
      ->execute()
      ->fetchAll();

    $rows = [];
    foreach ($registrations as $record) {
      $rows[] = [
        'id' => $record->id,
        'event_name' => $events[$record->event_id] ?? $this->t('Unknown Event'),
        'name' => $record->name,
        'email' => $record->email,
        'registered_on' => date('Y-m-d H:i:s', $record->created),
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
