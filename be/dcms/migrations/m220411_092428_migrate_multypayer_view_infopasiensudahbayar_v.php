<?php

use yii\db\Migration;

/**
 * Class m220411_092428_migrate_multypayer_view_infopasiensudahbayar_v
 */
class m220411_092428_migrate_multypayer_view_infopasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasiensudahbayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasiensudahbayar_v" AS
             SELECT pendaftaran.pendaftaran_id,
                pendaftaran.pembayaranpelayanan_id,
                pendaftaran.tgl_pembayaran,
                pendaftaran.no_pembayaran,
                pendaftaran.ruangan_id,
                pendaftaran.ruangan_nama,
                pendaftaran.no_pendaftaran,
                pendaftaran.tgl_pendaftaran,
                pendaftaran.tgl_pulang,
                pendaftaran.no_rekam_medik,
                    CASE
                        WHEN (pendaftaran.nama_pasien IS NULL) THEN pendaftaran.nama_pembeli
                        ELSE pendaftaran.nama_pasien
                    END AS nama_pasien,
                pendaftaran.tanggal_lahir,
                pendaftaran.umur,
                pendaftaran.jeniskelamin,
                pendaftaran.penjamin_id,
                pendaftaran.penjamin_nama,
                pendaftaran.carabayar_id,
                pendaftaran.carabayar_nama,
                pendaftaran.carabayar_id AS carabayar_id1,
                pendaftaran.carabayar_nama AS carabayar_nama1,
                pendaftaran.penjamin_id AS penjamin_id1,
                pendaftaran.penjamin_nama AS penjamin_nama1,
                pendaftaran.instalasi_id AS instalasi_id1,
                pendaftaran.instalasi_nama,
                pendaftaran.status_bayar,
                pendaftaran.closingkasir_id,
                pendaftaran.total_tagihan,
                pendaftaran.total_uang_muka,
                pendaftaran.subsidi_asuransi,
                pendaftaran.total_sudah_dibayarkan,
                pendaftaran.total_sisa_tagihan,
                pendaftaran.biaya_administrasi,
                pendaftaran.pembulatan,
                pendaftaran.jeniskasuspenyakit_nama,
                pendaftaran.pegawai_rd_rj,
                pendaftaran.kelaspelayanan_id,
                pendaftaran.kelaspelayanan_nama,
                pendaftaran.penjualanresep_id,
                pendaftaran.pembayaran_id,
                pendaftaran.total_ditagihkan,
                pendaftaran.returbayarpelayanan_id,
                pendaftaran.pagawaikasir_id,
                pendaftaran.is_stoptitipan,
                pendaftaran.is_pasientitipan,
                pendaftaran.kelas_ditagihkan_id,
                pendaftaran.kelas_ditagihkan_nama,
                pembayaran.tagihan,
                pembayaran.total_dibayar,
                    CASE
                        WHEN (pendaftaran.groupcarabayar_id <> 417) THEN pendaftaran.total_dijamin
                        ELSE (0)::double precision
                    END AS total_dijamin,
                pendaftaran.groupcarabayar_id,
                pembayaran.no_pembayaran_header,
                pembayaran.no_invoicepasien,
                    CASE
                        WHEN (gabung.qty > 0) THEN true
                        ELSE false
                    END AS is_gabung,
                gabung.no_invoicegabung
               FROM ((( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasienadmisi_id,
                        NULL::integer AS pembayaranpelayanan_id,
                        pembayaran_t.created_date AS tgl_pembayaran,
                        pembayaran_t.no_pembayaran,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
                                ELSE pasienadmisi.ruangan_id
                            END AS ruangan_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rj.ruangan_nama
                                ELSE ruangan_ri.ruangan_nama
                            END AS ruangan_nama,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_rj.tglpasienpulang
                                ELSE pulang_ri.tglpasienpulang
                            END AS tgl_pulang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.tanggal_lahir,
                        pendaftaran_t.umur,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
                                ELSE pasienadmisi.carabayar_id
                            END AS carabayar_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rj.carabayar_nama
                                ELSE carabayar_ri_1.carabayar_nama
                            END AS carabayar_nama,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
                                ELSE pasienadmisi.penjamin_id
                            END AS penjamin_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rj.penjamin_nama
                                ELSE penjamin_ri_1.penjamin_nama
                            END AS penjamin_nama,
                        fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
                                ELSE ruangan_ri.instalasi_id
                            END AS instalasi_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rj.instalasi_nama
                                ELSE instalasi_rj.instalasi_nama
                            END AS instalasi_nama,
                        NULL::integer AS closingkasir_id,
                        pembayaran_t.total_tagihan,
                        pembayaran_t.penggunaan_uangmuka AS total_uang_muka,
                        pembayaran_t.total_dijamin AS subsidi_asuransi,
                        pembayaran_t.total_dibayar AS total_sudah_dibayarkan,
                        pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
                        pembayaran_t.total_administrasi AS biaya_administrasi,
                        pembayaran_t.total_pembulatan AS pembulatan,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN peg_rd_rj.nama_pegawai
                                ELSE peg_ri.nama_pegawai
                            END AS pegawai_rd_rj,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_id
                                ELSE kelas_admisi.kelaspelayanan_id
                            END AS kelaspelayanan_id,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_nama
                                ELSE kelas_admisi.kelaspelayanan_nama
                            END AS kelaspelayanan_nama,
                        NULL::integer AS penjualanresep_id,
                        NULL::character varying AS nama_pembeli,
                        pembayaran_t.pembayaran_id,
                        pembayaran_t.total_ditagihkan,
                        NULL::integer AS returbayarpelayanan_id,
                        loginpemakai_k.pegawai_id AS pagawaikasir_id,
                        kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rj.groupcarabayar_id
                                ELSE carabayar_ri_1.groupcarabayar_id
                            END AS groupcarabayar_id,
                        pembayaran_t.total_dijamin,
                        pasienadmisi.is_stoptitipan,
                        pasienadmisi.is_pasientitipan,
                        pasienadmisi.kelas_ditagihkan_id
                       FROM ((((((((((((((((((((pendaftaran_t
                         JOIN ( SELECT a.pembayaran_id,
                                a.pendaftaran_id,
                                a.created_by,
                                a.created_date,
                                COALESCE(a.no_pembayaran, (b.no_pembayaran)::character varying) AS no_pembayaran,
                                a.total_tagihan,
                                a.penggunaan_uangmuka,
                                a.total_dijamin,
                                a.total_dibayar,
                                a.total_sisatagihan,
                                a.total_administrasi,
                                a.total_pembulatan,
                                a.total_ditagihkan
                               FROM (pembayaran_t a
                                 JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                                        "left"((pembayaranpelayanan_t.no_pembayaran)::text, 13) AS no_pembayaran
                                       FROM pembayaranpelayanan_t
                                      GROUP BY pembayaranpelayanan_t.pembayaran_id, ("left"((pembayaranpelayanan_t.no_pembayaran)::text, 13))) b ON ((a.pembayaran_id = b.pembayaran_id)))
                              WHERE (a.is_deleted = false)) pembayaran_t ON ((pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id)))
                         JOIN ( SELECT a.pasien_id,
                                a.nama_pasien,
                                a.no_rekam_medik,
                                a.tanggal_lahir,
                                a.jeniskelamin
                               FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN ( SELECT a.pasienadmisi_id,
                                a.pasienpulang_id,
                                a.kelaspelayanan_id,
                                a.kelas_ditagihkan_id,
                                a.penjamin_id,
                                a.carabayar_id,
                                a.pegawai_id,
                                a.ruangan_id,
                                a.is_stoptitipan,
                                a.is_pasientitipan
                               FROM pasienadmisi_t a) pasienadmisi ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id)))
                         LEFT JOIN ( SELECT a.ruangan_id,
                                a.ruangan_nama
                               FROM ruangan_m a) ruangan_rj ON ((pendaftaran_t.ruangan_id = ruangan_rj.ruangan_id)))
                         LEFT JOIN ( SELECT a.ruangan_id,
                                a.ruangan_nama,
                                a.instalasi_id
                               FROM ruangan_m a) ruangan_ri ON ((pasienadmisi.ruangan_id = ruangan_ri.ruangan_id)))
                         LEFT JOIN ( SELECT a.instalasi_id,
                                a.instalasi_nama
                               FROM instalasi_m a) instalasi_rj ON ((pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id)))
                         LEFT JOIN ( SELECT a.instalasi_id,
                                a.instalasi_nama
                               FROM instalasi_m a) instalasi_ri ON ((ruangan_ri.instalasi_id = instalasi_ri.instalasi_id)))
                         LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_rj ON ((pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id)))
                         LEFT JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_ri_1 ON ((pasienadmisi.penjamin_id = penjamin_ri_1.penjamin_id)))
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama,
                                a.groupcarabayar_id
                               FROM carabayar_m a) carabayar_rj ON ((pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id)))
                         LEFT JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama,
                                a.groupcarabayar_id
                               FROM carabayar_m a) carabayar_ri_1 ON ((pasienadmisi.carabayar_id = carabayar_ri_1.carabayar_id)))
                         LEFT JOIN ( SELECT a.pegawai_id,
                                a.nama_pegawai
                               FROM pegawai_m a) peg_rd_rj ON ((pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id)))
                         LEFT JOIN ( SELECT a.pegawai_id,
                                a.nama_pegawai
                               FROM pegawai_m a) peg_ri ON ((pasienadmisi.pegawai_id = peg_ri.pegawai_id)))
                         LEFT JOIN ( SELECT a.kelaspelayanan_id,
                                a.kelaspelayanan_nama
                               FROM kelaspelayanan_m a) kelas_pendaftaran ON ((pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id)))
                         LEFT JOIN ( SELECT a.kelaspelayanan_id,
                                a.kelaspelayanan_nama
                               FROM kelaspelayanan_m a) kelas_admisi ON ((pasienadmisi.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
                         LEFT JOIN ( SELECT a.loginpemakai_id,
                                a.pegawai_id
                               FROM loginpemakai_k a) loginpemakai_k ON ((pembayaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                         LEFT JOIN ( SELECT a.kelaspelayanan_id,
                                a.kelaspelayanan_nama
                               FROM kelaspelayanan_m a) kelas_ditagihkan ON ((pasienadmisi.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
                         LEFT JOIN ( SELECT a.pasienpulang_id,
                                a.tglpasienpulang
                               FROM pasienpulang_t a) pulang_rj ON ((pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id)))
                         LEFT JOIN ( SELECT a.pasienpulang_id,
                                a.tglpasienpulang
                               FROM pasienpulang_t a) pulang_ri ON ((pasienadmisi.pasienpulang_id = pulang_ri.pasienpulang_id)))
                    UNION ALL
                     SELECT NULL::integer AS pendaftaran_id,
                        NULL::integer AS pasienadmisi_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaran_t.created_date,
                        pembayaran_t.no_pembayaran,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        penjualanresep_t.tglresep AS tgl_pendaftaran,
                        NULL::timestamp without time zone AS tgl_pulang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.tanggal_lahir,
                        NULL::character varying AS umur,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
                        carabayar_m.carabayar_id,
                        carabayar_m.carabayar_nama,
                        penjamin_m.penjamin_id,
                        penjamin_m.penjamin_nama,
                        fgetnamalookup((pembayaranpelayanan_t.statusbayar)::integer) AS status_bayar,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama,
                        NULL::integer AS closingkasir_id,
                        pembayaran_t.total_tagihan,
                        pembayaran_t.penggunaan_uangmuka AS total_uang_muka,
                        pembayaran_t.total_dijamin AS subsidi_asuransi,
                        pembayaran_t.total_dibayar AS total_sudah_dibayarkan,
                        pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
                        pembayaran_t.total_administrasi AS biaya_administrasi,
                        pembayaran_t.total_pembulatan AS pembulatan,
                        NULL::character varying AS jeniskasuspenyakit_nama,
                        peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                        NULL::integer AS kelaspelayanan_id,
                        NULL::character varying AS kelaspelayanan_nama,
                        pembayaranpelayanan_t.penjualanresep_id,
                        penjualanresep_t.nama_pembeli,
                        pembayaranpelayanan_t.pembayaran_id,
                        pembayaran_t.total_ditagihkan,
                        NULL::integer AS returbayarpelayanan_id,
                        loginpemakai_k.pegawai_id AS pagawaikasir_id,
                        NULL::character varying AS kelas_ditagihkan_nama,
                        carabayar_m.groupcarabayar_id,
                        pembayaran_t.total_dijamin,
                        NULL::boolean AS is_stoptitipan,
                        NULL::boolean AS is_pasientitipan,
                        NULL::integer AS kelas_ditagihkan_id
                       FROM (((((((((pembayaranpelayanan_t
                         JOIN ( SELECT b.penjualanresep_id,
                                b.ruangan_id,
                                b.pasien_id,
                                b.pegawai_id,
                                b.noresep,
                                b.tglresep,
                                b.nama_pembeli
                               FROM penjualanresep_t b) penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                         JOIN ( SELECT b.pembayaran_id,
                                b.pendaftaran_id,
                                b.created_by,
                                b.created_date,
                                b.no_pembayaran,
                                b.total_tagihan,
                                b.penggunaan_uangmuka,
                                b.total_dijamin,
                                b.total_dibayar,
                                b.total_sisatagihan,
                                b.total_administrasi,
                                b.total_pembulatan,
                                b.total_ditagihkan
                               FROM pembayaran_t b) pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                         JOIN ( SELECT b.ruangan_id,
                                b.ruangan_nama,
                                b.instalasi_id
                               FROM ruangan_m b) ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
                         LEFT JOIN ( SELECT b.pasien_id,
                                b.nama_pasien,
                                b.no_rekam_medik,
                                b.tanggal_lahir,
                                b.jeniskelamin
                               FROM pasien_m b) pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                         JOIN ( SELECT b.carabayar_id,
                                b.carabayar_nama,
                                b.groupcarabayar_id
                               FROM carabayar_m b) carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ( SELECT b.penjamin_id,
                                b.penjamin_nama
                               FROM penjamin_m b) penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN ( SELECT b.instalasi_id,
                                b.instalasi_nama
                               FROM instalasi_m b) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN ( SELECT b.pegawai_id,
                                b.nama_pegawai
                               FROM pegawai_m b) peg_rd_rj ON ((penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id)))
                         LEFT JOIN ( SELECT b.loginpemakai_id,
                                b.pegawai_id
                               FROM loginpemakai_k b) loginpemakai_k ON ((pembayaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                      WHERE ((pembayaranpelayanan_t.is_active = true) AND (pembayaranpelayanan_t.is_deleted = false))) pendaftaran
                 LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
                        pembayaran_t.no_pembayaran AS no_pembayaran_header,
                        pembayaran_t.no_invoicepasien,
                        ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - pembayaran_t.total_discountpembayaran) AS tagihan,
                        ((((pembayaran_t.total_tunai + pembayaran_t.total_nontunai) + pembayaran_t.total_sisatagihan) + pembayaran_t.penggunaan_uangmuka) - COALESCE(pemberianpiutang_t.total_sisapiutang, (0)::double precision)) AS total_dibayar,
                        (pembayaran_t.total_dijamin + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin
                       FROM (pembayaran_t
                         LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
                      WHERE (pembayaran_t.is_deleted = false)) pembayaran ON ((pendaftaran.pembayaran_id = pembayaran.pembayaran_id)))
                 LEFT JOIN ( SELECT invoicegabungdetail_t.pendaftaran_id,
                        invoicegabungdetail_t.pembayaran_id,
                        invoicegabung_t.no_invoicegabung,
                        count(*) AS qty
                       FROM (invoicegabungdetail_t
                         JOIN invoicegabung_t ON ((invoicegabungdetail_t.invoicegabung_id = invoicegabung_t.invoicegabung_id)))
                      WHERE ((invoicegabungdetail_t.is_deleted IS FALSE) AND (invoicegabung_t.is_deleted IS FALSE))
                      GROUP BY invoicegabungdetail_t.pendaftaran_id, invoicegabungdetail_t.pembayaran_id, invoicegabung_t.no_invoicegabung) gabung ON (((pendaftaran.pendaftaran_id = gabung.pendaftaran_id) AND (pembayaran.pembayaran_id = gabung.pembayaran_id))))
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220411_092428_migrate_multypayer_view_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220411_092428_migrate_multypayer_view_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
