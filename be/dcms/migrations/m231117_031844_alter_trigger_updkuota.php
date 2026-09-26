<?php

use yii\db\Migration;

/**
 * Class m231117_031844_alter_trigger_updkuota
 */
class m231117_031844_alter_trigger_updkuota extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $upd_stokkuotadokter_from_antrian_try = file_get_contents(__DIR__ . '/definitions/upd_stokkuotadokter_from_antrian_try.fn.sql');
        $this->execute($upd_stokkuotadokter_from_antrian_try);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_031844_alter_trigger_updkuota cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_031844_alter_trigger_updkuota cannot be reverted.\n";

        return false;
    }
    */
}
