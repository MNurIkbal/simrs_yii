<?php

use yii\db\Migration;

/**
 * Class m231028_050209_rpp_792_autocreate_task_id
 */
class m231028_050209_rpp_792_autocreate_task_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigsystem_k ADD COLUMN IF NOT EXISTS auto_create_task_id bool NULL DEFAULT TRUE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_050209_rpp_792_autocreate_task_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_050209_rpp_792_autocreate_task_id cannot be reverted.\n";

        return false;
    }
    */
}
