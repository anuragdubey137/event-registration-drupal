<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Admin Event Configuration Form.
 */
class EventConfigForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Event Name
    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
      '#maxlength' => 255,
    ];

    // Event Category
    $form['event_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [
        'Online Workshop' => $this->t('Online Workshop'),
        'Hackathon' => $this->t('Hackathon'),
        'Conference' => $this->t('Conference'),
        'One-day Workshop' => $this->t('One-day Workshop'),
      ],
      '#required' => TRUE,
    ];

    // Event Date
    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    // Registration Start Date
    $form['registration_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration Start Date'),
      '#required' => TRUE,
    ];

    // Registration End Date
    $form['registration_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration End Date'),
      '#required' => TRUE,
    ];

    // Submit button
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $start = strtotime($form_state->getValue('registration_start'));
    $end = strtotime($form_state->getValue('registration_end'));
    $event = strtotime($form_state->getValue('event_date'));

    if ($start > $end) {
      $form_state->setErrorByName(
        'registration_start',
        $this->t('Registration start date must be before the end date.')
      );
    }

    if ($event < $end) {
      $form_state->setErrorByName(
        'event_date',
        $this->t('Event date must be after the registration end date.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();

    $connection->insert('event_registration_event')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'event_category' => $form_state->getValue('event_category'),
        'event_date' => $form_state->getValue('event_date'),
        'registration_start' => $form_state->getValue('registration_start'),
        'registration_end' => $form_state->getValue('registration_end'),
        'created' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();

    $this->messenger()->addStatus(
      $this->t('Event has been saved successfully.')
    );
  }

}
