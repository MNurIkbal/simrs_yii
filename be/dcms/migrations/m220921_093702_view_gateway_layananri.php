<?php

use yii\db\Migration;

/**
 * Class m220921_093702_view_gateway_layananri
 */
class m220921_093702_view_gateway_layananri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_layananri_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_layananri_v\"
        AS SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran AS waktu_pendaftaran,
            pendaftaran_t.tgl_pendaftaran AS waktu_masuk,
            pasienpulang_t.tglpasienpulang AS waktu_keluar,
            pasien_m.no_identitas_pasien AS no_identias,
            pasien_m.no_rekam_medik AS no_rekammedik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            ruangan_m.ruangan_nama AS ruangan,
            kelaspelayanan_m.kelaspelayanan_nama AS kelas,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            kamartempattidur_m.no_tempattidur,
            pendaftaran_t.keterangan_pendaftaran AS keterangan,
            penjamin_m.penjamin_nama AS penjamin,
            carabayar_m.carabayar_nama AS cara_bayar,
            COALESCE(asalrujukan_m.asalrujukan_nama, 'Datang Sendiri'::character varying) AS asal_rujukan,
            keadaan_masuk.lookup_name AS keadaan_masuk,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jenis_pelayanan,
            COALESCE(cppt_t.data_pemeriksaan, pendaftaran_periksa.data_pemeriksaan) AS data_pemeriksaan,
            COALESCE(asesmenmedis_t.keluhan_utama, asesmenperawatrd_t.keluhan_utama) AS keluhan_utama,
            COALESCE(asesmenmedis_t.berat_badan::character varying, asesmenperawatrd_t.berat_badan::character varying) AS berat_badan,
            COALESCE(asesmenmedis_t.tinggi_badan::character varying, asesmenperawatrd_t.tinggi_badan::character varying) AS tinggi_badan,
            COALESCE(asesmenmedis_t.nadi::character varying, asesmenperawatrd_t.nadi::character varying) AS nadi,
            COALESCE(asesmenmedis_t.rr::character varying, asesmenperawatrd_t.rr::character varying) AS respiration_rate,
            COALESCE(asesmenmedis_t.td_systolic::character varying, asesmenperawatrd_t.td_systolic::character varying) AS td_systolic,
            COALESCE(asesmenmedis_t.td_diastolic::character varying, asesmenperawatrd_t.td_diastolic::character varying) AS td_diastolic,
            COALESCE(asesmenmedis_t.suhu::character varying, asesmenperawatrd_t.suhu::character varying) AS suhu,
            diagnosa.diagnosa_utama AS diag_utama,
            diagnosa.diagnosa_penyerta AS diag_penunjang,
            tindakanpelayanan_t.data_tindakan,
            pasienpulang_t.cara_keluar,
            pasienpulang_t.kondisi_keluar,
            pasienpulang_t.tglpasienpulang AS tanggal_keluar,
            resumemedis_t.obat_pulang AS obat_saat_pulang
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.carakeluar_id,
                    carakeluar_m.carakeluar_nama AS cara_keluar,
                    a.kondisikeluar_id,
                    a.tgl_meninggal,
                    kondisikeluar_m.kondisikeluar_nama AS kondisi_keluar
                   FROM pasienpulang_t a
                     LEFT JOIN carakeluar_m ON a.carakeluar_id = carakeluar_m.carakeluar_id
                     LEFT JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT a.pasien_id,
                    a.no_identitas_pasien,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.kelaspelayanan_id
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id
                   FROM rujukan_t a) rujukan_m ON pendaftaran_t.rujukan_id = rujukan_m.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_m.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) keadaan_masuk ON pendaftaran_t.keadaan_masuk::integer = keadaan_masuk.lookup_id
             LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT a.tindakanpelayanan_id,
                                    a.pendaftaran_id,
                                    a.daftartindakan_id,
                                    a.tgl_tindakan::text AS tgl_tindakan,
                                    a.qty_tindakan,
                                    a.tarif_satuan,
                                    daftartindakan_m.daftartindakan_nama,
                                    kelompoktindakan_m.kelompoktindakan_nama,
                                    a.dokterpenanggungjawab_id,
                                    a.perawat1_id
                                   FROM tindakanpelayanan_t a
                                     LEFT JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                                  WHERE a.is_deleted = false AND a.is_active = true AND pendaftaran.pendaftaran_id = a.pendaftaran_id AND a.pasienadmisi_id IS NOT NULL) d) AS data_tindakan
                   FROM pendaftaran_t pendaftaran) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             LEFT JOIN ( SELECT cppt.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT a.tgl_cppt::text AS tgl_periksa,
                                    dokter.nama_pegawai AS dokter,
                                    dokter.nomorindukpegawai AS nik_dokter,
                                    perawat.nama_pegawai AS perawat,
                                    perawat.nomorindukpegawai AS nik_perawat
                                   FROM cppt_t a
                                     LEFT JOIN ( SELECT b.pegawai_id,
                                            b.nama_pegawai,
                                            b.nomorindukpegawai
                                           FROM pegawai_m b
                                          WHERE b.kelompokpegawai_id = 1) dokter ON a.pegawai_id = dokter.pegawai_id
                                     LEFT JOIN ( SELECT b.pegawai_id,
                                            b.nama_pegawai,
                                            b.nomorindukpegawai
                                           FROM pegawai_m b
                                          WHERE b.kelompokpegawai_id = 2) perawat ON a.pegawai_id = perawat.pegawai_id
                                  WHERE a.pasienadmisi_id IS NOT NULL AND cppt.pendaftaran_id = a.pendaftaran_id) d) AS data_pemeriksaan
                   FROM cppt_t cppt
                  WHERE cppt.pasienadmisi_id IS NULL
                  GROUP BY cppt.pendaftaran_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT pasienadmisi_t_1.tgl_admisi::text AS tgl_periksa,
                                    dokter.nama_pegawai AS dokter,
                                    dokter.nomorindukpegawai AS nik_dokter,
                                    perawat.nama_pegawai AS perawat,
                                    perawat.nomorindukpegawai AS nik_perawat
                                   FROM ( SELECT a_1.pasienadmisi_id,
                                            a_1.tgl_admisi,
                                            a_1.pegawai_id
                                           FROM pasienadmisi_t a_1) pasienadmisi_t_1
                                     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                                            pegawai_m.nama_pegawai,
                                            pegawai_m.nomorindukpegawai
                                           FROM pegawai_m
                                          WHERE pegawai_m.kelompokpegawai_id = 1) dokter ON pasienadmisi_t_1.pegawai_id = dokter.pegawai_id
                                     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                                            pegawai_m.nama_pegawai,
                                            pegawai_m.nomorindukpegawai
                                           FROM pegawai_m
                                          WHERE pegawai_m.kelompokpegawai_id = 2) perawat ON pasienadmisi_t_1.pegawai_id = perawat.pegawai_id
                                  WHERE pasienadmisi_t_1.pasienadmisi_id = a.pasienadmisi_id) d) AS data_pemeriksaan
                   FROM pasienadmisi_t a) pendaftaran_periksa ON pendaftaran_t.pendaftaran_id = pendaftaran_periksa.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.a_diag_utama AS diagnosa_utama,
                    a.a_diag_penyerta AS diagnosa_penyerta
                   FROM cppt_t a
                     JOIN ( SELECT max(b.cppt_id) AS cppt_id,
                            b.pendaftaran_id
                           FROM cppt_t b
                          WHERE b.is_deleted IS FALSE AND b.pasienadmisi_id IS NOT NULL
                          GROUP BY b.pendaftaran_id) last_cppt ON a.cppt_id = last_cppt.cppt_id
                  WHERE a.is_deleted IS FALSE AND a.pasienadmisi_id IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.keluhan AS keluhan_utama,
                    a.berat_badan,
                    a.tinggi_badan,
                    a.detak_nadi AS nadi,
                    a.pernafasan AS rr,
                    a.tekanan_darah AS td,
                    a.suhu_tubuh AS suhu,
                    a.td_systolic,
                    a.td_diastolic
                   FROM asesmenperawatrd_t a
                     JOIN ( SELECT max(b.asesmenperawatrd_id) AS asesmenperawatrd_id,
                            b.pendaftaran_id
                           FROM asesmenperawatrd_t b
                          GROUP BY b.pendaftaran_id) last_askep ON a.asesmenperawatrd_id = last_askep.asesmenperawatrd_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.keluhan_utama,
                    a.berat_badan,
                    a.tinggi_badan,
                    a.detak_nadi AS nadi,
                    a.pernapasan AS rr,
                    a.tekanan_darah AS td,
                    a.suhu_tubuh AS suhu,
                    a.td_systolic,
                    a.td_diastolic
                   FROM asesmenmedis_t a
                     JOIN ( SELECT max(b.asesmenmedis_id) AS asesmenmedis_id,
                            b.pendaftaran_id
                           FROM asesmenmedis_t b
                          GROUP BY b.pendaftaran_id) last_asmed ON a.asesmenmedis_id = last_asmed.asesmenmedis_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
             LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
                    a.obat_pulang
                   FROM resumemedisri_t a
                  WHERE a.is_deleted = false) resumemedis_t ON pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id
          WHERE pendaftaran_t.is_deleted = false AND pendaftaran_t.is_active = true;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_093702_view_gateway_layananri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_093702_view_gateway_layananri cannot be reverted.\n";

        return false;
    }
    */
}
