<?php

use yii\db\Migration;

/**
 * Class m220330_050215_migrate_DHC198_data_lookuptransaksi
 */
class m220330_050215_migrate_DHC198_data_lookuptransaksi extends Migration
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
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'golongan_tindakan_bedah\', 0, \'Filter golongan\', \'true\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220330_050215_migrate_DHC198_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_050215_migrate_DHC198_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
