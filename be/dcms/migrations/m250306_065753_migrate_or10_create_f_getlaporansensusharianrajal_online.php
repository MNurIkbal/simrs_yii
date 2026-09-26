<?php

use yii\db\Migration;

/**
 * Class m250306_065753_migrate_or10_create_f_getlaporansensusharianrajal_online
 */
class m250306_065753_migrate_or10_create_f_getlaporansensusharianrajal_online extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.f_getlaporansensusharianrajal_online(date, date)");
        $query = file_get_contents(__DIR__ . '/definitions/f_getlaporansensusharianrajal_online.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250306_065753_migrate_or10_create_f_getlaporansensusharianrajal_online cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250306_065753_migrate_or10_create_f_getlaporansensusharianrajal_online cannot be reverted.\n";

        return false;
    }
    */
}
