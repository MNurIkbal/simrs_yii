<?php

use yii\db\Migration;

/**
 * Class m221021_103840_migrate_MHG3977_view_closing_kasir_view
 */
class m221021_103840_migrate_MHG3977_view_closing_kasir_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->execute('
            DROP VIEW IF EXISTS "public"."closing_kasir_view";

        ');

        $this->execute('
            CREATE VIEW "public"."closing_kasir_view" AS  SELECT agr_bukti_bayar.jenis,
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
                        WHEN agr_bukti_bayar.jenis = \'PEMBAYARAN_PIUTANG\'::text THEN agr_bukti_bayar.no_identitas
                        WHEN pendaftaran_t.no_pendaftaran IS NULL AND penjualanresep_t.noresep IS NULL THEN agr_bukti_bayar.no_identitas
                        WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep::text
                        WHEN penjualanresep_t.noresep IS NULL THEN pendaftaran_t.no_pendaftaran::text
                        ELSE NULL::text
                    END AS no_pendaftaran,
                    CASE
                        WHEN pendaftaran_t.no_pendaftaran IS NULL AND penjualanresep_t.noresep IS NULL THEN agr_bukti_bayar.nama_identitas
                        WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli::text
                        WHEN penjualanresep_t.nama_pembeli IS NULL THEN pasien_m.nama_pasien::text
                        ELSE NULL::text
                    END AS nama_pasien,
                agr_bukti_bayar.pegawai1_id,
                agr_bukti_bayar.no_pembayaran,
                pasien_m.no_rekam_medik,
                agr_bukti_bayar.carabayar_id,
                agr_bukti_bayar.carabayar_nama,
                agr_bukti_bayar.penjamin_id,
                agr_bukti_bayar.penjamin_nama,
                COALESCE(agr_bukti_bayar.total_tagihan, 0::double precision) AS jmlpembayaran,
                COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) AS pembayaran_tunai,
                COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
                COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin,
                agr_bukti_bayar.pembayaran_id, 
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT pembayaranmetode_t.pembayaran_id,
                                pembayaranmetode_t.metode_bayar,
                                pembayaranmetode_t.no_kartu,
                                lkp_tipe_pembayaran.lookup_name AS tipe
                               FROM pembayaranmetode_t
                                 JOIN ( SELECT a.jenisnontunai_id,
                                        a.tipe_pembayaran
                                       FROM jenisnontunai_m a) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
                                 LEFT JOIN ( SELECT a.lookup_id,
                                        a.lookup_name
                                       FROM lookup_m a) lkp_tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = lkp_tipe_pembayaran.lookup_id
                              WHERE pembayaranmetode_t.pembayaran_id = agr_bukti_bayar.pembayaran_id) x) AS additional_nontunai,
                agr_bukti_bayar.keterangan
               FROM ( SELECT \'PEMBAYARAN\'::text AS jenis,
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
                        pembayaran_t.no_pembayaran,
                        pembayaran_t.pendaftaran_id,
                        pendaftaran_pembayaran.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_pembayaran.penjamin_id,
                        pembayaran_penjamin.penjamin_nama,
                        0 AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                            CASE
                                WHEN carabayar_m.carabayar_id = 5 THEN pembayaran_t.total_dijamin + pembayaran_t.total_pembulatan + pembayaran_t.total_piutang
                                WHEN carabayar_m.carabayar_id = 2 THEN pembayaran_t.total_penjamin - pembayaran_t.total_discount
                                ELSE pembayaran_t.total_penjamin
                            END AS total_penjamin,
                        pembayaran_t.total_nontunai,
                            CASE
                                WHEN carabayar_m.carabayar_id = 2 THEN pembayaran_t.total_tunai + pembayaran_t.total_kembalian
                                ELSE pembayaran_t.total_tunai
                            END AS total_tunai,
                        pembayaran_t.total_tagihan,
                        pembayaran_t.pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        pembayaran_t.catatan AS keterangan
                       FROM tandabuktibayar_t
                         JOIN ( SELECT a.no_pembayaran,
                                a.pendaftaran_id,
                                a.pembayaran_id,
                                a.total_nontunai,
                                a.total_ditagihkan,
                                COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                                a.total_tunai - a.total_kembalian AS total_tunai,
                                a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                                a.catatan,
                                a.total_discount,
                                a.total_discountpembayaran,
                                a.total_dijamin,
                                a.pembulatan,
                                a.total_pembulatan,
                                a.total_kembalian,
                                COALESCE(pemberianpiutang_t.total_piutang) AS total_piutang
                               FROM pembayaran_t a
                                 LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                                        a1.total_piutang
                                       FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                                 LEFT JOIN ( SELECT sum(a1.total_dibayar) AS total_nontunai,
                                        a1.pembayaran_id
                                       FROM pembayaranmetode_t a1
                                      WHERE a1.is_deleted = false
                                      GROUP BY a1.pembayaran_id) total_pembayaran ON a.pembayaran_id = total_pembayaran.pembayaran_id) pembayaran_t ON tandabuktibayar_t.pembayaran_id = pembayaran_t.pembayaran_id
                         JOIN ( SELECT a.pendaftaran_id,
                                COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_id,
                                COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) AS carabayar_id
                               FROM pendaftaran_t a
                                 LEFT JOIN ( SELECT b.pasienadmisi_id,
                                        b.carabayar_id,
                                        b.penjamin_id
                                       FROM pasienadmisi_t b) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_pembayaran ON pembayaran_t.pendaftaran_id = pendaftaran_pembayaran.pendaftaran_id
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m ON pendaftaran_pembayaran.carabayar_id = carabayar_m.carabayar_id
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m ON pendaftaran_pembayaran.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                                string_agg(penjamin_m_1.penjamin_nama::text, \', \'::text) AS penjamin_nama
                               FROM pembayaranpelayanan_t
                                 JOIN ( SELECT a.penjamin_id,
                                        a.penjamin_nama
                                       FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                              GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    UNION ALL
                     SELECT \'BATAL_PEMBAYARAN\'::text AS jenis,
                        NULL::integer AS tandabuktibayar_id,
                        tandabuktikeluar_t.tandabuktikeluar_id,
                        pembatalanpembayaran_t.ruangan_id,
                        NULL::integer AS bayaruangmuka_id,
                        tandabuktikeluar_t.closingkasir_id,
                        NULL::integer AS pembayaranpelayanan_id,
                        NULL::integer AS shift_id,
                        NULL::integer AS nourutkasir,
                        tandabuktikeluar_t.no_buktikeluar AS nobuktibayar,
                        pembatalanpembayaran_t.deleted_date AS tglbuktibayar,
                        NULL::double precision AS uangditerima,
                        loginpemakai_k.pegawai_id,
                        pembatalanpembayaran_t.no_pembayaran,
                        pembatalanpembayaran_t.pendaftaran_id,
                        pendaftaran_pembayaran.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_pembayaran.penjamin_id,
                        pembayaran_penjamin.penjamin_nama,
                        pembatalanpembayaran_t.total_tunai AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                            CASE
                                WHEN pendaftaran_pembayaran.carabayar_id = 2 THEN \'-1\'::integer::double precision * (pembatalanpembayaran_t.total_penjamin - pembatalanpembayaran_t.total_discount)
                                ELSE - pembatalanpembayaran_t.total_penjamin
                            END AS total_penjamin,
                        \'-1\'::integer::double precision * pembatalanpembayaran_t.total_nontunai,
                            CASE
                                WHEN carabayar_m.carabayar_id = 2 THEN \'-1\'::integer::double precision * (pembatalanpembayaran_t.total_tunai + pembatalanpembayaran_t.total_kembalian)
                                ELSE \'-1\'::integer::double precision * pembatalanpembayaran_t.total_tunai
                            END AS total_tunai,
                        \'-1\'::integer::double precision * pembatalanpembayaran_t.total_tagihan,
                        pembatalanpembayaran_t.pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        pembatalanpembayaran_t.alasan_batal AS keterangan
                       FROM tandabuktikeluar_t
                         JOIN ( SELECT a.pembatalanpembayaran_id,
                                a.no_pembayaran,
                                a.pendaftaran_id,
                                a.pembayaran_id,
                                a.total_nontunai,
                                a.total_ditagihkan,
                                COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                                a.total_tunai - a.total_kembalian AS total_tunai,
                                a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                                a.deleted_date AS tgl_batal,
                                pembayaranpelayanan_t.alasan_batal,
                                pembayaranpelayanan_t.ruangan_id,
                                pembayaranpelayanan_t.deleted_date,
                                a.total_discount,
                                a.total_discountpembayaran,
                                a.total_dijamin,
                                a.total_pembulatan,
                                a.total_kembalian,
                                COALESCE(pemberianpiutang_t.total_piutang) AS total_piutang
                               FROM pembatalanpembayaran_t a
                                 LEFT JOIN ( SELECT a1.pembayaran_id,
                                        a1.ruangan_id,
                                        pembayaran_t.deleted_date,
                                        pembayaran_t.alasan_batal
                                       FROM pembayaranpelayanan_t a1
                                         LEFT JOIN ( SELECT a_1.deleted_date,
                                                a_1.alasan_batal,
                                                a_1.pembayaran_id
                                               FROM pembayaran_t a_1) pembayaran_t ON a1.pembayaran_id = pembayaran_t.pembayaran_id
                                      GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t.deleted_date, pembayaran_t.alasan_batal) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                                 LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                                        a1.total_piutang
                                       FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                              WHERE a.is_deleted = false) pembatalanpembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembatalanpembayaran_t.pembatalanpembayaran_id
                         JOIN ( SELECT a.pendaftaran_id,
                                a.penjamin_id,
                                a.carabayar_id
                               FROM pendaftaran_t a) pendaftaran_pembayaran ON pembatalanpembayaran_t.pendaftaran_id = pendaftaran_pembayaran.pendaftaran_id
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m ON pendaftaran_pembayaran.carabayar_id = carabayar_m.carabayar_id
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m ON pendaftaran_pembayaran.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN ( SELECT a.loginpemakai_id,
                                a.pegawai_id
                               FROM loginpemakai_k a) loginpemakai_k ON tandabuktikeluar_t.created_by = loginpemakai_k.loginpemakai_id
                         LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                                string_agg(penjamin_m_1.penjamin_nama::text, \', \'::text) AS penjamin_nama
                               FROM pembayaranpelayanan_t
                                 JOIN ( SELECT a.penjamin_id,
                                        a.penjamin_nama
                                       FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                              GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembatalanpembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                      WHERE tandabuktikeluar_t.is_deleted = false
                    UNION ALL
                     SELECT \'UANG_MUKA\'::text AS jenis,
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
                        bayaruangmuka_t.jumlah_uangmuka AS uangditerima,
                        tandabuktibayar_t.pegawai1_id,
                        bayaruangmuka_t.no_uangmuka AS no_pembayaran,
                        bayaruangmuka_t.pendaftaran_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
                                ELSE pasienadmisi_t.carabayar_id
                            END AS carabayar_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN carabayar_pendaftaran.carabayar_nama
                                ELSE carabayar_admisi.carabayar_nama
                            END AS carabayar_nama,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
                                ELSE pasienadmisi_t.penjamin_id
                            END AS penjamin_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN penjamin_pendaftaran.penjamin_nama
                                ELSE penjamin_admisi.penjamin_nama
                            END AS penjamin_nama,
                        bayaruangmuka_t.jumlah_uangmuka AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        0 AS total_penjamin,
                            CASE
                                WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN bayaruangmuka_t.jumlah_uangmuka
                                ELSE 0::double precision
                            END AS total_nontunai,
                            CASE
                                WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN bayaruangmuka_t.jumlah_uangmuka
                                ELSE 0::double precision
                            END AS total_tunai,
                        0 AS total_tagihan,
                        NULL::bigint AS pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        bayaruangmuka_t.keterangan_uangmuka AS keterangan
                       FROM tandabuktibayar_t
                         JOIN ( SELECT b.bayaruangmuka_id,
                                b.pendaftaran_id,
                                b.no_uangmuka,
                                b.jumlah_uangmuka,
                                b.metode_pembayaran,
                                b.keterangan_uangmuka
                               FROM bayaruangmuka_t b) bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
                         JOIN ( SELECT b.pendaftaran_id,
                                b.pasienadmisi_id,
                                b.carabayar_id,
                                b.penjamin_id
                               FROM pendaftaran_t b) pendaftaran ON bayaruangmuka_t.pendaftaran_id = pendaftaran.pendaftaran_id
                         LEFT JOIN ( SELECT b.pasienadmisi_id,
                                b.carabayar_id,
                                b.penjamin_id
                               FROM pasienadmisi_t b) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_pendaftaran ON pendaftaran.carabayar_id = carabayar_pendaftaran.carabayar_id
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_pendaftaran ON pendaftaran.penjamin_id = penjamin_pendaftaran.penjamin_id
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
                      WHERE tandabuktibayar_t.bayaruangmuka_id IS NOT NULL
                    UNION ALL
                     SELECT \'BATAL_UANG_MUKA\'::text AS jenis,
                        NULL::integer AS tandabuktibayar_id,
                        tandabuktikeluar_t.tandabuktikeluar_id,
                        pembatalanuangmuka_t.ruangan_id,
                        pembatalanuangmuka_t.bayaruangmuka_id,
                        tandabuktikeluar_t.closingkasir_id,
                        NULL::integer AS pembayaranpelayanan_id,
                        NULL::integer AS shift_id,
                        NULL::integer AS nourutkasir,
                        tandabuktikeluar_t.no_buktikeluar,
                        pembatalanuangmuka_t.tglpembatalan AS tglbuktibayar,
                        pembatalanuangmuka_t.jmlkaskeluarbatal,
                        loginpemakai_k.pegawai_id,
                        tandabuktikeluar_t.no_buktikeluar,
                        pembatalanuangmuka_t.pendaftaran_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
                                ELSE pasienadmisi_t.carabayar_id
                            END AS carabayar_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN carabayar_pendaftaran.carabayar_nama
                                ELSE carabayar_admisi.carabayar_nama
                            END AS carabayar_nama,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
                                ELSE pasienadmisi_t.penjamin_id
                            END AS penjamin_id,
                            CASE
                                WHEN pendaftaran.pasienadmisi_id IS NULL THEN penjamin_pendaftaran.penjamin_nama
                                ELSE penjamin_admisi.penjamin_nama
                            END AS penjamin_nama,
                        pembatalanuangmuka_t.jmlkaskeluarbatal AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        0 AS total_penjamin,
                            CASE
                                WHEN pembatalanuangmuka_t.metode_pembayaran = 28 THEN \'-1\'::integer::double precision * pembatalanuangmuka_t.jmlkaskeluarbatal
                                ELSE 0::double precision
                            END AS total_nontunai,
                            CASE
                                WHEN pembatalanuangmuka_t.metode_pembayaran = 27 THEN \'-1\'::integer::double precision * pembatalanuangmuka_t.jmlkaskeluarbatal
                                ELSE 0::double precision
                            END AS total_tunai,
                        0 AS total_tagihan,
                        NULL::bigint AS pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        pembatalanuangmuka_t.keterangan_batal AS keterangan
                       FROM tandabuktikeluar_t
                         JOIN ( SELECT b.pembatalanuangmuka_id,
                                b.bayaruangmuka_id,
                                bayaruangmuka_t.pendaftaran_id,
                                b.jmlkaskeluarbatal,
                                b.tglpembatalan,
                                b.keterangan_batal,
                                bayaruangmuka_t.metode_pembayaran,
                                bayaruangmuka_t.ruangan_id
                               FROM pembatalanuangmuka_t b
                                 JOIN ( SELECT a.bayaruangmuka_id,
                                        a.pendaftaran_id,
                                        a.metode_pembayaran,
                                        a.ruangan_id
                                       FROM bayaruangmuka_t a) bayaruangmuka_t ON b.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id) pembatalanuangmuka_t ON tandabuktikeluar_t.pembatalanuangmuka_id = pembatalanuangmuka_t.pembatalanuangmuka_id
                         JOIN ( SELECT b.pendaftaran_id,
                                b.pasienadmisi_id,
                                b.carabayar_id,
                                b.penjamin_id
                               FROM pendaftaran_t b) pendaftaran ON pembatalanuangmuka_t.pendaftaran_id = pendaftaran.pendaftaran_id
                         LEFT JOIN ( SELECT b.pasienadmisi_id,
                                b.carabayar_id,
                                b.penjamin_id
                               FROM pasienadmisi_t b) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_pendaftaran ON pendaftaran.carabayar_id = carabayar_pendaftaran.carabayar_id
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_pendaftaran ON pendaftaran.penjamin_id = penjamin_pendaftaran.penjamin_id
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
                         LEFT JOIN ( SELECT b.loginpemakai_id,
                                b.pegawai_id
                               FROM loginpemakai_k b) loginpemakai_k ON tandabuktikeluar_t.created_by = loginpemakai_k.loginpemakai_id
                      WHERE tandabuktikeluar_t.is_deleted = false
                    UNION ALL
                     SELECT \'PENGEMBALIAN_UANG_MUKA\'::text AS jenis,
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
                        tandabuktikeluar_t.no_buktikeluar AS no_pembayaran,
                        pengembalianuangmuka_t.pendaftaran_id,
                        pendaftaran.carabayar_id,
                        NULL::character varying AS carabayar_nama,
                        NULL::integer AS penjamin_id,
                        NULL::character varying AS penjamin_nama,
                        pengembalianuangmuka_t.total_pengembalian AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        NULL::double precision AS total_penjamin,
                            CASE
                                WHEN tandabuktikeluar_t.is_tunai = false THEN - pengembalianuangmuka_t.total_pengembalian
                                ELSE 0::double precision
                            END AS total_nontunai,
                            CASE
                                WHEN tandabuktikeluar_t.is_tunai = true THEN - pengembalianuangmuka_t.total_pengembalian
                                ELSE 0::double precision
                            END AS total_tunai,
                        0 AS total_tagihan,
                        NULL::bigint AS pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        NULL::text AS keterangan
                       FROM tandabuktikeluar_t
                         JOIN ( SELECT c.pengembalianuangmuka_id,
                                c.created_by,
                                c.pendaftaran_id,
                                c.total_pengembalian
                               FROM pengembalianuangmuka_t c) pengembalianuangmuka_t ON pengembalianuangmuka_t.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
                         LEFT JOIN ( SELECT c.pendaftaran_id,
                                c.carabayar_id
                               FROM pendaftaran_t c) pendaftaran ON pengembalianuangmuka_t.pendaftaran_id = pendaftaran.pendaftaran_id
                         LEFT JOIN ( SELECT c.loginpemakai_id,
                                c.pegawai_id
                               FROM loginpemakai_k c) loginpemakai_k ON pengembalianuangmuka_t.created_by = loginpemakai_k.loginpemakai_id
                      WHERE tandabuktikeluar_t.is_deleted = false
                    UNION ALL
                     SELECT \'RETUR\'::text AS jenis,
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
                        pembayaran_t.pendaftaran_id,
                        pendaftaran.carabayar_id,
                        NULL::character varying AS carabayar_nama,
                        NULL::integer AS penjamin_id,
                        NULL::character varying AS penjamin_nama,
                        pembayaran_t.total_dibayar AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        NULL::double precision AS total_penjamin,
                        - returbayarpelayanan_t.total_nontunai AS total_nontunai,
                        - returbayarpelayanan_t.total_biayaretur AS total_tunai,
                        0 AS total_tagihan,
                        pembayaran_t.pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        returbayarpelayanan_t.keterangan_retur AS keterangan
                       FROM returbayarpelayanan_t
                         JOIN ( SELECT c.returbayarpelayanan_id,
                                c.tandabuktikeluar_id,
                                c.ruangan_id,
                                c.closingkasir_id,
                                c.shift_id,
                                c.no_buktikeluar,
                                c.tgl_buktikeluar,
                                c.uang_diterima
                               FROM tandabuktikeluar_t c) tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
                         JOIN ( SELECT c.tandabuktibayar_id,
                                c.pembayaran_id
                               FROM tandabuktibayar_t c
                              WHERE c.is_deleted = false) tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
                         JOIN ( SELECT c.pembayaran_id,
                                c.pendaftaran_id,
                                c.total_dibayar
                               FROM pembayaran_t c
                              WHERE c.is_deleted = false) pembayaran_t ON tandabuktibayar_t.pembayaran_id = pembayaran_t.pembayaran_id
                         JOIN ( SELECT c.pendaftaran_id,
                                c.carabayar_id
                               FROM pendaftaran_t c) pendaftaran ON pembayaran_t.pendaftaran_id = pendaftaran.pendaftaran_id
                         JOIN ( SELECT c.loginpemakai_id,
                                c.pegawai_id
                               FROM loginpemakai_k c) loginpemakai_k ON returbayarpelayanan_t.created_by = loginpemakai_k.loginpemakai_id
                      WHERE returbayarpelayanan_t.is_deleted = false
                    UNION ALL
                     SELECT \'PEMBAYARAN_PIUTANG\'::text AS jenis,
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
                                WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN carabayar_resep.carabayar_nama
                                ELSE carabayar_pendaftaran.carabayar_nama
                            END AS carabayar_nama,
                        NULL::integer AS penjamin_id,
                            CASE
                                WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN penjamin_resep.penjamin_nama
                                ELSE penjamin_pendaftaran.penjamin_nama
                            END AS penjamin_nama,
                        pembayaranpiutang_t.total_bayarpiutang AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        0 AS total_penjamin,
                            CASE
                                WHEN pembayaranpiutang_t.metode_pembayaran = 28 THEN pembayaranpiutang_t.total_bayarpiutang
                                ELSE 0::double precision
                            END AS total_nontunai,
                            CASE
                                WHEN pembayaranpiutang_t.metode_pembayaran = 27 THEN pembayaranpiutang_t.total_bayarpiutang
                                ELSE 0::double precision
                            END AS total_tunai,
                        pemberianpiutang_t.total_sisapiutang AS total_tagihan,
                        NULL::bigint AS pembayaran_id,
                        penjualanresep.nama_pembeli AS nama_identitas,
                        pembayaranpiutang_t.no_pembayaranpiutang AS no_identitas,
                        pembayaranpiutang_t.catatan AS keterangan
                       FROM pembayaranpiutang_t
                         JOIN ( SELECT d.pemberianpiutang_id,
                                d.pendaftaran_id,
                                d.penjualanresep_id,
                                d.total_sisapiutang
                               FROM pemberianpiutang_t d) pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                         JOIN ( SELECT d.pembayaranpiutang_id,
                                d.tandabuktibayar_id,
                                d.ruangan_id,
                                d.closingkasir_id,
                                d.shift_id,
                                d.nobuktibayar,
                                d.tglbuktibayar,
                                d.uangditerima
                               FROM tandabuktibayar_t d) tandabuktibayar_t ON pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id
                         JOIN ( SELECT d.loginpemakai_id,
                                d.pegawai_id
                               FROM loginpemakai_k d) loginpemakai_k ON pembayaranpiutang_t.created_by = loginpemakai_k.loginpemakai_id
                         LEFT JOIN ( SELECT d.pendaftaran_id,
                                d.carabayar_id,
                                d.penjamin_id
                               FROM pendaftaran_t d) pendaftaran ON pemberianpiutang_t.pendaftaran_id = pendaftaran.pendaftaran_id
                         LEFT JOIN ( SELECT d.penjualanresep_id,
                                d.carabayar_id,
                                d.penjamin_id,
                                d.nama_pembeli
                               FROM penjualanresep_t d) penjualanresep ON pemberianpiutang_t.penjualanresep_id = penjualanresep.penjualanresep_id
                         LEFT JOIN ( SELECT d.carabayar_id,
                                d.carabayar_nama
                               FROM carabayar_m d) carabayar_pendaftaran ON pendaftaran.carabayar_id = carabayar_pendaftaran.carabayar_id
                         LEFT JOIN ( SELECT d.carabayar_id,
                                d.carabayar_nama
                               FROM carabayar_m d) carabayar_resep ON penjualanresep.carabayar_id = carabayar_resep.carabayar_id
                         LEFT JOIN ( SELECT d.penjamin_id,
                                d.penjamin_nama
                               FROM penjamin_m d) penjamin_pendaftaran ON pendaftaran.penjamin_id = penjamin_pendaftaran.penjamin_id
                         LEFT JOIN ( SELECT d.penjamin_id,
                                d.penjamin_nama
                               FROM penjamin_m d) penjamin_resep ON penjualanresep.penjamin_id = penjamin_resep.penjamin_id
                      WHERE pembayaranpiutang_t.is_deleted = false
                    UNION ALL
                     SELECT \'PEMBAYARAN_RESEP_BEBAS\'::text AS jenis,
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
                        penjamin_m.carabayar_id,
                        carabayar_m.carabayar_nama,
                        penjualanresep.penjamin_id,
                        pembayaran_penjamin.penjamin_nama,
                        pembayaran_t.total_dibayar AS jmlpembayaran,
                        pembayaranpelayanan_t.penjualanresep_id,
                        pembayaran_t.total_dijamin + pembayaran_t.total_pembulatan AS total_penjamin,
                        pembayaran_t.total_nontunai,
                        pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS total_tunai,
                        pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan + pembayaran_t.pembulatan - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) AS total_tagihan,
                        pembayaran_t.pembayaran_id,
                        NULL::text AS nama_identitas,
                        NULL::text AS no_identitas,
                        penjualanresep.catatan AS keterangan
                       FROM pembayaranpelayanan_t
                         JOIN ( SELECT a.pembayaran_id,
                                a.pendaftaran_id,
                                a.total_tagihan,
                                a.total_administrasi,
                                a.total_discount,
                                a.total_discountpembayaran,
                                a.total_tunai,
                                a.total_kembalian,
                                a.total_nontunai,
                                a.total_dijamin,
                                a.no_pembayaran,
                                a.is_deleted,
                                a.pemberianpiutang_id,
                                a.created_by,
                                a.total_dibayar,
                                a.total_pembulatan,
                                a.pembulatan
                               FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                         JOIN ( SELECT e.penjualanresep_id,
                                e.penjamin_id,
                                e.pendaftaran_id,
                                e.catatan
                               FROM penjualanresep_t e
                              WHERE e.pendaftaran_id IS NULL) penjualanresep ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep.penjualanresep_id
                         JOIN ( SELECT e.pembayaran_id,
                                e.tandabuktibayar_id,
                                e.ruangan_id,
                                e.closingkasir_id,
                                e.shift_id,
                                e.nobuktibayar,
                                e.tglbuktibayar,
                                e.uangditerima
                               FROM tandabuktibayar_t e) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                         JOIN ( SELECT e.loginpemakai_id,
                                e.pegawai_id
                               FROM loginpemakai_k e) loginpemakai_k ON pembayaran_t.created_by = loginpemakai_k.loginpemakai_id
                         JOIN ( SELECT e.penjamin_id,
                                e.penjamin_nama,
                                e.carabayar_id
                               FROM penjamin_m e) penjamin_m ON penjualanresep.penjamin_id = penjamin_m.penjamin_id
                         JOIN ( SELECT e.carabayar_id,
                                e.carabayar_nama
                               FROM carabayar_m e) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                         LEFT JOIN ( SELECT e.pemberianpiutang_id,
                                e.total_piutang
                               FROM pemberianpiutang_t e) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                         LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
                                string_agg(penjamin_m_1.penjamin_nama::text, \', \'::text) AS penjamin_nama
                               FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                                 JOIN ( SELECT a.penjamin_id,
                                        a.penjamin_nama
                                       FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t_1.penjamin_id = penjamin_m_1.penjamin_id
                              GROUP BY pembayaranpelayanan_t_1.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                    UNION ALL
                     SELECT \'BATAL_PEMBAYARAN_RESEP_BEBAS\'::text AS jenis,
                        NULL::integer AS tandabuktibayar_id,
                        tandabuktikeluar_t.tandabuktikeluar_id,
                        pembatalanpembayaran_t.ruangan_id,
                        NULL::integer AS bayaruangmuka_id,
                        tandabuktikeluar_t.closingkasir_id,
                        NULL::integer AS pembayaranpelayanan_id,
                        NULL::integer AS shift_id,
                        NULL::integer AS nourutkasir,
                        tandabuktikeluar_t.no_buktikeluar AS nobuktibayar,
                        pembatalanpembayaran_t.deleted_date AS tglbuktibayar,
                        NULL::double precision AS uangditerima,
                        loginpemakai_k.pegawai_id,
                        pembatalanpembayaran_t.no_pembayaran,
                        NULL::integer AS pendaftaran_id,
                        pembatalanpembayaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pembatalanpembayaran_t.penjamin_id,
                        pembayaran_penjamin.penjamin_nama,
                        pembatalanpembayaran_t.total_tunai AS jmlpembayaran,
                        penjualanresep_t_1.penjualanresep_id,
                            CASE
                                WHEN pembatalanpembayaran_t.carabayar_id = 2 THEN \'-1\'::integer::double precision * (pembatalanpembayaran_t.total_penjamin - pembatalanpembayaran_t.total_discount)
                                ELSE - pembatalanpembayaran_t.total_penjamin
                            END AS total_penjamin,
                        \'-1\'::integer::double precision * pembatalanpembayaran_t.total_nontunai,
                            CASE
                                WHEN carabayar_m.carabayar_id = 2 THEN \'-1\'::integer::double precision * (pembatalanpembayaran_t.total_tunai + pembatalanpembayaran_t.total_kembalian)
                                ELSE \'-1\'::integer::double precision * pembatalanpembayaran_t.total_tunai
                            END AS total_tunai,
                        \'-1\'::integer::double precision * pembatalanpembayaran_t.total_tagihan,
                        pembatalanpembayaran_t.pembayaran_id,
                        penjualanresep_t_1.nama_pembeli AS nama_identitas,
                        NULL::text AS no_identitas,
                        pembatalanpembayaran_t.alasan_batal AS keterangan
                       FROM tandabuktikeluar_t
                         JOIN ( SELECT a.pembatalanpembayaran_id,
                                pembayaranpelayanan_t.no_pembayaran,
                                pembayaranpelayanan_t.penjualanresep_id,
                                a.pembayaran_id,
                                a.total_nontunai,
                                a.total_ditagihkan,
                                COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                                a.total_tunai - a.total_kembalian AS total_tunai,
                                a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                                a.deleted_date AS tgl_batal,
                                pembayaranpelayanan_t.alasan_batal,
                                pembayaranpelayanan_t.ruangan_id,
                                pembayaranpelayanan_t.deleted_date,
                                a.total_discount,
                                a.total_discountpembayaran,
                                a.total_dijamin,
                                a.total_pembulatan,
                                a.total_kembalian,
                                COALESCE(pemberianpiutang_t.total_piutang) AS total_piutang,
                                pembayaranpelayanan_t.carabayar_id,
                                pembayaranpelayanan_t.penjamin_id
                               FROM pembatalanpembayaran_t a
                                 LEFT JOIN ( SELECT a1.pembayaran_id,
                                        a1.ruangan_id,
                                        pembayaran_t.deleted_date,
                                        pembayaran_t.alasan_batal,
                                        a1.carabayar_id,
                                        a1.penjamin_id,
                                        a1.penjualanresep_id,
                                        a1.no_pembayaran
                                       FROM pembayaranpelayanan_t a1
                                         LEFT JOIN ( SELECT a_1.deleted_date,
                                                a_1.alasan_batal,
                                                a_1.pembayaran_id
                                               FROM pembayaran_t a_1) pembayaran_t ON a1.pembayaran_id = pembayaran_t.pembayaran_id
                                      GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t.deleted_date, pembayaran_t.alasan_batal, a1.carabayar_id, a1.penjamin_id, a1.penjualanresep_id, a1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                                 LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                                        a1.total_piutang
                                       FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                              WHERE a.is_deleted = false) pembatalanpembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembatalanpembayaran_t.pembatalanpembayaran_id
                         JOIN ( SELECT a.penjualanresep_id,
                                a.nama_pembeli,
                                a.noresep
                               FROM penjualanresep_t a) penjualanresep_t_1 ON pembatalanpembayaran_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m ON pembatalanpembayaran_t.carabayar_id = carabayar_m.carabayar_id
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m ON pembatalanpembayaran_t.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN ( SELECT a.loginpemakai_id,
                                a.pegawai_id
                               FROM loginpemakai_k a) loginpemakai_k ON tandabuktikeluar_t.created_by = loginpemakai_k.loginpemakai_id
                         LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                                string_agg(penjamin_m_1.penjamin_nama::text, \', \'::text) AS penjamin_nama
                               FROM pembayaranpelayanan_t
                                 JOIN ( SELECT a.penjamin_id,
                                        a.penjamin_nama
                                       FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                              GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembatalanpembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
                      WHERE tandabuktikeluar_t.is_deleted = false
                    UNION ALL
                     SELECT
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN \'PENERIMAAN\'::text
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN \'PENGELUARAN\'::text
                                ELSE NULL::text
                            END AS jenis,
                        penerimaan.tandabuktibayar_id,
                        pengeluaran.tandabuktikeluar_id,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.ruangan_id
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.ruangan_id
                                ELSE NULL::integer
                            END AS ruangan_id,
                        NULL::integer AS bayaruangmuka_id,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.closingkasir_id
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.closingkasir_id
                                ELSE NULL::integer
                            END AS closingkasir_id,
                        NULL::integer AS pembayaranpelayanan_id,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.shift_id
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.shift_id
                                ELSE NULL::integer
                            END AS shift_id,
                        NULL::integer AS nourutkasir,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.nobuktibayar
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.no_buktikeluar
                                ELSE NULL::character varying
                            END AS nobuktibayar,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.tglbuktibayar
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.tgl_buktikeluar
                                ELSE NULL::timestamp without time zone
                            END AS tglbuktibayar,
                            CASE
                                WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.uangditerima
                                WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.uang_diterima
                                ELSE 0::double precision
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
                                WHEN pembayarantransaksi_t.metode_pembayaran = 28 AND pembayarantransaksi_t.jenis_transaksi = 668 THEN pembayarantransaksi_t.jumlah
                                WHEN pembayarantransaksi_t.metode_pembayaran = 28 AND pembayarantransaksi_t.jenis_transaksi = 669 THEN - pembayarantransaksi_t.jumlah
                                ELSE 0::double precision
                            END AS total_nontunai,
                            CASE
                                WHEN pembayarantransaksi_t.metode_pembayaran = 27 AND pembayarantransaksi_t.jenis_transaksi = 668 THEN pembayarantransaksi_t.jumlah
                                WHEN pembayarantransaksi_t.metode_pembayaran = 27 AND pembayarantransaksi_t.jenis_transaksi = 669 THEN - pembayarantransaksi_t.jumlah
                                ELSE 0::double precision
                            END AS total_tunai,
                        0 AS total_tagihan,
                        pembayarantransaksi_t.pembayarantransaksi_id,
                            CASE
                                WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_nama
                                WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nama_pegawai
                                WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien.nama_pasien
                                ELSE NULL::character varying
                            END AS nama_identitas,
                            CASE
                                WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_kode
                                WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nomorindukpegawai
                                WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien.no_rekam_medik
                                ELSE NULL::character varying
                            END AS no_identitas,
                        concat(kategoritransaksi_m.kategoritransaksi_nama, \' - \', COALESCE(pembayarantransaksi_t.deskripsi, \'\'::text)) AS keterangan
                       FROM pembayarantransaksi_t
                         LEFT JOIN ( SELECT f.penerimaanumum_id,
                                f.tandabuktibayar_id,
                                f.ruangan_id,
                                f.closingkasir_id,
                                f.shift_id,
                                f.nobuktibayar,
                                f.tglbuktibayar,
                                f.uangditerima
                               FROM tandabuktibayar_t f) penerimaan ON pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id
                         LEFT JOIN ( SELECT f.tandabuktikeluar_id,
                                f.pembayarantransaksi_id,
                                f.ruangan_id,
                                f.closingkasir_id,
                                f.shift_id,
                                f.no_buktikeluar,
                                f.tgl_buktikeluar,
                                f.uang_diterima
                               FROM tandabuktikeluar_t f) pengeluaran ON pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id
                         LEFT JOIN ( SELECT f.pasien_id,
                                f.nama_pasien,
                                f.no_rekam_medik
                               FROM pasien_m f) pasien ON pembayarantransaksi_t.pasien_id = pasien.pasien_id
                         LEFT JOIN ( SELECT f.pegawai_id,
                                f.nama_pegawai,
                                f.nomorindukpegawai
                               FROM pegawai_m f) pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
                         LEFT JOIN ( SELECT f.supplier_id,
                                f.supplier_nama,
                                f.supplier_kode
                               FROM supplier_m f) supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
                         JOIN ( SELECT f.loginpemakai_id,
                                f.pegawai_id
                               FROM loginpemakai_k f) loginpemakai_k ON pembayarantransaksi_t.created_by = loginpemakai_k.loginpemakai_id
                         LEFT JOIN ( SELECT a.kategoritransaksi_id,
                                a.kategoritransaksi_nama
                               FROM kategoritransaksi_m a) kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id) agr_bukti_bayar
                 LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
                        pendaftaran.pasien_id,
                        pendaftaran.no_pendaftaran
                       FROM pendaftaran_t pendaftaran) pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
                 LEFT JOIN ( SELECT pasien.pasien_id,
                        pasien.nama_pasien,
                        pasien.no_rekam_medik
                       FROM pasien_m pasien) pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                 LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
                        penjualanresep.noresep,
                        penjualanresep.nama_pembeli
                       FROM penjualanresep_t penjualanresep) penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221021_103840_migrate_MHG3977_view_closing_kasir_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221021_103840_migrate_MHG3977_view_closing_kasir_view cannot be reverted.\n";

        return false;
    }
    */
}
