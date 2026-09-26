<?php

use yii\db\Migration;

/**
 * Class m200409_071417_migrate_20200409_01
 */
class m200409_071417_migrate_20200409_01 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."cetakkwitansibkm_v";');

        $this->execute("
            CREATE VIEW \"public\".\"cetakkwitansibkm_v\" AS  SELECT 'PEMBAYARAN'::text AS jenis,
    pembayaranpelayanan_t.pembayaranpelayanan_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    pembayaran_t.total_dibayar AS total_terbayar,
    (((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) + pembayaran_t.total_pembulatan) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaranpelayanan_t.pembayaran_id
   FROM (((((((((tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL))
UNION ALL
 SELECT 'UANG_MASUK'::text AS jenis,
    bayaruangmuka_t.bayaruangmuka_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    NULL::integer AS pembayaranpelayanan_id,
    bayaruangmuka_t.bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    bayaruangmuka_t.no_uangmuka AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    bayaruangmuka_t.tgl_uangmuka AS tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    0 AS total_tagihan,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_nontunai,
    NULL::integer AS pembayaran_id
   FROM ((((((((tandabuktibayar_t
     JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL))
UNION ALL
 SELECT 'RETUR'::text AS jenis,
    tandabuktikeluar_t.returbayarpelayanan_id AS transaksi_id,
    NULL::integer AS tandabuktibayar_id,
    tandabuktikeluar_t.tandabuktikeluar_id,
    NULL::integer AS pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    NULL::integer AS pendaftaran_id,
    NULL::integer AS instalasi_id,
    returbayarpelayanan_t.no_returbayar AS no_kwitansi,
    tandabuktikeluar_t.no_buktikeluar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    returbayarpelayanan_t.tgl_returpelayanan AS tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    NULL::character varying AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    0 AS total_tagihan,
    (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
    (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
    NULL::integer AS pembayaran_id
   FROM ((((((((((returbayarpelayanan_t
     JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((returbayarpelayanan_t.is_deleted = false) AND (tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.is_deleted = false))
UNION ALL
 SELECT 'PENJUALAN_RESEP_BEBAS'::text AS jenis,
    pembayaranpelayanan_t.penjualanresep_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    pembayaranpelayanan_t.penjualanresep_id,
    NULL::integer AS pendaftaran_id,
    NULL::integer AS instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    penjualanresep_t.noresep AS no_pendaftaran,
    penjualanresep_t.tglresep AS tgl_pendaftaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    NULL::character varying AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    NULL::character varying AS instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    (((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) + (+ pembayaran_t.total_pembulatan)) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaranpelayanan_t.pembayaran_id
   FROM (((((pembayaranpelayanan_t
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
  WHERE (penjualanresep_t.pendaftaran_id IS NULL);");
        
        $this->execute('ALTER TABLE "public"."cetakkwitansibkm_v" OWNER TO "postgres";');
        
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
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin
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
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_nontunai,
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
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN pembayaranpiutang_t.total_bayarpiutang
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN pembayaranpiutang_t.total_bayarpiutang
            ELSE (0)::double precision
        END AS total_nontunai,
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
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin
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
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)));
");
        
        $this->execute('ALTER TABLE "public"."infoclosingkasir_v" OWNER TO "postgres";');
        
        $this->execute('DROP VIEW if exists "public"."infopasienbpjs_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienbpjs_v\" AS  SELECT rincian.jenis,
    rincian.pendaftaran_id,
    rincian.tgl_pendaftaran,
    rincian.no_pendaftaran,
    rincian.instalasi_id,
    rincian.instalasi_nama,
    rincian.pasien_id,
    rincian.no_rekam_medik,
    rincian.nama_pasien,
    rincian.carabayar_id,
    rincian.carabayar_nama,
    rincian.penjamin_id,
    rincian.penjamin_nama,
    rincian.ruangan_id,
    rincian.ruangan_nama,
    rincian.jeniskasuspenyakit_id,
    rincian.jeniskasuspenyakit_nama,
    rincian.pegawai_id,
    rincian.dokter_dpjp,
    rincian.status_verifikasi,
    rincian.status_verif,
    rincian.umur,
    rincian.kelaspelayanan_id,
    rincian.kelaspelayanan_nama,
    rincian.jeniskelas_nama,
    rincian.pasienpulang_id,
    rincian.tglpasienpulang,
    rincian.carakeluar_id,
    rincian.carakeluar_nama,
    rincian.nosep,
    rincian.kamarruangan_id,
    rincian.kamarruangan_nokamar,
    rincian.kamartempattidur_id,
    rincian.no_tempattidur,
    rincian.pasienadmisi_id,
    rincian.status_bayar,
    rincian.stat_bayar,
    rincian.total_tagihan,
    rincian.nokartuasuransi,
    rincian.jeniskelamin,
    rincian.tanggal_lahir,
    rincian.carakeluarinacbg_id,
    rincian.carakeluar_value,
    rincian.lama_rawat,
    rincian.urutankelas,
    rincian.pengajuanklaimdetail_id,
    rincian.bpjs_id,
    rincian.kelas_bpjs,
    masukkamar_t.kelaspelayanan_id AS naik_kelas,
    rincian.naik_kelas AS naik_kelas_klaim,
    masukkamar_t.lamadirawat_kamar,
    rincian.jeniskelas_id,
    profilrumahsakit_m.kodetarifbpjs_id,
    rincian.tarif_polieksekutif,
    rincian.is_naikkelas,
    rincian.is_rawatintensif,
    rincian.lama_kelasintensif,
    rincian.ventilator,
    rincian.status_klaim,
    rincian.klaiminacbg_id,
    rincian.jenis_kelasrawat,
    rincian.is_terkirim
   FROM ((( SELECT 'RJ-RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            NULL::integer AS kamarruangan_id,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::integer AS kamartempattidur_id,
            NULL::character varying AS no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            1 AS lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pendaftaran_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim
           FROM (((((((((((((((pendaftaran_t
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             JOIN jeniskelas_m ON ((kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id)))
             JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
             JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN (hitung.instalasi_id = 3) THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM (( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM (tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM (((tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL)
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM ((obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (obatalkespasien_t.resepturdetail_id IS NULL)
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM ((((obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
                             LEFT JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (obatalkespasien_t.resepturdetail_id IS NOT NULL)
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON ((hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON (((pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id) AND (pendaftaran_t.instalasi_id = cektagihaninstalasi.instalasi_id))))
             LEFT JOIN pengajuanklaimdetail_t ON (((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
             LEFT JOIN klaiminacbg_t ON (((pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id) AND (klaiminacbg_t.is_deleted = false))))
          WHERE ((pendaftaran_t.carabayar_id = 6) AND (pendaftaran_t.instalasi_id <> 3))
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienadmisi_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pasienadmisi_t.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            pasienadmisi_t.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            pasienpulang_t.lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pasienadmisi_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim
           FROM ((((((((((((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
             JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             JOIN jeniskelas_m ON ((kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id)))
             JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
             JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
             JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
             JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN (hitung.instalasi_id = 3) THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM (( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM (tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM (((tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL)
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM ((obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (obatalkespasien_t.resepturdetail_id IS NULL)
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM ((((obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
                             LEFT JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
                             LEFT JOIN ruangan_m ruangan_m_1 ON ((reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id)))
                             LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                          WHERE (obatalkespasien_t.resepturdetail_id IS NOT NULL)
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON ((hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON (((pendaftaran_t.pasienadmisi_id = cektagihaninstalasi.pasienadmisi_id) AND (pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id))))
             LEFT JOIN pengajuanklaimdetail_t ON (((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
             LEFT JOIN klaiminacbg_t ON (((pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id) AND (klaiminacbg_t.is_deleted = false))))
          WHERE (pasienadmisi_t.carabayar_id = 6)) rincian
     LEFT JOIN masukkamar_t ON (((rincian.pasienadmisi_id = masukkamar_t.pasienadmisi_id) AND (masukkamar_t.pindahkamar_id IS NULL))))
     LEFT JOIN profilrumahsakit_m ON (((profilrumahsakit_m.is_deleted = false) AND (profilrumahsakit_m.is_active = true))))
  GROUP BY rincian.jenis, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.no_pendaftaran, rincian.instalasi_id, rincian.instalasi_nama, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincian.ruangan_id, rincian.ruangan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter_dpjp, rincian.status_verifikasi, rincian.status_verif, rincian.umur, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskelas_nama, rincian.pasienpulang_id, rincian.tglpasienpulang, rincian.carakeluar_id, rincian.carakeluar_nama, rincian.nosep, rincian.kamarruangan_id, rincian.kamarruangan_nokamar, rincian.kamartempattidur_id, rincian.no_tempattidur, rincian.pasienadmisi_id, rincian.status_bayar, rincian.stat_bayar, rincian.total_tagihan, rincian.nokartuasuransi, rincian.jeniskelamin, rincian.tanggal_lahir, rincian.carakeluarinacbg_id, rincian.carakeluar_value, rincian.kelas_bpjs, rincian.lama_rawat, rincian.urutankelas, rincian.pengajuanklaimdetail_id, rincian.bpjs_id, masukkamar_t.kelaspelayanan_id, masukkamar_t.lamadirawat_kamar, rincian.jeniskelas_id, profilrumahsakit_m.kodetarifbpjs_id, rincian.tarif_polieksekutif, rincian.is_naikkelas, rincian.is_rawatintensif, rincian.lama_kelasintensif, rincian.ventilator, rincian.naik_kelas, rincian.status_klaim, rincian.klaiminacbg_id, rincian.jenis_kelasrawat, rincian.is_terkirim;");
        
        $this->execute('ALTER TABLE "public"."infopasienbpjs_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200409_071417_migrate_20200409_01 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200409_071417_migrate_20200409_01 cannot be reverted.\n";

        return false;
    }
    */
}
