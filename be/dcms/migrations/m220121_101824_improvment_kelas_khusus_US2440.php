<?php

use yii\db\Migration;

/**
 * Class m220121_101824_improvment_kelas_khusus_US2440
 */
class m220121_101824_improvment_kelas_khusus_US2440 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'kelas_khusus\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'kelas_khusus\', 0, \'Konfigurasi kelas khusus, ambil dari kelaspelayanan_m\', \'[]\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220121_101824_improvment_kelas_khusus_US2440 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_101824_improvment_kelas_khusus_US2440 cannot be reverted.\n";

        return false;
    }
    */
}
