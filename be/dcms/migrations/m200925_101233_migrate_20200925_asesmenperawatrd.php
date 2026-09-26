<?php

use yii\db\Migration;

/**
 * Class m200925_101233_migrate_20200925_asesmenperawatrd
 */
class m200925_101233_migrate_20200925_asesmenperawatrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "tgl_keluar" timestamp(6);');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "agama_id" int4;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pekerjaan_id" int4;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "caramasuk_id" int4;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pendidikan_id" int4;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "jalur_nafas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "jalur_nafas_oksigen" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pernafasan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pernafasan_spontan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pernafasan_takipnea" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pernafasan_gargling" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "tensi" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "reguler" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "ireguler" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "capilary_refill" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "perfusi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "akral" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pendarahan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pendarahan_cc" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_bradikarida" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_takikardia" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pupil" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "reaksi_pupil" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "reaksi_pupil_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "kepala" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "abdomen" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "maksilofacial" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "parineum" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "tulan_leher" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "muskuloskeletal" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "paru_paru" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "extremitas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "skala_wong_baker" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "asesmen_auto" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "asesmen_allo" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "r_penyakitsaatini" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "r_pengobatan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "jalan_nafas_bersin" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "nilai_decubitus" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "nilai_luka_bakar" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kepala" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kepala_lacerasi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kepala_battle_sign" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kepala_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_mata" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_mulut" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_mulut_luka_dalam" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_mulut_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_telinga" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_leher" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_leher_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_extremitas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_extremitas_pulsasi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_simetris_asimetris" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_pneumo_hamatotoraks" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_nyeri_lokasi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_nyeri_kapan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_nyeri_durasi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_nyeri_kegiatan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_dada_bunyi_jantung" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_abdomen" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_abdomen_memas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_abdomen_nyeri" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_abdomen_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_abdomen_bising_usus" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_pelvis" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_pelvis_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_medulla_spinalis" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kolumna_vertebralis" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "perasaan_klien" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "sosial_support" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "hubungan_pasien" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "keluarga_lain" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "keadaan_emosi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "suku_id" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "bahasa_dipakai" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "bahasa_dipakai_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_penerjemah" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "media" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "media_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "identifikasi_hambatan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_sistem_rujukan" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "materi" varchar(100) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "edukator" varchar(100) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_ketersediaan_pasien" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_kemampuan_membaca" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "bahasa" varchar(100) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_dibutuhkan_penerjemah" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "penerjemah_bahasa" varchar(100) ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_hambatan_emotional" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "hambatan_emotional_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "is_keterbatasan_fisik" bool;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "keterbatasan_fisik" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "bersihan_jalan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "pola_nafas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_gas" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "nyeri" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "penurunan_jantung" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_perfusi_cerebral" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_perfusi_perifer" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "valume_cairan_tubuh" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_thermoregulasi_hypertermi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_thermoregulasi_hypotermi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "tujuan_pulang" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "tujuan_pulang_lainnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "transportasi" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "orang_merawat" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "sarana_kesehatan" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "masuk_ke" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "persen_luka_bakar" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "kategori_triase_sehari" text ;');
$this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "kategori_triase_disaster" text ;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_101233_migrate_20200925_asesmenperawatrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_101233_migrate_20200925_asesmenperawatrd cannot be reverted.\n";

        return false;
    }
    */
}
