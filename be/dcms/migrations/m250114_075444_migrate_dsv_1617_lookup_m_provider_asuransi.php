<?php

use yii\db\Migration;

/**
 * Class m250114_075444_migrate_dsv_1617_lookup_m_provider_asuransi
 */
class m250114_075444_migrate_dsv_1617_lookup_m_provider_asuransi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
                1326,
                1327
            );
        ');

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(1326, 'provider_asuransi', 'APLN', NULL, NULL, NULL, NULL, '2024-09-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);"
        );

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(1327, 'provider_asuransi', 'MCARE', NULL, NULL, NULL, NULL, '2024-09-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250114_075444_migrate_dsv_1617_lookup_m_provider_asuransi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250114_075444_migrate_dsv_1617_lookup_m_provider_asuransi cannot be reverted.\n";

        return false;
    }
    */
}
