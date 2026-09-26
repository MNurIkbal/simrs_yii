<?php

use yii\db\Migration;

/**
 * Class m250306_065758_migrate_or10_create_f_getlaporansensusharianrajal
 */
class m250306_065758_migrate_or10_create_f_getlaporansensusharianrajal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.f_getlaporansensusharianrajal(date, date, int4)");
        $query = file_get_contents(__DIR__ . '/definitions/f_getlaporansensusharianrajal.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250306_065758_migrate_or10_create_f_getlaporansensusharianrajal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250306_065758_migrate_or10_create_f_getlaporansensusharianrajal cannot be reverted.\n";

        return false;
    }
    */
}
