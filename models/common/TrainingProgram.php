<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_program".
 *
 * @property int $id
 * @property string $name
 * @property string|null $ped_council_date
 * @property string|null $ped_council_number
 * @property int|null $author_id
 * @property int $capacity
 * @property int $student_left_age
 * @property int $student_right_age
 * @property int $focus_id
 * @property int $thematic_direction_id
 * @property int $hour_capacity
 * @property int $level
 * @property int $allow_remote
 * @property string|null $doc_file
 * @property string|null $edit_docs
 * @property string|null $key_words
 * @property int $actual
 *
 * @property AuthorProgram[] $authorPrograms
 * @property BranchProgram[] $branchPrograms
 * @property TrainingGroup[] $trainingGroups
 * @property People $author
 * @property Focus $focus
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
            [['name', 'focus_id', 'level'], 'required'],
            [['ped_council_date'], 'safe'],
            [['author_id', 'capacity', 'student_left_age', 'student_right_age', 'focus_id', 'thematic_direction_id', 'hour_capacity', 'level', 'allow_remote', 'actual'], 'integer'],
            [['name', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words'], 'string', 'max' => 1000],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['focus_id'], 'exist', 'skipOnError' => true, 'targetClass' => Focus::className(), 'targetAttribute' => ['focus_id' => 'id']],
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
            'ped_council_date' => 'Ped Council Date',
            'ped_council_number' => 'Ped Council Number',
            'author_id' => 'Author ID',
            'capacity' => 'Capacity',
            'student_left_age' => 'Student Left Age',
            'student_right_age' => 'Student Right Age',
            'focus_id' => 'Focus ID',
            'thematic_direction_id' => 'Thematic Direction ID',
            'hour_capacity' => 'Hour Capacity',
            'level' => 'Level',
            'allow_remote' => 'Allow Remote',
            'doc_file' => 'Doc File',
            'edit_docs' => 'Edit Docs',
            'key_words' => 'Key Words',
            'actual' => 'Actual',
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
     * Gets query for [[TrainingGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroups()
    {
        return $this->hasMany(TrainingGroup::className(), ['training_program_id' => 'id']);
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(People::className(), ['id' => 'author_id']);
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
}
