<?php

use yii\db\Migration;

/**
 * Class m250123_080217_migrate_dsv_1632_lookuptransaksi_konfig_hide_pasien
 */
class m250123_080217_migrate_dsv_1632_lookuptransaksi_konfig_hide_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfig_cron_hide_pasien_meninggal';");
        $this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('konfig_cron_hide_pasien_meninggal', 0, 'Konfigurasi Untuk Hide Pasien Meninggal', NULL, NULL, NULL);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250123_080217_migrate_dsv_1632_lookuptransaksi_konfig_hide_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250123_080217_migrate_dsv_1632_lookuptransaksi_konfig_hide_pasien cannot be reverted.\n";

        return false;
    }
    */
}
