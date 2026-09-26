<?php

use yii\db\Migration;

/**
 * Class m201008_234649_migrate_20201007_asesmenmedisrd
 */
class m201008_234649_migrate_20201007_asesmenmedisrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "tgl_pasien_datang" timestamp(6);');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "triage" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "dikirim_oleh" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "kasus_polisi" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "cara_datang" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "cara_datang_diantar" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "asesmen_allo" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "asesmen_auto" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "keluhan_utama" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "riwayat_penyakit_sekarang" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "riwayat_penyakit_dahulu" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "riwayat_terapi_sebelumnya" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "alergi" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "laboratorium" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "radiologi" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "ekg" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "lain_lain" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "diagnosa_id" int4;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "terapi" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "tindak_lanjut" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "ku_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "tekanandarah_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "nadi_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "pernapasan_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "saturasi_o2_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "suhu_keluar" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "berat_badan" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "tinggi_badan" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "imt" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "tekanandarah" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "nadi" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "pernapasan" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "suhu" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "saturasi_o2" varchar(30) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "skrining_nyeri" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "skala_nyeri" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "skala_nyeri_anak" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "keadaan_umum" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "kesadaran" text ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "keterangan_gcs" varchar(255) ;');
$this->execute('ALTER TABLE "public"."asesmenmedisrd_t" ADD COLUMN IF NOT EXISTS "pilih_skala" text ;');
$this->execute('ALTER TABLE "public"."periksatubuh_t" ADD COLUMN IF NOT EXISTS "tgl_periksa" timestamp(6);');
$this->execute('ALTER TABLE "public"."periksatubuh_t" ADD COLUMN IF NOT EXISTS "terapi" text ;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_234649_migrate_20201007_asesmenmedisrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_234649_migrate_20201007_asesmenmedisrd cannot be reverted.\n";

        return false;
    }
    */
}
