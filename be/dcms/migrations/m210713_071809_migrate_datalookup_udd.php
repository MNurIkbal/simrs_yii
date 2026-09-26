<?php

use yii\db\Migration;

/**
 * Class m210713_071809_migrate_datalookup_udd
 */
class m210713_071809_migrate_datalookup_udd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type ilike '%status_udd%';");

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1029, 'status_udd', 'Belum Proses', 'Belum Proses', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1030, 'status_udd', 'Proses', 'Proses', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1031, 'status_udd', 'Dikirim', 'Dikirim', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1032, 'status_udd', 'Diterima', 'Diterima', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

        $this->execute('TRUNCATE TABLE waktupemberian_m RESTART IDENTITY;');

        $this->execute("
            INSERT INTO public.waktupemberian_m(waktupemberian_id, waktu_pemberian, jam_mulai, jam_akhir, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1, 'Pagi', '05:00:00', '10:00:00', NULL, '2021-06-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(2, 'Siang', '10:01:00', '15:00:00', NULL, '2021-06-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(3, 'Sore', '15:01:00', '18:00:00', NULL, '2021-06-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(4, 'Malam', '18:01:00', '24:00:00', NULL, '2021-06-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute("SELECT setval('\"public\".\"waktupemberian_m_waktupemberian_id_seq\"', 4, TRUE);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210713_071809_migrate_datalookup_udd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210713_071809_migrate_datalookup_udd cannot be reverted.\n";

        return false;
    }
    */
}
