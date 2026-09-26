<?php

use yii\db\Migration;

/**
 * Class m200326_163440_migrate_20200326_2
 */
class m200326_163440_migrate_20200326_2 extends Migration
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
            WHEN (pendaftaran_t.no_pendaftaran IS NULL) THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
        CASE
            WHEN (agr_bukti_bayar.total_tagihan IS NULL) THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tagihan
        END AS jmlpembayaran,
        CASE
            WHEN (agr_bukti_bayar.total_tunai IS NULL) THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
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
            tandabuktibayar_t.jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
            pembayaran_penjamin.total_tunai,
            pembayaran_penjamin.total_tagihan,
            pembayaran_penjamin.pembayaran_id
           FROM (((((tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON ((bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
             LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             LEFT JOIN ( SELECT (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    pembayaran_t.total_nontunai,
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
          WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL))
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
            (- returbayarpelayanan_t.total_nontunai),
            (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
            0 AS total_tagihan,
            pembayaran_t.pembayaran_id
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
            pembayaranpiutang_t.total_bayarpiutang,
            pemberianpiutang_t.penjualanresep_id,
            NULL::double precision AS total_penjamin,
            0 AS total_nontunai,
            pembayaranpiutang_t.total_bayarpiutang AS total_tunai,
            pemberianpiutang_t.total_sisapiutang AS total_tagihan,
            NULL::bigint AS pembayaran_id
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
            pembayaran_t.total_dibayar,
            pembayaranpelayanan_t.penjualanresep_id,
            NULL::double precision AS total_penjamin,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_tunai,
            pembayaran_t.total_tagihan,
            pembayaran_t.pembayaran_id
           FROM ((((((pembayaranpelayanan_t
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN penjualanresep_t penjualanresep_t_1 ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN loginpemakai_k ON ((pembayaran_t.created_by = loginpemakai_k.loginpemakai_id)))
             JOIN penjamin_m penjamin_m_1 ON ((penjualanresep_t_1.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN carabayar_m carabayar_m_1 ON ((penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id)))
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
            pembayarantransaksi_t.jumlah AS total_dibayar,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN pembayarantransaksi_t.jumlah
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE (0)::double precision
                END AS total_nontunai,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN pembayarantransaksi_t.jumlah
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE (0)::double precision
                END AS total_tunai,
            pembayarantransaksi_t.jumlah AS total_tagihan,
            pembayarantransaksi_t.pembayarantransaksi_id
           FROM (((pembayarantransaksi_t
             LEFT JOIN tandabuktibayar_t penerimaan ON ((pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id)))
             LEFT JOIN tandabuktikeluar_t pengeluaran ON ((pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
             JOIN loginpemakai_k ON ((pembayarantransaksi_t.created_by = loginpemakai_k.loginpemakai_id)))) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id)))
     LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((pasien_m.pasien_id = pendaftaran_t.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id)));");
        
        $this->execute('ALTER TABLE "public"."closing_kasir_view" OWNER TO "postgres";');
        
        $this->execute('DROP VIEW if exists "public"."detailpemesananobatalkes_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"detailpemesananobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.keterangan_pesan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    pesanobatdetail_t.jumlah_pesan,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatdetail_t.mutasiobatdetail_id,
    pesanobatdetail_t.satuankecil_id,
    pesanobatdetail_t.pesanobatdetail_id,
    pesanobatdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanobatdetail_t.jumlah_input AS qty_besar,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.discount,
    pesanobatdetail_t.satuan_pemesanan,
    sat_pemesanan.satuanunit_nama AS satuan_pemesanan_nama,
    pesanobatdetail_t.jumlah_pesan AS qty_kecil,
    pesanobatalkes_t.statuspesan,
    COALESCE(stok_pengirim.qty_tersedia, (0)::double precision) AS stok_pengirim,
    COALESCE(stok_pemesan.qty_tersedia, (0)::double precision) AS stok_pemesan
   FROM (((((((((((pesanobatdetail_t
     JOIN pesanobatalkes_t ON ((pesanobatdetail_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
     JOIN ruangan_m ON ((pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangpemesan ON ((pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id)))
     JOIN instalasi_m instalasipesan ON ((ruangpemesan.instalasi_id = instalasipesan.instalasi_id)))
     JOIN obatalkes_m ON ((pesanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_besar ON ((pesanobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((pesanobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_pemesanan ON ((pesanobatdetail_t.satuan_pemesanan = sat_pemesanan.satuanunit_id)))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pengirim ON (((pesanobatalkes_t.ruangan_id = stok_pengirim.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pengirim.obatalkes_id))))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pemesan ON (((pesanobatalkes_t.ruanganpemesan_id = stok_pemesan.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pemesan.obatalkes_id))))
  WHERE ((pesanobatdetail_t.is_active = true) AND (pesanobatdetail_t.is_deleted = false));");
        
        $this->execute('ALTER TABLE "public"."detailpemesananobatalkes_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infodatapendaftaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infodatapendaftaran_v\" AS  SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN (data_info.tglpasienpulang IS NULL) THEN data_info.tglpasienpulang_ri
            ELSE data_info.tglpasienpulang
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idpendaftaran IS NOT NULL)) THEN data_info.no_bpjspendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NOT NULL)) THEN data_info.no_bpjsadmisi
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransipendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran,
    (data_info.tagihan_belumbayar)::integer AS tagihan_belumbayar
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            ((COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, (0)::double precision)) - COALESCE(pengembalianuangmuka_t.total_pengembalian, (0)::double precision)) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN (penjualan_resep.biaya_adm IS NULL) THEN (0)::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran,
            COALESCE(belum_bayar.total_tagihan, (0)::double precision) AS tagihan_belumbayar
           FROM ((((((((((((((((((((((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN ruangan_m ruang_ri ON ((pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ruang_ri.instalasi_id = ins_ri.instalasi_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN carabayar_m carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE (bayaruangmuka_t_1.is_deleted = false)
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
             LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE (pengembalianuangmuka_t_1.is_deleted = false)
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE (pemakaianuangmuka_t_1.is_deleted = false)
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
             LEFT JOIN pegawai_m dok_rj_rd ON ((pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id)))
             LEFT JOIN pegawai_m dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
             LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
             LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON ((pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id)))
             LEFT JOIN asuransipasien_m asuransi_admisi ON ((pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id)))
             LEFT JOIN pemberianpiutang_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE ((pt.status_bayar = 349) AND (pt.is_deleted = false))
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON ((penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM tindakanpelayanan_t
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) belum_bayar ON ((pendaftaran_t.pendaftaran_id = belum_bayar.pendaftaran_id)))
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) AS obat,
            ((COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision)) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            pemberianpiutang_t.total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran,
            (tagihan_resep.tagihan_obat)::integer AS tagihan_belumbayar
           FROM ((((((((penjualanresep_t
             LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN pemberianpiutang_t ON ((penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id)))
             LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                   FROM obatalkespasien_t
                  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                  GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON ((penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id)))
          WHERE (((penjualanresep_t.jenispenjualan)::text = ANY (ARRAY['343'::text, '345'::text])) AND (penjualanresep_t.is_deleted = false))) data_info;");
        
        $this->execute('ALTER TABLE "public"."infodatapendaftaran_v" OWNER TO "postgres";');
        
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
    pembayaran_t.total_dijamin
   FROM ((((((((((((closingkasir_t
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
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar, pembayaran_t.total_administrasi, pembayaran_t.total_discount, pembayaran_t.total_discountpembayaran
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
 SELECT 'resep_bebas'::text AS tipe,
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
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    pasien_m.pasien_id,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
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
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, penjualanresep_t.penjualanresep_id, penjualanresep_t.noresep, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
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
    closingkasir_t.ruangan_id,
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
    penerimaan.no_transaksi AS no_pendaftaran,
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
    penerimaan.jumlah AS total_tagihan,
        CASE
            WHEN (penerimaan.metode_pembayaran = 27) THEN penerimaan.jumlah
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (penerimaan.metode_pembayaran = 28) THEN (- penerimaan.jumlah)
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayarantransaksi_t penerimaan ON ((tandabuktibayar_t.penerimaanumum_id = penerimaan.pembayarantransaksi_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_penerimaan ON ((penerimaan.pegawai_id = peg_penerimaan.pegawai_id)))
     LEFT JOIN pasien_m ON ((penerimaan.pasien_id = pasien_m.pegawai_id)))
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
    pengeluaran.pembayarantransaksi_id AS pendaftaran_id,
    pengeluaran.no_transaksi AS no_pendaftaran,
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
    pengeluaran.jumlah AS total_tagihan,
        CASE
            WHEN (pengeluaran.metode_pembayaran = 27) THEN pengeluaran.jumlah
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
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_pengeluaran ON ((pengeluaran.pegawai_id = peg_pengeluaran.pegawai_id)))
     LEFT JOIN pasien_m ON ((pengeluaran.pasien_id = pasien_m.pegawai_id)))
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
                                (vrow->>'penjualanresep_id')::INTEGER,
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
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200326_163440_migrate_20200326_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200326_163440_migrate_20200326_2 cannot be reverted.\n";

        return false;
    }
    */
}
