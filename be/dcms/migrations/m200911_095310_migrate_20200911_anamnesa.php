<?php

use yii\db\Migration;

/**
 * Class m200911_095310_migrate_20200911_anamnesa
 */
class m200911_095310_migrate_20200911_anamnesa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "tujuan_rujukan" varchar(20) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "sumber_data" varchar(255) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "rujukan" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "rujukan_rs" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "diagnosa_rujukan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "berat_badan" float8;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "tinggi_badan" float8;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nadi" int2;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "rr" varchar(255) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "td" varchar(255) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "suhu" varchar(50) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_penyakit" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_penyakit_nama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dirawat" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dirawat_diagnosa" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dirawat_waktu" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dirawat_tempat" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dioperasi" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dioperasi_diagnosa" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "dioperasi_waktu" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "obat_dikonsumsi" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "obat_dikonsumsi_nama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_penyakit_keluarga" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_penyakit_keluarga_list" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "ketergantungan" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "ketergantungan_jenis" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_pekerjaan" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "riwayat_pekerjaan_nama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "alergi" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "alergi_makanan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "reaksi_alergi" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "status_psikologi" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "status_sosial" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nama_kerabat_terdekat" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "hubungan_kerabat_terdekat" varchar(100) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kontak_kerabat_terdekat" varchar(100) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "status_ekonomi" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nilai_kebudayaan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "hambatan" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "jenis_hambatan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "butuh_penerjemah" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "butuh_penerjemah_nama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "bahasa_isyarat" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kesediaan_menerima_informasi" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kebutuhan_edukasi" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kebutuhan_edukasi_lainnya" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kebutuhan_edukasi_keperawatan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "resiko_cedera_pertama" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "resiko_cedera_kedua" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "hasil_resiko" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "aktivitas" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "bantuan_aktivitas" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "alat_bantu_jalan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nyeri_kronis_pertama" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "lokasi_nyeri_kronis_pertama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "frekuensi_nyeri_kronis_pertama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "durasi_nyeri_kronis_pertama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nyeri_kronis_kedua" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "lokasi_nyeri_kronis_kedua" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "frekuensi_nyeri_kronis_kedua" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "durasi_nyeri_kronis_kedua" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "skor_nyeri" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nyeri_menjalar" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kualitas_nyeri" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "faktor_pereda_nyeri" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1a" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1b" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1c1" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1c2" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1c3" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_1c4" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_2" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nutrisi_2b" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "nilai_nutrisi" int4;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "diagnosa_khusus" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "jenis_diagnosa_khusus" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "diagnosa_keperawatan" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "suku_id" int4;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "kemampuan_membaca" bool;');
$this->execute('ALTER TABLE "public"."anamnesa_t" ADD IF NOT EXISTS "bahasa" text COLLATE "pg_catalog"."default";');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_095310_migrate_20200911_anamnesa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_095310_migrate_20200911_anamnesa cannot be reverted.\n";

        return false;
    }
    */
}
