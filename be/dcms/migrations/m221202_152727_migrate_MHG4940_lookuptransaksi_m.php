<?php

use yii\db\Migration;

/**
 * Class m221202_152727_migrate_MHG4940_lookuptransaksi_m
 */
class m221202_152727_migrate_MHG4940_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('depo_ruangan_bmhp', 0, 'Konfigurasi untuk mengaktifkan depo bmhp memilih ke ruangannya masing-masing
        ', 'false', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221202_152727_migrate_MHG4940_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221202_152727_migrate_MHG4940_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
