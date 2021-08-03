<?php

namespace app\models;

use app\models\common\BranchProgram;
use app\models\common\TeacherGroup;
use app\models\common\TeacherParticipant;
use app\models\common\TrainingGroup;
use app\models\common\User;
use app\models\work\BranchProgramWork;
use app\models\work\TeacherGroupWork;
use app\models\work\UserWork;
use app\models\components\UserRBAC;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\TrainingGroupWork;

/**
 * SearchTrainingGroup represents the model behind the search form of `app\models\common\TrainingGroup`.
 */
class SearchTrainingGroup extends TrainingGroupWork
{
    public $programName;
    public $budgetText;
    public $branchId;
    public $teacherId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'training_program_id', 'teacher_id', 'open', 'budgetText'], 'integer'],
            [['start_date', 'finish_date', 'photos', 'present_data', 'work_data', 'branchId', 'teacherId'], 'safe'],
            ['programName', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /*
        $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $groups = TrainingGroupWork::find()->where(['teacher_id' => $user->aka])->all();
        $branchs = BranchProgramWork::find()->where(['branch_id' => $params ["SearchTrainingGroup"]["branchId"]])->all();
        $teachers = TeacherGroupWork::find()->where(['teacher_id' => $params ["SearchTrainingGroup"]["teacherId"]])->all();
        $idsB = [];
        $idsTG = [];
        foreach ($branchs as $branch)
            $idsB[] = $branch->training_program_id;
        foreach ($teachers as $teacher)
            $idsTG[] = $teacher->training_group_id;
        if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 22)) //доступ на просмотр ВСЕХ групп
        {
            if (count($branchs) > 0 && count($teachers) > 0)
                $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB])->andWhere(['in', 'training_group.id', $idsTG]);
            else if (count($teachers) > 0)
                    $groups = TrainingGroupWork::find()->where(['in', 'training_group.id', $idsTG]);
                else if (count($branchs) > 0)
                    $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB]);
                else
                    $groups = TrainingGroupWork::find();

        }
        else if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 24)) //доступ на просмотр групп СВОЕГО ОТДЕЛА
        {
            $branchs = \app\models\work\PeoplePositionBranchWork::find()->select('branch_id')->distinct()->where(['people_id' => $user->aka])->all();

            if ($branchs !== null)
            {
                $branchs_id = [];
                foreach ($branchs as $branch) $branchs_id[] = $branch->branch_id;
                $groups_id = \app\models\work\TrainingGroupLessonWork::find()->select('training_group_id')->distinct()->where(['in', 'branch_id', $branchs_id])->all();
                $newGroups_id = [];
                foreach ($groups_id as $group_id) $newGroups_id[] = $group_id->training_group_id;

                if (count($branchs) > 0 && count($teachers) > 0)
                    $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB])->andWhere(['in', 'training_group.id', $idsTG])->andWhere(['in', 'training_group.id', $newGroups_id]);
                else if (count($teachers) > 0)
                    $groups = TrainingGroupWork::find()->where(['in', 'training_group.id', $idsTG])->andWhere(['in', 'training_group.id', $newGroups_id]);
                else if (count($branchs) > 0)
                    $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB])->andWhere(['in', 'training_group.id', $newGroups_id]);
                else
                {

                    $groups = TrainingGroupWork::find()->where(['in', 'training_group.id', $newGroups_id]);

                }

            }
        }
        else
        {
            $teachers2 = \app\models\work\TeacherGroupWork::find()->select('training_group_id')->distinct()->where(['teacher_id' => $user->aka])->all();
            $teachers_id = [];
            foreach ($teachers2 as $teacher) $teachers_id[] = $teacher->training_group_id;
            if (count($branchs) > 0 && count($teachers) > 0)
                $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB])->andWhere(['in', 'training_group.id', $idsTG])->andWhere(['in', 'training_group.id', $teachers_id]);
            else if (count($teachers) > 0)
                $groups = TrainingGroupWork::find()->where(['in', 'training_group.id', $idsTG])->andWhere(['in', 'training_group.id', $teachers_id]);
            else if (count($branchs) > 0)
                $groups = TrainingGroupWork::find()->where(['in', 'training_program_id', $idsB])->andWhere(['in', 'training_group.id', $teachers_id]);
            else {

                $groups = TrainingGroupWork::find()->andWhere(['in', 'training_group.id', $teachers_id]);
            }
        } */

        $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $groups = TrainingGroupWork::find()->where(['teacher_id' => $user->aka])->all();
        $newGroups_id = [];
        if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 23)) //доступ на редактирование ВСЕХ групп
        {
            $groups = TrainingGroupWork::find()->all();
            foreach ($groups as $group) $newGroups_id[] = $group->id;
        }
        else if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 25)) //доступ на редактирование групп СВОЕГО ОТДЕЛА
        {
            $branchs = \app\models\common\PeoplePositionBranch::find()->select('branch_id')->distinct()->where(['people_id' => $user->aka])->all();
            if ($branchs !== null)
            {
                $branchs_id = [];
                foreach ($branchs as $branch) $branchs_id[] = $branch->branch_id;
                $groups_id = \app\models\common\TrainingGroupLesson::find()->select('training_group_id')->distinct()->where(['in', 'branch_id', $branchs_id])->all();

                $newGroups_id = [];
                foreach ($groups_id as $group) $newGroups_id[] = $group->training_group_id;
            }
        }

        $teachers = \app\models\common\TeacherGroup::find()->select('training_group_id')->distinct()->where(['teacher_id' => $user->aka])->all();
        foreach ($teachers as $teacher) $newGroups_id[] = $teacher->training_group_id;

        //$query = TrainingGroup::find()->where(['teacher_id' => $user->aka]);
        $query = TrainingGroupWork::find()->where(['in', 'training_group.id', $newGroups_id]);
        $query->joinWith(['trainingProgram trainingProgram']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['programName'] = [
            'asc' => ['trainingProgram.name' => SORT_ASC],
            'desc' => ['trainingProgram.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'number' => $this->number,
            'training_program_id' => $this->training_program_id,
            'teacher_id' => $this->teacher_id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'open' => $this->open,
            'budget' => $this->budget,
        ]);

        $query->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'present_data', $this->present_data])
            ->andFilterWhere(['like', 'budget', $this->budgetText])
            ->andFilterWhere(['like', 'trainingProgram.name', $this->programName])
            ->andFilterWhere(['like', 'work_data', $this->work_data]);

        return $dataProvider;
    }
}
