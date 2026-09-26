<?php

use yii\db\Migration;

/**
 * Class m211006_142937_migrate_US1610_MHKN_feedbackpasienranap
 */
class m211006_142937_migrate_US1610_MHKN_feedbackpasienranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id=1058;
        ");
        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1058, 'status_periksa', 'Batal Rujuk Rawat Inap', 'Batal Rawat Inap', NULL, NULL, NULL, '2021-09-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211006_142937_migrate_US1610_MHKN_feedbackpasienranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211006_142937_migrate_US1610_MHKN_feedbackpasienranap cannot be reverted.\n";

        return false;
    }
    */
}
