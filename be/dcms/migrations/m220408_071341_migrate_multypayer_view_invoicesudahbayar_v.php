<?php

use yii\db\Migration;

/**
 * Class m220408_071341_migrate_multypayer_view_invoicesudahbayar_v
 */
class m220408_071341_migrate_multypayer_view_invoicesudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoicesudahbayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoicesudahbayar_v" AS  SELECT tagihan.pendaftaran_id,
                tagihan.no_pendaftaran,
                pasien_m.no_rekam_medik,
                    CASE
                        WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
                        ELSE pasien_m.nama_pasien
                    END AS nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                dok_1.nama_pegawai AS dok_pendaftaran,
                dok_2.nama_pegawai AS dok_ranap,
                r_1.ruangan_nama AS r_pendaftaran,
                r_2.ruangan_nama AS r_ranap, 
                tagihan.carabayar_nama,
                tagihan.penjamin_nama,
                tagihan.total_tagihan,
                tagihan.total_uang_muka,
                tagihan.total_sudah_dibayarkan,
                (tagihan.total_sisatagihan)::integer AS total_sisatagihan,
                fgetnamalookup(tagihan.status_bayar) AS status_bayar,
                tagihan.tgl_pendaftaran,
                tagihan.pembayaranpelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.tandabuktibayar_id,
                (tagihan.pembulatan)::integer AS pembulatan,
                tagihan.biaya_administrasi,
                tagihan.tgl_pembayaran,
                tagihan.no_pembayaran,
                    CASE
                        WHEN (tagihan.pasienadmisi_id IS NULL) THEN i_1.instalasi_id
                        WHEN (tagihan.pasienadmisi_id IS NOT NULL) THEN i_2.instalasi_id
                        ELSE NULL::integer
                    END AS instalasi_id,
                i_1.instalasi_nama AS i_pendaftaran,
                i_2.instalasi_nama AS i_ranap,
                (tagihan.total_subsidiasuransi)::integer AS total_subsidiasuransi,
                tagihan.penjualanresep_id,
                pasien_m.tanggal_lahir,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                tagihan.adm_resep,
                tagihan.pembayaran_id,
                pasien_m.alamat_pasien AS alamat,
                COALESCE(tagihan.total_dijamin, (0)::double precision) AS total_dijamin,
                    CASE
                        WHEN (tagihan.pasienadmisi_id IS NULL) THEN pulang_rj.tglpasienpulang
                        ELSE pulang_ri.tglpasienpulang
                    END AS tgl_pasienpulang
               FROM (((((((((((( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
                        pasienadmisi_t.pegawai_id AS dok_ranap_id,
                        pendaftaran_t.ruangan_id AS r_pendaftaran_id,
                        pasienadmisi_t.ruangan_id AS r_ranap_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rj.carabayar_nama
                                ELSE carabayar_ri.carabayar_nama
                            END AS carabayar_nama,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rj.penjamin_nama
                                ELSE penjamin_ri.penjamin_nama
                            END AS penjamin_nama,
                        pembayaran_t.total_tagihan,
                        pembayaran_t.penggunaan_uangmuka AS total_uang_muka,
                        pembayaran_t.total_dibayar AS total_sudah_dibayarkan,
                        pembayaran_t.total_sisatagihan,
                        pendaftaran_t.status_bayar,
                        pendaftaran_t.tgl_pendaftaran,
                        NULL::integer AS pembayaranpelayanan_id,
                        pendaftaran_t.kelaspelayanan_id,
                        NULL::integer AS tandabuktibayar_id,
                        pembayaran_t.total_pembulatan AS pembulatan,
                        pembayaran_t.total_administrasi AS biaya_administrasi,
                        pembayaran_t.created_date AS tgl_pembayaran,
                        pembayaran_t.no_pembayaran,
                        pembayaran_t.total_dijamin AS total_subsidiasuransi,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        pendaftaran_t.pasienadmisi_id,
                        0 AS adm_resep,
                        pembayaran_t.pembayaran_id,
                        pembayaran_t.total_dijamin,
                        pendaftaran_t.pasienpulang_id AS pulangrj_id,
                        pasienadmisi_t.pasienpulang_id AS pulangri_id
                       FROM ((((((pendaftaran_t
                         JOIN ( SELECT a.pembayaran_id,
                                a.pendaftaran_id,
                                a.created_by,
                                a.created_date,
                                a.no_pembayaran,
                                a.total_tagihan,
                                a.penggunaan_uangmuka,
                                a.total_dijamin,
                                a.total_dibayar,
                                a.total_sisatagihan,
                                a.total_administrasi,
                                a.total_pembulatan,
                                a.total_ditagihkan
                               FROM pembayaran_t a
                              WHERE (a.is_deleted = false)) pembayaran_t ON ((pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id)))
                         LEFT JOIN ( SELECT a.pasienadmisi_id,
                                a.pasienpulang_id,
                                a.penjamin_id,
                                a.carabayar_id,
                                a.pegawai_id,
                                a.ruangan_id
                               FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_rj ON ((pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id)))
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_rj ON ((pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id)))
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
                        pasienadmisi_t.pegawai_id AS dok_ranap_id,
                        pendaftaran_t.ruangan_id AS r_pendaftaran_id,
                        pasienadmisi_t.ruangan_id AS r_ranap_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rj.carabayar_nama
                                ELSE carabayar_ri.carabayar_nama
                            END AS carabayar_nama,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rj.penjamin_nama
                                ELSE penjamin_ri.penjamin_nama
                            END AS penjamin_nama,
                        0 AS total_tagihan,
                        bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
                        0 AS total_sudah_dibayarkan,
                        0 AS total_sisatagihan,
                        pendaftaran_t.status_bayar,
                        pendaftaran_t.tgl_pendaftaran,
                        NULL::integer AS pembayaranpelayanan_id,
                        pendaftaran_t.kelaspelayanan_id,
                        bayaruangmuka_t.tandabuktibayar_id,
                        0 AS pembulatan,
                        tandabuktibayar_t.biayaadministrasi,
                        bayaruangmuka_t.tgl_uangmuka,
                        bayaruangmuka_t.no_uangmuka,
                        0 AS total_subsidiasuransi,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        pendaftaran_t.pasienadmisi_id,
                        0 AS adm_resep,
                        NULL::integer AS pembayaran_id,
                        0 AS total_dijamin,
                        pendaftaran_t.pasienpulang_id AS pulangrj_id,
                        NULL::integer AS pulangri_id
                       FROM (((((((pendaftaran_t
                         JOIN ( SELECT b.pendaftaran_id,
                                b.tandabuktibayar_id,
                                b.jumlah_uangmuka,
                                b.tgl_uangmuka,
                                b.no_uangmuka
                               FROM bayaruangmuka_t b) bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
                         JOIN ( SELECT b.tandabuktibayar_id,
                                b.biayaadministrasi
                               FROM tandabuktibayar_t b) tandabuktibayar_t ON ((tandabuktibayar_t.tandabuktibayar_id = bayaruangmuka_t.tandabuktibayar_id)))
                         LEFT JOIN ( SELECT b.pasienadmisi_id,
                                b.pasienpulang_id,
                                b.penjamin_id,
                                b.carabayar_id,
                                b.pegawai_id,
                                b.ruangan_id
                               FROM pasienadmisi_t b) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_rj ON ((pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id)))
                         LEFT JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_rj ON ((pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id)))
                         LEFT JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama
                               FROM carabayar_m b) carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
                    UNION ALL
                     SELECT NULL::integer AS pendaftaran_id,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        penjualanresep_t.pasien_id,
                        NULL::integer AS jeniskasuspenyakit_id,
                        penjualanresep_t.pegawai_id AS dok_pendaftaran_id,
                        NULL::integer AS dok_ranap_id,
                        penjualanresep_t.ruangan_id AS r_pendaftaran_id,
                        NULL::integer AS r_ranap_id,
                        carabayar_m.carabayar_nama,
                        penjamin_m.penjamin_nama,
                        pembayaran_t.total_tagihan,
                        pembayaran_t.penggunaan_uangmuka AS total_uang_muka,
                        pembayaran_t.total_dibayar AS total_sudah_dibayarkan,
                        pembayaran_t.total_sisatagihan,
                        penjualanresep_t.status_bayar,
                        penjualanresep_t.tglresep AS tgl_pendaftaran,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        NULL::integer AS kelaspelayanan_id,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        pembayaran_t.total_pembulatan AS pembulatan,
                        pembayaran_t.total_administrasi AS biaya_administrasi,
                        pembayaran_t.created_date AS tgl_pembayaran,
                        pembayaran_t.no_pembayaran,
                        pembayaran_t.total_dijamin AS total_subsidiasuransi,
                        penjualanresep_t.penjualanresep_id,
                        penjualanresep_t.nama_pembeli,
                        NULL::integer AS pasienadmisi_id,
                        penjualanresep_t.biayaadministrasi AS adm_resep,
                        pembayaran_t.pembayaran_id,
                        pembayaran_t.total_dijamin,
                        NULL::integer AS pulangrj_id,
                        NULL::integer AS pulangri_id
                       FROM ((((pembayaranpelayanan_t
                         JOIN ( SELECT c.penjualanresep_id,
                                c.ruangan_id,
                                c.pasien_id,
                                c.pegawai_id,
                                c.noresep,
                                c.tglresep,
                                c.nama_pembeli,
                                c.status_bayar,
                                c.biayaadministrasi
                               FROM penjualanresep_t c) penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                         JOIN ( SELECT c.pembayaran_id,
                                c.pendaftaran_id,
                                c.created_by,
                                c.created_date,
                                c.no_pembayaran,
                                c.total_tagihan,
                                c.penggunaan_uangmuka,
                                c.total_dijamin,
                                c.total_dibayar,
                                c.total_sisatagihan,
                                c.total_administrasi,
                                c.total_pembulatan,
                                c.total_ditagihkan
                               FROM pembayaran_t c) pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                         JOIN ( SELECT c.carabayar_id,
                                c.carabayar_nama
                               FROM carabayar_m c) carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ( SELECT c.penjamin_id,
                                c.penjamin_nama
                               FROM penjamin_m c) penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                      WHERE (pembayaranpelayanan_t.is_deleted = false)) tagihan
                 LEFT JOIN ( SELECT d.pasien_id,
                        d.nama_pasien,
                        d.no_rekam_medik,
                        d.tanggal_lahir,
                        d.jeniskelamin,
                        d.alamat_pasien
                       FROM pasien_m d) pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN ( SELECT d.jeniskasuspenyakit_id,
                        d.jeniskasuspenyakit_nama
                       FROM jeniskasuspenyakit_m d) jeniskasuspenyakit_m ON ((tagihan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN ( SELECT d.pegawai_id,
                        d.nama_pegawai
                       FROM pegawai_m d) dok_1 ON ((tagihan.dok_pendaftaran_id = dok_1.pegawai_id)))
                 LEFT JOIN ( SELECT d.pegawai_id,
                        d.nama_pegawai
                       FROM pegawai_m d) dok_2 ON ((tagihan.dok_ranap_id = dok_2.pegawai_id)))
                 LEFT JOIN ( SELECT d.ruangan_id,
                        d.ruangan_nama,
                        d.instalasi_id
                       FROM ruangan_m d) r_1 ON ((tagihan.r_pendaftaran_id = r_1.ruangan_id)))
                 LEFT JOIN ( SELECT d.ruangan_id,
                        d.ruangan_nama,
                        d.instalasi_id
                       FROM ruangan_m d) r_2 ON ((tagihan.r_ranap_id = r_2.ruangan_id)))
                 LEFT JOIN ( SELECT d.instalasi_id,
                        d.instalasi_nama
                       FROM instalasi_m d) i_1 ON ((i_1.instalasi_id = r_1.instalasi_id)))
                 LEFT JOIN ( SELECT d.instalasi_id,
                        d.instalasi_nama
                       FROM instalasi_m d) i_2 ON ((i_2.instalasi_id = r_2.instalasi_id)))
                 LEFT JOIN ( SELECT d.kelaspelayanan_id,
                        d.kelaspelayanan_nama
                       FROM kelaspelayanan_m d) kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN ( SELECT d.pasienpulang_id,
                        d.tglpasienpulang
                       FROM pasienpulang_t d) pulang_rj ON ((tagihan.pulangrj_id = pulang_rj.pasienpulang_id)))
                 LEFT JOIN ( SELECT d.pasienpulang_id,
                        d.tglpasienpulang
                       FROM pasienpulang_t d) pulang_ri ON ((tagihan.pulangri_id = pulang_ri.pasienpulang_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_071341_migrate_multypayer_view_invoicesudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_071341_migrate_multypayer_view_invoicesudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
