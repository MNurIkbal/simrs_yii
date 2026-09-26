<?php

use yii\db\Migration;

/**
 * Class m221125_132358_migrate_MHG4226_data_lookuptransaksi
 */
class m221125_132358_migrate_MHG4226_data_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'keramat_spri\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m" ("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'keramat_spri\', 0, \'Konfigurasi cara pulan rujuk rawat inap SPRI untuk rumah sakit Keramat\', \'true\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221125_132358_migrate_MHG4226_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221125_132358_migrate_MHG4226_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
