<?php

use yii\db\Migration;

/**
 * Class m230503_061929_migrate_GA259_GA260_cppt_t
 */
class m230503_061929_migrate_GA259_GA260_cppt_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.cppt_t ADD IF NOT EXISTS is_verbal_order bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230503_061929_migrate_GA259_GA260_cppt_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230503_061929_migrate_GA259_GA260_cppt_t cannot be reverted.\n";

        return false;
    }
    */
}
