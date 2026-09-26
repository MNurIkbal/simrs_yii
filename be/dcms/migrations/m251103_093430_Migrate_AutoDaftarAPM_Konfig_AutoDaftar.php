<?php

use yii\db\Migration;

/**
 * Class m251103_093430_Migrate_AutoDaftarAPM_Konfig_AutoDaftar
 */
class m251103_093430_Migrate_AutoDaftarAPM_Konfig_AutoDaftar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m lm where lm.kode_transaksi = 'auto_daftar_apm'");
        
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value,kode_nama,kode_singkatan) VALUES
        	 ('auto_daftar_apm',0,'kode_id = 0 adalah false, APM hanya untuk ambil antrian reservasi saja. kode_id = , adalah true APM bisa melakukan autodaftar',NULL,NULL,NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251103_093430_Migrate_AutoDaftarAPM_Konfig_AutoDaftar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251103_093430_Migrate_AutoDaftarAPM_Konfig_AutoDaftar cannot be reverted.\n";

        return false;
    }
    */
}
