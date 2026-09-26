<?php

use yii\db\Migration;

/**
 * Class m210915_084811_improvment_laporan_spri_US1306_1307
 */
class m210915_084811_improvment_laporan_spri_US1306_1307 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS infeksi TEXT;
        ');

        $this->execute('
            ALTER TABLE ruangan_m DROP COLUMN IF EXISTS kualifikasi_ruangan_id;
        ');

        $this->execute('
            ALTER TABLE ruangan_m ADD IF NOT EXISTS klasifikasi_ruangan_id int4;
        ');

        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
                1059,
                1060,
                1061,
                1062,
                1066
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1059, \'klasifikasi_ruangan\', \'Isolasi\', \'Isolasi\', NULL, NULL, NULL, \'2021-09-09 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1060, \'klasifikasi_ruangan\', \'ICU\', \'ICU\', NULL, NULL, NULL, \'2021-09-09 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1061, \'klasifikasi_ruangan\', \'ICCU\', \'ICCU\', NULL, NULL, NULL, \'2021-09-09 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1062, \'klasifikasi_ruangan\', \'NICU/PICU\', \'NICU/PICU\', NULL, NULL, NULL, \'2021-09-09 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1066, \'klasifikasi_ruangan\', \'LDR\', \'LDR\', NULL, NULL, NULL, \'2021-09-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210915_084811_improvment_laporan_spri_US1306_1307 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210915_084811_improvment_laporan_spri_US1306_1307 cannot be reverted.\n";

        return false;
    }
    */
}
