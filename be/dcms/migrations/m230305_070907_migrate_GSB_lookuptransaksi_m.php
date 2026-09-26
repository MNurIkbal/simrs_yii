<?php

use yii\db\Migration;

/**
 * Class m230305_070907_migrate_GSB_lookuptransaksi_m
 */
class m230305_070907_migrate_GSB_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m   
            WHERE lookuptransaksi_m.kode_transaksi::text = \'set_kamar_sensus\'::text;
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m" ("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'set_kamar_sensus\', 0, \'Filter ruangan Bayi Sehat pada sensus harian ranap\', \'9999\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230305_070907_migrate_GSB_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_070907_migrate_GSB_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
