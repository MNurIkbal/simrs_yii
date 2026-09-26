<?php

use yii\db\Migration;

/**
 * Class m200328_050824_migrate_20200327
 */
class m200328_050824_migrate_20200327 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
   FROM (((((((( SELECT 'PEMBAYARAN'::text AS jenis,
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
            pembayaranpelayanan_t.no_pembayaran,
                CASE
                    WHEN (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL) THEN bayaruangmuka_t.pendaftaran_id
                    WHEN (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) THEN pembayaranpelayanan_t.pendaftaran_id
                    ELSE NULL::integer
                END AS pendaftaran_id,
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
           FROM (((((tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON ((bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
             LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             LEFT JOIN ( SELECT (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_penjamin,
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
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (tandabuktibayar_t.penerimaanumum_id IS NULL))
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
            pemberianpiutang_t.penjualanresep_id,
            0 AS total_penjamin,
            0 AS total_nontunai,
            pembayaranpiutang_t.total_bayarpiutang AS total_tunai,
            pemberianpiutang_t.total_sisapiutang AS total_tagihan,
            NULL::bigint AS pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
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
            0 AS total_penjamin,
            pembayaran_t.total_nontunai,
            (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
            ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
            pembayaran_t.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM ((((((pembayaranpelayanan_t
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN penjualanresep_t penjualanresep_t_1 ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN loginpemakai_k ON ((pembayaran_t.created_by = loginpemakai_k.loginpemakai_id)))
             JOIN penjamin_m penjamin_m_1 ON ((penjualanresep_t_1.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN carabayar_m carabayar_m_1 ON ((penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id)))
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
     LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((pasien_m.pasien_id = pendaftaran_t.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id)));
");
        
        $this->execute('ALTER TABLE "public"."closing_kasir_view" OWNER TO "postgres";');
        
        $this->execute('DROP VIEW if exists "public"."infoclosingkasir_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infoclosingkasir_v\" AS  SELECT 'pembayaran'::text AS tipe,
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
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
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
   FROM (((((((((((((closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar, pembayaran_t.total_administrasi, pembayaran_t.total_discount, pembayaran_t.total_discountpembayaran, pemberianpiutang_t.total_piutang
UNION ALL
 SELECT 'uang_muka'::text AS tipe,
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
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    0 AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM (((((((((((closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.bayaruangmuka_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.tglbuktibayar, tandabuktibayar_t.uangditerima, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
UNION ALL
 SELECT 'retur'::text AS tipe,
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
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
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
   FROM ((((((((((((((closingkasir_t
     JOIN ( SELECT tandabuktikeluar_t_1.closingkasir_id,
            tandabuktikeluar_t_1.tgl_buktikeluar,
            tandabuktikeluar_t_1.returbayarpelayanan_id,
            tandabuktikeluar_t_1.jml_pembayaran,
            tandabuktikeluar_t_1.uang_diterima
           FROM tandabuktikeluar_t tandabuktikeluar_t_1
          GROUP BY tandabuktikeluar_t_1.closingkasir_id, tandabuktikeluar_t_1.tgl_buktikeluar, tandabuktikeluar_t_1.returbayarpelayanan_id, tandabuktikeluar_t_1.jml_pembayaran, tandabuktikeluar_t_1.uang_diterima) tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
     JOIN returbayarpelayanan_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktikeluar_t.uang_diterima, tandabuktikeluar_t.jml_pembayaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, returbayarpelayanan_t.total_biayaretur, returbayarpelayanan_t.total_nontunai, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar
UNION ALL
 SELECT 'pembayaran_piutang'::text AS tipe,
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
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
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
 SELECT 'penjualan_resep_bebas'::text AS tipe,
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
    0 AS total_tagihan,
    pembayaran_t.total_tunai,
    pembayaran_t.total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                                        total_dibayar

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'metode_bayar')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dibayar')::FLOAT                     
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
        
        $this->execute('DELETE from lookup_m WHERE lookup_id BETWEEN 668 and 702;');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(668, 'jenis_transaksi', 'Penerimaan', 'Penerimaan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(669, 'jenis_transaksi', 'Pengeluaran', 'Pengeluaran', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(700, 'tipe_transaksi', 'Vendor', 'Vendor', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(701, 'tipe_transaksi', 'Karyawan', 'Karyawan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(702, 'tipe_transaksi', 'Pasien', 'Pasien', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");


        $this->execute("DELETE FROM lookup_m where lookup_type='metode_bayar'");
        
        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(27, 'metode_bayar', 'Tunai', 'Tunai', 1, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(28, 'metode_bayar', 'Non Tunai', 'Non Tunai', 2, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(403, 'metode_bayar', 'Tunai & Non Tunai', 'Tunai & Non Tunai', 3, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 't', 't', NULL, NULL);
");
        
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200328_050824_migrate_20200327 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200328_050824_migrate_20200327 cannot be reverted.\n";

        return false;
    }
    */
}
