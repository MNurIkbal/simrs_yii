<?php

use yii\db\Migration;

/**
 * Class m230919_065301_migrate_DSV582_view_laporanrekapkasir_v
 */
class m230919_065301_migrate_DSV582_view_laporanrekapkasir_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pembayarantransaksi_t ADD IF NOT EXISTS pendaftaran_id int4;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekapkasir_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanrekapkasir_v" AS  SELECT agr_bukti_bayar.jenis,
    agr_bukti_bayar.tglbuktibayar AS tanggal_pembayaran,
        CASE
            WHEN agr_bukti_bayar.jenis = \'PEMBAYARAN_PIUTANG\'::text THEN agr_bukti_bayar.no_identitas
            WHEN pendaftaran_t.no_pendaftaran IS NULL AND penjualanresep_t.noresep IS NULL THEN agr_bukti_bayar.no_identitas 
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep::text
            WHEN penjualanresep_t.noresep IS NULL THEN pendaftaran_t.no_pendaftaran::text
            ELSE NULL::text
        END AS no_pendaftaran,
    agr_bukti_bayar.no_pembayaran AS no_kwitansi,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL AND penjualanresep_t.noresep IS NULL THEN agr_bukti_bayar.nama_identitas
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli::text
            WHEN penjualanresep_t.nama_pembeli IS NULL THEN pasien_m.nama_pasien::text
            ELSE NULL::text
        END AS nama_pasien,
    COALESCE(agr_bukti_bayar.total_tagihan, 0::double precision) + agr_bukti_bayar.total_discount AS jumlah_tagihan,
    agr_bukti_bayar.deposite AS history_angsuran,
    COALESCE(total_eklaim.total, 0::double precision) AS nilai_koding,
    agr_bukti_bayar.total_discount AS diskon,
        CASE
            WHEN (COALESCE(agr_bukti_bayar.deposite, 0::double precision) - COALESCE(agr_bukti_bayar.total_tagihan, 0::double precision) + COALESCE(agr_bukti_bayar.penggunaan_uangmuka, 0::double precision)) < 0::double precision THEN 0::double precision
            ELSE
            CASE
                WHEN agr_bukti_bayar.carabayar_id = 5 THEN COALESCE(agr_bukti_bayar.deposite, 0::double precision) - COALESCE(agr_bukti_bayar.total_tagihan, 0::double precision) + COALESCE(agr_bukti_bayar.penggunaan_uangmuka, 0::double precision)
                ELSE COALESCE(agr_bukti_bayar.deposite, 0::double precision) - COALESCE(agr_bukti_bayar.penggunaan_uangmuka, 0::double precision)
            END
        END AS pengembalian,
    pegawai_m.nama_pegawai AS kasir,
    COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) AS tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS penjamin,
        CASE
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) > 0::double precision THEN concat(\'Cash, \', pembayaranmetode_t.metode_bayar, \', \', agr_bukti_bayar.penjamin_nama)
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) > 0::double precision THEN concat(\'Cash, \', pembayaranmetode_t.metode_bayar)
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) < 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) > 0::double precision THEN concat(\'Cash, \', pembayaranmetode_t.metode_bayar)
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) > 0::double precision THEN concat(\'Cash, \', COALESCE(agr_bukti_bayar.penjamin_nama, \'\'::text))
            WHEN COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) > 0::double precision THEN concat(agr_bukti_bayar.penjamin_nama, \', \', pembayaranmetode_t.metode_bayar)
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) = 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) = 0::double precision THEN concat(\'Cash\')
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) = 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) > 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) = 0::double precision THEN concat(pembayaranmetode_t.metode_bayar)
            WHEN COALESCE(agr_bukti_bayar.total_tunai, 0::double precision) = 0::double precision AND COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) = 0::double precision AND COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) > 0::double precision THEN concat(pembayaranpenjamin_t.penjamin_nama)
            ELSE NULL::text
        END AS keterangan,
    pembayarantransaksi_t.lain_lain_tunai,
    pembayaranpiutang_t.total_bayarpiutang AS piutang,
    pembayarantransaksi_t.lain_lain_non_tunai,
    pembayarantransaksi_t.keterangan AS keterangan_lain_lain,
    agr_bukti_bayar.total_tagihan AS nilai_omset
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
            pembayaran_t.catatan AS keterangan,
            pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran AS total_discount,
            uang_muka.deposite,
            pembayaran_t.penggunaan_uangmuka
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
                    COALESCE(pemberianpiutang_t.total_piutang) AS total_piutang,
                    a.is_deleted,
                    a.penggunaan_uangmuka
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
             LEFT JOIN ( SELECT sum(bayaruangmuka_t.jumlah_uangmuka) AS deposite,
                    bayaruangmuka_t.pendaftaran_id
                   FROM bayaruangmuka_t
                  WHERE bayaruangmuka_t.is_deleted IS FALSE
                  GROUP BY bayaruangmuka_t.pendaftaran_id) uang_muka ON pembayaran_t.pendaftaran_id = uang_muka.pendaftaran_id
          WHERE tandabuktibayar_t.is_deleted IS FALSE
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
            bayaruangmuka_t.keterangan_uangmuka AS keterangan,
            0 AS total_discount,
            bayaruangmuka_t.jumlah_uangmuka AS deposite,
            0 AS penggunaan_uangmuka
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
            penjualanresep.catatan AS keterangan,
            pembayaran_t.total_discount,
            0 AS deposite,
            0 AS penggunaan_uangmuka
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
          WHERE pembayaranpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN \'PENERIMAAN\'::text
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN \'PENGELUARAN\'::text
                    ELSE NULL::text
                END AS jenis,
            penerimaan.tandabuktibayar_id,
            pengeluaran.tandabuktikeluar_id,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.ruangan_id
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.ruangan_id
                    ELSE NULL::integer
                END AS ruangan_id,
            NULL::integer AS bayaruangmuka_id,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.closingkasir_id
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.closingkasir_id
                    ELSE NULL::integer
                END AS closingkasir_id,
            NULL::integer AS pembayaranpelayanan_id,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.shift_id
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.shift_id
                    ELSE NULL::integer
                END AS shift_id,
            NULL::integer AS nourutkasir,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.nobuktibayar
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.no_buktikeluar
                    ELSE NULL::character varying
                END AS nobuktibayar,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.tglbuktibayar
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.tgl_buktikeluar
                    ELSE NULL::timestamp without time zone
                END AS tglbuktibayar,
                CASE
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 668 THEN penerimaan.uangditerima
                    WHEN pembayarantransaksi_t_1.jenis_transaksi = 669 THEN pengeluaran.uang_diterima
                    ELSE 0::double precision
                END AS uangditerima,
            loginpemakai_k.pegawai_id,
            pembayarantransaksi_t_1.no_transaksi AS no_pembayaran,
            NULL::integer AS pendaftaran_id,
            NULL::integer AS carabayar_id,
            NULL::character varying AS carabayar_nama,
            NULL::integer AS penjamin_id,
            NULL::character varying AS penjamin_nama,
            0 AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
                CASE
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 28 AND pembayarantransaksi_t_1.jenis_transaksi = 668 THEN pembayarantransaksi_t_1.jumlah
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 28 AND pembayarantransaksi_t_1.jenis_transaksi = 669 THEN - pembayarantransaksi_t_1.jumlah
                    ELSE 0::double precision
                END AS total_nontunai,
                CASE
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 27 AND pembayarantransaksi_t_1.jenis_transaksi = 668 THEN pembayarantransaksi_t_1.jumlah
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 27 AND pembayarantransaksi_t_1.jenis_transaksi = 669 THEN - pembayarantransaksi_t_1.jumlah
                    ELSE 0::double precision
                END AS total_tunai,
            0 AS total_tagihan,
            pembayarantransaksi_t_1.pembayarantransaksi_id,
                CASE
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 700 THEN supplier_m.supplier_nama
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 701 THEN pegawai_m_1.nama_pegawai
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 702 THEN pasien.nama_pasien
                    ELSE NULL::character varying
                END AS nama_identitas,
                CASE
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 700 THEN supplier_m.supplier_kode
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 701 THEN pegawai_m_1.nomorindukpegawai
                    WHEN pembayarantransaksi_t_1.tipe_transaksi = 702 THEN pasien.no_rekam_medik
                    ELSE NULL::character varying
                END AS no_identitas,
            concat(kategoritransaksi_m.kategoritransaksi_nama, \' - \', COALESCE(pembayarantransaksi_t_1.deskripsi, \'\'::text)) AS keterangan,
            0 AS total_discount,
            0 AS deposite,
            0 AS penggunaan_uangmuka
           FROM pembayarantransaksi_t pembayarantransaksi_t_1
             LEFT JOIN ( SELECT f.penerimaanumum_id,
                    f.tandabuktibayar_id,
                    f.ruangan_id,
                    f.closingkasir_id,
                    f.shift_id,
                    f.nobuktibayar,
                    f.tglbuktibayar,
                    f.uangditerima
                   FROM tandabuktibayar_t f) penerimaan ON pembayarantransaksi_t_1.pembayarantransaksi_id = penerimaan.penerimaanumum_id
             LEFT JOIN ( SELECT f.tandabuktikeluar_id,
                    f.pembayarantransaksi_id,
                    f.ruangan_id,
                    f.closingkasir_id,
                    f.shift_id,
                    f.no_buktikeluar,
                    f.tgl_buktikeluar,
                    f.uang_diterima
                   FROM tandabuktikeluar_t f) pengeluaran ON pembayarantransaksi_t_1.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id
             LEFT JOIN ( SELECT f.pasien_id,
                    f.nama_pasien,
                    f.no_rekam_medik
                   FROM pasien_m f) pasien ON pembayarantransaksi_t_1.pasien_id = pasien.pasien_id
             LEFT JOIN ( SELECT f.pegawai_id,
                    f.nama_pegawai,
                    f.nomorindukpegawai
                   FROM pegawai_m f) pegawai_m_1 ON pembayarantransaksi_t_1.pegawai_id = pegawai_m_1.pegawai_id
             LEFT JOIN ( SELECT f.supplier_id,
                    f.supplier_nama,
                    f.supplier_kode
                   FROM supplier_m f) supplier_m ON pembayarantransaksi_t_1.supplier_id = supplier_m.supplier_id
             JOIN ( SELECT f.loginpemakai_id,
                    f.pegawai_id
                   FROM loginpemakai_k f) loginpemakai_k ON pembayarantransaksi_t_1.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.kategoritransaksi_id,
                    a.kategoritransaksi_nama
                   FROM kategoritransaksi_m a) kategoritransaksi_m ON pembayarantransaksi_t_1.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
          WHERE pembayarantransaksi_t_1.jenis_transaksi = 668 AND pembayarantransaksi_t_1.is_deleted IS FALSE) agr_bukti_bayar
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
           FROM penjualanresep_t penjualanresep) penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id
     LEFT JOIN pegawai_m ON agr_bukti_bayar.pegawai1_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT pembayaranmetode_t_1.pembayaran_id,
            string_agg(pembayaranmetode_t_1.metode_bayar::text, \', \'::text) AS metode_bayar
           FROM pembayaranmetode_t pembayaranmetode_t_1
             JOIN ( SELECT a.jenisnontunai_id,
                    a.tipe_pembayaran
                   FROM jenisnontunai_m a) jenisnontunai_m ON pembayaranmetode_t_1.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = lkp_tipe_pembayaran.lookup_id
          GROUP BY pembayaranmetode_t_1.pembayaran_id) pembayaranmetode_t ON pembayaranmetode_t.pembayaran_id = agr_bukti_bayar.pembayaran_id
     LEFT JOIN ( SELECT pembayaranpenjamin_t_1.pembayaran_id,
            pembayaranpenjamin_t_1.penjamin_nama
           FROM pembayaranpenjamin_t pembayaranpenjamin_t_1
          WHERE pembayaranpenjamin_t_1.is_deleted = false) pembayaranpenjamin_t ON agr_bukti_bayar.pembayaran_id = pembayaranpenjamin_t.pembayaran_id
     LEFT JOIN ( SELECT sy_klaimgroup_t.total,
            sy_kunjungan.no_pendaftaran
           FROM sy_klaimgroup_t
             JOIN ( SELECT DISTINCT a.sy_klaiminacbg_id,
                    a.kunjungan_id
                   FROM sy_klaiminacbg a) sy_klaiminacbg ON sy_klaimgroup_t.sy_klaiminacbg_id = sy_klaiminacbg.sy_klaiminacbg_id
             JOIN sy_kunjungan ON sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id) total_eklaim ON pendaftaran_t.no_pendaftaran::text = total_eklaim.no_pendaftaran::text
     LEFT JOIN ( SELECT pembayarantransaksi_t_1.pendaftaran_id,
                CASE
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 27 THEN sum(pembayarantransaksi_t_1.jumlah)
                    ELSE 0::double precision
                END AS lain_lain_tunai,
                CASE
                    WHEN pembayarantransaksi_t_1.metode_pembayaran = 28 THEN sum(pembayarantransaksi_t_1.jumlah)
                    ELSE 0::double precision
                END AS lain_lain_non_tunai,
            jenisnontunai_m.nama AS keterangan
           FROM pembayarantransaksi_t pembayarantransaksi_t_1
             LEFT JOIN jenisnontunai_m ON pembayarantransaksi_t_1.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
          WHERE pembayarantransaksi_t_1.jenis_transaksi = 668 AND pembayarantransaksi_t_1.is_deleted IS FALSE
          GROUP BY pembayarantransaksi_t_1.pendaftaran_id, pembayarantransaksi_t_1.metode_pembayaran, jenisnontunai_m.nama) pembayarantransaksi_t ON pendaftaran_t.pendaftaran_id = pembayarantransaksi_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            sum(a.total_bayarpiutang) AS total_bayarpiutang
           FROM pembayaranpiutang_t a
          WHERE a.is_deleted IS FALSE
          GROUP BY a.pendaftaran_id) pembayaranpiutang_t ON pendaftaran_t.pendaftaran_id = pembayaranpiutang_t.pendaftaran_id
  ORDER BY agr_bukti_bayar.tglbuktibayar;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_065301_migrate_DSV582_view_laporanrekapkasir_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_065301_migrate_DSV582_view_laporanrekapkasir_v cannot be reverted.\n";

        return false;
    }
    */
}
