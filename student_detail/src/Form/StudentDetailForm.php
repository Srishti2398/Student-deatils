<?php

namespace Drupal\student_detail\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides the form for adding countries.
 */
class StudentDetailForm extends FormBase {

  /**
   * Constructor function.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'student_detail_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['filters'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Filter'),
      '#open'  => TRUE,
    ];
    $form['filters']['state'] = [
      '#title' => 'State',
      '#type'  => 'search',
      '#default_value'    => isset($_GET['state']) ? $_GET['state'] : '',

    ];
    $form['filters']['city'] = [
      '#title' => 'City',
      '#type'  => 'search',
      '#default_value'    => isset($_GET['city']) ? $_GET['city'] : '',

    ];
    $form['filters']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Filter'),

    ];
    $form['table'] = $this->getStudentList();
    return $form;

  }

  /**
   *
   */
  public function getStudentList() {
    $header = [
      'name' => $this->t('Student Name'),
      'email' => $this->t('Email Id'),
      'state' => $this->t('State'),
      'city' => $this->t('City'),
      'passing_year' => $this->t('Passing Year'),
      'Score' => $this->t('Score'),
      'dob' => $this->t('dob'),
    ];
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->sort('field_score', 'DESC');
    $nids = $query->condition('type', 'student_details')->execute();
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $year = $node->field_passing_year->getString();
      $dob = $node->field_dob->getString();
      $newDob = date("jS M Y", strtotime($dob));
      $score = $node->field_score->getString();
      $rows[] = [
        'name' => $node->field_student_name->getString(),
        'email' => $node->field_email->getString(),
        'state' => $node->field_state->getString(),
        'city' => $node->field_city->getString(),
        'passing_year' => $year,
        'Score' => $score,
        'dob' => $newDob,
      ];
      $formData['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

    }
    return $formData;

  }

  /**
   *
   */
  public function filterStudentList($UserState, $UserCity) {
    $header = [
      'name' => $this->t('Student Name'),
      'email' => $this->t('Email Id'),
      'state' => $this->t('State'),
      'city' => $this->t('City'),
      'passing_year' => $this->t('Passing Year'),
      'Score' => $this->t('Score'),
      'dob' => $this->t('dob'),
      'grade' => $this->t('grade'),
    ];
    $UserState = 'Bihar';
    $UserCity = 'Patna';
    $database = \Drupal::database();
    if ((!empty($UserState))|| (!empty($UserCity))) {
      $node_nid = $database->query("SELECT nfd.nid FROM node_field_data AS nfd LEFT JOIN node__field_city AS nfc ON nfd.nid = nfc.entity_id LEFT JOIN node__field_state AS nfs ON nfc.entity_id = nfs.entity_id where nfc.field_city_value = '$UserCity' AND nfs.field_state_value = '$UserState' ")->fetch();
      $array[] = json_decode(json_encode($node_nid), TRUE);
      foreach ($node_nid as $nid) {
        $node = $this->entityTypeManager->getStorage('node')->load($node_nid);
        $year = $node->field_passing_year->getString();
        $dob = $node->field_dob->getString();
        $newDob = date("jS M Y", strtotime($dob));
        $score = $node->field_score->getString();
        $rows[] = [
          'name' => $node->field_student_name->getString(),
          'email' => $node->field_email->getString(),
          'state' => $node->field_state->getString(),
          'city' => $node->field_city->getString(),
          'passing_year' => $year,
          'Score' => $score,
          'dob' => $newDob,
        ];

      }
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

      return $form;
    }
    else {
      $studentData = $this->getStudentList();
      return $studentData;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $InputState = $field["state"];
    $InputCity = $field["city"];
    $this->filterStudentList($InputState, $InputCity);
    $url = new Url('student_detail.display_form');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }

}
