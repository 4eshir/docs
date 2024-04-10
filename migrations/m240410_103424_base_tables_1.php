<?php

use yii\db\Migration;

/**
 * Class m240410_103424_base_tables_1
 */
class m240410_103424_base_tables_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%allow_remote}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%auditorium_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%bot_message}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(1024),
        ]);

        $this->createTable('{{%branch}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%category_contract}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%category_smsp}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%certificat_templates}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'path' => $this->string(512),
        ]);

        $this->createTable('{{%certificat_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%characteristic_object}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'value_type' => $this->integer(),
        ]);

        $this->createTable('{{%company_type}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(128),
        ]);

        $this->createTable('{{%complex}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%control_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%copyright}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%distribution_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%document_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%entry}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer(),
        ]);

        $this->createTable('{{%errors}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(16),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%event_external}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%event_form}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%event_level}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%event_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%event_way}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%expire_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%finance_source}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%focus}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%foreign_event_goals}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%foreign_event_participants}}', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(128),
            'secondname' => $this->string(128),
            'patronymic' => $this->string(128)->null(),
            'birthdate' => $this->date()->defaultValue('2000-01-01'),
            'sex' => $this->string(32),
            'is_true' => $this->boolean()->defaultValue(1),
            'guaranted_true' => $this->boolean()->null(),
            'email' => $this->string(128)->null(),
        ]);

        $this->createTable('{{%install_place}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%kind_object}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%license}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%license_term_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%license_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%ownership_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%participation_scope}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%patchnotes}}', [
            'id' => $this->primaryKey(),
            'first_number' => $this->integer(),
            'second_number' => $this->integer(),
            'date' => $this->date(),
            'text' => $this->string(2048),
        ]);

        $this->createTable('{{%personal_data}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%position}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%product_union}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'count' => $this->integer(),
            'average_price' => $this->float()->comment('средняя цена за единицу'),
            'average_cost' => $this->float()->comment('средняя общая стоимость'),
            'date' => $this->date()->comment('дата постановки на учет'),
        ]);

        $this->createTable('{{%project_theme}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'description' => $this->string(256)->null(),
        ]);

        $this->createTable('{{%project_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%regulation_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%responsibility_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%role_function_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%russian_names}}', [
            'ID' => $this->primaryKey(),
            'Name' => $this->string(128),
            'Sex' => $this->string(16),
            'PeoplesCount' => $this->integer(),
            'WhenPeoplesCount' => $this->date(),
            'Source' => $this->string(128),
        ]);

        $this->createTable('{{%send_method}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('{{%thematic_direction}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(16),
            'full_name' => $this->string(128),
        ]);

        $this->createTable('{{%use_years}}', [
            'id' => $this->primaryKey(),
            'start_date' => $this->string(128),
        ]);

        $this->createTable('{{%version}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access}}');
        $this->dropTable('{{%allow_remote}}');
        $this->dropTable('{{%auditorium_type}}');
        $this->dropTable('{{%bot_message}}');
        $this->dropTable('{{%branch}}');
        $this->dropTable('{{%category_contract}}');
        $this->dropTable('{{%category_smsp}}');
        $this->dropTable('{{%certificat_templates}}');
        $this->dropTable('{{%certificat_type}}');
        $this->dropTable('{{%characteristic_object}}');
        $this->dropTable('{{%company_type}}');
        $this->dropTable('{{%complex}}');
        $this->dropTable('{{%control_type}}');
        $this->dropTable('{{%copyright}}');
        $this->dropTable('{{%country}}');
        $this->dropTable('{{%distribution_type}}');
        $this->dropTable('{{%document_type}}');
        $this->dropTable('{{%entry}}');
        $this->dropTable('{{%errors}}');
        $this->dropTable('{{%event_external}}');
        $this->dropTable('{{%event_form}}');
        $this->dropTable('{{%event_level}}');
        $this->dropTable('{{%event_type}}');
        $this->dropTable('{{%event_way}}');
        $this->dropTable('{{%expire_type}}');
        $this->dropTable('{{%finance_source}}');
        $this->dropTable('{{%focus}}');
        $this->dropTable('{{%foreign_event_goals}}');
        $this->dropTable('{{%foreign_event_participants}}');
        $this->dropTable('{{%install_place}}');
        $this->dropTable('{{%kind_object}}');
        $this->dropTable('{{%license}}');
        $this->dropTable('{{%license_term_type}}');
        $this->dropTable('{{%license_type}}');
        $this->dropTable('{{%ownership_type}}');
        $this->dropTable('{{%participation_scope}}');
        $this->dropTable('{{%patchnotes}}');
        $this->dropTable('{{%personal_data}}');
        $this->dropTable('{{%position}}');
        $this->dropTable('{{%product_union}}');
        $this->dropTable('{{%project_theme}}');
        $this->dropTable('{{%project_type}}');
        $this->dropTable('{{%regulation_type}}');
        $this->dropTable('{{%responsibility_type}}');
        $this->dropTable('{{%role_function_type}}');
        $this->dropTable('{{%russian_names}}');
        $this->dropTable('{{%send_method}}');
        $this->dropTable('{{%thematic_direction}}');
        $this->dropTable('{{%use_years}}');
        $this->dropTable('{{%version}}');

        return true;
    }
}
