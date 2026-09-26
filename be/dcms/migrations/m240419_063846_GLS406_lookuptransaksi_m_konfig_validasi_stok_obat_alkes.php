<?php

use yii\db\Migration;

/**
 * Class m240419_063846_GLS406_lookuptransaksi_m_konfig_validasi_stok_obat_alkes
 */
class m240419_063846_GLS406_lookuptransaksi_m_konfig_validasi_stok_obat_alkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfig_validasi_stok_obat_alkes';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('konfig_validasi_stok_obat_alkes', 0, 'Untuk konfig validasi stok obat, true = tidak bisa order obat jika qty lebih besar dari stok', 'true', 'Validasi Stok Obat Alkes', NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240419_063846_GLS406_lookuptransaksi_m_konfig_validasi_stok_obat_alkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240419_063846_GLS406_lookuptransaksi_m_konfig_validasi_stok_obat_alkes cannot be reverted.\n";

        return false;
    }
    */
}
