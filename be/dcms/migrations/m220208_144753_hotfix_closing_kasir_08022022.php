<?php

use yii\db\Migration;

/**
 * Class m220208_144753_hotfix_closing_kasir_08022022
 */
class m220208_144753_hotfix_closing_kasir_08022022 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP VIEW IF EXISTS "public"."closing_kasir_view";');

		        $this->execute("
		            CREATE VIEW public.closing_kasir_view AS  SELECT agr_bukti_bayar.jenis,
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
            WHEN agr_bukti_bayar.jenis = 'PEMBAYARAN_PIUTANG'::text THEN agr_bukti_bayar.no_identitas
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
                    fgetnamalookup(jenisnontunai_m.tipe_pembayaran) AS tipe
                   FROM pembayaranmetode_t
                     JOIN jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
                  WHERE pembayaranmetode_t.pembayaran_id = agr_bukti_bayar.pembayaran_id) x) AS additional_nontunai,
    NULL::text AS keterangan
   FROM ( SELECT 'PEMBAYARAN'::text AS jenis,
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
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.pendaftaran_id,
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
           FROM tandabuktibayar_t
             JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    pembayaran_t.total_nontunai,
                    pembayaran_t.total_ditagihkan,
                    pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS total_tunai,
                    pembayaran_t.total_tagihan + pembayaran_t.total_administrasi - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) AS total_tagihan
                   FROM pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE pembayaranmetode_t.is_deleted = false
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE pembayaran_t.is_deleted = false) pembayaran_penjamin ON pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false AND pembayaranpelayanan_t.penjualanresep_id IS NULL AND pembayaranpelayanan_t.pendaftaran_id IS NOT NULL AND tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = false
        UNION ALL
         SELECT 'UANG_MASUK'::text AS jenis,
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
            bayaruangmuka_t.no_uangmuka AS no_pembayaran,
            bayaruangmuka_t.pendaftaran_id,
                CASE
                    WHEN pendaftaran_t_1.pasienadmisi_id IS NULL THEN pendaftaran_t_1.carabayar_id
                    ELSE pasienadmisi_t.carabayar_id
                END AS carabayar_id,
                CASE
                    WHEN pendaftaran_t_1.pasienadmisi_id IS NULL THEN carabayar_pendaftaran.carabayar_nama
                    ELSE carabayar_admisi.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN pendaftaran_t_1.pasienadmisi_id IS NULL THEN pendaftaran_t_1.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN pendaftaran_t_1.pasienadmisi_id IS NULL THEN penjamin_pendaftaran.penjamin_nama
                    ELSE penjamin_admisi.penjamin_nama
                END AS penjamin_nama,
            bayaruangmuka_t.jumlah_uangmuka AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            0 AS total_penjamin,
                CASE
                    WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN tandabuktibayar_t.uangditerima
                    ELSE 0::double precision
                END AS total_nontunai,
                CASE
                    WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN tandabuktibayar_t.uangditerima
                    ELSE 0::double precision
                END AS total_tunai,
            0 AS total_tagihan,
            NULL::bigint AS pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM tandabuktibayar_t
             JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
             JOIN pendaftaran_t pendaftaran_t_1 ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN carabayar_m carabayar_pendaftaran ON pendaftaran_t_1.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN carabayar_m carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
             LEFT JOIN penjamin_m penjamin_pendaftaran ON pendaftaran_t_1.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN penjamin_m penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false AND tandabuktibayar_t.bayaruangmuka_id IS NOT NULL
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
            - returbayarpelayanan_t.total_nontunai AS total_nontunai,
            - returbayarpelayanan_t.total_biayaretur AS total_tunai,
            0 AS total_tagihan,
            pembayaran_t.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM returbayarpelayanan_t
             JOIN tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
             JOIN tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
             JOIN pembayaranpelayanan_t ON tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN loginpemakai_k ON returbayarpelayanan_t.created_by = loginpemakai_k.loginpemakai_id
          WHERE returbayarpelayanan_t.is_deleted = false AND tandabuktibayar_t.is_deleted = false AND pembayaranpelayanan_t.is_deleted = false
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
                    WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN carabayar_resep.carabayar_nama
                    ELSE carabayar_m_1.carabayar_nama
                END AS carabayar_nama,
            NULL::integer AS penjamin_id,
                CASE
                    WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN penjamin_resep.penjamin_nama
                    ELSE penjamin_m_1.penjamin_nama
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
            penjualanresep_t_1.nama_pembeli AS nama_identitas,
            pembayaranpiutang_t.no_pembayaranpiutang AS no_identitas
           FROM pembayaranpiutang_t
             JOIN pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             JOIN tandabuktibayar_t ON pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id
             JOIN loginpemakai_k ON pembayaranpiutang_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN pendaftaran_t pendaftaran_t_1 ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
             LEFT JOIN penjualanresep_t penjualanresep_t_1 ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pendaftaran_t_1.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN carabayar_m carabayar_resep ON penjualanresep_t_1.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pendaftaran_t_1.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN penjamin_m penjamin_resep ON penjualanresep_t_1.penjamin_id = penjamin_resep.penjamin_id
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
            pembayaran_t.total_dijamin AS total_penjamin,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS total_tunai,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) AS total_tagihan,
            pembayaran_t.pembayaran_id,
            NULL::text AS nama_identitas,
            NULL::text AS no_identitas
           FROM pembayaranpelayanan_t
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN penjualanresep_t penjualanresep_t_1 ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t_1.penjualanresep_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             JOIN loginpemakai_k ON pembayaran_t.created_by = loginpemakai_k.loginpemakai_id
             JOIN penjamin_m penjamin_m_1 ON penjualanresep_t_1.penjamin_id = penjamin_m_1.penjamin_id
             JOIN carabayar_m carabayar_m_1 ON penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
          WHERE penjualanresep_t_1.pendaftaran_id IS NULL AND pembayaranpelayanan_t.is_deleted = false
        UNION ALL
         SELECT
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN 'PENERIMAAN'::text
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN 'PENGELUARAN'::text
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
                    WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien_m_1.nama_pasien
                    ELSE NULL::character varying
                END AS nama_identitas,
                CASE
                    WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_kode
                    WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nomorindukpegawai
                    WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien_m_1.no_rekam_medik
                    ELSE NULL::character varying
                END AS no_identitas
           FROM pembayarantransaksi_t
             LEFT JOIN tandabuktibayar_t penerimaan ON pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id
             LEFT JOIN tandabuktikeluar_t pengeluaran ON pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id
             LEFT JOIN pasien_m pasien_m_1 ON pembayarantransaksi_t.pasien_id = pasien_m_1.pasien_id
             LEFT JOIN pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
             JOIN loginpemakai_k ON pembayarantransaksi_t.created_by = loginpemakai_k.loginpemakai_id) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id; ");
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220208_144753_hotfix_closing_kasir_08022022 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220208_144753_hotfix_closing_kasir_08022022 cannot be reverted.\n";

        return false;
    }
    */
}
