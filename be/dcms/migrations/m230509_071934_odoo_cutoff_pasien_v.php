<?php

use yii\db\Migration;

/**
 * Class m230509_071934_odoo_cutoff_pasien_v
 */
class m230509_071934_odoo_cutoff_pasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        DROP VIEW IF EXISTS newodoo_pasien_v
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.newodoo_pasien_v
        AS 
        select 
            id,
            sync_id_api,
            registration_code,
            \"name\",
            display_name,
            title_name,
            date_of_birth,
            gender,
            phone,
            mobile,
            contact_person,
            fax,
            email,
            street,
            street2,
            street3,
            city,
            zip,
            passport,
            ktp,
            wipro_block,
            patient,
            active,
            keterangan,
            tgl_proses,
            pasien_id
        from (
        SELECT 
        distinct on (pasien_id)
        pasien_r.id,
            pasien_r.pasien_id AS sync_id_api,
            pasien_r.no_rekam_medik AS registration_code,
            pasien_r.nama_pasien AS name,
            pasien_r.nama_pasien AS display_name,
            lower(fgetnamalookup(pasien_r.namadepan::integer)::text) AS title_name,
            COALESCE(pasien_r.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
            lower(fgetvaluelookup(pasien_r.jeniskelamin::integer)::text) AS gender,
            COALESCE(pasien_r.no_telepon_pasien, '-'::character varying) AS phone,
            COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS mobile,
            COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS contact_person,
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
                CASE
                    WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS TRUE THEN true
                    WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS FALSE THEN false
                    WHEN pasien_r.is_active IS FALSE AND pasien_r.is_deleted IS FALSE THEN true
                    ELSE true
                END AS wipro_block,
            true AS patient,
            true AS active,
            pasien_r.keterangan,
            pasien_r.tgl_proses,
            pasien_r.pasien_id
        FROM pasien_r where keterangan ='UPDATE' and date(tgl_proses) <> date(created_date)
        order by pasien_id, tgl_proses desc
        ) t
        union all
        select 
        null::integer as id,
            pasien_m.pasien_id AS sync_id_api,
            pasien_m.no_rekam_medik AS registration_code,
            pasien_m.nama_pasien AS name,
            pasien_m.nama_pasien AS display_name,
            lower(fgetnamalookup(pasien_m.namadepan::integer)::text) AS title_name,
            COALESCE(pasien_m.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
            lower(fgetvaluelookup(pasien_m.jeniskelamin::integer)::text) AS gender,
            COALESCE(pasien_m.no_telepon_pasien, '-'::character varying) AS phone,
            COALESCE(pasien_m.no_mobile_pasien, '-'::character varying) AS mobile,
            COALESCE(pasien_m.no_mobile_pasien, '-'::character varying) AS contact_person,
            '-'::text AS fax,
            COALESCE(pasien_m.alamatemail, '-'::character varying) AS email,
            COALESCE(pasien_m.alamat_sekarang, '-'::text) AS street,
            '-'::text AS street2,
            '-'::text AS street3,
            COALESCE(pasien_m.alamat_pasien, '-'::text) AS city,
            '-'::text AS zip,
                CASE
                    WHEN pasien_m.jenisidentitas::text = '99'::text THEN pasien_m.no_identitas_pasien
                    ELSE '-'::character varying
                END AS passport,
                CASE
                    WHEN pasien_m.jenisidentitas::text = '94'::text THEN pasien_m.no_identitas_pasien
                    ELSE '-'::character varying
                END AS ktp,
                CASE
                    WHEN pasien_m.is_active IS TRUE AND pasien_m.is_deleted IS TRUE THEN true
                    WHEN pasien_m.is_active IS TRUE AND pasien_m.is_deleted IS FALSE THEN false
                    WHEN pasien_m.is_active IS FALSE AND pasien_m.is_deleted IS FALSE THEN true
                    ELSE true
                END AS wipro_block,
            true AS patient,
            true AS active,
            'INSERT' as keterangan,
            pasien_m.created_date as tgl_proses,
            pasien_m.pasien_id
            from pasien_m 
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_071934_odoo_cutoff_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_071934_odoo_cutoff_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
