<?php

use yii\db\Migration;

/**
 * Class m201214_051009_oddo_20201214_penyesuaianview
 */
class m201214_051009_oddo_20201214_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
        CASE
            WHEN pasien_r.jenisidentitas::text = '99'::text THEN pasien_r.no_identitas_pasien
            ELSE '-'::character varying
        END AS passport,
        CASE
            WHEN pasien_r.jenisidentitas::text = '94'::text THEN pasien_r.no_identitas_pasien
            ELSE '-'::character varying
        END AS ktp,
    false AS wipro_block,
    true AS patient,
    pasien_r.is_active AS active,
    6 AS sync_type,
    pasien_r.keterangan,
    pasien_r.tgl_proses,
    pasien_r.is_sent,
    pasien_r.is_sending,
    pasien_r.pasien_id,
    pasien_r.additional_data,
    COALESCE(pasien_m.additional_pasien, '-'::character varying::text) AS no_identitas
   FROM pasien_r
     JOIN pasien_m ON pasien_r.pasien_id = pasien_m.pasien_id;");
        
        $this->execute('ALTER TABLE "public"."int_pasien_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201214_051009_oddo_20201214_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_051009_oddo_20201214_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
