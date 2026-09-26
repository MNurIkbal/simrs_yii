<?php

use yii\db\Migration;

/**
 * Class m221027_062347_migrate_MHG4284_lookuptransaksi_m
 */
class m221027_062347_migrate_MHG4284_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'tindakan_harga_bmhp\';
        ');


        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m" ("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'tindakan_harga_bmhp\', 0, \'Untuk menampilkan harga tindakan pada BMHP\', \'false\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221027_062347_migrate_MHG4284_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221027_062347_migrate_MHG4284_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
