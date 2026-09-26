<?php

use yii\db\Migration;

/**
 * Class m240423_083329_migrate_DSV_1234_unduh_dokumen_lookup_m
 */
class m240423_083329_migrate_DSV_1234_unduh_dokumen_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (2172);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (2172, 'unduh_dokumen', 'Belum', NULL, NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240423_083329_migrate_DSV_1234_unduh_dokumen_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240423_083329_migrate_DSV_1234_unduh_dokumen_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
