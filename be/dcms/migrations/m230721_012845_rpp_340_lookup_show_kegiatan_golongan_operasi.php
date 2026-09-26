<?php

use yii\db\Migration;

/**
 * Class m230721_012845_rpp_340_lookup_show_kegiatan_golongan_operasi
 */
class m230721_012845_rpp_340_lookup_show_kegiatan_golongan_operasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("DELETE FROM lookuptransaksi_m where kode_transaksi = 'show_kegiatan_golongan_operasi' ");

        $this->execute("
        INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi)
        VALUES ('show_kegiatan_golongan_operasi',1,'Konfigurasi untuk memunculkan atau menyembunyikan kolom kegiatan & golongan operasi di halaman Intra Operasi')
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230721_012845_rpp_340_lookup_show_kegiatan_golongan_operasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230721_012845_rpp_340_lookup_show_kegiatan_golongan_operasi cannot be reverted.\n";

        return false;
    }
    */
}
