<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "as_install".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $as_admin_id
 * @property int $cabinet
 * @property int $count
 *
 * @property AsAdmin $asAdmin
 * @property Branch $branch
 */
class AsInstall extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'as_install';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'as_admin_id', 'cabinet', 'count'], 'required'],
            [['branch_id', 'as_admin_id', 'cabinet', 'count'], 'integer'],
            [['as_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsAdmin::className(), 'targetAttribute' => ['as_admin_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'as_admin_id' => 'As Admin ID',
            'cabinet' => 'Cabinet',
            'count' => 'Count',
        ];
    }

    /**
     * Gets query for [[AsAdmin]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmin()
    {
        return $this->hasOne(AsAdmin::className(), ['id' => 'as_admin_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }
}
