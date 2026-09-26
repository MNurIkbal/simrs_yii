<?php

use yii\db\Migration;

/**
 * Class m250306_065746_migrate_or10_create_f_getlaporansensusharianrajal_langsung
 */
class m250306_065746_migrate_or10_create_f_getlaporansensusharianrajal_langsung extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.f_getlaporansensusharianrajal_langsung(date, date)");
        $query = file_get_contents(__DIR__ . '/definitions/f_getlaporansensusharianrajal_langsung.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250306_065746_migrate_or10_create_f_getlaporansensusharianrajal_langsung cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250306_065746_migrate_or10_create_f_getlaporansensusharianrajal_langsung cannot be reverted.\n";

        return false;
    }
    */
}
