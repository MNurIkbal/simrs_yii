<?php

use yii\db\Migration;

/**
 * Class m251014_080715_feature_seeder_eklaim
 */
class m251014_080715_feature_seeder_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO \"public\".\"lookup_m\" (\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES (2502, 'status_inacbg', 'INACBG Koreksi', 'INACBG Koreksi', NULL, NULL, NULL, '2025-10-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute("INSERT INTO \"public\".\"lookup_m\" (\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES (2503, 'status_inacbg', 'INACBG Grouping', 'INACBG Grouping', NULL, NULL, NULL, '2025-10-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute("INSERT INTO \"public\".\"lookup_m\" (\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES (2504, 'status_inacbg', 'INACBG Final', 'INACBG Final', NULL, NULL, NULL, '2025-10-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2501, 'status_idrg', 'Final Idrg', 'Final Idrg', NULL, NULL, NULL, '2025-10-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2500, 'status_idrg', 'Koreksi Idrg', 'Koreksi Idrg', NULL, NULL, NULL, '2025-10-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('cutoff_eklaim_idrg', 1, 'Konfigurasi untuk cut off tanggal eklaim', '2025-10-10', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251014_080715_feature_seeder_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251014_080715_feature_seeder_eklaim cannot be reverted.\n";

        return false;
    }
    */
}
