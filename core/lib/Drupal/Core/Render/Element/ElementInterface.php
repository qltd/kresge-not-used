<?php

/**
 * @file
 * Contains \Drupal\Core\Render\Element\ElementInterface.
 */

namespace Drupal\Core\Render\Element;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Provides an interface for element plugins.
 *
 * Render element plugins allow modules to declare their own Render API element
 * types and specify the default values for the properties. The values returned
 * by the getInfo() method of the element plugin will be merged with the
 * properties specified in render arrays. Thus, you can specify defaults for any
 * Render API keys, in addition to those explicitly documented by
 * \Drupal\Core\Render\ElementInfoManagerInterface::getInfo().
 *
 * Some render elements are specifically form input elements; see
 * \Drupal\Core\Render\Element\FormElementInterface for more information.
 *
 * @see \Drupal\Core\Render\ElementInfoManager
 * @see \Drupal\Core\Render\Annotation\RenderElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see plugin_api
 *
 * @ingroup theme_render
 */
interface ElementInterface extends PluginInspectionInterface {

  /**
   * Returns the element properties for this element.
   *
   * @return array
   *   An array of element properties. See
   *   \Drupal\Core\Render\ElementInfoManagerInterface::getInfo() for
   *   documentation of the standard properties of all elements, and the
   *   return value format.
   */
  public function getInfo();

}
