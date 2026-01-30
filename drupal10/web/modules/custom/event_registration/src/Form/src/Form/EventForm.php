<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Event Registration Admin Form.
 */
class EventForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    $form['event_category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Category'),
      '#required' => TRUE,
    ];

    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    $form['registration_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration Start'),
      '#required' => TRUE,
    ];

    $form['registration_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration End'),
      '#required' => TRUE,
    ];

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
    $connection = Database::getConnection();
    $connection->insert('event_registration_event')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'event_category' => $form_state->getValue('event_category'),
        'event_date' => $form_state->getValue('event_date'),
        'registration_start' => $form_state->getValue('registration_start'),
        'registration_end' => $form_state->getValue('registration_end'),
        'created' => \Drupal::time()->getCurrentTime(),
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('Event has been saved successfully.'));
  }
}
