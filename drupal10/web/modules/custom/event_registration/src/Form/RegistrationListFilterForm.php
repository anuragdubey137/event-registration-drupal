<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Form to filter and list event registrations.
 */
class RegistrationListFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'registration_list_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // Wrap form for card-like UI
    $form['#prefix'] = '<div class="event-registration-form">';
    $form['#suffix'] = '</div>';

    $connection = Database::getConnection();

    // Fetch unique event dates
    $event_dates = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_date'])
      ->distinct()
      ->execute()
      ->fetchCol();

    $form['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => ['' => $this->t('- All Dates -')] + array_combine($event_dates, $event_dates),
      '#ajax' => [
        'callback' => '::filterAjaxCallback',
        'wrapper' => 'registration-table-wrapper',
      ],
    ];

    // Fetch events for selected date
    $selected_date = $form_state->getValue('event_date');

    $events_query = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_name']);

    if (!empty($selected_date)) {
      $events_query->condition('e.event_date', $selected_date);
    }

    $events = $events_query->execute()->fetchCol();

    $form['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => ['' => $this->t('- All Events -')] + array_combine($events, $events),
      '#ajax' => [
        'callback' => '::filterAjaxCallback',
        'wrapper' => 'registration-table-wrapper',
      ],
    ];

    // Participant count
    $form['participant_count'] = [
      '#markup' => '<div class="participant-count"><strong>' .
        $this->t('Total Participants:') .
        '</strong> ' . $this->getParticipantCount(
          $form_state->getValue('event_date'),
          $form_state->getValue('event_name')
        ) . '</div>',
    ];

    // CSV Export
    $form['export_csv'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV'),
      '#submit' => ['::exportCsv'],
    ];

    // Table wrapper
    $form['registrations'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'registration-table-wrapper',
      ],
    ];

    $form['registrations']['table'] = $this->getRegistrationTable(
      $form_state->getValue('event_date'),
      $form_state->getValue('event_name')
    );
    $form['#attached']['library'][] = 'event_registration/admin';

    return $form;
  }

  /**
   * AJAX callback.
   */
  public function filterAjaxCallback(array &$form, FormStateInterface $form_state) {
    return $form['registrations'];
  }

  /**
   * Get total participants count.
   */
  private function getParticipantCount($event_date = '', $event_name = '') {
    $connection = Database::getConnection();

    $query = $connection->select('event_registration_user', 'r');
    $query->addExpression('COUNT(r.id)', 'total');
    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');

    if (!empty($event_date)) {
      $query->condition('e.event_date', $event_date);
    }

    if (!empty($event_name)) {
      $query->condition('e.event_name', $event_name);
    }

    return (int) $query->execute()->fetchField();
  }

  /**
   * Build registration table.
   */
  private function getRegistrationTable($event_date = '', $event_name = '') {
    $connection = Database::getConnection();

    $query = $connection->select('event_registration_user', 'r');
    $query->fields('r', [
      'id',
      'user_name',
      'user_email',
      'registered_on',
      'event_id',
    ]);

    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');
    $query->fields('e', [
      'event_name',
      'event_date',
      'event_category',
    ]);

    if (!empty($event_date)) {
      $query->condition('e.event_date', $event_date);
    }

    if (!empty($event_name)) {
      $query->condition('e.event_name', $event_name);
    }

    $query->orderBy('r.registered_on', 'DESC');

    $results = $query->execute()->fetchAll();

    $rows = [];
    foreach ($results as $row) {
      $rows[] = [
        $row->user_name,
        $row->user_email,
        $row->event_category,
        $row->event_name,
        $row->event_date,
        date('d M Y, h:i A', $row->registered_on),
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'delete' => [
                'title' => $this->t('Delete'),
                'url' => Url::fromRoute('event_registration.delete', [
                  'registration' => $row->id,
                ]),
                'attributes' => [
                  'class' => ['use-ajax', 'button', 'button--danger'],
                  'data-dialog-type' => 'modal',
                ],
              ],
            ],
          ],
        ],
      ];
    }

    return [
      '#type' => 'table',
      '#header' => [
        $this->t('Name'),
        $this->t('Email'),
        $this->t('Category'),
        $this->t('Event Name'),
        $this->t('Event Date'),
        $this->t('Submitted On'),
        $this->t('Operations'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found.'),
      '#attributes' => [
        'class' => ['registration-table'],
      ],
    ];
  }

  /**
   * CSV Export handler.
   */
  public function exportCsv(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();

    $query = $connection->select('event_registration_user', 'r');
    $query->fields('r', ['user_name', 'user_email', 'registered_on']);
    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');
    $query->fields('e', ['event_name', 'event_date', 'event_category']);

    $results = $query->execute()->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registrations.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, [
      'Name',
      'Email',
      'Category',
      'Event Name',
      'Event Date',
      'Submitted On',
    ]);

    foreach ($results as $row) {
      fputcsv($output, [
        $row->user_name,
        $row->user_email,
        $row->event_category,
        $row->event_name,
        $row->event_date,
        date('d M Y, h:i A', $row->registered_on),
      ]);
    }

    fclose($output);
    exit;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Required by FormBase, not used here.
  }

}
