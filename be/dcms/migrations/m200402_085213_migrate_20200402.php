<?php

use yii\db\Migration;

/**
 * Class m200402_085213_migrate_20200402
 */
class m200402_085213_migrate_20200402 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."jenisnontunai_m" (
  "jenisnontunai_id" serial8,
  "kode" varchar(30) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default",
  "bank_id" int4,
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
  CONSTRAINT "jenisnontunai_m_pkey" PRIMARY KEY ("jenisnontunai_id")
)
;');

        $this->execute('ALTER TABLE "public"."jenisnontunai_m" OWNER TO "postgres";');

        $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'bank_m\');');

        $this->execute('ALTER TABLE "public"."bank_m" 
                          ALTER COLUMN "no_rekening" TYPE varchar(100) COLLATE "pg_catalog"."default";
                        ');

        $this->execute('select public.deps_restore_dependencies(\'public\', \'bank_m\');');

        $this->execute('ALTER TABLE "public"."bayaruangmuka_t" ADD COLUMN "metode_pembayaran" int2;');

        $this->execute('ALTER TABLE "public"."bayaruangmuka_t" ADD COLUMN "jenisnontunai_id" int4;');

        $this->execute('COMMENT ON COLUMN "public"."bayaruangmuka_t"."metode_pembayaran" IS \'lookup_type = metode_bayar\';');

        $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD COLUMN "jenisnontunai_id" int4;');

        $this->execute('ALTER TABLE "public"."pembayaranpiutang_t" ADD COLUMN "metode_pembayaran" int2;');

        $this->execute('ALTER TABLE "public"."pembayaranpiutang_t" ADD COLUMN "jenisnontunai_id" int4;');

        $this->execute('COMMENT ON COLUMN "public"."pembayaranpiutang_t"."metode_pembayaran" IS \'lookup_type = metode_bayar\';');

        $this->execute('ALTER TABLE "public"."pembayarantransaksi_t" ADD COLUMN "jenisnontunai_id" int4;');

        $this->execute('DROP VIEW if exists "public"."infoclosingkasir_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoclosingkasir_v\" AS  SELECT 'PEMBAYARAN'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    COALESCE((pembayaran_t.total_dijamin + pemberianpiutang_t.total_piutang), (0)::double precision) AS total_dijamin
   FROM ((((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'UANG_MASUK'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    bayaruangmuka_t.jumlah_uangmuka AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    bayaruangmuka_t.jumlah_uangmuka AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'RETUR'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktikeluar_t.jml_pembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktikeluar_t.uang_diterima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
    (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
    0 AS total_dijamin
   FROM (((((((((((((closingkasir_t
     JOIN tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
     JOIN returbayarpelayanan_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'PEMBAYARAN_PIUTANG'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN pemberianpiutang_t.penjualanresep_id
            ELSE pemberianpiutang_t.pendaftaran_id
        END AS pendaftaran_id,
    pemberianpiutang_t.no_pemberianpiutang AS no_pendaftaran,
    pasien_m.pasien_id,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN carabayar_resep.carabayar_nama
            ELSE carabayar_m.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjamin_resep.penjamin_nama
            ELSE penjamin_m.penjamin_nama
        END AS penjamin_nama,
    0 AS total_tagihan,
    pembayaranpiutang_t.total_bayarpiutang AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpiutang_t ON ((tandabuktibayar_t.pembayaranpiutang_id = pembayaranpiutang_t.pembayaranpiutang_id)))
     JOIN pemberianpiutang_t ON ((pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN penjualanresep_t ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_resep ON ((penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_resep ON ((penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'PEMBAYARAN_RESEP_BEBAS'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    NULL::integer AS pasien_id,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    pemberianpiutang_t.total_piutang AS total_dijamin
   FROM (((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'penerimaan'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    tandabuktibayar_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    penerimaan.pembayarantransaksi_id AS pendaftaran_id,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN supplier_m.supplier_kode
            WHEN (penerimaan.tipe_transaksi = 701) THEN peg_penerimaan.nomorindukpegawai
            WHEN (penerimaan.tipe_transaksi = 702) THEN pasien_m.no_rekam_medik
            ELSE NULL::character varying
        END AS no_pendaftaran,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN penerimaan.supplier_id
            WHEN (penerimaan.tipe_transaksi = 701) THEN penerimaan.pegawai_id
            WHEN (penerimaan.tipe_transaksi = 702) THEN penerimaan.pasien_id
            ELSE NULL::integer
        END AS pasien_id,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (penerimaan.tipe_transaksi = 701) THEN peg_penerimaan.nama_pegawai
            WHEN (penerimaan.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    NULL::character varying AS carabayar_nama,
    NULL::character varying AS penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (penerimaan.metode_pembayaran = 27) THEN penerimaan.jumlah
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (penerimaan.metode_pembayaran = 28) THEN penerimaan.jumlah
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayarantransaksi_t penerimaan ON ((tandabuktibayar_t.penerimaanumum_id = penerimaan.pembayarantransaksi_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_penerimaan ON ((penerimaan.pegawai_id = peg_penerimaan.pegawai_id)))
     LEFT JOIN pasien_m ON ((penerimaan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN supplier_m ON ((penerimaan.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
UNION ALL
 SELECT 'pengeluaran'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    tandabuktikeluar_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktikeluar_t.jml_pembayaran AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pengeluaran.pembayarantransaksi_id AS pendaftaran_id,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN supplier_m.supplier_kode
            WHEN (pengeluaran.tipe_transaksi = 701) THEN peg_pengeluaran.nomorindukpegawai
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pasien_m.no_rekam_medik
            ELSE NULL::character varying
        END AS no_pendaftaran,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN pengeluaran.supplier_id
            WHEN (pengeluaran.tipe_transaksi = 701) THEN pengeluaran.pegawai_id
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pengeluaran.pasien_id
            ELSE NULL::integer
        END AS pasien_id,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (pengeluaran.tipe_transaksi = 701) THEN peg_pengeluaran.nama_pegawai
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS nama_pasien,
    tandabuktikeluar_t.uang_diterima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    NULL::character varying AS carabayar_nama,
    NULL::character varying AS penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (pengeluaran.metode_pembayaran = 27) THEN (- pengeluaran.jumlah)
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (pengeluaran.metode_pembayaran = 28) THEN (- pengeluaran.jumlah)
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
     JOIN pembayarantransaksi_t pengeluaran ON ((tandabuktikeluar_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((tandabuktikeluar_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_pengeluaran ON ((pengeluaran.pegawai_id = peg_pengeluaran.pegawai_id)))
     LEFT JOIN pasien_m ON ((pengeluaran.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN supplier_m ON ((pengeluaran.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)));");

        $this->execute('ALTER TABLE "public"."infoclosingkasir_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infokartustokobatalkes2_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infokartustokobatalkes2_v\" AS  SELECT stok_oa.stokobatalkes_id,
    stok_oa.obatalkes_id,
    stok_oa.tanggal_transaksi,
    stok_oa.no_transakasi,
    obatalkes_m.obatalkes_nama,
    stok_oa.qtystok_in,
    stok_oa.qtystok_out,
    stok_oa.stok,
    satuanunit_m.satuanunit_nama,
    stok_oa.tglkadaluarsa,
    stok_oa.keterangan,
    stok_oa.ruangan_asal_id,
    stok_oa.ruangan_tujuan_id,
    ruangan_asal.ruangan_nama AS ruangan_asal_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
    stok_oa.ruangan_id,
        CASE stok_oa.keterangan
            WHEN 'Penerimaan Mutasi'::text THEN ruangan_asal.ruangan_nama
            WHEN 'Mutasi Obat'::text THEN ruangan_tujuan.ruangan_nama
            WHEN 'Penjualan Resep'::text THEN stok_oa.nama_pasien
            WHEN 'Batal Penjualan'::text THEN stok_oa.nama_pasien_batal
            WHEN 'Retur Resep'::text THEN stok_oa.nama_pasien_retur
            ELSE '-'::character varying
        END AS reference,
    stok_oa.stok_tersedia
   FROM ((((( SELECT stokobatalkes_t.stokobatalkes_id,
            stokobatalkes_t.obatalkes_id,
                CASE
                    WHEN (stokobatalkes_t.is_deleted = true) THEN (0)::double precision
                    ELSE stokobatalkes_t.qtystok_in
                END AS qtystok_in,
                CASE
                    WHEN (stokobatalkes_t.is_deleted = true) THEN (0)::double precision
                    ELSE stokobatalkes_t.qtystok_out
                END AS qtystok_out,
            stokobatalkes_t.stok,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.ruangan_id,
                CASE
                    WHEN (stokobatalkes_t.tglstok_in IS NOT NULL) THEN (stokobatalkes_t.tglstok_in)::text
                    WHEN (stokobatalkes_t.tglstok_out IS NOT NULL) THEN (stokobatalkes_t.tglstok_out)::text
                    ELSE ''::text
                END AS tanggal_transaksi,
                CASE
                    WHEN (penerimaan_obat.no_penerimaan IS NOT NULL) THEN (penerimaan_obat.no_penerimaan)::text
                    WHEN (terima_mutasi.noterimamutasi IS NOT NULL) THEN (terima_mutasi.noterimamutasi)::text
                    WHEN (retur_resep.no_returresep IS NOT NULL) THEN (retur_resep.no_returresep)::text
                    WHEN (retur_penerimaan.no_returpenerimaanobat IS NOT NULL) THEN (retur_penerimaan.no_returpenerimaanobat)::text
                    WHEN (mutasi_obat.nomutasioa IS NOT NULL) THEN (mutasi_obat.nomutasioa)::text
                    WHEN ((penjualan.no_penjualan IS NOT NULL) AND (retur_resep.no_returresep IS NULL) AND (batal_resep.no_pembatalan IS NULL)) THEN (penjualan.no_penjualan)::text
                    WHEN (pemusnahan_obat.nopemusnahan IS NOT NULL) THEN (pemusnahan_obat.nopemusnahan)::text
                    WHEN (stok_opname.nostokopname IS NOT NULL) THEN (stok_opname.nostokopname)::text
                    WHEN (pemakaian_obat.nopemakaian_obat IS NOT NULL) THEN (pemakaian_obat.nopemakaian_obat)::text
                    WHEN (produksi_obat.no_produksiobat IS NOT NULL) THEN (produksi_obat.no_produksiobat)::text
                    WHEN (store_expire.no_storexpired IS NOT NULL) THEN (store_expire.no_storexpired)::text
                    WHEN (penerimaan_supp.no_penerimaan IS NOT NULL) THEN (penerimaan_supp.no_penerimaan)::text
                    WHEN (adjustment_masuk.no_adjusmen IS NOT NULL) THEN (adjustment_masuk.no_adjusmen)::text
                    WHEN (adjustment_keluar.no_adjusmen IS NOT NULL) THEN (adjustment_keluar.no_adjusmen)::text
                    WHEN (batal_resep.no_pembatalan IS NOT NULL) THEN (batal_resep.no_pembatalan)::text
                    ELSE ''::text
                END AS no_transakasi,
                CASE
                    WHEN (stokobatalkes_t.is_deleted = true) THEN 'Batal Transaksi'::text
                    WHEN (penerimaan_obat.penerimaanobatdetail_id IS NOT NULL) THEN 'Penerimaan Supplier'::text
                    WHEN (terima_mutasi.terimamutasiobatdetail_id IS NOT NULL) THEN 'Penerimaan Mutasi'::text
                    WHEN (retur_resep.returresepdetail_id IS NOT NULL) THEN 'Retur Resep'::text
                    WHEN (retur_penerimaan.returpenerimaanobatdetail_id IS NOT NULL) THEN 'Retur Penerimaan Supplier'::text
                    WHEN (mutasi_obat.mutasiobatdetail_id IS NOT NULL) THEN 'Mutasi Obat'::text
                    WHEN ((penjualan.obatalkespasien_id IS NOT NULL) AND (penjualan.penjualanresep_id IS NOT NULL) AND (batal_resep.pembatalanresep_id IS NULL) AND (retur_resep.returresepdetail_id IS NULL)) THEN 'Penjualan Resep'::text
                    WHEN ((penjualan.obatalkespasien_id IS NOT NULL) AND (penjualan.penjualanresep_id IS NULL)) THEN 'BMHP'::text
                    WHEN (pemusnahan_obat.pemusnahanobatdetail_id IS NOT NULL) THEN 'Pemusnahan Obat'::text
                    WHEN (stok_opname.stokopnamedetail_id IS NOT NULL) THEN 'Stok Opname'::text
                    WHEN (pemakaian_obat.pemakaianobatdetail_id IS NOT NULL) THEN 'Pemakaian Ruangan'::text
                    WHEN (produksi_obat.produksiobatdetail_id IS NOT NULL) THEN 'Produksi Obat'::text
                    WHEN (store_expire.storexpiredobatdetail_id IS NOT NULL) THEN 'Store Expired Obat'::text
                    WHEN (penerimaan_supp.penerimaansuppdetail_id IS NOT NULL) THEN 'Penerimaan Alternatif'::text
                    WHEN (adjustment_masuk.adjusmenobatmasuk_id IS NOT NULL) THEN 'Adjusmen Obat Masuk'::text
                    WHEN (adjustment_keluar.adjusmenobatkeluar_id IS NOT NULL) THEN 'Adjusmen Obat Keluar'::text
                    WHEN (batal_resep.pembatalanresep_id IS NOT NULL) THEN 'Batal Penjualan'::text
                    ELSE '-'::text
                END AS keterangan,
                CASE
                    WHEN (mutasi_obat.ruanganasal_id IS NOT NULL) THEN COALESCE(mutasi_obat.ruanganasal_id, 0)
                    WHEN (terima_mutasi.ruanganasal_id IS NOT NULL) THEN COALESCE(terima_mutasi.ruanganasal_id, 0)
                    ELSE COALESCE(stokobatalkes_t.ruangan_id, 0)
                END AS ruangan_asal_id,
                CASE
                    WHEN (mutasi_obat.ruangantujuan_id IS NOT NULL) THEN COALESCE(mutasi_obat.ruangantujuan_id, 0)
                    WHEN (terima_mutasi.ruanganpenerima_id IS NOT NULL) THEN COALESCE(terima_mutasi.ruanganpenerima_id, 0)
                    ELSE COALESCE(stokobatalkes_t.ruangan_id, 0)
                END AS ruangan_tujuan_id,
            penjualan.nama_pasien,
            batal_resep.nama_pasien_batal,
            retur_resep.nama_pasien_retur,
            stokobatalkes_t.stok_tersedia
           FROM (((((((((((((((stokobatalkes_t
             LEFT JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                    penerimaanobat_t.no_penerimaan
                   FROM (penerimaanobat_t
                     JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))) penerimaan_obat ON ((stokobatalkes_t.penerimaanobatdetail_id = penerimaan_obat.penerimaanobatdetail_id)))
             LEFT JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                    terimamutasiobat_t.noterimamutasi,
                    terimamutasiobat_t.ruanganpenerima_id,
                    terimamutasiobat_t.ruanganasal_id
                   FROM (terimamutasiobat_t
                     JOIN terimamutasiobatdetail_t ON ((terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id)))) terima_mutasi ON ((terima_mutasi.terimamutasiobatdetail_id = stokobatalkes_t.terimamutasidetail_id)))
             LEFT JOIN ( SELECT returresepdetail_t.returresepdetail_id,
                    returresep_t.no_returresep,
                        CASE
                            WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
                            ELSE pasien_m.nama_pasien
                        END AS nama_pasien_retur
                   FROM (((((returresep_t
                     JOIN returresepdetail_t ON ((returresep_t.returresep_id = returresepdetail_t.returresep_id)))
                     LEFT JOIN obatalkespasien_t ON ((obatalkespasien_t.obatalkespasien_id = returresepdetail_t.obatalkespasien_id)))
                     LEFT JOIN penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                     LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = penjualanresep_t.pendaftaran_id)))) retur_resep ON ((retur_resep.returresepdetail_id = stokobatalkes_t.returresepdetail_id)))
             LEFT JOIN ( SELECT returpenerimaanobat_t.no_returpenerimaanobat,
                    returpenerimaanobatdetail_t.returpenerimaanobatdetail_id
                   FROM (returpenerimaanobat_t
                     JOIN returpenerimaanobatdetail_t ON ((returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id)))) retur_penerimaan ON ((retur_penerimaan.returpenerimaanobatdetail_id = stokobatalkes_t.returpenerimaanobatdetail_id)))
             LEFT JOIN ( SELECT mutasiobatruangan_t.nomutasioa,
                    mutasiobatdetail_t.mutasiobatdetail_id,
                    mutasiobatruangan_t.ruanganasal_id,
                    mutasiobatruangan_t.ruangantujuan_id
                   FROM (mutasiobatruangan_t
                     JOIN mutasiobatdetail_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))) mutasi_obat ON ((mutasi_obat.mutasiobatdetail_id = stokobatalkes_t.mutasiobatdetail_id)))
             LEFT JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                        CASE
                            WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
                            ELSE pasien_m.nama_pasien
                        END AS nama_pasien,
                    obatalkespasien_t.penjualanresep_id,
                        CASE
                            WHEN (penjualanresep_t.noresep IS NOT NULL) THEN penjualanresep_t.noresep
                            ELSE pendaftaran_t.no_pendaftaran
                        END AS no_penjualan
                   FROM (((obatalkespasien_t
                     LEFT JOIN pasien_m ON ((obatalkespasien_t.pasien_id = pasien_m.pasien_id)))
                     LEFT JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))) penjualan ON ((penjualan.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id)))
             LEFT JOIN ( SELECT pemusnahanobat_t.nopemusnahan,
                    pemusnahanobatdetail_t.pemusnahanobatdetail_id
                   FROM (pemusnahanobat_t
                     JOIN pemusnahanobatdetail_t ON ((pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id)))) pemusnahan_obat ON ((pemusnahan_obat.pemusnahanobatdetail_id = stokobatalkes_t.pemusnahanobatdetail_id)))
             LEFT JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                    stokopname_t.nostokopname
                   FROM (stokopname_t
                     JOIN stokopnamedetail_t ON ((stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id)))) stok_opname ON ((stok_opname.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id)))
             LEFT JOIN ( SELECT pemakaianobat_t.nopemakaian_obat,
                    pemakaianobatdetail_t.pemakaianobatdetail_id
                   FROM (pemakaianobat_t
                     JOIN pemakaianobatdetail_t ON ((pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id)))) pemakaian_obat ON ((pemakaian_obat.pemakaianobatdetail_id = stokobatalkes_t.pemakaianobatdetail_id)))
             LEFT JOIN ( SELECT produksiobat_t.no_produksiobat,
                    produksiobatdetail_t.produksiobatdetail_id
                   FROM (produksiobat_t
                     JOIN produksiobatdetail_t ON ((produksiobatdetail_t.produksiobat_id = produksiobat_t.produksiobat_id)))) produksi_obat ON ((produksi_obat.produksiobatdetail_id = stokobatalkes_t.produksiobatdetail_id)))
             LEFT JOIN ( SELECT storexpiredobat_t.no_storexpired,
                    storexpiredobatdetail_t.storexpiredobatdetail_id
                   FROM (storexpiredobat_t
                     JOIN storexpiredobatdetail_t ON ((storexpiredobatdetail_t.storexpiredobat_id = storexpiredobat_t.storexpiredobat_id)))) store_expire ON ((store_expire.storexpiredobatdetail_id = stokobatalkes_t.storexpiredobatdetail_id)))
             LEFT JOIN ( SELECT penerimaansupp_t.no_penerimaan,
                    penerimaansuppdetail_t.penerimaansuppdetail_id
                   FROM (penerimaansupp_t
                     JOIN penerimaansuppdetail_t ON ((penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id)))) penerimaan_supp ON ((penerimaan_supp.penerimaansuppdetail_id = stokobatalkes_t.penerimaansuppdetail_id)))
             LEFT JOIN ( SELECT adjusmenobat_t.no_adjusmen,
                    adjusmenobatmasuk_t.adjusmenobatmasuk_id
                   FROM (adjusmenobat_t
                     JOIN adjusmenobatmasuk_t ON ((adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjustment_masuk ON ((adjustment_masuk.adjusmenobatmasuk_id = stokobatalkes_t.adjusmenobatmasuk_id)))
             LEFT JOIN ( SELECT adjusmenobat_t.no_adjusmen,
                    adjusmenobatkeluar_t.adjusmenobatkeluar_id
                   FROM (adjusmenobat_t
                     JOIN adjusmenobatkeluar_t ON ((adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjustment_keluar ON ((adjustment_keluar.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id)))
             LEFT JOIN ( SELECT penjualanresep_t.pembatalanresep_id,
                    pembatalanresep_t.no_pembatalan,
                        CASE
                            WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
                            ELSE pasien_m.nama_pasien
                        END AS nama_pasien_batal
                   FROM (((pembatalanresep_t
                     JOIN penjualanresep_t ON ((penjualanresep_t.pembatalanresep_id = pembatalanresep_t.pembatalanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                     LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = penjualanresep_t.pendaftaran_id)))) batal_resep ON ((batal_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id)))) stok_oa
     JOIN obatalkes_m ON ((obatalkes_m.obatalkes_id = stok_oa.obatalkes_id)))
     JOIN satuanunit_m ON ((obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id)))
     JOIN ruangan_m ruangan_asal ON ((ruangan_asal.ruangan_id = stok_oa.ruangan_asal_id)))
     JOIN ruangan_m ruangan_tujuan ON ((ruangan_tujuan.ruangan_id = stok_oa.ruangan_tujuan_id)));
");

        $this->execute('ALTER TABLE "public"."infokartustokobatalkes2_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infotarifrs_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotarifrs_v\" AS  SELECT 'tindakan'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    r_tindakan.ruangan_nama,
    r_tindakan.instalasi_id,
    ins_tindakan.instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    daftartindakan_m.is_akomodasi,
    penjamin_m.carabayar_id,
    daftartindakan_m.is_konsultasi
   FROM ((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON (((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_deleted IS FALSE))))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN tindakanruangan_mp ON ((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id)))
     JOIN ruangan_m r_tindakan ON ((tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id)))
     JOIN instalasi_m ins_tindakan ON ((r_tindakan.instalasi_id = ins_tindakan.instalasi_id)))
  WHERE ((tindakanruangan_mp.is_deleted = false) AND (perdatarif_m.is_active = true) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))
UNION ALL
 SELECT 'paket'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    r_paket.ruangan_nama,
    r_paket.instalasi_id,
    ins_paket.instalasi_nama,
    paketruangan_mp.ruangan_id AS ruanganpaket_id,
    r_paket.ruangan_namalainnya AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama,
    NULL::integer AS kategoritindakan_id,
    NULL::character varying AS kategoritindakan_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    NULL::boolean AS is_akomodasi,
    penjamin_m.carabayar_id,
    false AS is_konsultasi
   FROM ((((((((tariftindakan_m
     JOIN tipepaket_m ON ((tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON (((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_deleted IS FALSE))))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN paketruangan_mp ON ((tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id)))
     JOIN ruangan_m r_paket ON ((paketruangan_mp.ruangan_id = r_paket.ruangan_id)))
     JOIN instalasi_m ins_paket ON ((r_paket.instalasi_id = ins_paket.instalasi_id)))
  WHERE ((paketruangan_mp.is_deleted = false) AND (perdatarif_m.is_active = true) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))
UNION ALL
 SELECT 'paket'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    r_paket.ruangan_nama,
    r_paket.instalasi_id,
    ins_paket.instalasi_nama,
    paketruangan_mp.ruangan_id AS ruanganpaket_id,
    r_paket.ruangan_namalainnya AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama,
    NULL::integer AS kategoritindakan_id,
    NULL::character varying AS kategoritindakan_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    NULL::boolean AS is_akomodasi,
    penjamin_m.carabayar_id,
    false AS is_konsultasi
   FROM ((((((((tariftindakan_m
     JOIN tipepaket_m ON ((tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON (((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_deleted IS FALSE))))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN paketruangan_mp ON ((tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id)))
     JOIN ruangan_m r_paket ON ((paketruangan_mp.ruangan_id = r_paket.ruangan_id)))
     JOIN instalasi_m ins_paket ON ((r_paket.instalasi_id = ins_paket.instalasi_id)))
  WHERE ((paketruangan_mp.is_deleted = false) AND (perdatarif_m.is_active = true) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NOT NULL));");
        
        $this->execute('ALTER TABLE "public"."infotarifrs_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."closing_kasir_view";');
        
        $this->execute("
            CREATE VIEW \"public\".\"closing_kasir_view\" AS  SELECT agr_bukti_bayar.jenis,
    agr_bukti_bayar.tandabuktibayar_id,
    agr_bukti_bayar.tandabuktikeluar_id,
    agr_bukti_bayar.ruangan_id,
    agr_bukti_bayar.bayaruangmuka_id,
    agr_bukti_bayar.closingkasir_id,
    agr_bukti_bayar.pembayaranpelayanan_id,
    agr_bukti_bayar.shift_id,
    agr_bukti_bayar.nourutkasir,
    agr_bukti_bayar.nobuktibayar,
    agr_bukti_bayar.tglbuktibayar,
    agr_bukti_bayar.uangditerima,
    agr_bukti_bayar.pendaftaran_id,
        CASE
            WHEN (agr_bukti_bayar.jenis = 'PEMBAYARAN_PIUTANG'::text) THEN agr_bukti_bayar.no_identitas
            WHEN ((pendaftaran_t.no_pendaftaran IS NULL) AND (penjualanresep_t.noresep IS NULL)) THEN agr_bukti_bayar.no_identitas
            WHEN (pendaftaran_t.no_pendaftaran IS NULL) THEN (penjualanresep_t.noresep)::text
            WHEN (penjualanresep_t.noresep IS NULL) THEN (pendaftaran_t.no_pendaftaran)::text
            ELSE NULL::text
        END AS no_pendaftaran,
        CASE
            WHEN ((pendaftaran_t.no_pendaftaran IS NULL) AND (penjualanresep_t.noresep IS NULL)) THEN agr_bukti_bayar.nama_identitas
            WHEN (pasien_m.nama_pasien IS NULL) THEN (penjualanresep_t.nama_pembeli)::text
            WHEN (penjualanresep_t.nama_pembeli IS NULL) THEN (pasien_m.nama_pasien)::text
            ELSE NULL::text
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
    COALESCE(agr_bukti_bayar.total_tagihan, (0)::double precision) AS jmlpembayaran,
    COALESCE(agr_bukti_bayar.total_tunai, (0)::double precision) AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, (0)::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, (0)::double precision) AS pembayaran_penjamin,
    agr_bukti_bayar.pembayaran_id
   FROM (((( SELECT 'PEMBAYARAN'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
            NULL::integer AS tandabuktikeluar_id,
            tandabuktibayar_t.ruangan_id,
            NULL::integer AS bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            0 AS jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
            pembayaran_penjamin.total_tunai,
            pembayaran_penjamin.total_tagihan,
            pembayaran_penjamin.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM ((((tandabuktibayar_t
             JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN ( SELECT (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    pembayaran_t.total_nontunai,
                    pembayaran_t.total_ditagihkan,
                    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
                    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan
                   FROM ((pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE (pembayaranmetode_t.is_deleted = false)
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON ((total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id)))
                  WHERE (pembayaran_t.is_deleted = false)) pembayaran_penjamin ON ((pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
             LEFT JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL))
        UNION ALL
         SELECT 'UANG_MASUK'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
            NULL::integer AS tandabuktikeluar_id,
            tandabuktibayar_t.ruangan_id,
            tandabuktibayar_t.bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            bayaruangmuka_t.no_uangmuka AS no_pembayaran,
            bayaruangmuka_t.pendaftaran_id,
                CASE
                    WHEN (pendaftaran_t_1.pasienadmisi_id IS NULL) THEN pendaftaran_t_1.carabayar_id
                    ELSE pasienadmisi_t.carabayar_id
                END AS carabayar_id,
                CASE
                    WHEN (pendaftaran_t_1.pasienadmisi_id IS NULL) THEN carabayar_pendaftaran.carabayar_nama
                    ELSE carabayar_admisi.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN (pendaftaran_t_1.pasienadmisi_id IS NULL) THEN pendaftaran_t_1.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN (pendaftaran_t_1.pasienadmisi_id IS NULL) THEN penjamin_pendaftaran.penjamin_nama
                    ELSE penjamin_admisi.penjamin_nama
                END AS penjamin_nama,
            bayaruangmuka_t.jumlah_uangmuka AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            0 AS total_penjamin,
            0 AS total_nontunai,
            bayaruangmuka_t.jumlah_uangmuka AS total_tunai,
            0 AS total_tagihan,
            NULL::bigint AS pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM (((((((tandabuktibayar_t
             JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN carabayar_m carabayar_pendaftaran ON ((pendaftaran_t_1.carabayar_id = carabayar_pendaftaran.carabayar_id)))
             LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_pendaftaran ON ((pendaftaran_t_1.penjamin_id = penjamin_pendaftaran.penjamin_id)))
             LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL))
        UNION ALL
         SELECT 'RETUR'::text AS jenis,
            NULL::integer AS tandabuktibayar_id,
            tandabuktikeluar_t.tandabuktikeluar_id,
            tandabuktikeluar_t.ruangan_id,
            NULL::integer AS bayaruangmuka_id,
            tandabuktikeluar_t.closingkasir_id,
            NULL::integer AS pembayaranpelayanan_id,
            tandabuktikeluar_t.shift_id,
            NULL::integer AS nourutkasir,
            tandabuktikeluar_t.no_buktikeluar,
            tandabuktikeluar_t.tgl_buktikeluar,
            tandabuktikeluar_t.uang_diterima,
            loginpemakai_k.pegawai_id AS pegawai1_id,
            returbayarpelayanan_t.no_returbayar AS no_pembayaran,
            pembayaranpelayanan_t.pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            NULL::character varying AS carabayar_nama,
            NULL::integer AS penjamin_id,
            NULL::character varying AS penjamin_nama,
            pembayaran_t.total_dibayar AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
            (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
            (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
            0 AS total_tagihan,
            pembayaran_t.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM (((((returbayarpelayanan_t
             JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
             JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
             JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN loginpemakai_k ON ((returbayarpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
          WHERE ((returbayarpelayanan_t.is_deleted = false) AND (tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.is_deleted = false))
        UNION ALL
         SELECT 'PEMBAYARAN_PIUTANG'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
            NULL::integer AS tandabuktikeluar_id,
            tandabuktibayar_t.ruangan_id,
            NULL::integer AS bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            NULL::integer AS pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            NULL::integer AS nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            loginpemakai_k.pegawai_id,
            pembayaranpiutang_t.no_pembayaranpiutang,
            pemberianpiutang_t.pendaftaran_id,
            NULL::integer AS carabayar_id,
                CASE
                    WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN carabayar_resep.carabayar_nama
                    ELSE carabayar_m_1.carabayar_nama
                END AS carabayar_nama,
            NULL::integer AS penjamin_id,
                CASE
                    WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjamin_resep.penjamin_nama
                    ELSE penjamin_m_1.penjamin_nama
                END AS penjamin_nama,
            pembayaranpiutang_t.total_bayarpiutang AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            0 AS total_penjamin,
            0 AS total_nontunai,
            pembayaranpiutang_t.total_bayarpiutang AS total_tunai,
            pemberianpiutang_t.total_sisapiutang AS total_tagihan,
            NULL::bigint AS pembayaran_id,
            penjualanresep_t_1.nama_pembeli AS nama_identitas,
            pembayaranpiutang_t.no_pembayaranpiutang AS no_identitas
           FROM (((((((((pembayaranpiutang_t
             JOIN pemberianpiutang_t ON ((pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id)))
             JOIN loginpemakai_k ON ((pembayaranpiutang_t.created_by = loginpemakai_k.loginpemakai_id)))
             LEFT JOIN pendaftaran_t pendaftaran_t_1 ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN penjualanresep_t penjualanresep_t_1 ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id)))
             LEFT JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t_1.carabayar_id = carabayar_m_1.carabayar_id)))
             LEFT JOIN carabayar_m carabayar_resep ON ((penjualanresep_t_1.carabayar_id = carabayar_resep.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t_1.penjamin_id = penjamin_m_1.penjamin_id)))
             LEFT JOIN penjamin_m penjamin_resep ON ((penjualanresep_t_1.penjamin_id = penjamin_resep.penjamin_id)))
        UNION ALL
         SELECT 'PEMBAYARAN_RESEP_BEBAS'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
            NULL::integer AS tandabuktikeluar_id,
            tandabuktibayar_t.ruangan_id,
            NULL::integer AS bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            NULL::integer AS nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            loginpemakai_k.pegawai_id,
            pembayaranpelayanan_t.no_pembayaran,
            NULL::integer AS pendaftaran_id,
            penjamin_m_1.carabayar_id,
            carabayar_m_1.carabayar_nama,
            penjualanresep_t_1.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pembayaran_t.total_dibayar AS jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pemberianpiutang_t.total_piutang AS total_penjamin,
            pembayaran_t.total_nontunai,
            (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
            ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
            pembayaran_t.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM (((((((pembayaranpelayanan_t
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN penjualanresep_t penjualanresep_t_1 ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN loginpemakai_k ON ((pembayaran_t.created_by = loginpemakai_k.loginpemakai_id)))
             JOIN penjamin_m penjamin_m_1 ON ((penjualanresep_t_1.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN carabayar_m carabayar_m_1 ON ((penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id)))
             LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
          WHERE (penjualanresep_t_1.pendaftaran_id IS NULL)
        UNION ALL
         SELECT
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN 'PENERIMAAN'::text
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN 'PENGELUARAN'::text
                    ELSE NULL::text
                END AS jenis,
            penerimaan.tandabuktibayar_id,
            pengeluaran.tandabuktikeluar_id,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.ruangan_id
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.ruangan_id
                    ELSE NULL::integer
                END AS ruangan_id,
            NULL::integer AS bayaruangmuka_id,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.closingkasir_id
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.closingkasir_id
                    ELSE NULL::integer
                END AS closingkasir_id,
            NULL::integer AS pembayaranpelayanan_id,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.shift_id
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.shift_id
                    ELSE NULL::integer
                END AS shift_id,
            NULL::integer AS nourutkasir,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.nobuktibayar
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.no_buktikeluar
                    ELSE NULL::character varying
                END AS nobuktibayar,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.tglbuktibayar
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.tgl_buktikeluar
                    ELSE NULL::timestamp without time zone
                END AS tglbuktibayar,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN penerimaan.uangditerima
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN pengeluaran.uang_diterima
                    ELSE (0)::double precision
                END AS uangditerima,
            loginpemakai_k.pegawai_id,
            pembayarantransaksi_t.no_transaksi AS no_pembayaran,
            NULL::integer AS pendaftaran_id,
            NULL::integer AS carabayar_id,
            NULL::character varying AS carabayar_nama,
            NULL::integer AS penjamin_id,
            NULL::character varying AS penjamin_nama,
            0 AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
                CASE
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE (0)::double precision
                END AS total_nontunai,
                CASE
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE (0)::double precision
                END AS total_tunai,
            0 AS total_tagihan,
            pembayarantransaksi_t.pembayarantransaksi_id,
                CASE
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 700) THEN supplier_m.supplier_nama
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 701) THEN pegawai_m.nama_pegawai
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 702) THEN pasien_m_1.nama_pasien
                    ELSE NULL::character varying
                END AS nama_identitas,
                CASE
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 700) THEN supplier_m.supplier_kode
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 701) THEN pegawai_m.nomorindukpegawai
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 702) THEN pasien_m_1.no_rekam_medik
                    ELSE NULL::character varying
                END AS no_identitas
           FROM ((((((pembayarantransaksi_t
             LEFT JOIN tandabuktibayar_t penerimaan ON ((pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id)))
             LEFT JOIN tandabuktikeluar_t pengeluaran ON ((pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
             LEFT JOIN pasien_m pasien_m_1 ON ((pembayarantransaksi_t.pasien_id = pasien_m_1.pasien_id)))
             LEFT JOIN pegawai_m ON ((pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN supplier_m ON ((pembayarantransaksi_t.supplier_id = supplier_m.supplier_id)))
             JOIN loginpemakai_k ON ((pembayarantransaksi_t.created_by = loginpemakai_k.loginpemakai_id)))) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pasien_m.pasien_id = pendaftaran_t.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id)));
");
        
        $this->execute('ALTER TABLE "public"."closing_kasir_view" OWNER TO "postgres";');
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"ins_pembayaran\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
                
            DECLARE
                paramJson VARCHAR;
                vPembayaranpelayanan json;
                vPembayaranpenjamin json;
                vPembayaranmetode json;
                    vPembayarandiskon json;
                vPembayaran_id INTEGER;
                vrow json;
                
                
            BEGIN
                paramJson := NEW.additional_data;
                vPembayaranpelayanan := paramJson::json->>'pembayaran_pelayanan';
                vPembayaranpenjamin := paramJson::json->>'pembayaran_penjamin';
                vPembayaranmetode := paramJson::json->>'pembayaran_jenis_pembayaran';
                    vPembayarandiskon := paramJson::json->>'pembayaran_diskon';
                vPembayaran_id := NEW.pembayaran_id;


            -- insert ke pembayaranpelayanan_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpelayanan_t (
                                        pembayaran_id,
                                        carabayar_id,
                                        ruangan_id,
                                        penjamin_id,
                                        pendaftaran_id,
                                        tandabuktibayar_id,
                                        pasien_id,
                                        pasienadmisi_id,
                                        ruangan_pelakhir_id,
                                        no_pembayaran,
                                        tgl_pembayaran, 
                                        total_biayaoa,
                                        total_biayatindakan,
                                        total_biayapelayanan,
                                        total_subsidiasuransi,
                                        total_subsidipemerintah,
                                        total_subsidirs,
                                        total_iurbiaya,
                                        total_bayartindakan,
                                        total_discount,
                                        total_pembebasan,
                                        total_sisatagihan,
                                        statusbayar,
                                        penjualanresep_id,  
                                        biaya_administrasi,
                                        e_collection,
                                        no_rekening,
                                        nama_pemrekening,
                                        penggunaan_uangmuka,
                                        total_terbayar,
                                        pembulatan,
                                        additional_data

                     ) VALUES (
                                vPembayaran_id,
                                (vrow->>'carabayar_id')::INTEGER,
                                (vrow->>'ruangan_id')::INTEGER,
                                (vrow->>'penjamin_id')::INTEGER,
                                (vrow->>'pendaftaran_id')::INTEGER,
                                (vrow->>'tandabuktibayar_id')::INTEGER,
                                (vrow->>'pasien_id')::INTEGER,
                                (vrow->>'pasienadmisi_id')::INTEGER,
                                (vrow->>'ruangan_pelakhir_id')::INTEGER,
                                (vrow->>'no_pembayaran')::INTEGER,
                                (vrow->>'tgl_pembayaran')::TIMESTAMP,
                                (vrow->>'total_biayaoa')::FLOAT,
                                (vrow->>'total_biayatindakan')::FLOAT,
                                (vrow->>'total_biayapelayanan')::FLOAT,
                                (vrow->>'total_subsidiasuransi')::FLOAT,
                                (vrow->>'total_subsidipemerintah')::FLOAT,
                                (vrow->>'total_subsidirs')::FLOAT,
                                (vrow->>'total_iurbiaya')::FLOAT,
                                (vrow->>'total_bayartindakan')::FLOAT,
                                (vrow->>'total_discount')::FLOAT,
                                (vrow->>'total_pembebasan')::FLOAT,
                                (vrow->>'total_sisatagihan')::FLOAT,
                                (vrow->>'statusbayar')::VARCHAR,
                                CASE WHEN vrow->>'pendaftaran_id' IS NOT NULL THEN
                                                                    NULL
                                                                ELSE
                                                                    (vrow->>'penjualanresep_id')::INTEGER
                                                                END,
                                (vrow->>'biaya_administrasi')::FLOAT,
                                (vrow->>'e_collection')::BOOLEAN,
                                (vrow->>'no_rekening')::VARCHAR,
                                (vrow->>'nama_pemrekening')::VARCHAR,
                                (vrow->>'penggunaan_uangmuka')::FLOAT,
                                (vrow->>'total_terbayar')::FLOAT,
                                (vrow->>'pembulatan')::FLOAT,
                                (vrow->>'additional_data')::TEXT
                        );
                    END IF;
                END LOOP;
                
                    
            -- insert ke pembayaranpenjamin_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpenjamin)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpenjamin_t (
                                        pembayaran_id,
                                        penjamin_id,
                                        penjamin_nama,
                                        no_kartu,
                                        total_dijamin

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'penjamin_id')::INTEGER,
                                        (vrow->>'penjamin_nama')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dijamin')::FLOAT                         
                                );
                    END IF;
                END LOOP;
                
                -- insert ke pembayaranmetode_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranmetode)
              LOOP
                    IF (vrow->>'metode_bayar' IS NOT NULL) THEN
                        INSERT INTO pembayaranmetode_t (
                                        pembayaran_id,
                                        metode_bayar,
                                        no_kartu,
                                        total_dibayar,
                                                                                jenisnontunai_id

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'metode_bayar')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dibayar')::FLOAT,                     
                                        (vrow->>'jenisnontunai_id')::INTEGER                     
                                );
                    END IF;
                END LOOP;


                    -- insert ke pembayarandiskon_t
                    FOR vrow IN SELECT * FROM json_array_elements(vPembayarandiskon)
                    LOOP 
                        IF (vrow->>'total_diskon' IS NOT NULL) THEN
                            INSERT INTO pembayarandiskon_t (
                                                    pembayaran_id,
                                                    pegawai_id,
                                                    komponentarif_id,
                                                    total_komponentarif,
                                                    total_diskon
                            ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>'pegawai_id')::INTEGER,
                                                    (vrow->>'komponentarif_id')::INTEGER,
                                                    (vrow->>'total_komponentarif')::FLOAT,
                                                    (vrow->>'total_diskon')::FLOAT
                            );
                            END IF;
                    END LOOP;
                    
                    
                RETURN NEW;
            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        
        $this->execute('ALTER FUNCTION "public"."ins_pembayaran"() OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200402_085213_migrate_20200402 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200402_085213_migrate_20200402 cannot be reverted.\n";

        return false;
    }
    */
}
