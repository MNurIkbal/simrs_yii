<?php

use yii\db\Migration;

/**
 * Class m210223_020106_lookup_statuspenunjang_20210222
 */
class m210223_020106_lookup_statuspenunjang_20210222 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookup_m WHERE lookup_type =\'status_penunjang\' and lookup_id=692;');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(692, 'status_penunjang', 'RESCHEDULE', 'RESCHEDULE', NULL, NULL, '{\"instalasi_id\" : [12]}', CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210223_020106_lookup_statuspenunjang_20210222 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210223_020106_lookup_statuspenunjang_20210222 cannot be reverted.\n";

        return false;
    }
    */
}
