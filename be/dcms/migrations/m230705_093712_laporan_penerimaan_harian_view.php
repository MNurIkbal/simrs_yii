<?php

use yii\db\Migration;

/**
 * Class m230705_093712_laporan_penerimaan_harian_view
 */
class m230705_093712_laporan_penerimaan_harian_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
        $this->execute("DROP VIEW IF EXISTS public.laporanpenerimaandatakasir_v;");
        $this->execute("CREATE OR REPLACE VIEW public.laporanpenerimaandatakasir_v
            AS  SELECT x.tanggal,
            x.instalasi_nama,
            userlogin.nama_pegawai AS kasir,
            x.deposite,
            x.omset,
            x.pemakaian_deposite,
            x.penjamin_piutang,
            x.jumlah
                FROM ( SELECT 1 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    'Cash'::character varying AS omset,
                    0 AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                    sum(pembayaran_t.total_tunai) - sum(pembayaran_t.total_kembalian) AS jumlah
                FROM pembayaran_t
                    JOIN ( SELECT a.pembayaran_id,
                            a.closingkasir_id,
                            a.tandabuktibayar_id,
                            a.created_by,
                            a.tglbuktibayar,
                            a.ruangan_id
                        FROM tandabuktibayar_t a
                        WHERE a.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.pasienadmisi_id,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.ruangan_id,
                            a.kelaspelayanan_id
                        FROM pendaftaran_t a) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ( SELECT a.pasien_id,
                            a.nama_pasien
                        FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    LEFT JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                        FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                        FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                    LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.ruangan_id,
                            a.kelaspelayanan_id
                        FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id
                    LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                        FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                WHERE (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) > 0::double precision AND pembayaran_t.is_deleted IS FALSE
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by
                UNION ALL
                SELECT 2 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    pembayaranmetode_t.metode_bayar AS omset,
                    0 AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                    sum(pembayaranmetode_t.total_dibayar) AS jumlah
                FROM pembayaran_t
                    LEFT JOIN ( SELECT b.pembayaran_id,
                            b.closingkasir_id,
                            b.tandabuktibayar_id,
                            b.tglbuktibayar,
                            b.ruangan_id
                        FROM tandabuktibayar_t b
                        WHERE b.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    LEFT JOIN ( SELECT b.closingkasir_id,
                            b.ruangan_id,
                            b.tgl_closingkasir,
                            b.created_by
                        FROM closingkasir_t b
                        WHERE b.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT b.pendaftaran_id,
                            b.pasien_id,
                            b.pasienadmisi_id,
                            b.no_pendaftaran,
                            b.carabayar_id,
                            b.penjamin_id,
                            b.ruangan_id,
                            b.kelaspelayanan_id
                        FROM pendaftaran_t b) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ( SELECT b.pasien_id,
                            b.nama_pasien
                        FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    LEFT JOIN ( SELECT b.pembayaran_id,
                            b.jenisnontunai_id,
                            b.total_dibayar,
                            b.metode_bayar,
                            b.no_kartu
                        FROM pembayaranmetode_t b) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
                    LEFT JOIN ( SELECT b.jenisnontunai_id,
                            b.bank_id
                        FROM jenisnontunai_m b) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
                    LEFT JOIN ( SELECT b.carabayar_id,
                            b.carabayar_nama
                        FROM carabayar_m b) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT b.penjamin_id,
                            b.penjamin_nama
                        FROM penjamin_m b) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                    LEFT JOIN ( SELECT b.pasienadmisi_id,
                            b.ruangan_id,
                            b.kelaspelayanan_id
                        FROM pasienadmisi_t b) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                        FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id
                WHERE pembayaran_t.total_nontunai <> 0::double precision AND pembayaran_t.is_deleted IS FALSE
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by, pembayaranmetode_t.metode_bayar
                UNION ALL
                SELECT 3 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    NULL::character varying AS omset,
                    0 AS pemakaian_deposite,
                    pembayaran_penjamin.penjamin_nama AS penjamin_piutang,
                    sum(COALESCE(pembayaran_t.total_dijamin, 0::double precision)) + sum(COALESCE(pembayaran_t.total_pembulatan, 0::double precision)) + sum(COALESCE(pemberianpiutang_t.total_piutang, 0::double precision)) AS jumlah
                FROM pembayaran_t
                    LEFT JOIN ( SELECT c.tandabuktibayar_id,
                            c.pembayaran_id,
                            c.closingkasir_id,
                            c.tglbuktibayar,
                            c.ruangan_id
                        FROM tandabuktibayar_t c
                        WHERE c.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    LEFT JOIN ( SELECT c.closingkasir_id,
                            c.ruangan_id,
                            c.tgl_closingkasir,
                            c.created_by
                        FROM closingkasir_t c
                        WHERE c.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT c.pendaftaran_id,
                            c.pasien_id,
                            c.no_pendaftaran,
                            c.penjamin_id,
                            c.carabayar_id,
                            c.pasienadmisi_id,
                            c.ruangan_id,
                            c.kelaspelayanan_id
                        FROM pendaftaran_t c) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ( SELECT c.pasien_id,
                            c.nama_pasien
                        FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    LEFT JOIN ( SELECT c.pemberianpiutang_id,
                            c.total_piutang
                        FROM pemberianpiutang_t c) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                    LEFT JOIN ( SELECT c.carabayar_id,
                            c.carabayar_nama
                        FROM carabayar_m c) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT c.penjamin_id,
                            c.penjamin_nama
                        FROM penjamin_m c) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                    LEFT JOIN ( SELECT c.pembayaran_id,
                            c.no_kartu
                        FROM pembayaranmetode_t c) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
                    LEFT JOIN ( SELECT c.pasienadmisi_id,
                            c.ruangan_id,
                            c.kelaspelayanan_id
                        FROM pasienadmisi_t c) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                        FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id
                WHERE pembayaran_t.is_deleted IS FALSE AND pembayaran_t.total_dijamin <> 0::double precision OR pembayaran_t.pemberianpiutang_id IS NOT NULL
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by, pembayaran_penjamin.penjamin_nama
                UNION ALL
                SELECT 4 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    'Cash'::character varying AS omset,
                    0 AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                    sum(pembayaran_t.total_tunai) - sum(pembayaran_t.total_kembalian) AS jumlah
                FROM penjualanresep_t
                    JOIN ( SELECT d.penjualanresep_id,
                            d.pembayaran_id,
                            d.no_pembayaran,
                            d.pembayaranpelayanan_id
                        FROM pembayaranpelayanan_t d) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
                    JOIN ( SELECT d.pembayaran_id,
                            d.total_tunai,
                            d.total_kembalian,
                            d.no_pembayaran,
                            d.created_date,
                            d.created_by,
                            d.is_deleted
                        FROM pembayaran_t d
                        WHERE d.total_tunai <> 0::double precision) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                    JOIN ( SELECT d.pembayaran_id,
                            d.closingkasir_id,
                            d.pembayaranpelayanan_id,
                            d.tglbuktibayar,
                            d.ruangan_id
                        FROM tandabuktibayar_t d) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    JOIN ( SELECT d.closingkasir_id,
                            d.ruangan_id,
                            d.tgl_closingkasir,
                            d.created_by
                        FROM closingkasir_t d
                        WHERE d.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT d.penjamin_id,
                            d.penjamin_nama,
                            d.carabayar_id
                        FROM penjamin_m d) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                    JOIN ( SELECT d.carabayar_id,
                            d.carabayar_nama
                        FROM carabayar_m d) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON penjualanresep_t.ruangan_id = ruangan.ruangan_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                WHERE pembayaran_t.is_deleted IS FALSE
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by
                UNION ALL
                SELECT 5 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    pembayaranmetode_t.metode_bayar AS omset,
                    0 AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                    sum(pembayaranmetode_t.total_dibayar) AS jumlah
                FROM penjualanresep_t
                    JOIN ( SELECT e.penjualanresep_id,
                            e.pembayaran_id,
                            e.no_pembayaran,
                            e.pembayaranpelayanan_id
                        FROM pembayaranpelayanan_t e) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
                    JOIN ( SELECT e.pembayaran_id,
                            e.total_nontunai,
                            e.no_pembayaran,
                            e.created_date,
                            e.created_by,
                            e.is_deleted
                        FROM pembayaran_t e
                        WHERE e.total_nontunai <> 0::double precision) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                    JOIN ( SELECT e.pembayaran_id,
                            e.closingkasir_id,
                            e.pembayaranpelayanan_id,
                            e.tglbuktibayar,
                            e.ruangan_id
                        FROM tandabuktibayar_t e) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    JOIN ( SELECT e.closingkasir_id,
                            e.ruangan_id,
                            e.tgl_closingkasir,
                            e.created_by
                        FROM closingkasir_t e
                        WHERE e.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT e.penjamin_id,
                            e.penjamin_nama,
                            e.carabayar_id
                        FROM penjamin_m e) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                    JOIN ( SELECT e.carabayar_id,
                            e.carabayar_nama
                        FROM carabayar_m e) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT e.pembayaran_id,
                            e.jenisnontunai_id,
                            e.total_dibayar,
                            e.metode_bayar,
                            e.no_kartu
                        FROM pembayaranmetode_t e) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
                    LEFT JOIN ( SELECT e.jenisnontunai_id,
                            e.bank_id
                        FROM jenisnontunai_m e) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON penjualanresep_t.ruangan_id = ruangan.ruangan_id
                WHERE pembayaran_t.is_deleted IS FALSE
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by, pembayaranmetode_t.metode_bayar
                UNION ALL
                SELECT 6 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    NULL::character varying AS omset,
                    0 AS pemakaian_deposite,
                    pembayaran_penjamin.penjamin_nama AS penjamin_piutang,
                    sum(COALESCE(pembayaran_t.total_dijamin, 0::double precision)) + sum(COALESCE(pemberianpiutang_t.total_piutang, 0::double precision)) AS jumlah
                FROM penjualanresep_t
                    JOIN ( SELECT f.penjualanresep_id,
                            f.pembayaran_id,
                            f.no_pembayaran,
                            f.pembayaranpelayanan_id
                        FROM pembayaranpelayanan_t f) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
                    JOIN ( SELECT f.pembayaran_id,
                            f.pemberianpiutang_id,
                            f.total_dijamin,
                            f.no_pembayaran,
                            f.created_date,
                            f.created_by,
                            f.is_deleted
                        FROM pembayaran_t f
                        WHERE f.total_dijamin <> 0::double precision OR f.pemberianpiutang_id IS NOT NULL) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                    JOIN ( SELECT f.pembayaran_id,
                            f.closingkasir_id,
                            f.pembayaranpelayanan_id,
                            f.tglbuktibayar,
                            f.ruangan_id
                        FROM tandabuktibayar_t f) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    JOIN ( SELECT f.closingkasir_id,
                            f.ruangan_id,
                            f.tgl_closingkasir,
                            f.created_by
                        FROM closingkasir_t f
                        WHERE f.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT f.penjamin_id,
                            f.penjamin_nama,
                            f.carabayar_id
                        FROM penjamin_m f) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                    JOIN ( SELECT f.carabayar_id,
                            f.carabayar_nama
                        FROM carabayar_m f) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT f.pemberianpiutang_id,
                            f.total_piutang
                        FROM pemberianpiutang_t f) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                    LEFT JOIN ( SELECT f.pembayaran_id,
                            f.no_kartu
                        FROM pembayaranmetode_t f) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
                    LEFT JOIN ( SELECT a.pembayaran_id,
                            string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                        FROM pembayaranpelayanan_t a
                            JOIN ( SELECT a_1.penjamin_id,
                                    a_1.penjamin_nama
                                FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                        GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON penjualanresep_t.ruangan_id = ruangan.ruangan_id
                WHERE pembayaran_t.is_deleted IS FALSE
                GROUP BY (pembayaran_t.created_date::date), ruangan.instalasi_nama, pembayaran_t.created_by, pembayaran_penjamin.penjamin_nama
                UNION ALL
                SELECT 7 AS urutan,
                    tandabuktibayar_t.tglbuktibayar::date AS tanggal,
                    ruangan.instalasi_nama,
                    bayaruangmuka_t.created_by,
                        CASE
                            WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN 'Cash'::text
                            WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN jenisnontunai_m.metode_bayar::text
                            ELSE NULL::text
                        END AS deposite,
                    NULL::character varying AS omset,
                    0 AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                        CASE
                            WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN sum(tandabuktibayar_t.uangditerima)
                            WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN sum(tandabuktibayar_t.uangditerima)
                            ELSE 0::double precision
                        END AS jumlah
                FROM bayaruangmuka_t
                    JOIN ( SELECT g.bayaruangmuka_id,
                            g.closingkasir_id,
                            g.uangditerima,
                            g.ruangan_id,
                            g.tglbuktibayar,
                            g.created_by
                        FROM tandabuktibayar_t g
                        WHERE g.is_deleted = false AND g.bayaruangmuka_id IS NOT NULL) tandabuktibayar_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
                    LEFT JOIN ( SELECT g.closingkasir_id,
                            g.ruangan_id,
                            g.tgl_closingkasir,
                            g.created_by
                        FROM closingkasir_t g
                        WHERE g.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
                    JOIN ( SELECT g.pendaftaran_id,
                            g.pasien_id,
                            g.pasienadmisi_id,
                            g.carabayar_id,
                            g.penjamin_id,
                            g.no_pendaftaran,
                            g.ruangan_id
                        FROM pendaftaran_t g) pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ( SELECT a.pasien_id,
                            a.nama_pasien
                        FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    LEFT JOIN ( SELECT g.pasienadmisi_id,
                            g.carabayar_id,
                            g.penjamin_id,
                            g.ruangan_id,
                            g.kelaspelayanan_id
                        FROM pasienadmisi_t g) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id
                    LEFT JOIN ( SELECT g.carabayar_id,
                            g.carabayar_nama
                        FROM carabayar_m g) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
                    LEFT JOIN ( SELECT g.carabayar_id,
                            g.carabayar_nama
                        FROM carabayar_m g) carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
                    LEFT JOIN ( SELECT g.penjamin_id,
                            g.penjamin_nama
                        FROM penjamin_m g) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
                    LEFT JOIN ( SELECT g.penjamin_id,
                            g.penjamin_nama
                        FROM penjamin_m g) penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
                    LEFT JOIN ( SELECT g.jenisnontunai_id,
                            g.nama AS metode_bayar,
                            g.bank_id
                        FROM jenisnontunai_m g) jenisnontunai_m ON bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
                    LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                        FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE bayaruangmuka_t.is_deleted IS FALSE
                GROUP BY (tandabuktibayar_t.tglbuktibayar::date), ruangan.instalasi_nama, bayaruangmuka_t.created_by, jenisnontunai_m.metode_bayar, bayaruangmuka_t.metode_pembayaran
                UNION ALL
                SELECT 8 AS urutan,
                    pembayaran_t.created_date::date AS tanggal,
                    ruangan.instalasi_nama,
                    pembayaran_t.created_by,
                    NULL::text AS deposite,
                    NULL::character varying AS omset,
                    pembayaran_t.penggunaan_uangmuka AS pemakaian_deposite,
                    NULL::text AS penjamin_piutang,
                    0 AS jumlah
                FROM pembayaran_t
                    JOIN ( SELECT a.pembayaran_id,
                            a.closingkasir_id,
                            a.tandabuktibayar_id,
                            a.created_by,
                            a.tglbuktibayar,
                            a.ruangan_id
                        FROM tandabuktibayar_t a
                        WHERE a.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                    JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.pasienadmisi_id,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.ruangan_id,
                            a.kelaspelayanan_id
                        FROM pendaftaran_t a) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ( SELECT a.pasien_id,
                            a.nama_pasien
                        FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    LEFT JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                        FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                    LEFT JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                        FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                    LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.ruangan_id,
                            a.kelaspelayanan_id
                        FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    LEFT JOIN ( SELECT a.ruangan_id,
                            a.instalasi_id,
                            instalasi_m.instalasi_nama
                        FROM ruangan_m a
                            JOIN ( SELECT b.instalasi_id,
                                    b.instalasi_nama
                                FROM instalasi_m b) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id) ruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id
                WHERE pembayaran_t.penggunaan_uangmuka > 0::double precision AND pembayaran_t.is_deleted IS FALSE) x
                LEFT JOIN ( SELECT a.loginpemakai_id,
                        pegawai_m.nama_pegawai
                    FROM loginpemakai_k a
                        JOIN ( SELECT b.pegawai_id,
                                b.nama_pegawai
                            FROM pegawai_m b) pegawai_m ON a.loginpemakai_id = pegawai_m.pegawai_id) userlogin ON x.created_by = userlogin.loginpemakai_id
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230705_093712_laporan_penerimaan_harian_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230705_093712_laporan_penerimaan_harian_view cannot be reverted.\n";

        return false;
    }
    */
}
