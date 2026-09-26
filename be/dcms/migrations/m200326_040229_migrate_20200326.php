<?php

use yii\db\Migration;

/**
 * Class m200326_040229_migrate_20200326
 */
class m200326_040229_migrate_20200326 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pesanobatalkes_t" ALTER COLUMN "status_verifikasi" DROP DEFAULT;');

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
          WHERE (tandabuktibayar_t.is_deleted = false)
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
            pembayaranpiutang_t.pendaftaran_id,
            NULL::integer AS carabayar_id,
            NULL::character varying AS carabayar_nama,
            NULL::integer AS penjamin_id,
            NULL::character varying AS penjamin_nama,
            pembayaranpiutang_t.total_bayarpiutang,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
            pembayaranpiutang_t.total_bayarpiutang AS total_nontunai,
            pembayaranpiutang_t.total_bayarpiutang AS total_tunai,
            pembayaran_t.total_tagihan,
            pembayaran_t.pembayaran_id
           FROM ((((pembayaranpiutang_t
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pembayaranpiutang_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id)))
             JOIN loginpemakai_k ON ((pembayaranpiutang_t.created_by = loginpemakai_k.loginpemakai_id)))
             LEFT JOIN pembayaran_t ON ((pendaftaran_t_1.pendaftaran_id = pembayaran_t.pendaftaran_id)))
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
            penjualanresep_t_1.carabayar_id,
            NULL::character varying AS carabayar_nama,
            penjualanresep_t_1.penjamin_id,
            NULL::character varying AS penjamin_nama,
            pembayaran_t.total_dibayar,
            pembayaranpelayanan_t.penjualanresep_id,
            NULL::double precision AS total_penjamin,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_tunai,
            pembayaran_t.total_tagihan,
            pembayaran_t.pembayaran_id
           FROM ((((pembayaranpelayanan_t
             JOIN penjualanresep_t penjualanresep_t_1 ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             JOIN loginpemakai_k ON ((pembayaranpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
             JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
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

         $this->execute('DROP VIEW if exists "public"."infopemesananobatalkes_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopemesananobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatalkes_t.mutasiobatruangan_id,
    pesanobatalkes_t.statuspesan,
    fgetnamalookup((pesanobatalkes_t.statuspesan)::integer) AS status_pengiriman,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatalkes_t.keterangan_pesan,
    pesanobatalkes_t.status_verifikasi,
        CASE
            WHEN (pesanobatalkes_t.status_verifikasi IS NULL) THEN 'Tanpa Verifkasi'::character varying
            ELSE fgetnamalookup((pesanobatalkes_t.status_verifikasi)::integer)
        END AS status_verifikasi_nama
   FROM (((((pesanobatalkes_t
     JOIN ruangan_m ON ((pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangpemesan ON ((pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id)))
     JOIN instalasi_m instalasipesan ON ((ruangpemesan.instalasi_id = instalasipesan.instalasi_id)))
     LEFT JOIN mutasiobatruangan_t ON ((pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
  WHERE ((pesanobatalkes_t.is_active = true) AND (pesanobatalkes_t.is_deleted = false));");

         $this->execute('ALTER TABLE "public"."infopemesananobatalkes_v" OWNER TO "postgres";');

         $this->execute('DROP VIEW if exists "public"."infodistribusiobatalkes_v";');
         
         $this->execute("
            CREATE VIEW \"public\".\"infodistribusiobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatalkes_t.mutasiobatruangan_id,
    pesanobatalkes_t.statuspesan,
    fgetnamalookup((pesanobatalkes_t.statuspesan)::integer) AS status_pengiriman,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatalkes_t.keterangan_pesan,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.status_mutasi,
    fgetnamalookup(mutasiobatruangan_t.status_mutasi) AS status_penerimaan,
    terimamutasiobat_t.noterimamutasi,
    terimamutasiobat_t.tglterima,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
        CASE
            WHEN ((pesanobatalkes_t.statuspesan)::text = '398'::text) THEN 'Belum Dikirim'::text
            WHEN (mutasiobatruangan_t.status_mutasi = 401) THEN 'Sudah Dikirim'::text
            WHEN (mutasiobatruangan_t.status_mutasi = 400) THEN 'Diterima'::text
            ELSE '-'::text
        END AS status_distribusi,
    concat(mutasiobatruangan_t.nomutasioa,
        CASE
            WHEN (terimamutasiobat_t.noterimamutasi IS NULL) THEN ''::text
            ELSE concat(',', terimamutasiobat_t.noterimamutasi)
        END) AS reference,
        CASE
            WHEN (pesanobatalkes_t.mutasiobatruangan_id IS NULL) THEN (pesanobatalkes_t.statuspesan)::integer
            WHEN (pesanobatalkes_t.mutasiobatruangan_id IS NOT NULL) THEN mutasiobatruangan_t.status_mutasi
            WHEN (terimamutasiobat_t.mutasiobatruangan_id IS NOT NULL) THEN mutasiobatruangan_t.status_mutasi
            ELSE NULL::integer
        END AS status_id,
    pesanobatalkes_t.status_verifikasi,
        CASE
            WHEN (pesanobatalkes_t.status_verifikasi IS NULL) THEN 'Tanpa Verifkasi'::character varying
            ELSE fgetnamalookup((pesanobatalkes_t.status_verifikasi)::integer)
        END AS status_verifikasi_nama
   FROM ((((((pesanobatalkes_t
     JOIN ruangan_m ON ((pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangpemesan ON ((pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id)))
     JOIN instalasi_m instalasipesan ON ((ruangpemesan.instalasi_id = instalasipesan.instalasi_id)))
     LEFT JOIN mutasiobatruangan_t ON ((pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
     LEFT JOIN terimamutasiobat_t ON ((mutasiobatruangan_t.mutasiobatruangan_id = terimamutasiobat_t.mutasiobatruangan_id)))
  WHERE ((pesanobatalkes_t.is_active = true) AND (pesanobatalkes_t.is_deleted = false));
");
         
         $this->execute('ALTER TABLE "public"."infodistribusiobatalkes_v" OWNER TO "postgres";');
         
         $this->execute('DROP VIEW if exists "public"."infopembayarantransaksi_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopembayarantransaksi_v\" AS  SELECT pembayarantransaksi_t.pembayarantransaksi_id,
    pembayarantransaksi_t.jenis_transaksi,
    fgetnamalookup((pembayarantransaksi_t.jenis_transaksi)::integer) AS jenis,
    pembayarantransaksi_t.tgl_transaksi,
    pembayarantransaksi_t.no_transaksi,
    pembayarantransaksi_t.tipe_transaksi,
    fgetnamalookup((pembayarantransaksi_t.tipe_transaksi)::integer) AS tipe,
    pembayarantransaksi_t.supplier_id,
    supplier_m.supplier_nama,
    pembayarantransaksi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pembayarantransaksi_t.pasien_id,
    pasien_m.nama_pasien,
    pembayarantransaksi_t.metode_pembayaran,
    fgetnamalookup((pembayarantransaksi_t.metode_pembayaran)::integer) AS metode_pembayaran_nama,
    pembayarantransaksi_t.jumlah,
    pembayarantransaksi_t.kategoritransaksi_id,
    kategoritransaksi_m.kategoritransaksi_nama,
    pembayarantransaksi_t.deskripsi,
    pembayarantransaksi_t.referensi,
        CASE
            WHEN (pembayarantransaksi_t.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (pembayarantransaksi_t.tipe_transaksi = 701) THEN pegawai_m.nama_pegawai
            WHEN (pembayarantransaksi_t.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS dari_kepada,
    pembayarantransaksi_t.created_by
   FROM ((((pembayarantransaksi_t
     LEFT JOIN pegawai_m ON ((pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasien_m ON ((pembayarantransaksi_t.pasien_id = pasien_m.pegawai_id)))
     LEFT JOIN supplier_m ON ((pembayarantransaksi_t.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN kategoritransaksi_m ON ((pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id)))
  WHERE (pembayarantransaksi_t.is_deleted = false);");

         $this->execute('ALTER TABLE "public"."infopembayarantransaksi_v" OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200326_040229_migrate_20200326 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200326_040229_migrate_20200326 cannot be reverted.\n";

        return false;
    }
    */
}
