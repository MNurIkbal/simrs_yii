<?php

use yii\db\Migration;

/**
 * Class m201023_092939_migrate_2983_pagtgizi
 */
class m201023_092939_migrate_2983_pagtgizi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pagt_t" (
                "pagt_id" serial8 NOT NULL,
                pendaftaran_id int4,
                tgl_kajian timestamp(6),
                diagnosa_medis text,
                diet text,
                pandangan_alergi text,
                bb VARCHAR(20),
                tb VARCHAR(20),
                lila VARCHAR(20),
                imt_dewasa VARCHAR(20),
                imt_anak VARCHAR(20),
                bb_anak VARCHAR(20),
                ulna VARCHAR(20),
                status_gizi text,
                trigliserida VARCHAR(20),
                hdl VARCHAR(20),
                ldl VARCHAR(20),
                kolesterol VARCHAR(20),
                ureum VARCHAR(20),
                kreatinin VARCHAR(20),
                kalium VARCHAR(20),
                natrium VARCHAR(20),
                kalsium VARCHAR(20),
                phospor VARCHAR(20),
                sgot VARCHAR(20),
                sgpt VARCHAR(20),
                bilirubin VARCHAR(20),
                gd_sewaktu VARCHAR(20),
                gd_puasa VARCHAR(20),
                hba1c VARCHAR(20),
                dua_jam_pp VARCHAR(20),
                hb VARCHAR(20),
                albumin VARCHAR(20),
                ht VARCHAR(20),
                pemeriksaan_fisik text,
                pemeriksaan_fisik_lainnnya text,
                tekanan_darah VARCHAR(20),
                gangguan_pencernaan text,
                makan_pagi_pokok text,
                makan_pagi_hewani text,
                makan_pagi_nabati text,
                makan_pagi_sayur text,
                makan_pagi_buah text,
                makan_pagi_energi text,
                makan_pagi_protein text,
                makan_pagi_lemak text,
                makan_pagi_kh text,
                selingan_pagi_pokok text,
                selingan_pagi_hewani text,
                selingan_pagi_nabati text,
                selingan_pagi_sayur text,
                selingan_pagi_buah text,
                selingan_pagi_energi text,
                selingan_pagi_protein text,
                selingan_pagi_lemak text,
                selingan_pagi_kh text,
                makan_siang_pokok text,
                makan_siang_hewani text,
                makan_siang_nabati text,
                makan_siang_sayur text,
                makan_siang_buah text,
                makan_siang_energi text,
                makan_siang_protein text,
                makan_siang_lemak text,
                makan_siang_kh text,
                selingan_sore_pokok text,
                selingan_sore_hewani text,
                selingan_sore_nabati text,
                selingan_sore_sayur text,
                selingan_sore_buah text,
                selingan_sore_energi text,
                selingan_sore_protein text,
                selingan_sore_lemak text,
                selingan_sore_kh text,
                makan_malam_pokok text,
                makan_malam_hewani text,
                makan_malam_nabati text,
                makan_malam_sayur text,
                makan_malam_buah text,
                makan_malam_energi text,
                makan_malam_protein text,
                makan_malam_lemak text,
                makan_malam_kh text,
                selingan_malam_pokok text,
                selingan_malam_hewani text,
                selingan_malam_nabati text,
                selingan_malam_sayur text,
                selingan_malam_buah text,
                selingan_malam_energi text,
                selingan_malam_protein text,
                selingan_malam_lemak text,
                selingan_malam_kh text,
                total_pokok text,
                total_hewani text,
                total_nabati text,
                total_sayur text,
                total_buah text,
                total_energi text,
                total_protein text,
                total_lemak text,
                total_kh text,
                alergi text,
                diet_dijalankan text,
                olah_raga_hari VARCHAR(20),
                olah_raga_menit VARCHAR(20),
                kebiasaan_merokok VARCHAR(20),
                aktifitas_fisik text,
                diagnosa_gizi text,
                cara_intervensi text,
                diet_diberikan text,
                energi VARCHAR(20),
                lemak VARCHAR(20),
                protein VARCHAR(20),
                kh VARCHAR(20),
                tujuan_diet text,
                bentuk_makanan text,
                bentuk_makanan_saji text,
                bentuk_makanan_hari text,
                cara_pemberian text,
                cara_pemberian_vitamin text,
                bagi_makan_pagi_nasi text,
                bagi_makan_pagi_hewani text,
                bagi_makan_pagi_nabati text,
                bagi_makan_pagi_sayur text,
                bagi_makan_pagi_buah text,
                bagi_selingan_pagi_nasi text,
                bagi_selingan_pagi_hewani text,
                bagi_selingan_pagi_nabati text,
                bagi_selingan_pagi_sayur text,
                bagi_selingan_pagi_buah text,
                bagi_makan_siang_nasi text,
                bagi_makan_siang_hewani text,
                bagi_makan_siang_nabati text,
                bagi_makan_siang_sayur text,
                bagi_makan_siang_buah text,
                bagi_selingan_sore_nasi text,
                bagi_selingan_sore_hewani text,
                bagi_selingan_sore_nabati text,
                bagi_selingan_sore_sayur text,
                bagi_selingan_sore_buah text,
                bagi_makan_malam_nasi text,
                bagi_makan_malam_hewani text,
                bagi_makan_malam_nabati text,
                bagi_makan_malam_sayur text,
                bagi_makan_malam_buah text,
                bagi_selingan_malam_nasi text,
                bagi_selingan_malam_hewani text,
                bagi_selingan_malam_nabati text,
                bagi_selingan_malam_sayur text,
                bagi_selingan_malam_buah text,
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
                CONSTRAINT "pk_pagt_t" PRIMARY KEY ("pagt_id")
            );
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pagtmonev_t" (
                "pagtmonev_id" serial8 NOT NULL ,
                pagt_id int4 NOT NULL,
                tgl_monev timestamp(6),
                berat_badan VARCHAR(20),
                tekanan_darah VARCHAR(20), 
                nilai_lab_abnormal VARCHAR(20),
                oral_energi VARCHAR(20),
                oral_protein VARCHAR(20),
                oral_lemak VARCHAR(20),
                oral_kh VARCHAR(20),
                enteral_energi VARCHAR(20),
                enteral_protein VARCHAR(20),
                enteral_lemak VARCHAR(20),
                enteral_kh VARCHAR(20),
                parenteral_energi VARCHAR(20),
                parenteral_protein VARCHAR(20),
                parenteral_lemak VARCHAR(20),
                parenteral_kh VARCHAR(20),
                total_asupan_energi VARCHAR(20),
                total_asupan_protein VARCHAR(20),
                total_asupan_lemak VARCHAR(20),
                total_asupan_kh VARCHAR(20),
                evaluasi_usulan text,
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
                CONSTRAINT "pk_pagtmonev_t" PRIMARY KEY ("pagtmonev_id")
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201023_092939_migrate_2983_pagtgizi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_092939_migrate_2983_pagtgizi cannot be reverted.\n";

        return false;
    }
    */
}
