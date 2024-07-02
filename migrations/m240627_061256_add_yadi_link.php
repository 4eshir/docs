<?php

use yii\db\Migration;

/**
 * Class m240627_061256_add_yadi_link
 */
class m240627_061256_add_yadi_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('training_group', 'photos_link', $this->string(256)->null());
        $this->addColumn('training_group', 'present_data_link', $this->string(256)->null());
        $this->addColumn('training_group', 'work_data_link', $this->string(256)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('training_group', 'photos_link');
        $this->dropColumn('training_group', 'present_data_link');
        $this->dropColumn('training_group', 'work_data_link');

        return true;
    }
}
