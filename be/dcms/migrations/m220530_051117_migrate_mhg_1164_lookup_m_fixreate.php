<?php

use yii\db\Migration;

/**
 * Class m220530_051117_migrate_mhg_1164_lookup_m_fixreate
 */
class m220530_051117_migrate_mhg_1164_lookup_m_fixreate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DELETE FROM lookup_m WHERE lookup_id = 1204 ;');
		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1204, 'metode_harga', 'FIX RATE', 'FIX RATE', 5, NULL, NULL, '2022-05-10 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute('DELETE FROM lookup_m WHERE lookup_id = 1205 ;');
 		$this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1205, 'metode_harga', 'LAST', 'LAST', 4, NULL, NULL, '2022-05-10 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220530_051117_migrate_mhg_1164_lookup_m_fixreate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220530_051117_migrate_mhg_1164_lookup_m_fixreate cannot be reverted.\n";

        return false;
    }
    */
}
