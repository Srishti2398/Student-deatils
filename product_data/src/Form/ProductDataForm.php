<?php

namespace Drupal\product_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides the form for adding countries.
 */
class ProductDataForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_data_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $options = [
      'electonic' => 'Electronic',
      'education' => 'Education',
      'others' => 'Others',
    ];
    $radioOptions = [
      'yes' => 'yes',
      'No' => 'No',
    ];

    $form['productname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Product Name'),
      '#required' => TRUE,
      '#maxlength' => 60,
    ];

    $form['skucode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('SKU Code'),
      '#required' => TRUE,
      '#maxlength' => 10,
      '#default_value' => (isset($data['skucode'])) ? $data['skucode'] : '',
    ];
    $form['price'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' => '',
    ];
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#required' => TRUE,
      '#options' => $options,
    ];

    $form['instock'] = [
      '#type' => 'radios',
      '#title' => $this->t('In stock'),
      '#required' => TRUE,
      '#options' => $radioOptions,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#default_value' => $this->t('Save'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (is_numeric($form_state->getValue('productname'))) {
      $form_state->setErrorByName('productname', $this->t('Error, The Product Name Must Be A String'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $field = $form_state->getValues();
      $fields["productname"] = $field['productname'];
      $fields["skucode"] = $field['skucode'];
      $fields["price"] = $field['price'];
      $fields["category"] = $field['category'];
      $fields["instock"] = $field['instock'];
      $queryOne = \Drupal::database()->select('product_data', 'p');
      $queryOne->fields('p', ['skucode']);
      $result = $queryOne->execute()->fetchAll();
      if (!empty($result)) {
        foreach ($result as $data) {
          $code = $data->skucode;
        }
        if ($code == $fields["skucode"]) {
          $queryTwo = \Drupal::database();
          $queryTwo->update('product_data')
            ->fields($fields)
            ->condition('skucode', ['skucode' => $code])
            ->execute();
          \Drupal::messenger()->addMessage($this->t('Product details updated successfully'));
        }
        else {
          $queryThree = \Drupal::database();
          $queryThree->insert('product_data')
            ->fields($fields)->execute();
          \Drupal::messenger()->addMessage($this->t('New Product created successfully'));
        }
      }
      else {
        $queryThree = \Drupal::database();
        $queryThree->insert('product_data')
          ->fields($fields)->execute();
        \Drupal::messenger()->addMessage($this->t('New Product created successfully'));
      }
      $url = new Url('product_data.example');
      $response = new RedirectResponse($url->toString());
      $response->send();
    }
    catch (Exception $ex) {
      \Drupal::logger('product_data')->error($ex->getMessage());
    }

  }

}
