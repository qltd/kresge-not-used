<?php

/**
 * @file
 * Contains \Drupal\migrate_drupal\Tests\source\VariableTest.
 */

namespace Drupal\migrate_drupal\Tests\source;

use Drupal\migrate\Tests\MigrateSqlSourceTestCase;

/**
 * Tests the variable source plugin.
 *
 * @group migrate_drupal
 */
class VariableTest extends MigrateSqlSourceTestCase {

  const PLUGIN_CLASS = 'Drupal\migrate_drupal\Plugin\migrate\source\Variable';

  protected $migrationConfiguration = array(
    'id' => 'test',
    'highWaterProperty' => array('field' => 'test'),
    'idlist' => array(),
    'source' => array(
      'plugin' => 'd6_variable',
      'variables' => array(
        'foo',
        'bar',
      ),
    ),
  );

  protected $expectedResults = array(
    array(
      'foo' => 1,
      'bar' => FALSE,
    ),
  );

  protected $databaseContents = array(
    'variable' => array(
      array('name' => 'foo', 'value' => 'i:1;'),
      array('name' => 'bar', 'value' => 'b:0;'),
    ),
  );

}

namespace Drupal\migrate_drupal\Tests\source;

use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;

class TestVariable extends \Drupal\migrate_drupal\Plugin\migrate\source\Variable {
  public function setDatabase(Connection $database) {
    $this->database = $database;
  }
  public function setModuleHandler(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }
}
