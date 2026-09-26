<?php

use yii\db\Migration;

/**
 * Class m220427_034042_migrate_US1358_data_lookuptransaksi_m
 */
class m220427_034042_migrate_US1358_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'ruangan_odc\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'ruangan_odc\', 103, \'kode ruangan ODC (di define sama user)\', NULL, NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220427_034042_migrate_US1358_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_034042_migrate_US1358_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
