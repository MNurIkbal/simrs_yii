<?php

use yii\db\Migration;

/**
 * Class m210903_033636_migrate_seederlookup
 */
class m210903_033636_migrate_seederlookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookup_m WHERE lookup_type =\'status_purchaserequest\';');

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(712, 'status_purchaserequest', 'Belum PO', 'Belum PO', 2, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(713, 'status_purchaserequest', 'Sudah PO', 'Sudah PO', 1, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(719, 'status_purchaserequest', 'PO Sebagian', 'PO Sebagian', 3, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(720, 'status_purchaserequest', 'Batal PR', 'Batal PR', 4, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1056, 'status_purchaserequest', 'Approved', 'Approve', 5, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1057, 'status_purchaserequest', 'Belum Approved', 'Belum Approved', 6, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210903_033636_migrate_seederlookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210903_033636_migrate_seederlookup cannot be reverted.\n";

        return false;
    }
    */
}
