<?php

use yii\db\Migration;

/**
 * Class m201208_102822_oddo_penyesuaianview_20200812
 */
class m201208_102822_oddo_penyesuaianview_20200812 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_penjamin_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_penjamin_v\" AS  SELECT concat('PEN', penjamin_m.penjamin_id) AS sync_id_api,
    penjamin_m.penjamin_kode AS vendor_code,
    penjamin_m.penjamin_nama AS name,
    penjamin_m.penjamin_nama AS display_name,
    carabayar_m.carabayar_nama AS customer_type_api,
    '-'::text AS contact_person,
    '-'::text AS phone,
    '-'::text AS mobile,
    '-'::text AS fax,
    '-'::text AS email,
    '-'::text AS website,
    penjamin_m.alamat_penjamin AS street,
    penjamin_m.alamat_penjamin AS street2,
    penjamin_m.alamat_penjamin AS street3,
    '-'::text AS city,
    '-'::text AS zip,
    NULL::text AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS taxid,
        CASE
            WHEN penjamin_m.is_active = true THEN false
            ELSE true
        END AS wipro_block,
        CASE
            WHEN carabayar_m.groupcarabayar_id = 417 THEN false
            ELSE true
        END AS insurance,
    true AS customer,
    penjamin_m.is_active AS active,
    6 AS sync_type,
    penjamin_m.penjamin_id,
    penjamin_m.additional_data
   FROM penjamin_m
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.is_deleted = false
  WHERE penjamin_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_penjamin_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_pegawai_v";');

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
    pegawai_m.is_active AS wipro_block,
    pegawai_m.is_active AS empblocked,
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

        $this->execute('DROP VIEW if exists "public"."int_pasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pasien_v\" AS  SELECT pasien_r.id,
    pasien_r.pasien_id AS sync_id_api,
    pasien_r.no_rekam_medik AS registration_code,
    pasien_r.nama_pasien AS name,
    pasien_r.nama_pasien AS display_name,
    lower(fgetnamalookup(pasien_r.namadepan::integer)::text) AS title_name,
    COALESCE(pasien_r.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
    lower(fgetvaluelookup(pasien_r.jeniskelamin::integer)::text) AS gender,
    COALESCE(pasien_r.no_telepon_pasien, '-'::character varying) AS phone,
    COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS mobile,
    '-'::text AS fax,
    COALESCE(pasien_r.alamatemail, '-'::character varying) AS email,
    COALESCE(pasien_r.alamat_sekarang, '-'::text) AS street,
    '-'::text AS street2,
    '-'::text AS street3,
    COALESCE(pasien_r.alamat_pasien, '-'::text) AS city,
    '-'::text AS zip,
    '-'::text AS passport,
    COALESCE(pasien_r.no_identitas_pasien, '-'::character varying) AS ktp,
    false AS wipro_block,
    true AS patient,
    pasien_r.is_active AS active,
    6 AS sync_type,
    pasien_r.keterangan,
    pasien_r.tgl_proses,
    pasien_r.is_sent,
    pasien_r.is_sending,
    pasien_r.pasien_id,
    pasien_r.additional_data
   FROM pasien_r;");

        $this->execute('ALTER TABLE "public"."int_pasien_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_102822_oddo_penyesuaianview_20200812 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_102822_oddo_penyesuaianview_20200812 cannot be reverted.\n";

        return false;
    }
    */
}
