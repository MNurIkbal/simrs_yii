<?php

use yii\db\Migration;

/**
 * Class m200414_043019_migrate20200414
 */
class m200414_043019_migrate20200414 extends Migration
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
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND ((pembayaran_t.total_tunai - pembayaran_t.total_kembalian) <> (0)::double precision))
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
          WHERE (((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision)) OR (pembayaran_t.pemberianpiutang_id IS NOT NULL))
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
          WHERE (((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision)) OR (pembayaran_t.pemberianpiutang_id IS NOT NULL))
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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_043019_migrate20200414 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_043019_migrate20200414 cannot be reverted.\n";

        return false;
    }
    */
}
