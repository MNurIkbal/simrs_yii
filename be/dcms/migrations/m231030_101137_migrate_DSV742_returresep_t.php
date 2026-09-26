<?php

use yii\db\Migration;

/**
 * Class m231030_101137_migrate_DSV742_returresep_t
 */
class m231030_101137_migrate_DSV742_returresep_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("ALTER TABLE public.returresep_t ADD IF NOT EXISTS status_retur int4 NULL DEFAULT 2118;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_101137_migrate_DSV742_returresep_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_101137_migrate_DSV742_returresep_t cannot be reverted.\n";

        return false;
    }
    */
}
