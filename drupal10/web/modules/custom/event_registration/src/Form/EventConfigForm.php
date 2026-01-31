<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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

    // Check admin permission
    if (!\Drupal::currentUser()->hasPermission('administer event registration')) {
      return [
        '#markup' => $this->t('Access denied. You do not have permission to configure events.'),
      ];
    }

    // Event Name
    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    // Event Category
    $form['event_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [
        'Online Workshop' => 'Online Workshop',
        'Hackathon' => 'Hackathon',
        'Conference' => 'Conference',
        'One-day Workshop' => 'One-day Workshop',
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
public function submitForm(array &$form, FormStateInterface $form_state) {
  $database = \Drupal::database();

  $database->insert('event_registration_event')
    ->fields([
      'event_name' => $form_state->getValue('event_name'),
      'event_category' => $form_state->getValue('event_category'),
      'event_date' => $form_state->getValue('event_date'),
      'registration_start' => $form_state->getValue('registration_start'),
      'registration_end' => $form_state->getValue('registration_end'),
      'created' => \Drupal::time()->getRequestTime(),
    ])
    ->execute();

  \Drupal::messenger()->addStatus($this->t('Event saved successfully.'));
}


}
