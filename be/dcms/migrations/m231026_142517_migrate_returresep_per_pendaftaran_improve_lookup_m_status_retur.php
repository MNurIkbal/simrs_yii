<?php

use yii\db\Migration;

/**
 * Class m231026_142517_migrate_returresep_per_pendaftaran_improve_lookup_m_status_retur
 */
class m231026_142517_migrate_returresep_per_pendaftaran_improve_lookup_m_status_retur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DELETE from lookup_m WHERE lookup_id in (2118,2119,2120);');
		
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2118, 'status_retur', 'Belum Verifikasi', NULL, NULL, NULL, NULL, '2023-09-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2119, 'status_retur', 'Verifikasi', NULL, NULL, NULL, NULL, '2023-09-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (2120, 'status_retur', 'Batal Retur', NULL, NULL, NULL, NULL, '2023-09-20 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_142517_migrate_returresep_per_pendaftaran_improve_lookup_m_status_retur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_142517_migrate_returresep_per_pendaftaran_improve_lookup_m_status_retur cannot be reverted.\n";

        return false;
    }
    */
}
