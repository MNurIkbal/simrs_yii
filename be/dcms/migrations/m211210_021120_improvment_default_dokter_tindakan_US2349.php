<?php

use yii\db\Migration;

/**
 * Class m211210_021120_improvment_default_dokter_tindakan_US2349
 */
class m211210_021120_improvment_default_dokter_tindakan_US2349 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'use_default_dpjp\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'use_default_dpjp\', 0, \'konfigurasi order tindakan bmhp mengambil dpjp (boolean)\', NULL, NULL, NULL);
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211210_021120_improvment_default_dokter_tindakan_US2349 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211210_021120_improvment_default_dokter_tindakan_US2349 cannot be reverted.\n";

        return false;
    }
    */
}
