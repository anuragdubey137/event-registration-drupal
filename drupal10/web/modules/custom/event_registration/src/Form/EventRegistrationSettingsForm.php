<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Event Registration settings.
 */
class EventRegistrationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'event_registration.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('event_registration.settings');

    $form['admin_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Admin notification email'),
      '#description' => $this->t('Email address to receive event registration notifications.'),
      '#default_value' => $config->get('admin_email'),
      '#required' => TRUE,
    ];

    $form['enable_admin_notification'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable admin notifications'),
      '#description' => $this->t('Send an email to admin when a user registers for an event.'),
      '#default_value' => $config->get('enable_admin_notification'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('event_registration.settings')
      ->set('admin_email', $form_state->getValue('admin_email'))
      ->set('enable_admin_notification', $form_state->getValue('enable_admin_notification'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
