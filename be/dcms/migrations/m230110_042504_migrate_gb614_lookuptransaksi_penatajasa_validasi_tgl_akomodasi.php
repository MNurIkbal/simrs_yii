<?php

use yii\db\Migration;

/**
 * Class m230110_042504_migrate_gb614_lookuptransaksi_penatajasa_validasi_tgl_akomodasi
 */
class m230110_042504_migrate_gb614_lookuptransaksi_penatajasa_validasi_tgl_akomodasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM lookuptransaksi_m WHERE kode_transaksi=\'penatajasa_validasi_tgl_akomodasi\';');
        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('penatajasa_validasi_tgl_akomodasi', 0, 'Konfigurasi validasi akomodasi bisa input tanggal yang sama', 'true');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230110_042504_migrate_gb614_lookuptransaksi_penatajasa_validasi_tgl_akomodasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230110_042504_migrate_gb614_lookuptransaksi_penatajasa_validasi_tgl_akomodasi cannot be reverted.\n";

        return false;
    }
    */
}
