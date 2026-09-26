<?php

use yii\db\Migration;

/**
 * Class m200303_062211_closing_kasir_view
 */
class m200303_062211_closing_kasir_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.closing_kasir_view;');
        $this->execute("
            CREATE OR REPLACE VIEW public.closing_kasir_view AS
             SELECT agr_bukti_bayar.tandabuktibayar_id,
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
                            CASE
                                WHEN (pembayaran_penjamin.total_tunai < (0)::double precision) THEN (0)::double precision
                                ELSE pembayaran_penjamin.total_tunai
                            END AS total_tunai,
                        pembayaran_penjamin.total_tagihan,
                        pembayaran_penjamin.pembayaran_id
                       FROM (((((tandabuktibayar_t
                         LEFT JOIN bayaruangmuka_t ON ((bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
                         LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
                         LEFT JOIN ( SELECT (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_penjamin,
                                pembayaran_t.pembayaran_id,
                                pembayaran_t.total_nontunai,
                                    CASE
                                        WHEN ((pembayaran_t.total_tunai - pembayaran_t.total_kembalian) < (0)::double precision) THEN (0)::double precision
                                        ELSE (pembayaran_t.total_tunai - pembayaran_t.total_kembalian)
                                    END AS total_tunai,
                                (pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) AS total_tagihan
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
                        tandabuktibayar_t.tandabuktibayar_id,
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
                        returbayarpelayanan_t.no_returbayar AS no_pembayaran,
                        pembayaranpelayanan_t.pendaftaran_id,
                        pembayaranpelayanan_t.carabayar_id,
                        NULL::character varying AS carabayar_nama,
                        pembayaranpelayanan_t.penjamin_id,
                        NULL::character varying AS penjamin_nama,
                        pembayaran_t.total_dibayar AS jmlpembayaran,
                        NULL::integer AS penjualanresep_id,
                        NULL::double precision AS total_penjamin,
                        (- returbayarpelayanan_t.total_nontunai),
                        (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
                        0 AS total_tagihan,
                        pembayaran_t.pembayaran_id
                       FROM (((returbayarpelayanan_t
                         JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktibayar_t.returbayarpelayanan_id)))
                         JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
                         JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                      WHERE ((returbayarpelayanan_t.is_deleted = false) AND (tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.is_deleted = false))) agr_bukti_bayar
                 LEFT JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id)))
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pasien_m ON ((pasien_m.pasien_id = pendaftaran_t.pasien_id)))
                 LEFT JOIN penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id)));");
         $this->execute('ALTER TABLE public.closing_kasir_view OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200303_062211_closing_kasir_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200303_062211_closing_kasir_view cannot be reverted.\n";

        return false;
    }
    */
}
