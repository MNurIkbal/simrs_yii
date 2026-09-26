<?php

use yii\db\Migration;

/**
 * Class m211125_021826_improvment_cron_pemulangan_pasien_rj_US2211
 */
class m211125_021826_improvment_cron_pemulangan_pasien_rj_US2211 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'selisih_waktu_pemulangan_pasien\';
        ');

        $this->execute('
            INSERT INTO "lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'selisih_waktu_pemulangan_pasien\', 1440, \'untuk mengatur selisih waktu pemulangan pasien rajal jika ingin diset harian, kode_id menyimpan waktu dalam satuan menit. 1 hari ke belakang = 1440 menit\', NULL, NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211125_021826_improvment_cron_pemulangan_pasien_rj_US2211 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211125_021826_improvment_cron_pemulangan_pasien_rj_US2211 cannot be reverted.\n";

        return false;
    }
    */
}
