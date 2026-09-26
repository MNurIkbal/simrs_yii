<?php

use yii\db\Migration;

/**
 * Class m230808_151310_glbj_164_lookuptransaksi_kelompokpegawai_id_nakes
 */
class m230808_151310_glbj_164_lookuptransaksi_kelompokpegawai_id_nakes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'kelompokpegawai_id_nakes';");

        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value) VALUES
        ('kelompokpegawai_id_nakes',0,'List Kelompok Pegawai Tenaga Kesehatan','[2]');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230808_151310_glbj_164_lookuptransaksi_kelompokpegawai_id_nakes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_151310_glbj_164_lookuptransaksi_kelompokpegawai_id_nakes cannot be reverted.\n";

        return false;
    }
    */
}
