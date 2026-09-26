<?php

use yii\db\Migration;

/**
 * Class m220314_045246_migrate_ORDH21_lookup_m
 */
class m230801_150300_migrate_RPP_380_lookuptransaksi_m  extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'konfig_worklist_semua_pasien_hide_dokter';");
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('konfig_worklist_semua_pasien_hide_dokter', 0, 'true = Dokter tidak bisa melihat tab worklist pasien, false = Dokter bisa melihat worklist pasien', false);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230801_150300_migrate_RPP-380_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_045246_migrate_ORDH21_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
