<?php

use yii\db\Migration;

/**
 * Class m211103_093437_migrate_seeder_tipe_obat
 */
class m211103_093437_migrate_seeder_tipe_obat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DELETE from lookup_m WHERE lookup_type=\'tipe_obat\'');

       $this->execute("
        INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1081, 'tipe_obat', 'GENERIK', 'GENERIK', NULL, '1', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1082, 'tipe_obat', 'ME TOO', 'ME TOO', NULL, '2', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1083, 'tipe_obat', 'ORIGINAL', 'ORIGINAL', NULL, '3', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211103_093437_migrate_seeder_tipe_obat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211103_093437_migrate_seeder_tipe_obat cannot be reverted.\n";

        return false;
    }
    */
}
