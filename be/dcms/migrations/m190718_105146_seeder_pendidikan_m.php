<?php

use yii\db\Migration;

/**
 * Class m190718_105146_seeder_pendidikan_m
 */
class m190718_105146_seeder_pendidikan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute('
          TRUNCATE TABLE pendidikan_m RESTART IDENTITY;
        ');


            $this->execute("
          INSERT INTO \"public\".\"pendidikan_m\"(\"pendidikan_id\", \"indexing_id\", \"pendidikan_urutan\", \"pendidikan_nama\", \"pendidikan_namalainnya\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\", \"pendidikan_kode\") VALUES 
(1, 1, 1, 'Belum Sekolah', 'Belum Sekolah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '1'),
(2, 1, 2, 'SD/ Sederajat', 'SD/ Sederajat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '2'),
(3, 1, 3, 'SMP / Sederajat', 'SMP / Sederajat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '3'),
(4, 1, 4, 'SMA / Sederajat', 'SMA / Sederajat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '4'),
(5, 1, 5, 'Diploma', 'Diploma', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '5'),
(6, 1, 6, 'Sarjana', 'Sarjana', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '6'),
(7, 1, 7, 'Magister', 'Magister', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '7'),
(8, 1, 8, 'Doktor', 'Doktor', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '8'),
(9, 1, 9, 'Lainnya', 'Lainnya', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, '9');
        ");


     $this->execute("SELECT setval('public.pendidikan_m_pendidikan_id_seq', (SELECT COALESCE(MAX(pendidikan_id) ,1)+1 FROM pendidikan_m), false);");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_105146_seeder_pendidikan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_105146_seeder_pendidikan_m cannot be reverted.\n";

        return false;
    }
    */
}
