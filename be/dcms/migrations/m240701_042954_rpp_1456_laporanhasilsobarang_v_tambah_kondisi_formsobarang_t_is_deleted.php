<?php

use yii\db\Migration;

/**
 * Class m240701_042954_rpp_1456_laporanhasilsobarang_v_tambah_kondisi_formsobarang_t_is_deleted
 */
class m240701_042954_rpp_1456_laporanhasilsobarang_v_tambah_kondisi_formsobarang_t_is_deleted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanhasilsobarang_v");
        $laporanhasilsobarang_v = file_get_contents(__DIR__ . '/definitions/laporanhasilsobarang_v_kondisi_is_deleted.sql');
        $this->execute($laporanhasilsobarang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240701_042954_rpp_1456_laporanhasilsobarang_v_tambah_kondisi_formsobarang_t_is_deleted cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240701_042954_rpp_1456_laporanhasilsobarang_v_tambah_kondisi_formsobarang_t_is_deleted cannot be reverted.\n";

        return false;
    }
    */
}
