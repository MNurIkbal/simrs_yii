<?php

use yii\db\Migration;

/**
 * Class m211231_104155_migrate_US2579_lookuptransaksi
 */
class m211231_104155_migrate_US2579_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'jabatan_kabag_keuangan';
        ");

        $this->execute("
            INSERT INTO \"public\".\"lookuptransaksi_m\"(\"kode_transaksi\", \"kode_id\", \"kode_fungsi\", \"additional_value\", \"kode_nama\", \"kode_singkatan\") VALUES ('jabatan_kabag_keuangan', 6, 'Jabatan Ketua Bagian Keuangan & Akuntansi', '54', NULL, NULL);

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211231_104155_migrate_US2579_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211231_104155_migrate_US2579_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
