<?php

use yii\db\Migration;

/**
 * Class m231201_110525_migrate_RPP453_lookuptransaksi_m
 */
class m231201_110525_migrate_RPP453_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfig_worklist_filter_ruangan';");
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfig_worklist_filter_urutan_periksa';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('konfig_worklist_filter_ruangan', 0, 'Untuk konfig filter ruangan pada worklist pasien', 'true', 'Filter Ruangan', NULL);");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('konfig_worklist_filter_urutan_periksa', 0, 'Untuk konfig urutan periksa pada worklist pasien', 'true', 'Urutan Periksa', NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231201_110525_migrate_RPP453_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231201_110525_migrate_RPP453_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
