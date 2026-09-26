<?php

use yii\db\Migration;

/**
 * Class m211213_112141_migrate_lookup_transaksi_konfirmasiunit
 */
class m211213_112141_migrate_lookup_transaksi_konfirmasiunit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookuptransaksi_m WHERE kode_transaksi=\'konfirm_instalasi\';');

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfirm_instalasi', 0, 'rd,ri,lab,rad,farmasi, ibs', '[2,3,4,5,6,12]', NULL, NULL);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211213_112141_migrate_lookup_transaksi_konfirmasiunit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211213_112141_migrate_lookup_transaksi_konfirmasiunit cannot be reverted.\n";

        return false;
    }
    */
}
