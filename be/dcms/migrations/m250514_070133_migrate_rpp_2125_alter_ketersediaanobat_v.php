<?php

use yii\db\Migration;

/**
 * Class m250514_070133_migrate_rpp_2125_alter_ketersediaanobat_v
 */
class m250514_070133_migrate_rpp_2125_alter_ketersediaanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS public.ketersediaanobat_v;');
        $ketersediaanobat_v = file_get_contents(__DIR__ . '/definitions/ketersediaanobat_v.sql');
        $this->execute($ketersediaanobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_070133_migrate_rpp_2125_alter_ketersediaanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_070133_migrate_rpp_2125_alter_ketersediaanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
