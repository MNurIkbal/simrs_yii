<?php

use yii\db\Migration;

/**
 * Class m230830_042108_migrate_GLBJ209_data_lookuptransaksi_m
 */
class m230830_042108_migrate_GLBJ209_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'konfig_antrian_prefix_dokter\';
        '); 

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m" ("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'konfig_antrian_prefix_dokter\', 0, \'Konfigurasi untuk prefix dokter pada antrian\', \'false\', NULL, NULL);
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_042108_migrate_GLBJ209_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_042108_migrate_GLBJ209_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
