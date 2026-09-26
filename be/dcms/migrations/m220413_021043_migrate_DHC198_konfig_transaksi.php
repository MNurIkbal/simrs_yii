<?php

use yii\db\Migration;

/**
 * Class m220413_021043_migrate_DHC198_konfig_transaksi
 */
class m220413_021043_migrate_DHC198_konfig_transaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m 
            WHERE kode_transaksi = \'golongan_tindakan_bedah\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'golongan_tindakan_bedah\', 0, \'Filter golongan\', \'false\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_021043_migrate_DHC198_konfig_transaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_021043_migrate_DHC198_konfig_transaksi cannot be reverted.\n";

        return false;
    }
    */
}
