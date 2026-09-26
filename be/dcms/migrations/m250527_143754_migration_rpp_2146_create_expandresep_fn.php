<?php

use yii\db\Migration;

/**
 * Class m250527_143754_migration_rpp_2146_create_expandresep_fn
 */
class m250527_143754_migration_rpp_2146_create_expandresep_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.expandresep_fn;');
        $expandresep_fn = file_get_contents(__DIR__ . '/definitions/expandresep_fn.sql');
        $this->execute($expandresep_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250527_143754_migration_rpp_2146_create_expandresep_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250527_143754_migration_rpp_2146_create_expandresep_fn cannot be reverted.\n";

        return false;
    }
    */
}
