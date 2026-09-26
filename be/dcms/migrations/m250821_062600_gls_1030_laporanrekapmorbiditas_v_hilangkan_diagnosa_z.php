<?php

use yii\db\Migration;

/**
 * Class m250821_062600_gls_1030_laporanrekapmorbiditas_v_hilangkan_diagnosa_z
 */
class m250821_062600_gls_1030_laporanrekapmorbiditas_v_hilangkan_diagnosa_z extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanrekapmorbiditas_v");
        $laporanrekapmorbiditas_v = file_get_contents(__DIR__ . '/definitions/laporanrekapmorbiditas_v.sql');
        $this->execute($laporanrekapmorbiditas_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250821_062600_gls_1030_laporanrekapmorbiditas_v_hilangkan_diagnosa_z cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250821_062600_gls_1030_laporanrekapmorbiditas_v_hilangkan_diagnosa_z cannot be reverted.\n";

        return false;
    }
    */
}
