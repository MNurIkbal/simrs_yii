<?php

use yii\db\Migration;

/**
 * Class m190423_062131_seed_shift_m
 */
class m190423_062131_seed_shift_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE shift_m RESTART IDENTITY;
            ');
        $this->execute('
            INSERT INTO public.shift_m(shift_id, shift_nama, shift_namalainnya, shift_jamawal, shift_jamakhir, shift_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, shift1_id) VALUES 
                (1, \'Pagi\', \'Pagi\', \'07:00:00\', \'12:00:00\', \'P\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL),
                (2, \'Sore\', \'Sore\', \'12:00:00\', \'17:00:00\', \'S\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL),
                (3, \'Malam\', \'Malam\', \'17:00:00\', \'23:30:00\', \'M\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL),
                (9, \'Subuh\', \'Subuh\', \'03:30:00\', \'06:00:00\', \'S\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL),
                (11, \'Dini hari\', \'Dini hari\', \'00:00:00\', \'01:00:00\', \'D\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL);
        ');
        $this->execute('
            SELECT setval(\'public.shift_m_shift_id_seq\', (SELECT COALESCE(MAX(shift_id) ,1)+1 FROM shift_m), false);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            TRUNCATE TABLE shift_m RESTART IDENTITY;
        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_062131_seed_shift_m cannot be reverted.\n";

        return false;
    }
    */
}
