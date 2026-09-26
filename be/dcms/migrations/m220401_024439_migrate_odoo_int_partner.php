<?php

use yii\db\Migration;

/**
 * Class m220401_024439_migrate_odoo_int_partner
 */
class m220401_024439_migrate_odoo_int_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."int_partner";');

         $this->execute("
            CREATE VIEW \"public\".\"int_partner\" AS  SELECT concat('SUP', supplier_m.supplier_id) AS sync_id_api,
    supplier_m.supplier_kode AS vendor_code,
    '-'::text AS parent_doctor_id,
    '-'::text AS doctor_code,
    supplier_m.supplier_nama AS name,
    supplier_m.supplier_nama AS display_name,
    NULL::text AS spec,
    '-'::text AS department,
    NULL::text AS type_employment,
    NULL::text AS type_doctor,
    '-'::text AS designation,
    NULL::text AS hrprofile,
    NULL::text AS gender,
    supplier_m.no_tlp AS contact_person,
    supplier_m.no_tlp AS phone,
    supplier_m.no_tlp AS mobile,
    supplier_m.no_fax AS fax,
    supplier_m.email,
    supplier_m.website,
    supplier_m.supplier_alamat AS street,
    supplier_m.supplier_alamat AS street2,
    supplier_m.supplier_alamat AS street3,
    propinsi_m.propinsi_nama AS city,
    NULL::text AS zip,
    false AS empblocked,
    true AS active,
        CASE
            WHEN supplier_m.is_active IS TRUE AND supplier_m.is_deleted IS TRUE THEN true
            WHEN supplier_m.is_active IS TRUE AND supplier_m.is_deleted IS FALSE THEN false
            WHEN supplier_m.is_active IS FALSE AND supplier_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS customer_type_api,
    true AS vendor_wipro,
    false AS costumer_wipro,
    true AS supplier,
    false AS customer,
    false AS insurance,
    false AS patient,
    false AS doctor,
    '6'::text AS sync_type,
    supplier_m.additional_data,
        CASE
            WHEN (supplier_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (supplier_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status,
    'SUPPLIER'::text AS jenis,
    supplier_m.supplier_id AS partner_id,
    '-'::text AS specialise_api_id
   FROM supplier_m
     LEFT JOIN propinsi_m ON supplier_m.propinsi_id = propinsi_m.propinsi_id
UNION ALL
 SELECT concat('PEN', penjamin_m.penjamin_id) AS sync_id_api,
    penjamin_m.penjamin_kode AS vendor_code,
    '-'::text AS parent_doctor_id,
    '-'::text AS doctor_code,
    penjamin_m.penjamin_nama AS name,
    penjamin_m.penjamin_nama AS display_name,
    NULL::text AS spec,
    '-'::text AS department,
    NULL::text AS type_employment,
    NULL::text AS type_doctor,
    '-'::text AS designation,
    NULL::text AS hrprofile,
    NULL::text AS gender,
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
    false AS empblocked,
    true AS active,
        CASE
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS TRUE THEN true
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS FALSE THEN false
            WHEN penjamin_m.is_active IS FALSE AND penjamin_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    carabayar_m.carabayar_nama AS customer_type_api,
    false AS vendor_wipro,
    true AS costumer_wipro,
    false AS supplier,
    true AS customer,
    true AS insurance,
    false AS patient,
    false AS doctor,
    '6'::text AS sync_type,
    penjamin_m.additional_data,
        CASE
            WHEN (penjamin_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (penjamin_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status,
    'PENJAMIN'::text AS jenis,
    penjamin_m.penjamin_id AS partner_id,
    '-'::text AS specialise_api_id
   FROM penjamin_m
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
UNION ALL
 SELECT concat('PEG', pegawai_m.pegawai_id) AS sync_id_api,
    '-'::character varying AS vendor_code,
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
    '-'::text AS contact_person,
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
        CASE
            WHEN pegawai_m.is_active = true THEN false
            ELSE true
        END AS empblocked,
    true AS active,
        CASE
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS TRUE THEN true
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS FALSE THEN false
            WHEN pegawai_m.is_active IS FALSE AND pegawai_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS customer_type_api,
    false AS vendor_wipro,
    false AS costumer_wipro,
    false AS supplier,
    false AS customer,
    false AS insurance,
    false AS patient,
    true AS doctor,
    '6'::text AS sync_type,
    pegawai_m.additional_data,
        CASE
            WHEN (pegawai_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (pegawai_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status,
    'PEGAWAI'::text AS jenis,
    pegawai_m.pegawai_id AS partner_id,
    pegawai_m.spesialis_id::text AS specialise_api_id
   FROM pegawai_m
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN kabupaten_m ON pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
UNION ALL
 SELECT concat('REF', perujuk_m.perujuk_id) AS sync_id_api,
    '-'::character varying AS vendor_code,
    '-'::text AS parent_doctor_id,
    perujuk_m.kodeppk AS doctor_code,
    perujuk_m.namaperujuk AS name,
    'EXTERNAL DOCTOR'::character varying AS display_name,
        CASE
            WHEN perujuk_m.asalrujukan_id = 5 THEN 'SPESIALIS'::character varying
            ELSE asalrujukan_m.asalrujukan_nama
        END AS spec,
    '-'::text AS department,
    'external'::text AS type_employment,
    'external'::text AS type_doctor,
    '-'::text AS designation,
    'Doctor'::text AS hrprofile,
    ''::text AS gender,
    perujuk_m.notelp AS contact_person,
    perujuk_m.notelp AS phone,
    perujuk_m.notelp AS mobile,
    '-'::text AS fax,
    '-'::character varying AS email,
    '-'::text AS website,
    'MAYAPADA'::text AS street,
    perujuk_m.alamatlengkap AS street2,
    perujuk_m.alamatlengkap AS street3,
    '-'::character varying AS city,
    '-'::text AS zip,
        CASE
            WHEN perujuk_m.is_active = true THEN false
            ELSE true
        END AS empblocked,
    true AS active,
        CASE
            WHEN perujuk_m.is_active IS TRUE AND perujuk_m.is_deleted IS TRUE THEN true
            WHEN perujuk_m.is_active IS TRUE AND perujuk_m.is_deleted IS FALSE THEN false
            WHEN perujuk_m.is_active IS FALSE AND perujuk_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS customer_type_api,
    false AS vendor_wipro,
    false AS costumer_wipro,
    false AS supplier,
    false AS customer,
    false AS insurance,
    false AS patient,
    true AS doctor,
    '6'::text AS sync_type,
    perujuk_m.additional_data,
        CASE
            WHEN (perujuk_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (perujuk_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status,
    'PERUJUK'::text AS jenis,
    perujuk_m.perujuk_id AS partner_id,
    '-'::text AS specialise_api_id
   FROM perujuk_m
     LEFT JOIN asalrujukan_m ON perujuk_m.asalrujukan_id = asalrujukan_m.asalrujukan_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220401_024439_migrate_odoo_int_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220401_024439_migrate_odoo_int_partner cannot be reverted.\n";

        return false;
    }
    */
}
