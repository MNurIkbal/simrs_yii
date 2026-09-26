<?php

use yii\db\Migration;

/**
 * Class m200415_060119_migrate_20200415
 */
class m200415_060119_migrate_20200415 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookup_m WHERE lookup_type=\'range_bulan\' ');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(703, 'range_bulan', '1 Bulan', '+1 month', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(704, 'range_bulan', '3 Bulan', '+3 month', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(705, 'range_bulan', '6 Bulan', '+6 month', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(706, 'range_bulan', '1 Tahun', '+1 year', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_060119_migrate_20200415 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_060119_migrate_20200415 cannot be reverted.\n";

        return false;
    }
    */
}
