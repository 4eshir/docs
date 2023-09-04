<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_program".
 *
 * @property int $id
 * @property string $name
 * @property int|null $thematic_direction_id
 * @property int $level
 * @property string|null $ped_council_date
 * @property string|null $ped_council_number
 * @property int|null $author_id
 * @property int $capacity
 * @property int $student_left_age
 * @property int $student_right_age
 * @property int|null $focus_id
 * @property int $allow_remote_id
 * @property string|null $doc_file
 * @property string|null $edit_docs
 * @property string|null $key_words
 * @property int $hour_capacity
 * @property int $actual
 * @property int|null $certificat_type_id
 * @property int|null $creator_id
 * @property string|null $description
 * @property int|null $last_update_id
 * @property int|null $is_network
 * @property string|null $contract
 *
 * @property AuthorProgram[] $authorPrograms
 * @property BranchProgram[] $branchPrograms
 * @property ThematicPlan[] $thematicPlans
 * @property TrainingGroup[] $trainingGroups
 * @property AllowRemote $allowRemote
 * @property CertificatType $certificatType
 * @property Focus $focus
 * @property User $lastUpdate
 * @property ThematicDirection $thematicDirection
 */
class TrainingProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'allow_remote_id'], 'required'],
            [['thematic_direction_id', 'level', 'author_id', 'capacity', 'student_left_age', 'student_right_age', 'focus_id', 'allow_remote_id', 'hour_capacity', 'actual', 'certificat_type_id', 'creator_id', 'last_update_id', 'is_network'], 'integer'],
            [['ped_council_date'], 'safe'],
            [['description'], 'string'],
            [['name', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words', 'contract'], 'string', 'max' => 1000],
            [['allow_remote_id'], 'exist', 'skipOnError' => true, 'targetClass' => AllowRemote::className(), 'targetAttribute' => ['allow_remote_id' => 'id']],
            [['certificat_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificatType::className(), 'targetAttribute' => ['certificat_type_id' => 'id']],
            [['focus_id'], 'exist', 'skipOnError' => true, 'targetClass' => Focus::className(), 'targetAttribute' => ['focus_id' => 'id']],
            [['last_update_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_update_id' => 'id']],
            [['thematic_direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThematicDirection::className(), 'targetAttribute' => ['thematic_direction_id' => 'id']],
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
            'thematic_direction_id' => 'Thematic Direction ID',
            'level' => 'Level',
            'ped_council_date' => 'Ped Council Date',
            'ped_council_number' => 'Ped Council Number',
            'author_id' => 'Author ID',
            'capacity' => 'Capacity',
            'student_left_age' => 'Student Left Age',
            'student_right_age' => 'Student Right Age',
            'focus_id' => 'Focus ID',
            'allow_remote_id' => 'Allow Remote ID',
            'doc_file' => 'Doc File',
            'edit_docs' => 'Edit Docs',
            'key_words' => 'Key Words',
            'hour_capacity' => 'Hour Capacity',
            'actual' => 'Actual',
            'certificat_type_id' => 'Certificat Type ID',
            'creator_id' => 'Creator ID',
            'description' => 'Description',
            'last_update_id' => 'Last Update ID',
            'is_network' => 'Is Network',
            'contract' => 'Contract',
        ];
    }

    /**
     * Gets query for [[AuthorPrograms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorPrograms()
    {
        return $this->hasMany(AuthorProgram::className(), ['training_program_id' => 'id']);
    }

    /**
     * Gets query for [[BranchPrograms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranchPrograms()
    {
        return $this->hasMany(BranchProgram::className(), ['training_program_id' => 'id']);
    }

    /**
     * Gets query for [[ThematicPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThematicPlans()
    {
        return $this->hasMany(ThematicPlan::className(), ['training_program_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroups()
    {
        return $this->hasMany(TrainingGroup::className(), ['training_program_id' => 'id']);
    }

    /**
     * Gets query for [[AllowRemote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAllowRemote()
    {
        return $this->hasOne(AllowRemote::className(), ['id' => 'allow_remote_id']);
    }

    /**
     * Gets query for [[CertificatType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCertificatType()
    {
        return $this->hasOne(CertificatType::className(), ['id' => 'certificat_type_id']);
    }

    /**
     * Gets query for [[Focus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFocus()
    {
        return $this->hasOne(Focus::className(), ['id' => 'focus_id']);
    }

    /**
     * Gets query for [[LastUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastUpdate()
    {
        return $this->hasOne(User::className(), ['id' => 'last_update_id']);
    }

    /**
     * Gets query for [[ThematicDirection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThematicDirection()
    {
        return $this->hasOne(ThematicDirection::className(), ['id' => 'thematic_direction_id']);
    }
}
