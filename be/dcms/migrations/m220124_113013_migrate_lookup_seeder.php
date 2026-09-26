<?php

use yii\db\Migration;

/**
 * Class m220124_113013_migrate_lookup_seeder
 */
class m220124_113013_migrate_lookup_seeder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM lookup_m WHERE lookup_type=\'nilai_pembulatan\';');

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(714, 'nilai_pembulatan', '10', '10', 2, NULL, NULL, '2020-08-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(715, 'nilai_pembulatan', '50', '50', 3, NULL, NULL, '2020-08-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(716, 'nilai_pembulatan', '100', '100', 4, NULL, NULL, '2020-08-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(717, 'nilai_pembulatan', '500', '500', 5, NULL, NULL, '2020-08-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(718, 'nilai_pembulatan', '1000', '1000', 6, NULL, NULL, '2020-08-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1133, 'nilai_pembulatan', '1', '1', 1, NULL, NULL, '2020-08-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220124_113013_migrate_lookup_seeder cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_113013_migrate_lookup_seeder cannot be reverted.\n";

        return false;
    }
    */
}
