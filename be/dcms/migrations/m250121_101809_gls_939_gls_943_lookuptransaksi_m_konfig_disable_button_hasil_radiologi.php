<?php

use yii\db\Migration;

/**
 * Class m250121_101809_gls_939_gls_943_lookuptransaksi_m_konfig_disable_button_hasil_radiologi
 */
class m250121_101809_gls_939_gls_943_lookuptransaksi_m_konfig_disable_button_hasil_radiologi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfig_disable_button_hasil_radiologi';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi)
        VALUES('konfig_disable_button_hasil_radiologi', 0, 'Konfigurasi untuk disable button view gambar hasil radiologi, jika pemeriksaan belum selesai / dibatalkan');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250121_101809_gls_939_gls_943_lookuptransaksi_m_konfig_disable_button_hasil_radiologi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250121_101809_gls_939_gls_943_lookuptransaksi_m_konfig_disable_button_hasil_radiologi cannot be reverted.\n";

        return false;
    }
    */
}
