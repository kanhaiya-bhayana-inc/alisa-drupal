<?php declare(strict_types = 1);

namespace Drupal\alisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Provides a Alisa form.
 */
final class AlisaForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'alisa_alisa';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['firstname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#required' => TRUE,
      '#maxlength' => 30,
    ];
    $form['lastname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#required' => TRUE,
      '#maxlength' => 30,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Enter email'),
      '#required' => TRUE,
      '#maxlength' => 50,
    ];
    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter phone'),
      '#required' => TRUE,
      '#maxlength' => 10,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $formFields = $form_state->getValues();

    $firstName = $formFields['firstname'];
    $lastName = $formFields['lastname'];
    $email = $formFields['email'];
    $phone = $formFields['phone'];

    if (!preg_match("/^([a-zA-Z']+)$/", $firstName)){
      $form_state->setErrorByName('firstname',$this->t('Please enter the valid first name...'));
    }
    if (!preg_match("/^([a-zA-Z']+)$/", $lastName)){
      $form_state->setErrorByName('lastname',$this->t('Please enter the valid last name...'));
    }

    if (!\Drupal::service('email.validator')->isValid($email)){
      $form_state->setErrorByName('email',$this->t('Please enter the valid email...'));
    }

    if (!preg_match("/^\d{1,10}$/", $phone)){
      $form_state->setErrorByName('phone',$this->t('Please enter the valid phone number...'));
    }

    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $conn = Database::getConnection();

    $formFields = $form_state->getValues();
    $formData['firstName'] = $formFields['firstname'];
    $formData['lastName'] = $formFields['lastname'];
    $formData['email'] = $formFields['email'];
    $formData['phone'] = $formFields['phone'];

    $conn->insert('alisa')
      ->fields($formData)->execute();


    $this->messenger()->addStatus($this->t('Information has been saved successfully.'));
    $form_state->setRedirect('alisa.alisa');
  }

}
