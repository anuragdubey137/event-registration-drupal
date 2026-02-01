<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

class EventRegistrationEditForm extends FormBase {

  public function getFormId() {
    return 'event_registration_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $registration = NULL) {
    $connection = Database::getConnection();

    $record = $connection->select('event_registration_registration', 'r')
      ->fields('r')
      ->condition('id', $registration)
      ->execute()
      ->fetchObject();

    if (!$record) {
      $this->messenger()->addError($this->t('Registration not found.'));
      return [];
    }

    $event_name = $connection->select('event_registration_event', 'e')
      ->fields('e', ['event_name'])
      ->condition('id', $record->event_id)
      ->execute()
      ->fetchField();

    $form['registration_id'] = [
      '#type' => 'hidden',
      '#value' => $record->id,
    ];

    $form['event'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event'),
      '#value' => $event_name,
      '#disabled' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $record->name,
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $record->email,
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('event_registration.registration_list'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    Database::getConnection()
      ->update('event_registration_registration')
      ->fields([
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
      ])
      ->condition('id', $form_state->getValue('registration_id'))
      ->execute();

    $this->messenger()->addMessage($this->t('Registration updated successfully.'));
    $form_state->setRedirect('event_registration.registration_list');
  }
}
