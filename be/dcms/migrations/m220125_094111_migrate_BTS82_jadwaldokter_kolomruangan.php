<?php

use yii\db\Migration;

/**
 * Class m220125_094111_migrate_BTS82_jadwaldokter_kolomruangan
 */
class m220125_094111_migrate_BTS82_jadwaldokter_kolomruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'ruangan_telekonsultasi\', 994, \'kode ruangan telekonsultasi\', NULL, NULL, NULL);
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220125_094111_migrate_BTS82_jadwaldokter_kolomruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220125_094111_migrate_BTS82_jadwaldokter_kolomruangan cannot be reverted.\n";

        return false;
    }
    */
}
