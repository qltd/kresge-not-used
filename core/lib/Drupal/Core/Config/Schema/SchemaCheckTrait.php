<?php

/**
 * @file
 * Contains \Drupal\Core\Config\Schema\SchemaCheckTrait.
 */

namespace Drupal\Core\Config\Schema;

use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\TypedData\PrimitiveInterface;
use Drupal\Core\TypedData\Type\BooleanInterface;
use Drupal\Core\TypedData\Type\StringInterface;
use Drupal\Core\TypedData\Type\FloatInterface;
use Drupal\Core\TypedData\Type\IntegerInterface;

/**
 * Provides a trait for checking configuration schema.
 */
trait SchemaCheckTrait {

  /**
   * The config schema wrapper object for the configuration object under test.
   *
   * @var \Drupal\Core\Config\Schema\Element
   */
  protected $schema;

  /**
   * The configuration object name under test.
   *
   * @var string
   */
  protected $configName;

  /**
   * Checks the TypedConfigManager has a valid schema for the configuration.
   *
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config
   *   The TypedConfigManager.
   * @param string $config_name
   *   The configuration name.
   * @param array $config_data
   *   The configuration data.
   *
   * @return array|bool
   *   FALSE if no schema found. List of errors if any found. TRUE if fully
   *   valid.
   */
  public function checkConfigSchema(TypedConfigManagerInterface $typed_config, $config_name, $config_data) {
    $this->configName = $config_name;
    if (!$typed_config->hasConfigSchema($config_name)) {
      return FALSE;
    }
    $definition = $typed_config->getDefinition($config_name);
    $data_definition = $typed_config->buildDataDefinition($definition, $config_data);
    $this->schema = $typed_config->create($data_definition, $config_data);
    $errors = array();
    foreach ($config_data as $key => $value) {
      $errors = array_merge($errors, $this->checkValue($key, $value));
    }
    if (empty($errors)) {
      return TRUE;
    }
    return $errors;
  }

  /**
   * Helper method to check data type.
   *
   * @param string $key
   *   A string of configuration key.
   * @param mixed $value
   *   Value of given key.
   *
   * @return array
   *   List of errors found while checking with the corresponding schema.
   */
  protected function checkValue($key, $value) {
    $error_key = $this->configName . ':' . $key;
    $element = $this->schema->get($key);
    if ($element instanceof Undefined) {
      // @todo Temporary workaround for https://www.drupal.org/node/2224761.
      $key_parts = explode('.', $key);
      if (array_pop($key_parts) == 'translation_sync' && strpos($this->configName, 'field.') === 0) {
        return array();
      }
      return array($error_key => 'Missing schema.');
    }

    // Do not check value if it is defined to be ignored.
    if ($element && $element instanceof Ignore) {
      return array();
    }

    if ($element && is_scalar($value) || $value === NULL) {
      $success = FALSE;
      $type = gettype($value);
      if ($element instanceof PrimitiveInterface) {
        $success =
          ($type == 'integer' && $element instanceof IntegerInterface) ||
          ($type == 'double' && $element instanceof FloatInterface) ||
          ($type == 'boolean' && $element instanceof BooleanInterface) ||
          ($type == 'string' && $element instanceof StringInterface) ||
          // Null values are allowed for all types.
          ($value === NULL);
      }
      $class = get_class($element);
      if (!$success) {
        return array($error_key => "Variable type is $type but applied schema class is $class.");
      }
    }
    else {
      $errors = array();
      if (!$element instanceof ArrayElement) {
        $errors[$error_key] = 'Non-scalar value but not defined as an array (such as mapping or sequence).';
      }

      // Go on processing so we can get errors on all levels. Any non-scalar
      // value must be an array so cast to an array.
      if (!is_array($value)) {
        $value = (array) $value;
      }
      // Recurse into any nested keys.
      foreach ($value as $nested_value_key => $nested_value) {
        $errors = array_merge($errors, $this->checkValue($key . '.' . $nested_value_key, $nested_value));
      }
      return $errors;
    }
    // No errors found.
    return array();
  }
}
