<?php

use yii\db\Migration;

/**
 * Class m231130_083858_migrate_RPP930_lookuptransaksi_m
 */
class m231130_083858_migrate_RPP930_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='flow_taskid_alur_jam_pelayanan';");

        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('flow_taskid_alur_jam_pelayanan', 0, 'true = flow pengirim task sesuai dengan deskripsi task RPP-930 || false = default', 'false', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231130_083858_migrate_RPP930_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231130_083858_migrate_RPP930_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
