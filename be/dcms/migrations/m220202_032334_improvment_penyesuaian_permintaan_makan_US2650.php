<?php

use yii\db\Migration;

/**
 * Class m220202_032334_improvment_penyesuaian_permintaan_makan_US2650
 */
class m220202_032334_improvment_penyesuaian_permintaan_makan_US2650 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS perubahan_diet int4;
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS kondisi_puasa varchar(255);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS puasa_tgl_awal timestamp(6);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS puasa_tgl_akhir timestamp(6);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS puasa_operasi_awal timestamp(6);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS puasa_operasi_akhir timestamp(6);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS buka_puasa timestamp(6);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS kesimpulan varchar(255);
        ');

        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS jenisdiet_lainnya varchar(255);
        ');

        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
                1131,
                1132
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1131, \'perubahan_diet\', \'Perubahan bentuk makanan\', \'Perubahan bentuk makanan\', NULL, NULL, NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1132, \'perubahan_diet\', \'Perubahan jenis diet\', \'Perubahan jenis diet\', NULL, NULL, NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');

        $this->execute('
            TRUNCATE TABLE jenisdiet_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."jenisdiet_m"("jenisdiet_id", "jenisdiet_kode", "jenisdiet_nama", "jenisdiet_keterangan", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'D01\', \'Diet jantung\', \'Diet jantung\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'D02\', \'Diet Hati\', \'Diet Hati\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, \'D03\', \'Diet DM ......kalori\', \'Diet DM ......kalori\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, \'D04\', \'Diet TKTP (Tinggi Kalori Tinggi Protein)\', \'Diet TKTP (Tinggi Kalori Tinggi Protein)\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, \'D05\', \'Diet Rendah Garam\', \'Diet Rendah Garam\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (7, \'D06\', \'Diet Rendah Protein.....gram\', \'Diet Rendah Protein.....gram\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (8, \'D07\', \'Diet rendah kalori.....kkal\', \'Diet rendah kalori.....kkal\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, \'D08\', \'Diet rendah purin\', \'Diet rendah purin\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (10, \'D09\', \'Diet rendah serat (GE/TD)\', \'Diet rendah serat (GE/TD)\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (11, \'D10\', \'Diet tinggi serat\', \'Diet tinggi serat\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (12, \'D11\', \'Diet rendah lemak\', \'Diet rendah lemak\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (13, \'D12\', \'Diet rendah kalium\', \'Diet rendah kalium\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (14, \'D13\', \'Diet lainnya.......\', \'Diet lainnya.......\', NULL, \'2022-01-20 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_032334_improvment_penyesuaian_permintaan_makan_US2650 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_032334_improvment_penyesuaian_permintaan_makan_US2650 cannot be reverted.\n";

        return false;
    }
    */
}
