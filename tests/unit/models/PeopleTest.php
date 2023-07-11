<?php
namespace tests\unit\models;

use app\fixtures\PeopleFixture;
use app\models\work\PeopleWork;
use yii\helpers\ArrayHelper;

class PeopleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    


    protected function _after()
    {
    }

    // tests
    public function testAddDefaultPeople()
    {
        $check = $this->tester->grabFixtures('peoples');

        $fixes = ArrayHelper::getColumn($check, 'firstname');

        expect_that($fixes[0] == 'test');

        //expect_that($check['peoples']->getModel('people1')->firstname == 'test1');

    }
}