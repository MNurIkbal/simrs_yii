<?php

use yii\db\Migration;

/**
 * Class m241217_041442_RPP461_laporanpenjualanobatalkes_v
 */
class m241217_041442_RPP461_laporanpenjualanobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenjualanobatalkes_v");
        $laporanpenjualanobatalkes_v = file_get_contents(__DIR__ . '/definitions/laporanpenjualanobatalkes_v.sql');
        $this->execute($laporanpenjualanobatalkes_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241217_041442_RPP461_laporanpenjualanobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241217_041442_RPP461_laporanpenjualanobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
