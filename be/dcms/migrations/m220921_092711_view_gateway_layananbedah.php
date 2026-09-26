<?php

use yii\db\Migration;

/**
 * Class m220921_092711_view_gateway_layananbedah
 */
class m220921_092711_view_gateway_layananbedah extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_layananbedah_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_layananbedah_v\"
        AS SELECT pasienmasukpenunjang_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_permintaan,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_identitas_pasien,
            COALESCE(penjamin_ri.carabayar_id, penjamin_rj.carabayar_id) AS carabayar_id,
            COALESCE(carabayar_ri.carabayar_nama, carabayar_rj.carabayar_nama) AS carabayar_nama,
            COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rj.penjamin_nama) AS penjamin_nama,
            pasienmasukpenunjang_t.instalasiasal_id,
            instalasi_m.instalasi_nama AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            dr_anastesi.nama_pegawai AS dok_anastesi,
            dr_perujuk.nama_pegawai AS dok_perujuk,
            rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
            rencanaoperasi_t.jam_rencana_mulai AS jam_mulai,
            rencanaoperasi_t.jam_rencana_selesai AS jam_selesai,
            look_statusperiksa.lookup_name AS status_verifikasi,
            look_statusperiksa.lookup_name AS status_operasi,
            intra_operasi.data_intra_operasi,
            posisi_tim.data_tim_operasi,
            tindakan_operasi.data_tindakan_operasi,
            bmhp_operasi.data_penggunaan_bmhp,
            NULL::text AS data_penggunaan_cairan,
            NULL::text AS data_alat_ditinggal,
            NULL::text AS data_pemeriksaan_pelengkap,
            konsultasi_operasi.data_tindakan_konsultasi,
            post_operasi.data_post_operasi
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.tgl_permintaan,
                    a.rencanaoperasi_id,
                    a.dr_operator_id,
                    a.dr_anastesi_id,
                    a.jam_rencana_mulai,
                    a.jam_rencana_selesai
                   FROM rencanaoperasi_t a) rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.tgl_kirimpasien,
                    a.no_orderkeunitlain,
                    a.status_penunjang,
                    a.pegawai_id
                   FROM pasienkirimkeunitlain_t a
                  WHERE a.instalasi_id = 12) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.jeniskasuspenyakit_id,
                    a.kelaspelayanan_id,
                    a.bpjs_id,
                    a.no_pendaftaran,
                    a.tgl_pendaftaran,
                    a.umur,
                    a.label_gelang
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.bpjs_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.photopasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.no_identitas_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.kode_ruangan_bpjs
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN ( SELECT jeniskasuspenyakit_m_1.jeniskasuspenyakit_id,
                    jeniskasuspenyakit_m_1.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m jeniskasuspenyakit_m_1) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.ruangan_nama
                   FROM ruangan_m ruangan_m_1) ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
             LEFT JOIN ( SELECT penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    penjamin_m.carabayar_id
                   FROM penjamin_m) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
             LEFT JOIN ( SELECT carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    carabayar_m.carabayar_kode_warna
                   FROM carabayar_m) carabayar_rj ON penjamin_rj.carabayar_id = carabayar_rj.carabayar_id
             LEFT JOIN ( SELECT kelaspelayanan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama
                   FROM kelaspelayanan_m) kelas_rj ON pendaftaran_t.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
             LEFT JOIN ( SELECT penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    penjamin_m.carabayar_id
                   FROM penjamin_m) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             LEFT JOIN ( SELECT carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    carabayar_m.carabayar_kode_warna
                   FROM carabayar_m) carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
             LEFT JOIN ( SELECT kelaspelayanan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama
                   FROM kelaspelayanan_m) kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
             LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                    pegawai_m_1.nama_pegawai
                   FROM pegawai_m pegawai_m_1) dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
             LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                    pegawai_m_1.nama_pegawai
                   FROM pegawai_m pegawai_m_1) dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
             LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                    pegawai_m_1.nama_pegawai
                   FROM pegawai_m pegawai_m_1) dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
             LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id, a.pegawai_id) a.pendaftaran_id,
                    a.pegawai_id,
                    a.a_diag_utama
                   FROM cppt_t a
                  WHERE a.is_deleted = false AND a.is_active = true AND a.is_instruksi_pulang = false) cppt_t ON pasienmasukpenunjang_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.kelaspelayanan_id
                   FROM kamarruangan_m a) kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT bpjs_t.bpjs_id,
                    bpjs_t.nokartuasuransi
                   FROM bpjs_t) bpjs_rjrd ON pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id
             LEFT JOIN ( SELECT bpjs_t.bpjs_id,
                    bpjs_t.nokartuasuransi
                   FROM bpjs_t) bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
             LEFT JOIN ( SELECT pendaftaranol_t_1.pendaftaran_id,
                    pendaftaranol_t_1.no_pendaftaranol,
                    pendaftaranol_t_1.jenis_reservasi
                   FROM pendaftaranol_t pendaftaranol_t_1) pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    pegawai_login.nama_pegawai
                   FROM loginpemakai_k a
                     JOIN pegawai_m pegawai_login ON a.pegawai_id = pegawai_login.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS tindakan,
                                    golonganoperasi_m.golonganoperasi_nama AS golongan_operasi
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.golonganoperasi_id
                                           FROM operasi_m a_1) operasi_m ON daftartindakan_m.daftartindakan_id = operasi_m.daftartindakan_id
                                     JOIN ( SELECT a_1.golonganoperasi_id,
                                            a_1.golonganoperasi_nama
                                           FROM golonganoperasi_m a_1) golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
                                  WHERE permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id) x) AS pemeriksaan
                   FROM pasienkirimkeunitlain_t a) order_operasi ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = order_operasi.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT verifikasibedah_r.operasi_nama AS tindakan,
                                    verifikasibedah_r.golonganoperasi_nama AS golongan_operasi
                                   FROM verifikasibedah_r
                                  WHERE verifikasibedah_r.is_deleted = false AND verifikasibedah_r.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id) x) AS pemeriksaan
                   FROM pasienmasukpenunjang_t a) verif_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = verif_operasi.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pasienmasukpenunjang_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT inpostoperasi_t.pasienmasukpenunjang_id,
                                    inpostoperasi_t.is_surgicalsavety AS surgical_sefety_checklist,
                                    inpostoperasi_t.masuk_kamar,
                                    inpostoperasi_t.mulai_anastesi,
                                    inpostoperasi_t.selesai_anastesi,
                                    inpostoperasi_t.mulai_operasi,
                                    inpostoperasi_t.selesai_operasi
                                   FROM inpostoperasi_t
                                  WHERE inpostoperasi_t.is_deleted IS FALSE AND rencanaoperasi.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id) d) AS data_intra_operasi
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) intra_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = intra_operasi.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT pegawai_m_1.nama_pegawai,
                                    posisi.lookup_name AS posisi
                                   FROM timoperasi_t
                                     JOIN ( SELECT a.pegawai_id,
                                            a.nama_pegawai
                                           FROM pegawai_m a) pegawai_m_1 ON timoperasi_t.pegawai_id = pegawai_m_1.pegawai_id
                                     JOIN ( SELECT lookup_m.lookup_id,
                                            lookup_m.lookup_name
                                           FROM lookup_m) posisi ON timoperasi_t.posisi_tim = posisi.lookup_id
                                  WHERE timoperasi_t.is_deleted IS FALSE AND rencanaoperasi.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id) d) AS data_tim_operasi
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) posisi_tim ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = posisi_tim.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT inpostoperasi_t.pasienmasukpenunjang_id,
                                    daftartindakan_m.daftartindakan_nama
                                   FROM inpostoperasidetail_t
                                     JOIN ( SELECT a.inpostoperasi_id,
                                            a.pasienmasukpenunjang_id
                                           FROM inpostoperasi_t a) inpostoperasi_t ON inpostoperasidetail_t.inpostoperasi_id = inpostoperasi_t.inpostoperasi_id
                                     JOIN ( SELECT a.daftartindakan_id,
                                            a.daftartindakan_nama
                                           FROM daftartindakan_m a
                                          WHERE a.is_konsultasi IS FALSE) daftartindakan_m ON inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE inpostoperasidetail_t.is_deleted IS FALSE AND rencanaoperasi.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id) d) AS data_tindakan_operasi
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) tindakan_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_operasi.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT inpostoperasi_t.pasienmasukpenunjang_id,
                                    daftartindakan_m.daftartindakan_nama
                                   FROM inpostoperasidetail_t
                                     JOIN ( SELECT a.inpostoperasi_id,
                                            a.pasienmasukpenunjang_id
                                           FROM inpostoperasi_t a) inpostoperasi_t ON inpostoperasidetail_t.inpostoperasi_id = inpostoperasi_t.inpostoperasi_id
                                     JOIN ( SELECT a.daftartindakan_id,
                                            a.daftartindakan_nama
                                           FROM daftartindakan_m a
                                          WHERE a.is_konsultasi IS FALSE) daftartindakan_m ON inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE inpostoperasidetail_t.is_deleted IS FALSE AND rencanaoperasi.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id) d) AS data_tindakan_konsultasi
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) konsultasi_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = konsultasi_operasi.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT bmhpoperasi_t.pasienmasukpenunjang_id,
                                    bmhpoperasi_t.obatalkes_id,
                                    obatalkes_m.obatalkes_nama AS daftartindakan_nama
                                   FROM bmhpoperasi_t
                                     JOIN ( SELECT a.obatalkes_id,
                                            a.obatalkes_nama
                                           FROM obatalkes_m a) obatalkes_m ON bmhpoperasi_t.obatalkes_id = obatalkes_m.obatalkes_id
                                  WHERE bmhpoperasi_t.is_deleted IS FALSE AND rencanaoperasi.pasienmasukpenunjang_id = bmhpoperasi_t.pasienmasukpenunjang_id) d) AS data_penggunaan_bmhp
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) bmhp_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = bmhp_operasi.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT rencanaoperasi.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT inpostoperasi_t.jam_masuk_rec,
                                    inpostoperasi_t.jam_keluar_rec,
                                    NULL::text AS cairan_infus,
                                    lp_ku.lookup_name AS kesadaran_umum,
                                    lp_tk.lookup_name AS tingkat_kesadaran,
                                    NULL::text AS kondisi_pasien
                                   FROM inpostoperasi_t
                                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                                            lookup_m.lookup_name
                                           FROM lookup_m) lp_ku ON inpostoperasi_t.kesadaran_umum = lp_ku.lookup_id
                                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                                            lookup_m.lookup_name
                                           FROM lookup_m) lp_tk ON inpostoperasi_t.tingkat_kesadaran = lp_tk.lookup_id
                                  WHERE inpostoperasi_t.is_recovery IS TRUE AND rencanaoperasi.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id) d) AS data_post_operasi
                   FROM rencanaoperasi_t rencanaoperasi
                  GROUP BY rencanaoperasi.pasienmasukpenunjang_id) post_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = post_operasi.pasienmasukpenunjang_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092711_view_gateway_layananbedah cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092711_view_gateway_layananbedah cannot be reverted.\n";

        return false;
    }
    */
}
