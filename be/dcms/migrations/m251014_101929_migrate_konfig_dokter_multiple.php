<?php

use yii\db\Migration;

/**
 * Class m251014_101929_migrate_konfig_dokter_multiple
 */
class m251014_101929_migrate_konfig_dokter_multiple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi='eklaim_dokter_multiple';");

        $this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('eklaim_dokter_multiple', 0, 'true = dokter bisa freetext dan lebih dari 1, false = dokter hanya bisa 1', 'false', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251014_101929_migrate_konfig_dokter_multiple cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251014_101929_migrate_konfig_dokter_multiple cannot be reverted.\n";

        return false;
    }
    */
}
