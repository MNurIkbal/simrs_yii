<?php

use yii\db\Migration;

/**
 * Class m220525_080223_migrate_MHG1538_data_lookuptransaksi_m
 */
class m220525_080223_migrate_MHG1538_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'tindakan_spesialis\';
        ');
        
        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m" ("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'tindakan_spesialis\', 0, \'Konfig tindakan spesialis\', \'true\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220525_080223_migrate_MHG1538_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220525_080223_migrate_MHG1538_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
