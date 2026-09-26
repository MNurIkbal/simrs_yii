<?php

use yii\db\Migration;

/**
 * Class m220609_104113_migrate_VCS196_lookuptransaksi_m
 */
class m220609_104113_migrate_VCS196_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'kelompok_tindakan_id';");
        $this->execute("INSERT INTO \"public\".\"lookuptransaksi_m\" (\"kode_transaksi\", \"kode_id\", \"kode_fungsi\", \"additional_value\", \"kode_nama\", \"kode_singkatan\") VALUES ('kelompok_tindakan_id', 201, 'Kelompok Tindakan Id', '[{\"operand\":\"IN\",\"column\":\"kelompoktindakan_id\",\"value\":[17]}]', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220609_104113_migrate_VCS196_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220609_104113_migrate_VCS196_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
