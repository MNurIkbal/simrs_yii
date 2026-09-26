<?php

use yii\db\Migration;

/**
 * Class m220215_080147_migrate_ORDH7_infoorderanrad_v
 */
class m220215_080147_migrate_ORDH7_infoorderanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoorderanrad_v";');
        $this->execute("CREATE VIEW \"public\".\"infoorderanrad_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
        pasienkirimkeunitlain_t.pendaftaran_id,
        pasienkirimkeunitlain_t.pasienadmisi_id,
        pendaftaran_t.no_pendaftaran,
        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
        pendaftaran_t.pasien_id,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pendaftaran_t.umur,
        fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
        kelaspelayanan_m.kelaspelayanan_nama,
        pendaftaran_t.instalasi_id,
        instalasi_m.instalasi_nama,
        ruangan_m.ruangan_nama,
        NULL::character varying AS kamarruangan_nokamar,
        NULL::character varying AS no_tempattidur,
        pendaftaran_t.pegawai_id,
        pegawai_m.nama_pegawai AS dokter_perujuk,
        pendaftaran_t.carabayar_id,
        carabayar_m.carabayar_nama,
        pendaftaran_t.penjamin_id,
        penjamin_m.penjamin_nama,
        pasienkirimkeunitlain_t.status_penunjang,
        fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
        pendaftaran_t.kelaspelayanan_id,
        pendaftaran_t.jeniskasuspenyakit_id,
        pendaftaran_t.ruangan_id,
        pendaftaran_t.tgl_pendaftaran,
        pendaftaran_t.kunjungan,
        pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
        pasien_m.tanggal_lahir,
        pendaftaran_t.status_pasien,
        carabayar_m.groupcarabayar_id,
        pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
        pasienmasukpenunjang_t.is_bayar,
        pasienmasukpenunjang_t.status_periksa,
        pasien_m.alamat_pasien,
        NULL::character varying AS kode_pos,
        pasien_m.no_telepon_pasien,
        ruangan_m.ruangan_singkatan AS kode_ruangan,
            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                WHEN 0 THEN 'Pendaftaran'::text
                ELSE 'Unit'::text
            END AS unit_asal,
            CASE
                WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
                WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
                ELSE diagnosa.diagnosa_utama
            END AS nama_diagnosa,
        pemeriksaan.nama_pemeriksaan,
        penjamin_m.penjamin_kode AS carabayar_kode,
        pasienkirimkeunitlain_t.catatan_dokterpengirim,
        pembayaran.no_pembayaran,
        pegawai_m.nomorindukpegawai,
        pasien_m.jeniskelamin AS jenis_kelamin_id,
            CASE
                WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN 'Belum Bayar'::text
                ELSE COALESCE(tindakanpelayanan.status_bayar, 'Batal Bayar'::text)
            END AS status_bayar,
        pasienkirimkeunitlain_t.is_rujukan,
        COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
        COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
        fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
        rujukan_t.asalrujukan_id,
        rujukan_t.rujukandari_id,
            CASE
                WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN ruangan_m.ruangan_nama
                ELSE asalrujukan_m.asalrujukan_nama
            END AS asalrujukan_nama,
        perujuk_m.namaperujuk AS rujukandari_nama
       FROM pasienkirimkeunitlain_t
         JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
         JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
         JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
         JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
         LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
         LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
         LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
         LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
                x.status_bayar
               FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN 'Sudah Bayar'::text
                                WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                                ELSE 'Batal Bayar'::text
                            END AS status_bayar
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted = false
                      GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
              GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    CASE
                        WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                        ELSE NULL::json
                    END AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
              WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                cppt_t.a_diag_utama AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN ( SELECT cppt_t_1.cppt_id,
                        cppt_t_1.pendaftaran_id,
                        cppt_t_1.a_diag_utama,
                        cppt_t_1.a_diag_penyerta
                       FROM cppt_t cppt_t_1
                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                cppt_last.pendaftaran_id
                               FROM cppt_t cppt_last
                              WHERE cppt_last.is_deleted = false
                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
              WHERE cppt_t.a_diag_utama IS NOT NULL) diagnosa_rd ON pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id
         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                resumemedisri_t.diag_utama AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                 JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
              WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa_ri ON pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id
         LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text, ', '::text) AS nama_pemeriksaan
               FROM permintaankepenunjang_t
                 JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
              GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
         LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                pembayaranpelayanan_t.no_pembayaran
               FROM tindakansudahbayar_t
                 JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                 JOIN tindakanpelayanan_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
              GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, pembayaranpelayanan_t.no_pembayaran) pembayaran ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pembayaran.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                count(*) AS jml_pemeriksaan
               FROM permintaankepenunjang_t
              WHERE permintaankepenunjang_t.is_deleted IS FALSE
              GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
         LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                permintaankepenunjang_t.is_cyto
               FROM permintaankepenunjang_t
              WHERE permintaankepenunjang_t.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
      WHERE pasienkirimkeunitlain_t.instalasi_id = 5
    UNION ALL
     SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
        pendaftaran_t.pendaftaran_id,
        pasienkirimkeunitlain_t.pasienadmisi_id,
        pendaftaran_t.no_pendaftaran,
        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
        pendaftaran_t.pasien_id,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pendaftaran_t.umur,
        fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
        kelaspelayanan_m.kelaspelayanan_nama,
        ruangan_m.instalasi_id,
        instalasi_m.instalasi_nama,
        ruangan_m.ruangan_nama,
        kamarruangan_m.kamarruangan_nokamar,
        kamartempattidur_m.no_tempattidur,
        pasienadmisi_t.pegawai_id,
        pegawai_m.nama_pegawai AS dokter_perujuk,
        penjamin_m.carabayar_id,
        carabayar_m.carabayar_nama,
        pasienadmisi_t.penjamin_id,
        penjamin_m.penjamin_nama,
        pasienkirimkeunitlain_t.status_penunjang,
        fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
        pasienadmisi_t.kelaspelayanan_id,
        pendaftaran_t.jeniskasuspenyakit_id,
        pasienadmisi_t.ruangan_id,
        pendaftaran_t.tgl_pendaftaran,
        pendaftaran_t.kunjungan,
        pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
        pasien_m.tanggal_lahir,
        pendaftaran_t.status_pasien,
        carabayar_m.groupcarabayar_id,
        pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
        pasienmasukpenunjang_t.is_bayar,
        pasienmasukpenunjang_t.status_periksa,
        pasien_m.alamat_pasien,
        NULL::character varying AS kode_pos,
        pasien_m.no_telepon_pasien,
        ruangan_m.ruangan_singkatan AS kode_ruangan,
            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                WHEN 0 THEN 'Pendaftaran'::text
                ELSE 'Unit'::text
            END AS unit_asal,
        diagnosa.diagnosa_utama AS nama_diagnosa,
        pemeriksaan.nama_pemeriksaan,
        penjamin_m.penjamin_kode AS carabayar_kode,
        pasienkirimkeunitlain_t.catatan_dokterpengirim,
        pembayaran.no_pembayaran,
        pegawai_m.nomorindukpegawai,
        pasien_m.jeniskelamin AS jenis_kelamin_id,
            CASE
                WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN 'Belum Bayar'::text
                ELSE COALESCE(tindakanpelayanan.status_bayar, 'Batal Bayar'::text)
            END AS status_bayar,
        pasienkirimkeunitlain_t.is_rujukan,
        COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
        COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
        fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
        rujukan_t.asalrujukan_id,
        rujukan_t.rujukandari_id,
            CASE
                WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN ruangan_m.ruangan_nama
                ELSE asalrujukan_m.asalrujukan_nama
            END AS asalrujukan_nama,
        perujuk_m.namaperujuk AS rujukandari_nama
       FROM pasienkirimkeunitlain_t
         JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
         JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
         JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
         JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
         JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
         JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
         JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
         JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
         JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
         LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
         LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
         LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
         LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
                x.status_bayar
               FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN 'Sudah Bayar'::text
                                WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                                ELSE 'Batal Bayar'::text
                            END AS status_bayar
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted = false
                      GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
              GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    CASE
                        WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                        ELSE NULL::json
                    END AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
              WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
            UNION ALL
             SELECT pendaftaran_t_1.pendaftaran_id,
                cppt_t.a_diag_utama AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN ( SELECT cppt_t_1.cppt_id,
                        cppt_t_1.pendaftaran_id,
                        cppt_t_1.a_diag_utama,
                        cppt_t_1.a_diag_penyerta
                       FROM cppt_t cppt_t_1
                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                cppt_last.pendaftaran_id
                               FROM cppt_t cppt_last
                              WHERE cppt_last.is_deleted = false
                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
              WHERE cppt_t.a_diag_utama IS NOT NULL
            UNION ALL
             SELECT pendaftaran_t_1.pendaftaran_id,
                resumemedisri_t.diag_utama AS diagnosa_utama
               FROM pendaftaran_t pendaftaran_t_1
                 JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                 JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                 JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
              WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
         LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text, ', '::text) AS nama_pemeriksaan
               FROM permintaankepenunjang_t
                 JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
              GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
         LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                pembayaranpelayanan_t.no_pembayaran
               FROM tindakansudahbayar_t
                 JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                 JOIN tindakanpelayanan_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
              GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, pembayaranpelayanan_t.no_pembayaran) pembayaran ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pembayaran.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                count(*) AS jml_pemeriksaan
               FROM permintaankepenunjang_t
              WHERE permintaankepenunjang_t.is_deleted IS FALSE
              GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
         LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                permintaankepenunjang_t.is_cyto
               FROM permintaankepenunjang_t
              WHERE permintaankepenunjang_t.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
      WHERE pasienkirimkeunitlain_t.instalasi_id = 5;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_080147_migrate_ORDH7_infoorderanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_080147_migrate_ORDH7_infoorderanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
