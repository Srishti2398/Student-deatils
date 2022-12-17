<?php

namespace Drupal\student_detail\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for student_detail routes.
 */
class StudentDetailController extends ControllerBase {

  /**
   * Constructor function.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * Show student detail function.
   *
   * @return void
   */
  public function showStudentDetails() {
    $rows = [];
    // Headers for the table.
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
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $nids = $query->condition('type', 'student_details')->execute();
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $year = $node->field_passing_year->getString();
      $score = $node->field_score->getString();
      $grades = $this->studentGrades($score);
      $divisions = $this->studentDivision($score);
      // When passing year is greater than 2015.
      if ($year > '2015') {
        $rows[] = [
          'name' => $node->field_student_name->getString(),
          'email' => $node->field_email->getString(),
          'state' => $node->field_state->getString(),
          'city' => $node->field_city->getString(),
          'passing_year' => $year,
          'Score' => $score,
          'dob' => $node->field_dob->getString(),
          'Grade' => $grades,
        ];
      }
      // When passing year is greater than 2010.
      elseif ($year > '2010') {
        $rows[] = [
          'name' => $node->field_student_name->getString(),
          'email' => $node->field_email->getString(),
          'state' => $node->field_state->getString(),
          'city' => $node->field_city->getString(),
          'passing_year' => $year,
          'Score' => $score,
          'dob' => $node->field_dob->getString(),
          'Grade' => $divisions,
        ];
      }
    }
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    return $form;

  }

  /**
   * Student Grade function.
   *
   * @param string $score
   *
   * @return string
   */
  public function studentGrades($score) {
    if ($score > '85') {
      $grade = "A";
    }
    elseif ($score > '75') {
      $grade = "B";
    }
    elseif ($score > '65') {
      $grade = "C";
    }
    elseif ($score > '55') {
      $grade = "D";
    }
    elseif ($score > '45') {
      $grade = "E";
    }
    return $grade;
  }

  /**
   * Student division function.
   *
   * @param string $score
   *
   * @return string
   */
  public function studentDivision($score) {
    if ($score > '80') {
      $division = "first";
    }
    elseif ($score > '65') {
      $division = "second";
    }
    elseif ($score > '50') {
      $division = "third";
    }
    return $division;
  }

  /**
   * Top Student List function.
   *
   * @return array
   */
  public function topStudentList() {
    // Headers of the table.
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
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->sort('field_score', 'DESC');
    $query->range(0 , 5);
    $nids = $query->condition('type', 'student_details')->execute();
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $year = $node->field_passing_year->getString();
      $dob = $node->field_dob->getString();
      $newDob = date("jS M Y", strtotime($dob));
      $score = $node->field_score->getString();
      $grades = $this->studentGrades($score);
      $rows[] = [
        'name' => $node->field_student_name->getString(),
        'email' => $node->field_email->getString(),
        'state' => $node->field_state->getString(),
        'city' => $node->field_city->getString(),
        'passing_year' => $year,
        'Score' => $score,
        'dob' => $newDob,
        'Grade' => $grades,
      ];
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

    }
    return $form;
  }

  /**
   * Top Student statewise List function.
   *
   * @return array
   */
  public function topStudentListStateWise() {
    // Headers for the table.
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
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->sort('field_state', 'ASC');
    $query->sort('field_city', 'ASC');
    $query->sort('field_score', 'ASC');
    $nids = $query->condition('type', 'student_details')->execute();
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $year = $node->field_passing_year->getString();
      $dob = $node->field_dob->getString();
      $newDob = date("jS M Y", strtotime($dob));
      $score = $node->field_score->getString();
      $grades = $this->studentGrades($score);
      $rows[] = [
        'name' => $node->field_student_name->getString(),
        'email' => $node->field_email->getString(),
        'state' => $node->field_state->getString(),
        'city' => $node->field_city->getString(),
        'passing_year' => $year,
        'Score' => $score,
        'dob' => $newDob,
        'Grade' => $grades,
      ];
      $topRows = array_slice($rows, 0, 5);
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $topRows,
      ];

    }
    return $form;
  }

}
