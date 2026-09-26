<?php

use yii\db\Migration;

/**
 * Class m220202_025838_improvment_konfig_label_makanan_US2648
 */
class m220202_025838_improvment_konfig_label_makanan_US2648 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'ukuran_font_label_makanan\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'ukuran_font_label_makanan\', 0, \'konfigurasi untuk mengatur font dan margin-top pada cetakan label permintaan makan pasien dengan format dari kiri ke kanan [header_font,body_font,margin_top_1,margin_top_2]\', \'[15,14,-18,-3]\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_025838_improvment_konfig_label_makanan_US2648 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_025838_improvment_konfig_label_makanan_US2648 cannot be reverted.\n";

        return false;
    }
    */
}
