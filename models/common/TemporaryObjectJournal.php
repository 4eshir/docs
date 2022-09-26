<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "temporary_object_journal".
 *
 * @property int $id
 * @property int $user_give_id отдает
 * @property int $user_get_id получает
 * @property int $confirm_give
 * @property int $confirm_get
 * @property int $material_object_id
 * @property int|null $container_id куда положили объект
 * @property string|null $comment
 * @property string $date_give дата выдачи объекта
 * @property string $date_get дата предполагаемого возврата объекта
 * @property string|null $real_date_get реальная дата возврата
 *
 * @property User $userGive
 * @property User $userGet
 * @property MaterialObject $materialObject
 * @property Container $container
 */
class TemporaryObjectJournal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temporary_object_journal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_give_id', 'user_get_id', 'material_object_id', 'date_give', 'date_get'], 'required'],
            [['user_give_id', 'user_get_id', 'confirm_give', 'confirm_get', 'material_object_id', 'container_id'], 'integer'],
            [['date_give', 'date_get', 'real_date_get'], 'safe'],
            [['comment'], 'string', 'max' => 2000],
            [['user_give_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_give_id' => 'id']],
            [['user_get_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_get_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_give_id' => 'User Give ID',
            'user_get_id' => 'User Get ID',
            'confirm_give' => 'Confirm Give',
            'confirm_get' => 'Confirm Get',
            'material_object_id' => 'Material Object ID',
            'container_id' => 'Container ID',
            'comment' => 'Comment',
            'date_give' => 'Date Give',
            'date_get' => 'Date Get',
            'real_date_get' => 'Real Date Get',
        ];
    }

    /**
     * Gets query for [[UserGive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserGive()
    {
        return $this->hasOne(User::className(), ['id' => 'user_give_id']);
    }

    /**
     * Gets query for [[UserGet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserGet()
    {
        return $this->hasOne(User::className(), ['id' => 'user_get_id']);
    }

    /**
     * Gets query for [[MaterialObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'material_object_id']);
    }

    /**
     * Gets query for [[Container]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainer()
    {
        return $this->hasOne(Container::className(), ['id' => 'container_id']);
    }
}
