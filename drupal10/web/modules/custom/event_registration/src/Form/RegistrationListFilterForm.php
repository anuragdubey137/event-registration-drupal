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

    // Attach library for table styling
    $form['#attached']['library'][] = 'event_registration/admin_styles';

    $connection = Database::getConnection();

    // Fetch unique event dates for the dropdown
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

    // Fetch all events for selected date
    $selected_date = $form_state->getValue('event_date') ?? '';

    $events_query = $connection->select('event_registration_event', 'e')
      ->fields('e', ['id', 'event_name']);

    if ($selected_date) {
      $events_query->condition('e.event_date', $selected_date);
    }

    $events = $events_query->execute()->fetchAllKeyed(0, 1);

    $form['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => ['' => $this->t('- All Events -')] + $events,
      '#ajax' => [
        'callback' => '::filterAjaxCallback',
        'wrapper' => 'registration-table-wrapper',
      ],
    ];

    // Total participants count
    $participant_count = $this->getParticipantCount(
      $form_state->getValue('event_date'),
      $form_state->getValue('event_name')
    );

    $form['participant_count'] = [
      '#markup' => $this->t('<strong>Total Participants:</strong> @count', ['@count' => $participant_count]),
    ];

    // CSV Export button
    $form['export_csv'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV'),
      '#submit' => ['::exportCsv'],
    ];

    // Registration table container
    $form['registrations'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'registration-table-wrapper', 'class' => ['registration-table-wrapper']],
    ];

    $form['registrations']['table'] = $this->getRegistrationTable(
      $form_state->getValue('event_date'),
      $form_state->getValue('event_name')
    );

    return $form;
  }

  /**
   * AJAX callback for filtering table.
   */
  public function filterAjaxCallback(array $form, FormStateInterface $form_state) {
    return $form['registrations'];
  }

  /**
   * Get total participants count for filtered event/date.
   */
  private function getParticipantCount($event_date = '', $event_name = '') {
    $connection = Database::getConnection();
    $query = $connection->select('event_registration_registration', 'r');
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
   * Builds the registration table.
   */
  private function getRegistrationTable($event_date = '', $event_name = '') {
    $connection = Database::getConnection();

    $query = $connection->select('event_registration_registration', 'r');
    $query->fields('r', ['id', 'name', 'email', 'college_name', 'department', 'created', 'event_id']);
    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');
    $query->fields('e', ['event_name', 'event_date', 'event_category']);

    if (!empty($event_date)) {
        $query->condition('e.event_date', $event_date);
    }
    if (!empty($event_name)) {
        $query->condition('e.event_name', $event_name);
    }

    $query->orderBy('r.created', 'DESC');
    $results = $query->execute()->fetchAll();

    $rows = [];
    foreach ($results as $row) {
        $operations = [];
        if ($this->currentUser()->hasPermission('administer event registrations')) {
            $operations = [
                'edit' => [
                    'title' => $this->t('Edit'),
                    'url' => Url::fromRoute('event_registration.edit', ['registration' => $row->id]),
                ],
                'delete' => [
                    'title' => $this->t('Delete'),
                    'url' => Url::fromRoute('event_registration.delete', ['registration' => $row->id]),
                    'attributes' => [
                        'class' => ['use-ajax', 'button', 'button--danger'],
                        'data-dialog-type' => 'modal',
                        'data-dialog-options' => json_encode(['width' => 400]),
                    ],
                ],
            ];
        }

        $rows[] = [
            $row->name,
            $row->email,
            $row->college_name,
            $row->department,
            $row->event_category,
            $row->event_name,
            $row->event_date,
            date('d M Y, h:i A', $row->created),
            [
                'data' => [
                    '#type' => 'operations',
                    '#links' => $operations,
                ],
            ],
        ];
    }

    return [
        '#type' => 'table',
        '#header' => [
            $this->t('Name'),
            $this->t('Email'),
            $this->t('College'),
            $this->t('Department'),
            $this->t('Category'),
            $this->t('Event Name'),
            $this->t('Event Date'),
            $this->t('Submitted On'),
            $this->t('Operations'),
        ],
        '#rows' => $rows,
        '#empty' => $this->t('No registrations found.'),
        '#attributes' => ['class' => ['registration-table']],
    ];
  }

  /**
   * CSV Export handler.
   */
  public function exportCsv(array &$form, FormStateInterface $form_state) {
    $event_date = $form_state->getValue('event_date');
    $event_name = $form_state->getValue('event_name');

    $connection = Database::getConnection();
    $query = $connection->select('event_registration_registration', 'r');
    $query->fields('r', ['name', 'email', 'college_name', 'department', 'created']);
    $query->innerJoin('event_registration_event', 'e', 'r.event_id = e.id');
    $query->fields('e', ['event_name', 'event_date', 'event_category']);

    if (!empty($event_date)) {
        $query->condition('e.event_date', $event_date);
    }
    if (!empty($event_name)) {
        $query->condition('e.event_name', $event_name);
    }

    $results = $query->execute()->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registrations.csv"');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['Name', 'Email', 'College', 'Department', 'Category', 'Event Name', 'Event Date', 'Submitted On']);

    foreach ($results as $row) {
        fputcsv($output, [
            $row->name,
            $row->email,
            $row->college_name,
            $row->department,
            $row->event_category,
            $row->event_name,
            $row->event_date,
            date('d M Y, h:i A', $row->created),
        ]);
    }

    fclose($output);
    exit();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submission needed; table is filtered via AJAX.
  }
}
