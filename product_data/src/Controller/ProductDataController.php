<?php

namespace Drupal\product_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Returns responses for product_data routes.
 */
class ProductDataController extends ControllerBase {

  /**
   * Display the data table.
   *
   * @return array
   */
  public function display() {
    // Create table header.
    $headerTable = [
      'id' => $this->t('Sr No'),
      'productname' => $this->t('Product Name'),
      'instock' => $this->t('In Stock'),
      'category' => $this->t('Category'),
      'price' => $this->t('Price'),
      'skucode' => $this->t('SKU Code'),
      'remove' => $this->t('Delete'),
    ];
    // Select records from table.
    $query = \Drupal::database()->select('product_data', 'p');
    $query->fields('p', ['id', 'productname', 'skucode', 'price', 'category', 'instock']);
    $query->OrderBy('category', 'ASC');
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $data) {
      // Print the data from table.
      $url_delete = Url::fromRoute('product_data.delete_form', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $rows[] = [
        'id' => $data->id,
        'productname' => $data->productname,
        'instock' => $data->instock,
        'category' => $data->category,
        'price' => $data->price,
        'skucode' => $data->skucode,
        'remove' => $linkDelete ,
      ];

    }
    // Display data in site.
    $form['table'] = [
      '#type' => 'table',
      '#header' => $headerTable,
      '#rows' => $rows,
      '#empty' => $this->t('No users found'),
    ];
    return $form;

  }

  /**
   * Display sorted result function.
   *
   * @return array
   */
  public function sortedProductDetails() {
    $headerTable = [
      'id' => $this->t('Sr No'),
      'productname' => $this->t('Product Name'),
      'instock' => $this->t('In Stock'),
      'category' => $this->t('Category'),
      'price' => $this->t('Price'),
      'skucode' => $this->t('SKU Code'),
    ];

    $query = \Drupal::database()->select('product_data', 'p');
    $query->innerjoin('product_data', 'pd', 'pd.id =  p.id');
    $query->fields('p', ['id', 'productname', 'skucode', 'price', 'category', 'instock']);
    $query->condition('p.instock', '%yes%', 'LIKE');
    $query->OrderBy('p.price', 'ASC');
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $data) {
      $rows[] = [
        'id' => $data->id,
        'productname' => $data->productname,
        'instock' => $data->instock,
        'category' => $data->category,
        'price' => $data->price,
        'skucode' => $data->skucode,
      ];
    }
    // Display data in site.
    $form['table'] = [
      '#type' => 'table',
      '#header' => $headerTable,
      '#rows' => $rows,
      '#empty' => $this->t('No users found'),
    ];
    return $form;

  }

}
