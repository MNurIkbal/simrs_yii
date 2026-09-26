<?php

use yii\db\Migration;

/**
 * Class m250911_064849_rpp_2233_laporanpersalinan_v_lookupkeperawatan_m
 */
class m250911_064849_rpp_2233_laporanpersalinan_v_lookupkeperawatan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpersalinan_v");
        $laporanpersalinan_v = file_get_contents(__DIR__ . '/definitions/laporanpersalinan_v.sql');
        $this->execute($laporanpersalinan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250911_064849_rpp_2233_laporanpersalinan_v_lookupkeperawatan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250911_064849_rpp_2233_laporanpersalinan_v_lookupkeperawatan_m cannot be reverted.\n";

        return false;
    }
    */
}
