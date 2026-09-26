<?php

use yii\db\Migration;

/**
 * Class m201222_095900_migrate_20201222_datalookup
 */
class m201222_095900_migrate_20201222_datalookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookup_m WHERE lookup_type=\'status_penerimaan_po\';');

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(572, 'status_penerimaan_po', 'Belum Diterima', 'Belum Diterima', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(573, 'status_penerimaan_po', 'Belum Semua Diterima', 'Belum Semua Diterima', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(574, 'status_penerimaan_po', 'Sudah Semua Diterima', 'Sudah Semua Diterima', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(575, 'status_penerimaan_po', 'Dibatalkan', 'Dibatalkan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(580, 'status_penerimaan_po', 'Closing Supplier', 'Closing Supplier', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(686, 'status_penerimaan_po', 'Expired', 'Expired', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201222_095900_migrate_20201222_datalookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201222_095900_migrate_20201222_datalookup cannot be reverted.\n";

        return false;
    }
    */
}
