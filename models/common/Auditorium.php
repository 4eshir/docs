<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "auditorium".
 *
 * @property int $id
 * @property string $name
 * @property float $square
 * @property string|null $text
 * @property int|null $capacity
 * @property string|null $files
 * @property int $is_education
 * @property int $branch_id
 * @property int $include_square
 * @property int $window_count
 * @property int|null $auditorium_type_id
 *
 * @property Branch $branch
 * @property AuditoriumType $auditoriumType
 * @property LegacyResponsible[] $legacyResponsibles
 * @property LocalResponsibility[] $localResponsibilities
 * @property TemporaryJournal[] $temporaryJournals
 * @property TrainingGroupLesson[] $trainingGroupLessons
 */
class Auditorium extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditorium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'square', 'branch_id'], 'required'],
            [['square'], 'number'],
            [['capacity', 'is_education', 'branch_id', 'include_square', 'window_count', 'auditorium_type_id'], 'integer'],
            [['name', 'text', 'files'], 'string', 'max' => 1000],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['auditorium_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuditoriumType::className(), 'targetAttribute' => ['auditorium_type_id' => 'id']],
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
            'square' => 'Square',
            'text' => 'Text',
            'capacity' => 'Capacity',
            'files' => 'Files',
            'is_education' => 'Is Education',
            'branch_id' => 'Branch ID',
            'include_square' => 'Include Square',
            'window_count' => 'Window Count',
            'auditorium_type_id' => 'Auditorium Type ID',
        ];
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

    /**
     * Gets query for [[AuditoriumType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditoriumType()
    {
        return $this->hasOne(AuditoriumType::className(), ['id' => 'auditorium_type_id']);
    }

    /**
     * Gets query for [[LegacyResponsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyResponsibles()
    {
        return $this->hasMany(LegacyResponsible::className(), ['auditorium_id' => 'id']);
    }

    /**
     * Gets query for [[LocalResponsibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocalResponsibilities()
    {
        return $this->hasMany(LocalResponsibility::className(), ['auditorium_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['auditorium_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupLessons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLessons()
    {
        return $this->hasMany(TrainingGroupLesson::className(), ['auditorium_id' => 'id']);
    }
}
