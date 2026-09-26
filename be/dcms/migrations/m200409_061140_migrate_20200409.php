<?php

use yii\db\Migration;

/**
 * Class m200409_061140_migrate_20200409
 */
class m200409_061140_migrate_20200409 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpenerimaankasir_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenerimaankasir_v\" AS  SELECT pegawai_kasir.nama_pegawai AS kasir,
    rekap_kasir.tanggal,
    rekap_kasir.no_kwitansi,
    rekap_kasir.no_registrasi,
    rekap_kasir.info_pasien,
    rekap_kasir.rupiah,
    rekap_kasir.transaksi,
    rekap_kasir.keterangan,
    rekap_kasir.cara_bayar,
    rekap_kasir.penjamin
   FROM ((( SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN TAGIHAN'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM ((((((((pembayaranpelayanan_t
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_tunai <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaranmetode_t.total_dibayar AS rupiah,
            'NON TUNAI'::text AS transaksi,
            (('PEMBAYARAN TAGIHAN'::text || ' - '::text) || (pembayaranmetode_t.metode_bayar)::text) AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM (((((((((pembayaranpelayanan_t
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pembayaranmetode_t ON ((pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_nontunai <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN TAGIHAN'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM (((((((((pembayaranpelayanan_t
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM ((((((penjualanresep_t
             JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
          WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_tunai <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            pembayaranmetode_t.total_dibayar AS rupiah,
            'NON TUNAI'::text AS transaksi,
            (('PEMBAYARAN RESEP BEBAS'::text || ' - '::text) || (pembayaranmetode_t.metode_bayar)::text) AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM (((((((penjualanresep_t
             JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN pembayaranmetode_t ON ((pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id)))
          WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_nontunai <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by
           FROM (((((((penjualanresep_t
             JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
          WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            bayaruangmuka_t.no_uangmuka AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
                CASE
                    WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
                    WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
                    ELSE (0)::double precision
                END AS rupiah,
                CASE
                    WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN 'TUNAI'::text
                    WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
                    ELSE 'PENJAMIN'::text
                END AS transaksi,
                CASE
                    WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN 'UANG MASUK'::text
                    WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN (('UANG MASUK'::text || ' - '::text) || (jenisnontunai_m.nama)::text)
                    ELSE NULL::text
                END AS keterangan,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_pendaftaran.carabayar_nama
                    ELSE carabayar_admisi.carabayar_nama
                END AS cara_bayar,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_pendaftaran.penjamin_nama
                    ELSE penjamin_admisi.penjamin_nama
                END AS penjamin,
            closingkasir_t.created_by
           FROM ((((((((((bayaruangmuka_t
             JOIN tandabuktibayar_t ON ((bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN carabayar_m carabayar_pendaftaran ON ((pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id)))
             LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_pendaftaran ON ((pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id)))
             LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
             LEFT JOIN jenisnontunai_m ON ((bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char((closingkasir_t.tgl_closingkasir)::timestamp with time zone, 'YYYY-MM-DD'::text))::date AS tanggal,
            pembayaranpiutang_t.no_pembayaranpiutang AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
                CASE
                    WHEN (pemberianpiutang_t.penjualanresep_id IS NULL) THEN pasien_m.nama_pasien
                    WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
                CASE
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN pembayaranpiutang_t.total_bayarpiutang
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN pembayaranpiutang_t.total_bayarpiutang
                    ELSE (0)::double precision
                END AS rupiah,
                CASE
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN 'TUNAI'::text
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
                    ELSE 'PENJAMIN'::text
                END AS transaksi,
                CASE
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN 'PEMBAYARAN PIUTANG'::text
                    WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN (('PEMBAYARAN PIUTANG'::text || ' - '::text) || (jenisnontunai_m.nama)::text)
                    ELSE NULL::text
                END AS keterangan,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
                    ELSE carabayar_resep.carabayar_nama
                END AS cara_bayar,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
                    ELSE penjamin_resep.penjamin_nama
                END AS penjamin,
            closingkasir_t.created_by
           FROM (((((((((((pembayaranpiutang_t
             JOIN tandabuktibayar_t ON ((pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id)))
             JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN pemberianpiutang_t ON ((pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
             LEFT JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN penjualanresep_t ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN carabayar_m carabayar_resep ON ((penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id)))
             LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN penjamin_m penjamin_resep ON ((penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id)))
             LEFT JOIN jenisnontunai_m ON ((pembayaranpiutang_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
          WHERE (tandabuktibayar_t.is_deleted = false)
        UNION ALL
         SELECT
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN closing_bayar.ruangan_id
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN closing_keluar.ruangan_id
                    ELSE NULL::integer
                END AS kasir,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN (to_char(closing_bayar.tgl_closingkasir, 'YYYY-MM-DD'::text))::date
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (to_char(closing_keluar.tgl_closingkasir, 'YYYY-MM-DD'::text))::date
                    ELSE NULL::date
                END AS tanggal,
            pembayarantransaksi_t.no_transaksi AS no_kwitansi,
            NULL::character varying AS no_registrasi,
                CASE
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 700) THEN supplier_m.supplier_nama
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 701) THEN pegawai_m.nama_pegawai
                    WHEN (pembayarantransaksi_t.tipe_transaksi = 702) THEN pasien_m.nama_pasien
                    ELSE NULL::character varying
                END AS info_pasien,
                CASE
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
                    WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE (0)::double precision
                END AS rupiah,
                CASE
                    WHEN (pembayarantransaksi_t.metode_pembayaran = 27) THEN 'TUNAI'::text
                    WHEN (pembayarantransaksi_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
                    ELSE NULL::text
                END AS transaksi,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN 'PENERIMAAN'::text
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN 'PENGELUARAN'::text
                    ELSE NULL::text
                END AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN closing_bayar.created_by
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN closing_keluar.created_by
                    ELSE NULL::integer
                END AS created_by
           FROM (((((((pembayarantransaksi_t
             LEFT JOIN tandabuktibayar_t penerimaan ON ((pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id)))
             LEFT JOIN tandabuktikeluar_t pengeluaran ON ((pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
             LEFT JOIN closingkasir_t closing_bayar ON ((penerimaan.closingkasir_id = closing_bayar.closingkasir_id)))
             LEFT JOIN closingkasir_t closing_keluar ON ((pengeluaran.closingkasir_id = closing_keluar.closingkasir_id)))
             LEFT JOIN pasien_m ON ((pembayarantransaksi_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pegawai_m ON ((pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN supplier_m ON ((pembayarantransaksi_t.supplier_id = supplier_m.supplier_id)))
          WHERE ((penerimaan.closingkasir_id IS NOT NULL) OR (pengeluaran.closingkasir_id IS NOT NULL))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pendaftaran_t.no_pendaftaran
                    WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
                    WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            (- returbayarpelayanan_t.total_biayaretur) AS rupiah,
            'TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by
           FROM (((((((returbayarpelayanan_t
             JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
             JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
             LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
          WHERE ((tandabuktikeluar_t.is_deleted = false) AND (returbayarpelayanan_t.total_biayaretur <> (0)::double precision))
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pendaftaran_t.no_pendaftaran
                    WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
                    WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            (- returbayarpelayanan_t.total_nontunai) AS rupiah,
            'NON TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by
           FROM (((((((returbayarpelayanan_t
             JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
             JOIN closingkasir_t ON ((tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
             JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
             JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
             LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
          WHERE ((tandabuktikeluar_t.is_deleted = false) AND (returbayarpelayanan_t.total_nontunai <> (0)::double precision))) rekap_kasir
     JOIN loginpemakai_k ON ((rekap_kasir.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m pegawai_kasir ON ((loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id)));");
        
        $this->execute('ALTER TABLE "public"."laporanpenerimaankasir_v" OWNER TO "postgres";');
        
        $this->execute('DROP VIEW if exists "public"."pendaftaran_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"pendaftaran_v\" AS  SELECT pendaftaran_t.pendaftaran_id AS kunjungan_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rekammedik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir AS tgl_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.tgl_pendaftaran,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_pedaftaran.tglpasienpulang
            ELSE pulang_admisi.tglpasienpulang
        END AS tgl_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_pendaftaran.instalasi_id
            ELSE ruangan_pendaftaran.instalasi_id
        END AS instalasi_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_pendaftaran.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_pendaftaran.ruangan_nama
            ELSE ruangan_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_pendaftaran.carabayar_nama
            ELSE carabayar_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_pendaftaran.penjamin_nama
            ELSE penjamin_admisi.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelas_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_nama
            ELSE kelas_admisi.kelaspelayanan_nama
        END AS kelas_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS dokter_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_pendaftaran.nosep
            ELSE bpjs_admisi.nosep
        END AS no_sep,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup(pendaftaran_t.status_verifikasi)
            ELSE fgetnamalookup(pasienadmisi_t.status_verifikasi)
        END AS status_kunjungan,
    pendaftaran_t.additional_data,
    pendaftaran_t.created_date,
    pendaftaran_t.created_by,
    pendaftaran_t.modified_count,
    pendaftaran_t.last_modified_date,
    pendaftaran_t.last_modified_by,
    pendaftaran_t.is_deleted,
    pendaftaran_t.is_active,
    pendaftaran_t.deleted_date,
    pendaftaran_t.deleted_by
   FROM (((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t pulang_pedaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pedaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pendaftaran_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
     LEFT JOIN ruangan_m ruangan_pendaftaran ON ((pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_admisi ON ((pasienadmisi_t.ruangan_id = ruangan_admisi.ruangan_id)))
     LEFT JOIN instalasi_m instalasi_pendaftaran ON ((ruangan_pendaftaran.instalasi_id = instalasi_pendaftaran.instalasi_id)))
     LEFT JOIN instalasi_m instalasi_admisi ON ((ruangan_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
     LEFT JOIN carabayar_m carabayar_pendaftaran ON ((pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     LEFT JOIN penjamin_m penjamin_pendaftaran ON ((pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON ((pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
     LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
  WHERE ((pendaftaran_t.pasienpulang_id IS NOT NULL) OR (pasienadmisi_t.pasienpulang_id IS NOT NULL));
");
        
        $this->execute('ALTER TABLE "public"."pendaftaran_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienri_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienri_v\" AS  SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.jeniskelamin AS jeniskelamin_id,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    kamarruangan_m.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE ((x.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (x.is_instruksi_pulang = true))) > 0) THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    COALESCE(tagihan.sub_total, (0)::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, (0)::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor,
    bpjs_t.nosep,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    pendaftaran_t.keterangan_pendaftaran,
    pasienadmisi_t.asuransipasien_id,
    pendaftaran_t.is_stopakomodasi,
    pendaftaran_t.tgl_stopakomodasi,
        CASE
            WHEN (implementasi.sisa = 0) THEN true
            WHEN (implementasi.sisa <> 0) THEN false
            ELSE false
        END AS status_implementasi
   FROM (((((((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
     JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (asesmenmedis_t.is_deleted = false))))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN asesmenawal_t ON ((pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id)))
     LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON (((pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id))))
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM (monitorsetdiagnosa_t
             JOIN diagnosa_m ON ((monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id)))
          WHERE (monitorsetdiagnosa_t.is_deleted = false)) monitorsetdiagnosa ON ((pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id)))
     LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
            (count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi)) AS sisa
           FROM (((cppt_t
             LEFT JOIN instruksi_t ON (((cppt_t.cppt_id = instruksi_t.cppt_id) AND (instruksi_t.is_deleted = false))))
             LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> '455'::text))))
             LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> '455'::text))))
          WHERE (cppt_t.is_deleted = false)
          GROUP BY cppt_t.pendaftaran_id) implementasi ON ((pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id)))
  WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));
");
        $this->execute('ALTER TABLE "public"."infopasienri_v" OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200409_061140_migrate_20200409 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200409_061140_migrate_20200409 cannot be reverted.\n";

        return false;
    }
    */
}
