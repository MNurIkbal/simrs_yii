<?php

use yii\db\Migration;

/**
 * Class m201210_111046_oddo_20201210_penyesuaianview
 */
class m201210_111046_oddo_20201210_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
        $this->execute('ALTER TABLE "public"."ruangan_m" ADD IF NOT EXISTS "is_store" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."ruangan_m" ADD IF NOT EXISTS "is_mainstore" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."ruangan_m" ADD IF NOT EXISTS "is_substore" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."ruangan_m" ADD IF NOT EXISTS "is_cartstore" bool DEFAULT false;');

        $this->execute('DROP VIEW if exists "public"."int_ruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    '-'::text AS parent_id,
    ruangan_m.ruangan_nama AS name,
    ruangan_m.is_active AS active,
    6 AS sync_type,
    ruangan_m.ruangan_id,
    ruangan_m.additional_data,
    ruangan_m.is_store,
    ruangan_m.is_mainstore,
    ruangan_m.is_substore,
    ruangan_m.is_cartstore
   FROM ruangan_m
  WHERE ruangan_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_ruangan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if  exists "public"."int_pegawai_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pegawai_v\" AS  SELECT concat('PEG', pegawai_m.pegawai_id) AS sync_id_api,
    '-'::text AS parent_doctor_id,
    pegawai_m.nomorindukpegawai AS doctor_code,
    pegawai_m.nama_pegawai AS name,
    pegawai_m.nama_pegawai AS display_name,
    spesialis_m.spesialis_nama AS spec,
    '-'::text AS department,
    'internal'::text AS type_employment,
    'internal'::text AS type_doctor,
    '-'::text AS designation,
    kelompokpegawai_m.kelompokpegawai_nama AS hrprofile,
    lower(fgetnamalookup(pegawai_m.jeniskelamin::integer)::text) AS gender,
    pegawai_m.notelp_pegawai AS phone,
    pegawai_m.nomobile_pegawai AS mobile,
    '-'::text AS fax,
    pegawai_m.alamatemail AS email,
    '-'::text AS website,
    pegawai_m.alamat_pegawai AS street,
    pegawai_m.alamat_pegawai AS street2,
    pegawai_m.alamat_pegawai AS street3,
    kabupaten_m.kabupaten_nama AS city,
    '-'::text AS zip,
    spesialis_m.spesialis_nama AS specialise_api_id,
        CASE
            WHEN pegawai_m.is_active = true THEN false
            ELSE true
        END AS wipro_block,
        CASE
            WHEN pegawai_m.is_active = true THEN false
            ELSE true
        END AS empblocked,
    'TRUE'::text AS doctor,
    pegawai_m.is_active AS active,
    6 AS sync_type,
    pegawai_m.pegawai_id,
    pegawai_m.additional_data
   FROM pegawai_m
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN kabupaten_m ON pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
  WHERE pegawai_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_pegawai_v" OWNER TO "postgres";');

    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201210_111046_oddo_20201210_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201210_111046_oddo_20201210_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
