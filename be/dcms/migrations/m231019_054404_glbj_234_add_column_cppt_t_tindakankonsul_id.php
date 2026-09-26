<?php

use yii\db\Migration;

/**
 * Class m231019_054404_glbj_234_add_column_cppt_t_tindakankonsul_id
 */
class m231019_054404_glbj_234_add_column_cppt_t_tindakankonsul_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.cppt_t ADD IF NOT EXISTS tindakankonsul_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231019_054404_glbj_234_add_column_cppt_t_tindakankonsul_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231019_054404_glbj_234_add_column_cppt_t_tindakankonsul_id cannot be reverted.\n";

        return false;
    }
    */
}
