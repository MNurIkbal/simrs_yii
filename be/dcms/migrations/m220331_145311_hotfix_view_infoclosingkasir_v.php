<?php

use yii\db\Migration;

/**
 * Class m220331_145311_hotfix_view_infoclosingkasir_v
 */
class m220331_145311_hotfix_view_infoclosingkasir_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
            DROP VIEW IF EXISTS "public"."infoclosingkasir_v";
        ');

         $this->execute('
            CREATE VIEW "public"."infoclosingkasir_v" AS  SELECT \'PEMBAYARAN\'::text AS tipe,
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
                pembayaranpelayanan_t.tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
                (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
                pembayaran_t.total_nontunai,
                (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin,
                pembayaranmetode.metode_pembayaran,
                COALESCE(pembayaran_t.no_pembayaran, pembayaranpelayanan_t.no_pembayaran) AS no_pembayaran
               FROM (((((((((((((closingkasir_t
                 JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
                 JOIN pembayaranpelayanan_t ON (((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id) AND (pembayaranpelayanan_t.is_deleted = false)))) 
                 JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
                 JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayaranmetode_t.total_dibayar)::text), \',\'::text) AS metode_pembayaran,
                        pembayaranmetode_t.pembayaran_id
                       FROM (pembayaranmetode_t
                         JOIN jenisnontunai_m ON ((pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayaranmetode_t.pembayaran_id) pembayaranmetode ON ((pembayaran_t.pembayaran_id = pembayaranmetode.pembayaran_id)))
            UNION ALL
             SELECT \'UANG_MASUK\'::text AS tipe,
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
                0 AS total_dijamin,
                concat(jenisnontunai_m.tipe_pembayaran, \'=\',
                    CASE
                        WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
                        ELSE (0)::double precision
                    END) AS metode_pembayaran,
                bayaruangmuka_t.no_uangmuka AS no_pembayaran
               FROM ((((((((((((closingkasir_t
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
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m_1.tipe_pembayaran)::text, \'=\', (pembayaranmetode_t.total_dibayar)::text), \',\'::text) AS metode_pembayaran,
                        pembayaranmetode_t.pembayaran_id
                       FROM (pembayaranmetode_t
                         JOIN jenisnontunai_m jenisnontunai_m_1 ON ((pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m_1.jenisnontunai_id)))
                      GROUP BY pembayaranmetode_t.pembayaran_id) pembayaranmetode ON ((tandabuktibayar_t.pembayaran_id = pembayaranmetode.pembayaran_id)))
                 LEFT JOIN jenisnontunai_m ON ((bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
            UNION ALL
             SELECT \'RETUR\'::text AS tipe,
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
                0 AS total_dijamin,
                pembayaranmetode.metode_pembayaran,
                returbayarpelayanan_t.no_returbayar AS no_pembayaran
               FROM ((((((((((((((closingkasir_t
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
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayaranmetode_t.total_dibayar)::text), \',\'::text) AS metode_pembayaran,
                        pembayaranmetode_t.pembayaran_id
                       FROM (pembayaranmetode_t
                         JOIN jenisnontunai_m ON ((pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayaranmetode_t.pembayaran_id) pembayaranmetode ON ((pembayaran_t.pembayaran_id = pembayaranmetode.pembayaran_id)))
            UNION ALL
             SELECT \'PEMBAYARAN_PIUTANG\'::text AS tipe,
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
                0 AS total_dijamin,
                pembayaranpiutang.metode_pembayaran,
                pembayaranpiutang_t.no_pembayaranpiutang AS no_pembayaran
               FROM (((((((((((((((closingkasir_t
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
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayaranpiutang_t_1.total_bayarpiutang)::text), \',\'::text) AS metode_pembayaran,
                        pembayaranpiutang_t_1.pembayaranpiutang_id
                       FROM (pembayaranpiutang_t pembayaranpiutang_t_1
                         JOIN jenisnontunai_m ON ((pembayaranpiutang_t_1.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayaranpiutang_t_1.pembayaranpiutang_id) pembayaranpiutang ON ((pembayaranpiutang_t.pembayaranpiutang_id = pembayaranpiutang.pembayaranpiutang_id)))
            UNION ALL
             SELECT \'PEMBAYARAN_RESEP_BEBAS\'::text AS tipe,
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
                pembayaranpelayanan_t.tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
                (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
                pembayaran_t.total_nontunai,
                (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin,
                pembayaranmetode.metode_pembayaran,
                pembayaran_t.no_pembayaran
               FROM ((((((((((((closingkasir_t
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
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayaranmetode_t.total_dibayar)::text), \',\'::text) AS metode_pembayaran,
                        pembayaranmetode_t.pembayaran_id
                       FROM (pembayaranmetode_t
                         JOIN jenisnontunai_m ON ((pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayaranmetode_t.pembayaran_id) pembayaranmetode ON ((pembayaran_t.pembayaran_id = pembayaranmetode.pembayaran_id)))
            UNION ALL
             SELECT \'penerimaan\'::text AS tipe,
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
                0 AS total_dijamin,
                pembayarantransaksi.metode_pembayaran,
                penerimaan.no_transaksi AS no_pembayaran
               FROM (((((((((((closingkasir_t
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
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayarantransaksi_t.jumlah)::text), \',\'::text) AS metode_pembayaran,
                        pembayarantransaksi_t.pembayarantransaksi_id
                       FROM (pembayarantransaksi_t
                         JOIN jenisnontunai_m ON ((pembayarantransaksi_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayarantransaksi_t.pembayarantransaksi_id) pembayarantransaksi ON ((pembayarantransaksi.pembayarantransaksi_id = pembayarantransaksi.pembayarantransaksi_id)))
            UNION ALL
             SELECT \'pengeluaran\'::text AS tipe,
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
                0 AS total_dijamin,
                pembayarantransaksi.metode_pembayaran,
                pengeluaran.no_transaksi AS no_pembayaran
               FROM (((((((((((closingkasir_t
                 JOIN tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
                 JOIN pembayarantransaksi_t pengeluaran ON ((tandabuktikeluar_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((tandabuktikeluar_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m peg_pengeluaran ON ((pengeluaran.pegawai_id = peg_pengeluaran.pegawai_id)))
                 LEFT JOIN pasien_m ON ((pengeluaran.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN supplier_m ON ((pengeluaran.supplier_id = supplier_m.supplier_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
                 LEFT JOIN ( SELECT string_agg(concat((jenisnontunai_m.tipe_pembayaran)::text, \'=\', (pembayarantransaksi_t.jumlah)::text), \',\'::text) AS metode_pembayaran,
                        pembayarantransaksi_t.pembayarantransaksi_id
                       FROM (pembayarantransaksi_t
                         JOIN jenisnontunai_m ON ((pembayarantransaksi_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
                      GROUP BY pembayarantransaksi_t.pembayarantransaksi_id) pembayarantransaksi ON ((pembayarantransaksi.pembayarantransaksi_id = pembayarantransaksi.pembayarantransaksi_id)));

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_145311_hotfix_view_infoclosingkasir_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_145311_hotfix_view_infoclosingkasir_v cannot be reverted.\n";

        return false;
    }
    */
}
