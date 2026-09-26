<?php

use yii\db\Migration;

/**
 * Class m240619_063830_migrate_app_diskon_data_lookuptransaksi_m
 */
class m240619_063830_migrate_app_diskon_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = 'otoritas_penjamin_kasir';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m
            (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
            VALUES('otoritas_penjamin_kasir', 1326, 'Konfigurasi otoritas penjamin kasir', 'false', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240619_063830_migrate_app_diskon_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_063830_migrate_app_diskon_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
