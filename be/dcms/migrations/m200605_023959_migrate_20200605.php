<?php

use yii\db\Migration;

/**
 * Class m200605_023959_migrate_20200605
 */
class m200605_023959_migrate_20200605 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN "formulir_triage" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN "formulir_fisik" text COLLATE "pg_catalog"."default";');
        
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" ADD COLUMN "is_publishbpjs" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" ADD COLUMN "is_publishtelemedicine" bool DEFAULT false;');
        
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "kode_ecatalog" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_oral" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_antibiotic" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_psycothropica" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_prescibe_item" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_allow_franction" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_lasa" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_expiry" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_highalert" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_consigment" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "is_embalase" bool;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "strength" numeric(15,2) DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN "strength_uom" varchar(255) COLLATE "pg_catalog"."default";');
        
        $this->execute('ALTER TABLE "public"."pegawai_m" ADD COLUMN "is_publishweb" bool DEFAULT false;');

        $this->execute('ALTER TABLE "public"."ruangan_m" ADD COLUMN "lantai_id" int4;');

        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "negara_id" int4;');
        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "website" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "nama_pic" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "credit_limit" int4;');
        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "remarks" varchar(255) COLLATE "pg_catalog"."default";');

        $this->execute('DROP VIEW if exists "public"."infojanjipoli_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infojanjipoli_v\" AS  SELECT '673'::text AS transaksi_konsul,
    buatjanjipoli_t.buatjanjipoli_id,
    buatjanjipoli_t.tgl_buatjanji,
    buatjanjipoli_t.antrian_id,
    antrian_t.no_antrian,
    buatjanjipoli_t.pegawai_id,
    pegawai_m.nama_pegawai,
    buatjanjipoli_t.ruangan_id,
    ruangan_m.ruangan_nama,
    buatjanjipoli_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    fgetnamalookup((buatjanjipoli_t.hari_jadwal)::integer) AS hari,
    buatjanjipoli_t.tgl_jadwal,
    buatjanjipoli_t.is_rencanakontrol,
    fgetnamalookup((buatjanjipoli_t.status_janjipoli)::integer) AS status_janji,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    buatjanjipoli_t.carabayar_id,
    carabayar_m.carabayar_nama,
    buatjanjipoli_t.penjamin_id,
    penjamin_m.penjamin_nama,
    buatjanjipoli_t.keterangan_buatjanji,
    buatjanjipoli_t.by_phone,
    NULL::smallint AS status_approve,
    NULL::character varying AS status_approve_nama,
    NULL::bigint AS asalpoliklinikkonsul_id,
    NULL::character varying AS ruangan_asal,
    NULL::integer AS doktermengkonsul_id,
    NULL::character varying AS doktermengkonsul
   FROM ((((((((buatjanjipoli_t
     JOIN pasien_m ON ((buatjanjipoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((buatjanjipoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((buatjanjipoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN antrian_t ON (((buatjanjipoli_t.antrian_id)::integer = antrian_t.antrian_id)))
     LEFT JOIN pendaftaran_t ON ((buatjanjipoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN carabayar_m ON ((buatjanjipoli_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((buatjanjipoli_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((buatjanjipoli_t.is_active = true) AND (buatjanjipoli_t.is_deleted = false))
UNION ALL
 SELECT '672'::text AS transaksi_konsul,
    konsulpoli_t.konsulpoli_id AS buatjanjipoli_id,
    konsulpoli_t.tgl_konsulpoli AS tgl_buatjanji,
    NULL::text AS antrian_id,
    NULL::text AS no_antrian,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai,
    konsulpoli_t.ruangan_id,
    ruangan_tujuan.ruangan_nama,
    konsulpoli_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    NULL::text AS hari,
    konsulpoli_t.tgl_konsulpoli AS tgl_jadwal,
    NULL::boolean AS is_rencanakontrol,
    NULL::text AS status_janji,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_tujuan.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    konsulpoli_t.catatan_dokter_konsul AS keterangan_buatjanji,
    NULL::boolean AS by_phone,
    konsulpoli_t.status_approve,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve_nama,
    konsulpoli_t.asalpoliklinikkonsul_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    pendaftaran_t.pegawai_id AS doktermengkonsul_id,
    dok_mengkonsul.nama_pegawai AS doktermengkonsul
   FROM (((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN ruangan_m ruangan_tujuan ON ((konsulpoli_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
  WHERE (konsulpoli_t.status_approve = 564);");

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama
   FROM (((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12))
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ruangan_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (ruangan_penunjang.instalasi_id = 12) AND (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (pendaftaran_t.instalasi_id <> 12));");
        
        $this->execute('CREATE TABLE "public"."jenisantriandetail_m" (
  "jenisantriandetail_id" serial8,
  "jenisantrian_id" int4,
  "jenisantrian_nama" varchar(100) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "jenisantriandetail_m_pkey" PRIMARY KEY ("jenisantriandetail_id")
)
;');
        $this->execute('COMMENT ON COLUMN "public"."jenisantriandetail_m"."jenisantrian_id" IS \'lookup_type=\'\'jenis_antrian\'\'\';');
        $this->execute('COMMENT ON COLUMN "public"."jenisantriandetail_m"."jenisantrian_nama" IS \'lookup_type=\'\'jenis_antrian\'\'\';');

        $this->execute('CREATE TABLE "public"."jenisantrianruangan_mp" (
  "jenisantriandetail_id" int4 NOT NULL,
  "ruangan_id" int4 NOT NULL,
  CONSTRAINT "jenisantrianruangan_mp_pkey" PRIMARY KEY ("jenisantriandetail_id", "ruangan_id")
)
;');

        $this->execute('CREATE TABLE "public"."lantai_m" (
  "lantai_id" serial8,
  "lantai_nama" varchar(255) COLLATE "pg_catalog"."default",
  "gedung" varchar(255) COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "lantai_m_pkey" PRIMARY KEY ("lantai_id")
)
;');
        $this->execute("
            CREATE VIEW \"public\".\"timoperasi_v\" AS  SELECT timoperasi_t.timoperasi_id,
    timoperasi_t.pasienmasukpenunjang_id,
    timoperasi_t.inpostoperasi_id,
    operasi_m.operasi_id,
    operasi_m.operasi_nama,
    golonganoperasi_m.golonganoperasi_id AS jenisoperasi_id,
    golonganoperasi_m.golonganoperasi_nama AS jenisoperasi_nama,
    inpostoperasidetail_t.daftartindakan_id AS klasifikasioperasi_id,
    daftartindakan_m.daftartindakan_nama AS klasifikasioperasi_nama,
    jasa.daftartindakan_nama AS nama_jasa,
    pegawai_m.nama_pegawai,
    fgetnamalookup(timoperasi_t.posisi_tim) AS posisi_tim,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    inpostoperasidetail_t.harga AS harga_operasi,
    timoperasi_t.persentase,
    timoperasi_t.harga AS harga_persentase
   FROM ((((((((((timoperasi_t
     JOIN inpostoperasi_t ON (((timoperasi_t.inpostoperasi_id = inpostoperasi_t.inpostoperasi_id) AND (timoperasi_t.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id))))
     JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     JOIN tindakanoperasi_mp ON ((timoperasi_t.posisi_tim = tindakanoperasi_mp.timoperasi_id)))
     JOIN daftartindakan_m jasa ON ((tindakanoperasi_mp.daftartindakan_id = jasa.daftartindakan_id)))
     LEFT JOIN operasi_m ON ((inpostoperasidetail_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((timoperasi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN daftartindakan_m ON ((inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)));");

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200605_023959_migrate_20200605 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200605_023959_migrate_20200605 cannot be reverted.\n";

        return false;
    }
    */
}
