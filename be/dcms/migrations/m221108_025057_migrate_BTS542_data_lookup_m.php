<?php

use yii\db\Migration;

/**
 * Class m221108_025057_migrate_BTS542_data_lookup_m
 */
class m221108_025057_migrate_BTS542_data_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM public.lookup_m WHERE lookup_id = 1305;');
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(1305, 'satusehat', 'organization_id', '10000132', NULL, NULL, NULL, '2022-11-03 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221108_025057_migrate_BTS542_data_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221108_025057_migrate_BTS542_data_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
