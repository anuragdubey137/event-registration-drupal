<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Form to delete a registration with AJAX modal confirmation.
 */
class EventRegistrationDeleteForm extends ConfirmFormBase {

  /**
   * Registration ID to delete.
   *
   * @var int
   */
  protected $registrationId;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete this registration?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('event_registration.admin_registrations');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $registration = NULL) {
    $this->registrationId = $registration;

    // Attach the AJAX dialog library
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->registrationId) {
      Database::getConnection()
        ->delete('event_registration_registration')
        ->condition('id', $this->registrationId)
        ->execute();

      $this->messenger()->addStatus($this->t('Registration deleted successfully.'));
    }

    // Redirect back to the registration list
    $form_state->setRedirect('event_registration.admin_registrations');
  }
}
