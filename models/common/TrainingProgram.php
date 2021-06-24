<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;

/**
 * This is the model class for table "training_program".
 *
 * @property int $id
 * @property string $name
 * @property int $thematic_direction_id
 * @property int $level
 * @property string|null $ped_council_date
 * @property string|null $ped_council_number
 * @property int $author_id
 * @property int $capacity
 * @property int $student_left_age
 * @property int $student_right_age
 * @property int $focus_id
 * @property int $allow_remote
 * @property string|null $doc_file
 * @property string|null $edit_docs
 * @property string|null $key_words
 * @property int $hour_capacity
 *
 * @property BranchProgram[] $branchPrograms
 * @property People $author
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
            [['name', 'author_id', 'focus', 'hour_capacity'], 'required'],
            [['ped_council_date'], 'safe'],
            [['focus_id', 'author_id', 'capacity', 'student_left_age', 'student_right_age', 'allow_remote', ], 'integer'],
            [['name', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words'], 'string', 'max' => 1000],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['author_id' => 'id']],
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
            'name' => 'Название',
            'ped_council_date' => 'Дата педагогического совета',
            'ped_council_number' => 'Номер протокола педагогического совета',
            'author_id' => 'Составитель',
            'thematic_direction_id' => 'Тематическое направление',
            'level' => 'Уровень сложности',
            'authorsList' => 'Составители',
            'capacity' => 'Объем, ак. час.',
            'student_left_age' => 'Мин. возраст учащихся, лет',
            'student_right_age' => 'Макс. возраст учащихся, лет',
            'studentAge' => 'Возраст учащихся, лет',
            'focus_id' => 'Направленность',
            'stringFocus' => 'Направленность',
            'allow_remote' => 'С применением дистанционных технологий',
            'doc_file' => 'Документ программы',
            'docFile' => 'Документ программы',
            'edit_docs' => 'Редактируемые документы',
            'editDocs' => 'Редактируемые документы',
            'key_words' => 'Ключевые слова',
            'branchs' => 'Отдел(-ы) - место реализации',
            'hour_capacity' => 'Длительность 1 академического часа в минутах',
        ];
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
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(People::className(), ['id' => 'author_id']);
    }
}
