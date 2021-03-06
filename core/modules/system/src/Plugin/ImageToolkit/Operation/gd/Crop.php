<?php

/**
 * @file
 * Contains \Drupal\system\Plugin\ImageToolkit\Operation\gd\Crop.
 */

namespace Drupal\system\Plugin\ImageToolkit\Operation\gd;

use Drupal\Component\Utility\String;

/**
 * Defines GD2 Crop operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_crop",
 *   toolkit = "gd",
 *   operation = "crop",
 *   label = @Translation("Crop"),
 *   description = @Translation("Crops an image to a rectangle specified by the given dimensions.")
 * )
 */
class Crop extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return array(
      'x' => array(
        'description' => 'The starting x offset at which to start the crop, in pixels',
      ),
      'y' => array(
        'description' => 'The starting y offset at which to start the crop, in pixels',
      ),
      'width' => array(
        'description' => 'The width of the cropped area, in pixels',
        'required' => FALSE,
        'default' => NULL,
      ),
      'height' => array(
        'description' => 'The height of the cropped area, in pixels',
        'required' => FALSE,
        'default' => NULL,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Assure at least one dimension.
    if (empty($arguments['width']) && empty($arguments['height'])) {
      throw new \InvalidArgumentException("At least one dimension ('width' or 'height') must be provided to the image 'crop' operation");
    }

    // Preserve aspect.
    $aspect = $this->getToolkit()->getHeight() / $this->getToolkit()->getWidth();
    $arguments['height'] = empty($arguments['height']) ? $arguments['width'] * $aspect : $arguments['height'];
    $arguments['width'] = empty($arguments['width']) ? $arguments['height'] / $aspect : $arguments['width'];

    // Assure integers for all arguments.
    foreach (array('x', 'y', 'width', 'height') as $key) {
      $arguments[$key] = (int) round($arguments[$key]);
    }

    // Fail when width or height are 0 or negative.
    if ($arguments['width'] <= 0) {
      throw new \InvalidArgumentException(String::format("Invalid width (@value) specified for the image 'crop' operation", array('@value' => $arguments['width'])));
    }
    if ($arguments['height'] <= 0) {
      throw new \InvalidArgumentException(String::format("Invalid height (@value) specified for the image 'crop' operation", array('@value' => $arguments['height'])));
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments) {
    $res = $this->getToolkit()->createTmp($this->getToolkit()->getType(), $arguments['width'], $arguments['height']);

    if (!imagecopyresampled($res, $this->getToolkit()->getResource(), 0, 0, $arguments['x'], $arguments['y'], $arguments['width'], $arguments['height'], $arguments['width'], $arguments['height'])) {
      return FALSE;
    }

    // Destroy the original image and return the modified image.
    imagedestroy($this->getToolkit()->getResource());
    $this->getToolkit()->setResource($res);

    return TRUE;
  }

}
