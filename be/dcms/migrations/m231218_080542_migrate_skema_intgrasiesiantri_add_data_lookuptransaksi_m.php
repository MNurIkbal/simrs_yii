<?php

use yii\db\Migration;

/**
 * Class m231218_080542_migrate_skema_intgrasiesiantri_add_data_lookuptransaksi_m
 */
class m231218_080542_migrate_skema_intgrasiesiantri_add_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi = 'konfig_antrian_using_jenisantriandetail'");
		
		$this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfig_antrian_using_jenisantriandetail', 0, 'Konfig untuk Master Display Antrian - auto mapping jenisantriandetail', '', NULL, NULL);");
		
		$this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi = 'konfig_worklist_filter_ruangan'");
		
		$this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfig_worklist_filter_ruangan', 0, 'Untuk konfig filter ruangan pada worklist pasien', 'true', 'Filter Ruangan', NULL);");
		
		$this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi = 'konfig_worklist_filter_urutan_periksa'");
		
		$this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfig_worklist_filter_urutan_periksa', 0, 'Untuk konfig urutan periksa pada worklist pasien', 'true', 'Urutan Periksa', NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231218_080542_migrate_skema_intgrasiesiantri_add_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231218_080542_migrate_skema_intgrasiesiantri_add_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
