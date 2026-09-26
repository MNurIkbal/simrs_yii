<?php

use yii\db\Migration;

/**
 * Class m250514_070121_migrate_rpp_2125_alter_fgetketersediaanobat_fn
 */
class m250514_070121_migrate_rpp_2125_alter_fgetketersediaanobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.fgetketersediaanobat;');
        $fgetketersediaanobat = file_get_contents(__DIR__ . '/definitions/fgetketersediaanobat.fn.sql');
        $this->execute($fgetketersediaanobat);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_070121_migrate_rpp_2125_alter_fgetketersediaanobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_070121_migrate_rpp_2125_alter_fgetketersediaanobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
