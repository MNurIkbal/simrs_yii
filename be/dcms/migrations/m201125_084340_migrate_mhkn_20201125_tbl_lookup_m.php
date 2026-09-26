<?php

use yii\db\Migration;

/**
 * Class m201125_084340_migrate_mhkn_20201125_tbl_lookup_m
 */
class m201125_084340_migrate_mhkn_20201125_tbl_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE from lookup_m where lookup_type='jenis_laporan';
            ");
        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(729, 'jenis_laporan', 'Soap Dokter', 'Soap Dokter', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(730, 'jenis_laporan', 'Resume Dokter', 'Resume Dokter', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);

            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_084340_migrate_mhkn_20201125_tbl_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_084340_migrate_mhkn_20201125_tbl_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
