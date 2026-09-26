<?php

use yii\db\Migration;

/**
 * Class m200914_030051_migrate_20200914_tindakanoperasi
 */
class m200914_030051_migrate_20200914_tindakanoperasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanoperasi_mp" ADD IF NOT EXISTS "prosentase" float8 DEFAULT 0;');
         
        $this->execute("DELETE from lookup_m WHERE lookup_type='tim_operasi';");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(508, 'tim_operasi', 'Dokter Operator', 'dokter-operator', NULL, NULL, 'Dokter Bedah', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(509, 'tim_operasi', 'Dokter Anastesi', 'dokter-anastesi', NULL, NULL, 'Asisten Bedah 1', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(510, 'tim_operasi', 'Dokter Anak', 'dokter-anak', NULL, NULL, 'Asisten Bedah 2', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(511, 'tim_operasi', 'Asisten Anastesi 1', 'asisten-anastesi-1', NULL, NULL, 'Dokter Anastesi', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(512, 'tim_operasi', 'Asisten Anastesi 2', 'asisten-anastesi-2', NULL, NULL, 'Asisten Anastesi', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(513, 'tim_operasi', 'Asisten Bedah 1', 'asisten-bedah-1', NULL, NULL, 'Perawat Instrumen 1', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(514, 'tim_operasi', 'Asisten Bedah 2', 'asisten-bedah-2', NULL, NULL, 'Perawat Instrumen 2', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(515, 'tim_operasi', 'Perawat Instrumen', 'perawat-instrumen', NULL, NULL, 'Perawat Sirkuler 1', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(516, 'tim_operasi', 'Perawat Sirkuler', 'perawat-sirkuler', NULL, NULL, 'Perawat Sirkuler 2', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(517, 'tim_operasi', 'Perawat Anastesi', 'perawat-anastesi', NULL, NULL, 'Perawat Anastesi', '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_030051_migrate_20200914_tindakanoperasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_030051_migrate_20200914_tindakanoperasi cannot be reverted.\n";

        return false;
    }
    */
}
