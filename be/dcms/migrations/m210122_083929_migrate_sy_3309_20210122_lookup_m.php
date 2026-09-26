<?php

use yii\db\Migration;

/**
 * Class m210122_083929_migrate_sy_3309_20210122_lookup_m
 */
class m210122_083929_migrate_sy_3309_20210122_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_type='pengantar_sy';
        ");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (992, 'pengantar_sy', 'Lainnya', 'Lainnya', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (991, 'pengantar_sy', 'Ortu/Wali', 'Ortu/Wali', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (990, 'pengantar_sy', 'Pasien', 'Pasien', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210122_083929_migrate_sy_3309_20210122_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210122_083929_migrate_sy_3309_20210122_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
