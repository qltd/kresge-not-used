<?php

/**
 * @file
 * Contains \Drupal\system\DateFormatAccessControlHandler.
 */

namespace Drupal\system;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the date format entity type.
 *
 * @see \Drupal\system\Entity\DateFormat
 */
class DateFormatAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    // There are no restrictions on viewing a date format.
    if ($operation == 'view') {
      return TRUE;
    }
    // Locked date formats cannot be updated or deleted.
    elseif (in_array($operation, array('update', 'delete')) && $entity->isLocked()) {
      return FALSE;
    }

    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
