<?php

/**
 * @file
 * Provides views data for the entity_reference module.
 */

use Drupal\Core\Entity\ContentEntityDatabaseStorage;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Implements hook_field_views_data().
 */
function entity_reference_field_views_data(FieldStorageConfigInterface $field_storage) {
  $data = field_views_field_default_views_data($field_storage);
  $entity_manager = \Drupal::entityManager();
  foreach ($data as $table_name => $table_data) {
    // Add a relationship to the target entity type.
    $target_entity_type_id = $field_storage->getSetting('target_type');
    $target_entity_type = $entity_manager->getDefinition($target_entity_type_id);
    $target_base_table = $target_entity_type->getBaseTable();

    // Provide a relationship for the entity type with the entity reference
    // field.
    $args = array(
      '@label' => $target_entity_type->getLabel(),
      '@field_name' => $field_storage->getName(),
    );
    $data[$table_name][$field_storage->getName()]['relationship'] = array(
      'id' => 'standard',
      'base' => $target_base_table,
      'entity type' => $target_entity_type_id,
      'base field' => $target_entity_type->getKey('id'),
      'relationship field' => $field_storage->getName() . '_target_id',
      'title' => t('@label referenced from @field_name', $args),
      'label' => t('@field_name: @label', $args),
    );

    // Provide a reverse relationship for the entity type that is referenced by
    // the field.
    $pseudo_field_name = 'reverse__' . $field_storage->getTargetEntityTypeId() . '__' . $field_storage->getName();
    $data[$target_base_table][$pseudo_field_name]['relationship'] = array(
      'title' => t('@label using @field_name', $args),
      'help' => t('Relate each @label with a @field_name.', $args),
      'id' => 'entity_reverse',
      'field_name' => $field_storage->getName(),
      'field table' => ContentEntityDatabaseStorage::_fieldTableName($field_storage),
      'field field' => $field_storage->getName() . '_target_id',
      'base' => $target_entity_type->getBaseTable(),
      'base field' => $target_entity_type->getKey('id'),
      'label' => t('@field_name', array('@field_name' => $field_storage->getName())),
    );
  }

  return $data;
}
