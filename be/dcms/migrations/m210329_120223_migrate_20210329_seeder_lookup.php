<?php

use yii\db\Migration;

/**
 * Class m210329_120223_migrate_20210329_seeder_lookup
 */
class m210329_120223_migrate_20210329_seeder_lookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE from lookup_m WHERE lookup_type in ('kategori_operasi_lap','pembiusan_operasi_lap');");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(693, 'kategori_operasi_lap', 'Gawat Darurat (Emergency)', 'Gawat Darurat (Emergency)', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(694, 'kategori_operasi_lap', 'Berancana (Elective)', 'Berancana (Elective)', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(695, 'kategori_operasi_lap', 'Minor', 'Minor', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(696, 'kategori_operasi_lap', 'Medium', 'Medium', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(697, 'kategori_operasi_lap', 'Mayor', 'Mayor', 5, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(698, 'kategori_operasi_lap', 'Special', 'Special', 6, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(699, 'kategori_operasi_lap', 'Bersih', 'Bersih', 7, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1012, 'kategori_operasi_lap', 'Bersih Tercemar', 'Bersih Tercemar', 8, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1013, 'kategori_operasi_lap', 'Tercemar', 'Tercemar', 9, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1014, 'kategori_operasi_lap', 'Kotor', 'Kotor', 10, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1015, 'pembiusan_operasi_lap', 'General Anesthesia', 'General Anesthesia', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1016, 'pembiusan_operasi_lap', 'Regional Anesthesia', 'Regional Anesthesia', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1017, 'pembiusan_operasi_lap', 'Local Anesthesia', 'Local Anesthesia', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_120223_migrate_20210329_seeder_lookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_120223_migrate_20210329_seeder_lookup cannot be reverted.\n";

        return false;
    }
    */
}
