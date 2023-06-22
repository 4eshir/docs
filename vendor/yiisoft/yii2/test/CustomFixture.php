<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\test;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\TableSchema;

/**
 * ActiveFixture represents a fixture backed up by a [[modelClass|ActiveRecord class]] or a [[tableName|database table]].
 *
 * Either [[modelClass]] or [[tableName]] must be set. You should also provide fixture data in the file
 * specified by [[dataFile]] or overriding [[getData()]] if you want to use code to generate the fixture data.
 *
 * When the fixture is being loaded, it will first call [[resetTable()]] to remove any existing data in the table.
 * It will then populate the table with the data returned by [[getData()]].
 *
 * After the fixture is loaded, you can access the loaded data via the [[data]] property. If you set [[modelClass]],
 * you will also be able to retrieve an instance of [[modelClass]] with the populated data via [[getModel()]].
 *
 * For more details and usage information on ActiveFixture, see the [guide article on fixtures](guide:test-fixtures).
 *
 * @property-read TableSchema $tableSchema The schema information of the database table associated with this
 * fixture.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
interface CustomFixture
{
    public function restoreObject();
    public function autodelete();
}