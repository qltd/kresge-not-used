<?php

/**
 * @file
 * Contains \Drupal\block\Controller\CategoryAutocompleteController.
 */

namespace Drupal\block\Controller;

use Drupal\block\BlockManagerInterface;
use Drupal\Component\Utility\String;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns autocomplete responses for block categories.
 */
class CategoryAutocompleteController implements ContainerInjectionInterface {

  /**
   * The block manager.
   *
   * @var \Drupal\block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs a new CategoryAutocompleteController.
   *
   * @param \Drupal\block\BlockManagerInterface $block_manager
   *   The block manager.
   */
  public function __construct(BlockManagerInterface $block_manager) {
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block')
    );
  }

  /**
   * Retrieves suggestions for block category autocompletion.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing autocomplete suggestions.
   */
  public function autocomplete(Request $request) {
    $typed_category = $request->query->get('q');
    $matches = array();
    foreach ($this->blockManager->getCategories() as $category) {
      if (stripos($category, $typed_category) === 0) {
        $matches[] = array('value' => $category, 'label' => String::checkPlain($category));
      }
    }
    return new JsonResponse($matches);
  }

}
