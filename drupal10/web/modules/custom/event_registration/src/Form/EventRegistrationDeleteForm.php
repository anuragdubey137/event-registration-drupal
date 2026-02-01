<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

class EventRegistrationDeleteForm extends ConfirmFormBase {

  protected $registrationId;

  public function getFormId() {
    return 'event_registration_delete_form';
  }

  public function getQuestion() {
    return $this->t('Are you sure you want to delete this registration?');
  }

  public function getCancelUrl() {
    return Url::fromRoute('event_registration.registration_list');
  }

  public function getConfirmText() {
    return $this->t('Delete');
  }

  public function buildForm(array $form, FormStateInterface $form_state, $registration = NULL) {
    $this->registrationId = $registration;
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    Database::getConnection()
      ->delete('event_registration_registration')
      ->condition('id', $this->registrationId)
      ->execute();

    $this->messenger()->addMessage($this->t('Registration deleted.'));
    $form_state->setRedirect('event_registration.registration_list');
  }
}
