<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

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

    // Fetch distinct event categories
    $categories = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_category'])
      ->distinct()
      ->execute()
      ->fetchCol();

    $category_options = [];
    foreach ($categories as $category) {
      $category_options[$category] = $category;
    }

    // Full Name
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    // Email
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    // College
    $form['college'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
    ];

    // Department
    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
    ];

    // Event Category
    $form['event_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Category'),
      '#options' => $category_options,
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventDates',
        'wrapper' => 'event-date-wrapper',
      ],
    ];

    // Event Date wrapper
    $form['event_date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-date-wrapper'],
    ];

    $form['event_date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $this->getEventDates($form_state->getValue('event_category')),
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventNames',
        'wrapper' => 'event-name-wrapper',
      ],
    ];

    // Event Name wrapper
    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $form['event_name_wrapper']['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $this->getEventNames(
        $form_state->getValue('event_category'),
        $form_state->getValue('event_date')
      ),
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
    ];

    // Submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  /**
   * AJAX: Update event dates.
   */
  public function updateEventDates(array &$form, FormStateInterface $form_state) {
    return $form['event_date_wrapper'];
  }

  /**
   * AJAX: Update event names.
   */
  public function updateEventNames(array &$form, FormStateInterface $form_state) {
    return $form['event_name_wrapper'];
  }

  /**
   * Get event dates by category.
   */
  private function getEventDates($category) {
    if (empty($category)) {
      return [];
    }

    $dates = Database::getConnection()
      ->select('event_registration_event', 'e')
      ->fields('e', ['event_date'])
      ->condition('event_category', $category)
      ->orderBy('event_date', 'ASC')
      ->execute()
      ->fetchCol();

    return array_combine($dates, $dates);
  }

  /**
   * Get event names by category and date.
   */
  private function getEventNames($category, $date) {
    if (empty($category) || empty($date)) {
      return [];
    }

    $names = Database::getConnection()
      ->select('event_registration_event', 'e')
      ->fields('e', ['event_name'])
      ->condition('event_category', $category)
      ->condition('event_date', $date)
      ->orderBy('event_name', 'ASC')
      ->execute()
      ->fetchCol();

    return array_combine($names, $names);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Prevent special characters
    foreach (['name', 'college', 'department'] as $field) {
      if (preg_match('/[^a-zA-Z0-9 ]/', $form_state->getValue($field))) {
        $form_state->setErrorByName($field, $this->t('Special characters are not allowed.'));
      }
    }

    // Prevent duplicate registration
    $exists = Database::getConnection()
      ->select('event_registration_registration', 'r')
      ->fields('r', ['id'])
      ->condition('email', $form_state->getValue('email'))
      ->condition('event_name', $form_state->getValue('event_name'))
      ->condition('event_date', $form_state->getValue('event_date'))
      ->execute()
      ->fetchField();

    if ($exists) {
      $form_state->setErrorByName('email', $this->t('You have already registered for this event.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    Database::getConnection()->insert('event_registration_registration')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'event_date' => $form_state->getValue('event_date'),
        'event_category' => $form_state->getValue('event_category'),
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'college' => $form_state->getValue('college'),
        'department' => $form_state->getValue('department'),
        'created' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('You have successfully registered for the event.'));
  }

}
