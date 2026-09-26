<?php

use yii\db\Migration;

/**
 * Class m240723_105129_migrate_produksiobat_lookup
 */
class m240723_105129_migrate_produksiobat_lookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DELETE FROM penomoran_k WHERE penomoran_id in (2185,2183,2184,2186,2187,2188,2189);');
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2183, 'status_pemesananproduksi', 'Belum Verifikasi', 'Belum Verfifikasi', NULL, NULL, NULL, '2024-06-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2184, 'status_pemesananproduksi', 'Sudah Verifikasi', 'Sudah Verifikasi', NULL, NULL, NULL, '2024-06-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2185, 'status_pemesananproduksi', 'Batal Pemesanan', 'Batal Pemesanan', NULL, NULL, NULL, '2024-06-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2186, 'status_produksi', 'Produksi', 'Produksi', NULL, NULL, NULL, '2024-07-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2187, 'status_produksi', 'Define Material', 'Define Material', NULL, NULL, NULL, '2024-07-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2188, 'status_produksi', 'Batal Produksi', 'Batal Produksi', NULL, NULL, NULL, '2024-07-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2189, 'ket_hargaobat', 'Produksi Obat', 'Produksi Obat', NULL, NULL, NULL, '2024-07-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240723_105129_migrate_produksiobat_lookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240723_105129_migrate_produksiobat_lookup cannot be reverted.\n";

        return false;
    }
    */
}
