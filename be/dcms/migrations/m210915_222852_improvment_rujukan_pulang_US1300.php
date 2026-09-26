<?php

use yii\db\Migration;

/**
 * Class m210915_222852_improvment_rujukan_pulang_US1300
 */
class m210915_222852_improvment_rujukan_pulang_US1300 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."rujukanpulang_t" (
                "rujukanpulang_id" serial8 NOT NULL  ,
                "pendaftaran_id" int4 NOT NULL,
                "pasienadmisi_id" int4,
                rujukan_dituju VARCHAR(200),
                pic_rujukan_dituju VARCHAR(200),
                diagnosa_masuk TEXT,
                diagnosa_keluar TEXT,
                keluhan_utama TEXT,
                riwayat_penyakit_sekarang TEXT,
                riwayat_penyakit_dahulu TEXT,
                anamnesis_keluhan_utama TEXT,
                anamnesis_kesadaran TEXT,
                anamnesis_saturasi_o2 VARCHAR(30),
                anamnesis_tensi VARCHAR(30),
                anamnesis_suhu VARCHAR(30),
                anamnesis_nadi VARCHAR(30),
                anamnesis_pernafasan VARCHAR(30),
                alasan_dirujuk TEXT,
                pemeriksaan_penunjang TEXT,
                tindakan_terapi TEXT,
                tindakan_medis TEXT,
                tindakan_lainnya TEXT,
                derajat_0 TEXT,
                derajat_1 TEXT,
                derajat_2 TEXT,
                derajat_3 TEXT,
                tanggal_rujukan timestamp(6),
                keadaan_umum TEXT,
                kesadaran TEXT,
                tensi VARCHAR(30),
                suhu VARCHAR(30),
                nadi VARCHAR(30),
                pernafasan VARCHAR(30),
                saturasi_o2 VARCHAR(30),
                catatan_penting TEXT,
                pegawai_id int4,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "rujukanpulang_t_pkey" PRIMARY KEY ("rujukanpulang_id")
            )
            ;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210915_222852_improvment_rujukan_pulang_US1300 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210915_222852_improvment_rujukan_pulang_US1300 cannot be reverted.\n";

        return false;
    }
    */
}
