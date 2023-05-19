<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "product_union".
 *
 * @property int $id
 * @property string $name
 * @property int $count
 * @property float $average_price средняя цена за единицу
 * @property float $average_cost средняя общая стоимость
 * @property string $date дата постановки на учет
 *
 * @property UnionObject[] $unionObjects
 */
class ProductUnion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_union';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'count', 'average_price', 'average_cost', 'date'], 'required'],
            [['count'], 'integer'],
            [['average_price', 'average_cost'], 'number'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'count' => 'Count',
            'average_price' => 'Average Price',
            'average_cost' => 'Average Cost',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[UnionObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnionObjects()
    {
        return $this->hasMany(UnionObject::className(), ['union_id' => 'id']);
    }
}
