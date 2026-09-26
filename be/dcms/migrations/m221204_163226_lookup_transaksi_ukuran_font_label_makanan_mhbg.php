<?php

use yii\db\Migration;

/**
 * Class m221204_163226_lookup_transaksi_ukuran_font_label_makanan_mhbg
 */
class m221204_163226_lookup_transaksi_ukuran_font_label_makanan_mhbg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'ukuran_font_label_makanan_mhbg';");
        $this->execute("INSERT INTO \"public\".\"lookuptransaksi_m\" (\"kode_transaksi\", \"kode_id\", \"kode_fungsi\", \"additional_value\", \"kode_nama\", \"kode_singkatan\") VALUES ('ukuran_font_label_makanan_mhbg', 0, 'konfigurasi untuk mengatur font body pada cetakan', '6', NULL, NULL)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221204_163226_lookup_transaksi_ukuran_font_label_makanan_mhbg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221204_163226_lookup_transaksi_ukuran_font_label_makanan_mhbg cannot be reverted.\n";

        return false;
    }
    */
}
