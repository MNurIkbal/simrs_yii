<?php

use yii\db\Migration;

/**
 * Class m190507_094312_seed_jadwalbukapoli_m_2
 */
class m190507_094312_seed_jadwalbukapoli_m_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM jadwalbukapoli_m WHERE ruangan_id IN (3,18,52) AND hari IN (75,76,77,78,79,80,81);
        ');
        
        $this->execute('
             INSERT INTO public.jadwalbukapoli_m(ruangan_id, waktu_pelayanan, jam_mulai, jam_tutup, hari, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, shift_id, maxantrian_poli, kuota_online) VALUES 
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 75, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 76, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 77, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 78, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 79, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 80, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (3, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 81, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 75, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 76, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 77, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 78, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 79, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 80, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (18, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 81, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 75, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 76, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 77, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 78, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 79, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 80, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0),
            (52, \'00:00 - 23:59\', \'00:00:00\', \'23:59:00\', 81, NULL, \'2019-05-07 14:49:57\', 1, 0, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, 0, 0);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190507_094312_seed_jadwalbukapoli_m_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_094312_seed_jadwalbukapoli_m_2 cannot be reverted.\n";

        return false;
    }
    */
}
