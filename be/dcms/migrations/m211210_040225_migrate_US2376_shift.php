<?php

use yii\db\Migration;

/**
 * Class m211210_040225_migrate_US2376_shift
 */
class m211210_040225_migrate_US2376_shift extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" TRUNCATE TABLE shift_m RESTART IDENTITY;
        ");

        $this->execute("INSERT INTO public.shift_m(shift_id, shift_nama, shift_namalainnya, shift_jamawal, shift_jamakhir, shift_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, shift1_id) VALUES 
            (1, 'Pagi', 'Pagi', '00:00:00', '12:00:00', 'P', NULL, CURRENT_DATE, NULL, 3, CURRENT_DATE, 1, 'f', 't', NULL, NULL, NULL),
            (2, 'Sore', 'Sore', '12:00:00', '17:00:00', 'S', NULL, CURRENT_DATE, NULL, 2, CURRENT_DATE, 1, 'f', 't', NULL, NULL, NULL),
            (3, 'Malam', 'Malam', '17:00:00', '23:59:59', 'M', NULL, CURRENT_DATE, NULL, 2, CURRENT_DATE, 1, 'f', 't', NULL, NULL, NULL);
        ");

        $this->execute("SELECT setval('public.shift_m_shift_id_seq', 3, TRUE);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211210_040225_migrate_US2376_shift cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211210_040225_migrate_US2376_shift cannot be reverted.\n";

        return false;
    }
    */
}
