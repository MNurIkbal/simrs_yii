<?php

use yii\db\Migration;

/**
 * Class m231208_161912_migration_pcp_30_add_value_type_jenis_konsul_to_lookup_m
 */
class m231208_161912_migration_pcp_30_add_value_type_jenis_konsul_to_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {        
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 1437;");       
        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(1437, 'jenis_konsul', 'Ahli Rawat & Rawat Bersama', 'Ahli Rawat & Rawat Bersama', NULL, NULL, NULL, '2023-12-08 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231208_161912_migration_pcp_30_add_value_type_jenis_konsul_to_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231208_161912_migration_pcp_30_add_value_type_jenis_konsul_to_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
