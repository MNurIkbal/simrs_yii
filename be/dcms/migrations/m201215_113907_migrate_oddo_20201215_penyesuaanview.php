<?php

use yii\db\Migration;

/**
 * Class m201215_113907_migrate_oddo_20201215_penyesuaanview
 */
class m201215_113907_migrate_oddo_20201215_penyesuaanview extends Migration
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
        CASE
            WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS TRUE THEN true
            WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS FALSE THEN false
            WHEN pasien_r.is_active IS FALSE AND pasien_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
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
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS TRUE THEN true
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS FALSE THEN false
            WHEN penjamin_m.is_active IS FALSE AND penjamin_m.is_deleted IS FALSE THEN true
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
        CASE
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS TRUE THEN true
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS FALSE THEN false
            WHEN pegawai_m.is_active IS FALSE AND pegawai_m.is_deleted IS FALSE THEN true
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

     $this->execute('DROP VIEW if exists "public"."int_tindakan_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_tindakan_v\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
    daftartindakan_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    daftartindakan_m.daftartindakan_nama AS name,
    concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
    351 AS uom_id,
    daftartindakan_m.daftartindakan_kode AS default_code,
    'service'::text AS type,
        CASE
            WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS TRUE THEN true
            WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS FALSE THEN false
            WHEN daftartindakan_m.is_active IS FALSE AND daftartindakan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'TINDAKAN'::text AS jenis,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.additional_data
   FROM daftartindakan_m
  WHERE daftartindakan_m.is_deleted = false;");

     $this->execute('ALTER TABLE "public"."int_tindakan_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_kelompoktindakan_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_kelompoktindakan_v\" AS  SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
    '-'::text AS parent_id,
    kelompoktindakan_m.kelompoktindakan_nama AS name,
    true AS sync_is_service,
    'normal'::text AS type,
    kelompoktindakan_m.is_active AS active,
    6 AS sync_type,
    kelompoktindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.additional_data,
        CASE
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS TRUE THEN true
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS FALSE THEN false
            WHEN kelompoktindakan_m.is_active IS FALSE AND kelompoktindakan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM kelompoktindakan_m
  WHERE kelompoktindakan_m.is_deleted = false;");

     $this->execute('ALTER TABLE "public"."int_kelompoktindakan_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_kelompokobat_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_kelompokobat_v\" AS  SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    '-'::text AS parent_id,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    jenisobatalkes_m.is_active AS active,
    6 AS sync_type,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.additional_data,
        CASE
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS TRUE THEN true
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS FALSE THEN false
            WHEN jenisobatalkes_m.is_active IS FALSE AND jenisobatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM jenisobatalkes_m
  WHERE jenisobatalkes_m.is_deleted = false;");

     $this->execute('ALTER TABLE "public"."int_kelompokobat_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_obat_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_obat_v\" AS  SELECT concat('OBT', obatalkes_r.obatalkes_id) AS sync_id_api,
    obatalkes_r.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_r.obatalkes_nama AS name,
    concat('OBT', obatalkes_r.jenisobatalkes_id) AS categ_id,
    obatalkes_r.satuanbesar_id AS uom_po_id,
    obatalkes_r.satuanbesar_id AS uom2_id,
    obatalkes_r.satuankecil_id AS uom_id,
    obatalkes_r.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS TRUE THEN true
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS FALSE THEN false
            WHEN obatalkes_r.is_active IS FALSE AND obatalkes_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_r.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    obatalkes_r.kemasan_besar AS conversion_rate,
    6 AS sync_type,
    obatalkes_r.keterangan_rekap,
    obatalkes_r.id,
    obatalkes_r.is_sent,
    obatalkes_r.is_sending
   FROM obatalkes_r;");

     $this->execute('ALTER TABLE "public"."int_obat_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_paket_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_paket_v\" AS  SELECT concat('PKT', tipepaket_m.tipepaket_id) AS sync_id_api,
    tipepaket_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    tipepaket_m.tipepaket_nama AS name,
    351 AS uom_id,
    tipepaket_m.tipepaket_kode AS default_code,
    'service'::text AS type,
        CASE
            WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS TRUE THEN true
            WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS FALSE THEN false
            WHEN tipepaket_m.is_active IS FALSE AND tipepaket_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'PAKET'::text AS jenis,
    tipepaket_m.tipepaket_id,
    tipepaket_m.additional_data
   FROM tipepaket_m
  WHERE tipepaket_m.is_deleted = false;");

     $this->execute('ALTER TABLE "public"."int_paket_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if  exists "public"."int_satuanunit_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_satuanunit_v\" AS  SELECT satuanunit_r.satuanunit_id AS sync_id_api,
    satuanunit_r.satuanunit_nama AS name,
    satuanunit_r.satuanunit_namalain AS code,
    satuanunit_r.is_active AS active,
    1 AS factor,
    'reference'::text AS uom_type,
    1 AS category_id,
    6 AS sync_type,
    satuanunit_r.keterangan_rekap,
    satuanunit_r.id,
    satuanunit_r.is_sent,
    satuanunit_r.is_sending,
        CASE
            WHEN satuanunit_r.is_active IS TRUE AND satuanunit_r.is_deleted IS TRUE THEN true
            WHEN satuanunit_r.is_active IS TRUE AND satuanunit_r.is_deleted IS FALSE THEN false
            WHEN satuanunit_r.is_active IS FALSE AND satuanunit_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM satuanunit_r;");

     $this->execute('ALTER TABLE "public"."int_satuanunit_v" OWNER TO "postgres";');

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
    ruangan_m.is_cartstore,
        CASE
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS TRUE THEN true
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS FALSE THEN false
            WHEN ruangan_m.is_active IS FALSE AND ruangan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM ruangan_m
  WHERE ruangan_m.is_deleted = false;");

     $this->execute('ALTER TABLE "public"."int_ruangan_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_supplier_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_supplier_v\" AS  SELECT concat('SUP', supplier_r.supplier_id) AS sync_id_api,
    supplier_r.supplier_kode AS vendor_code,
    supplier_r.supplier_nama AS name,
    supplier_r.supplier_nama AS display_name,
    'CORPORATE'::text AS customer_type_api,
    supplier_r.no_tlp AS contact_person,
    supplier_r.no_tlp AS phone,
    supplier_r.no_tlp AS mobile,
    supplier_r.no_fax AS fax,
    supplier_r.email,
    supplier_r.website,
    supplier_r.supplier_alamat AS street,
    supplier_r.supplier_alamat AS street2,
    supplier_r.supplier_alamat AS street3,
    propinsi_m.propinsi_nama AS city,
    NULL::text AS zip,
    supplier_r.credit_limit AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS astaxid,
        CASE
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS TRUE THEN true
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS FALSE THEN false
            WHEN supplier_r.is_active IS FALSE AND supplier_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    true AS insruance,
    true AS customer,
    supplier_r.is_active AS active,
    6 AS sync_type,
    supplier_r.keterangan_rekap,
    supplier_r.id,
    supplier_r.is_sent,
    supplier_r.is_sending
   FROM supplier_r
     LEFT JOIN propinsi_m ON supplier_r.propinsi_id = propinsi_m.propinsi_id;");

     $this->execute('ALTER TABLE "public"."int_supplier_v" OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201215_113907_migrate_oddo_20201215_penyesuaanview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_113907_migrate_oddo_20201215_penyesuaanview cannot be reverted.\n";

        return false;
    }
    */
}
