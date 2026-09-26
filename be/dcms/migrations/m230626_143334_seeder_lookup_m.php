<?php

use yii\db\Migration;

/**
 * Class m230626_143334_seeder_lookup_m
 */
class m230626_143334_seeder_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_name = 'coder_nik';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_name = 'default_tarif';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_name = 'bpjs_dummy';");

        $this->execute("
            INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
            (1322, 'bpjs', 'coder_nik', '123123123123', NULL, NULL, NULL, '2023-03-31 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1900, 'bpjs', 'default_tarif', 'AP', NULL, NULL, NULL, '2023-03-31 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1938, 'bpjs', 'bpjs_dummy', '0069R0550523V11111', 1, NULL, NULL, '2023-06-26 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230626_143334_seeder_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230626_143334_seeder_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
