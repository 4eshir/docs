<?php
namespace unit\models;

use app\fixtures\TrainingProgramFixture;
use app\models\work\PeopleWork;
use yii\helpers\ArrayHelper;

class TrainingProgramTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    


    protected function _after()
    {
    }

    // tests
    public function testAddDefaultTrainingProgram()
    {
        $check = $this->tester->grabFixtures('training_program');

        $fixes = ArrayHelper::getColumn($check, 'name');

        expect_that($fixes[0] == 'test');
    }
}