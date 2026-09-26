<?php

use yii\db\Migration;

/**
 * Class m230628_120401_migration_skm_181_tambah_konfig_saveantrol
 */
class m230628_120401_migration_skm_181_tambah_konfig_saveantrol extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m where kode_transaksi = 'skip_returnantrian' ");

        $this->execute("
        INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi)
        VALUES ('skip_returnantrian',0,'Konfigurasi Return Create Antrian JKN di Pendaftaran Rawat Jalan (aktif/tidak)')
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230628_120401_migration_skm_181_tambah_konfig_saveantrol cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230628_120401_migration_skm_181_tambah_konfig_saveantrol cannot be reverted.\n";

        return false;
    }
    */
}
