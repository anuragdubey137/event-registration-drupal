<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class EventRegisterForm extends FormBase {

  public function getFormId() {
    return 'event_register_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $events = $connection->select('event_registration_event', 'e')
      ->fields('e', ['id', 'event_name'])
      ->orderBy('event_name', 'ASC')
      ->execute()
      ->fetchAllKeyed();

    $form['event'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Event'),
      '#options' => $events,
      '#required' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your Email'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $connection->insert('event_registration_registration')
      ->fields([
        'event_id' => $form_state->getValue('event'),
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'created' => \Drupal::time()->getCurrentTime(),
      ])
      ->execute();

    $this->messenger()->addMessage($this->t('You have successfully registered for the event.'));
  }
}
