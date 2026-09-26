<?php

use yii\db\Migration;

/**
 * Class m251027_085038_migrate_DSV_2117_insert_sumberttv_sbar_lookup_m
 */
class m251027_085038_migrate_DSV_2117_insert_sumberttv_sbar_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Check if the record already exists
        $exists = $this->db->createCommand("SELECT 1 FROM public.lookup_m WHERE lookup_id = 2266")->queryOne();
        
        if (!$exists) {
            $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2266, 'sumber_ttv', 'TTV SBAR', 'TTV SBAR', NULL, NULL, NULL, '2025-10-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        } else {
            echo "Record with lookup_id=2266 already exists, skipping insertion.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251027_085038_migrate_DSV_2117_insert_sumberttv_sbar_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251027_085038_migrate_DSV_2117_insert_sumberttv_sbar_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
