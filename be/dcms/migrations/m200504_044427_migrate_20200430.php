<?php

use yii\db\Migration;

/**
 * Class m200504_044427_migrate_20200430
 */
class m200504_044427_migrate_20200430 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."konsulpoli_t" ALTER COLUMN "last_modified_date" DROP NOT NULL;');

         $this->execute('ALTER TABLE "public"."konsulpoli_t" ALTER COLUMN "last_modified_date" DROP DEFAULT;');
         
         $this->execute('ALTER TABLE "public"."konsulpoli_t" ALTER COLUMN "deleted_date" DROP NOT NULL;');

         $this->execute('ALTER TABLE "public"."konsulpoli_t" ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN "tgl_selesaikonsul" timestamp(6);');

         $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN "jawaban_konsul" text COLLATE "pg_catalog"."default";');

         $this->execute('DROP VIEW if exists "public"."infokonsulpoli_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infokonsulpoli_v\" AS  SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul
   FROM (((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false));");

         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');
         $this->execute('');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200504_044427_migrate_20200430 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200504_044427_migrate_20200430 cannot be reverted.\n";

        return false;
    }
    */
}
