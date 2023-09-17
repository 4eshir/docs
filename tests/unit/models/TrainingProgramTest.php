<?php
namespace unit\models;

use app\fixtures\TrainingProgramFixture;
use app\models\work\PeopleWork;
use app\models\work\TrainingProgramWork;
use Yii;
use yii\helpers\ArrayHelper;

class TrainingProgramTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    


    protected function _after()
    {
        Yii::$app->db->createCommand()->insert(TrainingProgramWork::tableName(), [
            'name' => 'test',
        ])->execute();
    }

    // tests
    public function testAddDefaultTrainingProgram()
    {
        $check = $this->tester->grabFixtures('training_program');

        $fixes = ArrayHelper::getColumn($check, 'name');

        expect_that($fixes[0] == 'test');
    }
}