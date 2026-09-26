<?php

use yii\db\Migration;

/**
 * Class m240813_073924_gls_797_lookuptransaksi_m_add_gudang_pemusnahan
 */
class m240813_073924_gls_797_lookuptransaksi_m_add_gudang_pemusnahan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='gudang_pemusnahan';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('gudang_pemusnahan', 363, 'Ruangan gudang pemusnahan', '363', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240813_073924_gls_797_lookuptransaksi_m_add_gudang_pemusnahan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240813_073924_gls_797_lookuptransaksi_m_add_gudang_pemusnahan cannot be reverted.\n";

        return false;
    }
    */
}
