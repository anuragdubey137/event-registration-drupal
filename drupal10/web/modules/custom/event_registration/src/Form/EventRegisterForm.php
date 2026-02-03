<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Event Registration Form.
 */
class EventRegisterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_register_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // Permission check
    if (!$this->currentUser()->hasPermission('register_for_events')) {
      return [
        '#markup' => $this->t('You do not have permission to register for events.'),
      ];
    }

    $connection = Database::getConnection();

    /* -------------------------
     * BASIC USER FIELDS
     * ------------------------- */
    $form['user_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['user_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['college_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
    ];

    /* -------------------------
     * CATEGORY DROPDOWN
     * ------------------------- */
    $categories = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_category'])
      ->distinct()
      ->execute()
      ->fetchCol();

    $category_options = ['' => $this->t('- Select Category -')];
    foreach ($categories as $category) {
      $category_options[$category] = $category;
    }

    $form['event_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Category'),
      '#options' => $category_options,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventDate',
        'wrapper' => 'event-date-wrapper',
      ],
    ];

    /* -------------------------
     * EVENT DATE DROPDOWN
     * ------------------------- */
    $form['event_date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-date-wrapper'],
    ];

    $selected_category = $form_state->getValue('event_category');

    $date_options = ['' => $this->t('- Select Event Date -')];
    if ($selected_category) {
      $dates = $connection->select('event_registration_event', 'e')
        ->fields('e', ['event_date'])
        ->condition('e.event_category', $selected_category)
        ->distinct()
        ->execute()
        ->fetchCol();

      foreach ($dates as $date) {
        $date_options[$date] = $date;
      }
    }

    $form['event_date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $date_options,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventName',
        'wrapper' => 'event-name-wrapper',
      ],
    ];

    /* -------------------------
     * EVENT NAME DROPDOWN
     * ------------------------- */
    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $selected_date = $form_state->getValue(['event_date_wrapper', 'event_date']);

    $event_options = ['' => $this->t('- Select Event -')];
    if ($selected_category && $selected_date) {
      $events = $connection->select('event_registration_event', 'e')
        ->fields('e', ['id', 'event_name'])
        ->condition('e.event_category', $selected_category)
        ->condition('e.event_date', $selected_date)
        ->execute()
        ->fetchAllKeyed(0, 1);

      $event_options += $events;
    }

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $event_options,
      '#required' => TRUE,
    ];

    /* -------------------------
     * SUBMIT
     * ------------------------- */
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  /* -------------------------
   * AJAX CALLBACKS
   * ------------------------- */
  public function updateEventDate(array &$form, FormStateInterface $form_state) {
    return $form['event_date_wrapper'];
  }

  public function updateEventName(array &$form, FormStateInterface $form_state) {
    return $form['event_name_wrapper'];
  }

  /* -------------------------
   * VALIDATION
   * ------------------------- */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // No special characters in text fields
    foreach (['user_name', 'college_name', 'department'] as $field) {
      if (!preg_match('/^[a-zA-Z\s]+$/', $form_state->getValue($field))) {
        $form_state->setErrorByName(
          $field,
          $this->t('Special characters are not allowed.')
        );
      }
    }

    $email = $form_state->getValue('user_email');
    $event_id = $form_state->getValue('event_id');

    $connection = Database::getConnection();

    // Fetch event date
    $event_date = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_date'])
      ->condition('e.id', $event_id)
      ->execute()
      ->fetchField();

    // Prevent duplicate registration (Email + Event Date)
    $exists = $connection->select('event_registration_user', 'r')
      ->condition('r.user_email', $email)
      ->condition('r.event_date', $event_date)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($exists) {
      $form_state->setErrorByName(
        'user_email',
        $this->t('You have already registered for this event.')
      );
    }
  }

  /* -------------------------
   * SUBMIT HANDLER
   * ------------------------- */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $connection = Database::getConnection();

    // Get event date again
    $event_date = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_date'])
      ->condition('e.id', $form_state->getValue('event_id'))
      ->execute()
      ->fetchField();

    $connection->insert('event_registration_user')
      ->fields([
        'user_name' => $form_state->getValue('user_name'),
        'user_email' => $form_state->getValue('user_email'),
        'college_name' => $form_state->getValue('college_name'),
        'department' => $form_state->getValue('department'),
        'event_id' => $form_state->getValue('event_id'),
        'event_date' => $event_date,
        'registered_on' => time(),
      ])
      ->execute();

    $this->messenger()->addStatus(
      $this->t('You have successfully registered for the event.')
    );
  }

}
