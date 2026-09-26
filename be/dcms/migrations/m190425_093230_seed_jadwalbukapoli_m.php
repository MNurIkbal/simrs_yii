<?php

use yii\db\Migration;

/**
 * Class m190425_093230_seed_jadwalbukapoli_m
 */
class m190425_093230_seed_jadwalbukapoli_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM jadwalbukapoli_m WHERE ruangan_id IN (24, 18);
        ');

        $this->execute('
            INSERT INTO public.jadwalbukapoli_m(ruangan_id, waktu_pelayanan, jam_mulai, jam_tutup, hari, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, shift_id, maxantrian_poli, kuota_online) VALUES 
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 75, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 76, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 77, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 78, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 79, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 80, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (24, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 81, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 75, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 76, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 77, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 78, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 79, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 80, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 81, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 100, 100);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM jadwalbukapoli_m WHERE ruangan_id IN (24, 18);
        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190425_093230_seed_jadwalbukapoli_m cannot be reverted.\n";

        return false;
    }
    */
}
