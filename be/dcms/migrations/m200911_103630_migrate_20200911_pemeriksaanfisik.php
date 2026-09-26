<?php

use yii\db\Migration;

/**
 * Class m200911_103630_migrate_20200911_pemeriksaanfisik
 */
class m200911_103630_migrate_20200911_pemeriksaanfisik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ALTER COLUMN "pegawaiperawat_id" DROP NOT NULL;');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "dokter_id" int4;');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "anamnesa" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "keluhan_utama" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "riwayat_penyakit_sekarang" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "riwayat_penyakit_dahulu" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "riwayat_penyakit_keluarga" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "riyawat_penyakit_lainnya" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "kesadaran" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "imt" varchar(30) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "status_lokalis" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "pemeriksaan_penunjang" text COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."pemeriksaanfisik_t" ADD IF NOT EXISTS "diagnosa_kerja" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_103630_migrate_20200911_pemeriksaanfisik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_103630_migrate_20200911_pemeriksaanfisik cannot be reverted.\n";

        return false;
    }
    */
}
