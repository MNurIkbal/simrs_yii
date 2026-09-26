<?php

use yii\db\Migration;

/**
 * Class m220226_145727_migrate_DHC33_laporan_permintaan_makan
 */
class m220226_145727_migrate_DHC33_laporan_permintaan_makan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpermintaanmakanpasien_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanpermintaanmakanpasien_v" AS  SELECT pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            permintaanmakan_t.tgl_permintaanmakan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien, 
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
                CASE
                    WHEN (permintaanmakan_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_id
                    ELSE admisi.ruangan_id
                END AS ruangan_id,
                CASE
                    WHEN (permintaanmakan_t.pasienadmisi_id IS NULL) THEN (ruangan_m.ruangan_nama)::text
                    ELSE concat(admisi.ruangan_nama, \' - \', COALESCE(kamarruangan_m.kamarruangan_nokamar, \'\'::character varying), \' - \', COALESCE(kamartempattidur_m.no_tempattidur, \'\'::character varying))
                END AS ruangan_nama,
            permintaanmakandetail.menu_makan,
            permintaanmakandetail.jenisdiet_nama,
            diagnosa_rd.diagnosa_utama AS diagnosa,
            COALESCE(pendaftaran_t.is_stopakomodasi, false) AS is_stopakomodasi,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, \'\'::character varying) AS kamarruangan_nokamar,
            COALESCE(kamartempattidur_m.no_tempattidur, \'\'::character varying) AS no_tempattidur,
                CASE
                    WHEN (permintaanmakan_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
                    ELSE dr_admisi.nama_pegawai
                END AS dokter_dpjp,
                CASE
                    WHEN (permintaanmakan_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienadmisi_t.tgl_admisi,
            permintaanmakandetail.menu_makan AS new_diet
           FROM (((((((((((((((permintaanmakan_t
             JOIN pendaftaran_t ON ((permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasienadmisi_t ON ((permintaanmakan_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
             LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
             LEFT JOIN ruangan_m admisi ON ((kamarruangan_m.ruangan_id = admisi.ruangan_id)))
             LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dr_admisi ON ((pasienadmisi_t.ruangan_id = dr_admisi.pegawai_id)))
             LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             JOIN ( SELECT string_agg((daftartindakan_m.daftartindakan_nama)::text, \',\'::text) AS menu_makan,
                    string_agg((jenisdiet_m.jenisdiet_nama)::text, \',\'::text) AS jenisdiet_nama,
                    permintaanmakandetail_t.permintaanmakan_id
                   FROM ((permintaanmakandetail_t
                     JOIN daftartindakan_m ON ((permintaanmakandetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     JOIN jenisdiet_m ON ((permintaanmakandetail_t.jenisdiet_id = jenisdiet_m.jenisdiet_id)))
                  GROUP BY permintaanmakandetail_t.permintaanmakan_id
                  ORDER BY permintaanmakandetail_t.permintaanmakan_id) permintaanmakandetail ON ((permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail.permintaanmakan_id)))
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM ((pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                     JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                  WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM ((pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                     JOIN ( SELECT cppt_t_1.cppt_id,
                            cppt_t_1.pendaftaran_id,
                            cppt_t_1.a_diag_utama,
                            cppt_t_1.a_diag_penyerta
                           FROM (cppt_t cppt_t_1
                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                    cppt_last.pendaftaran_id
                                   FROM cppt_t cppt_last
                                  WHERE (cppt_last.is_deleted = false)
                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                  WHERE (cppt_t.a_diag_utama IS NOT NULL)) diagnosa_rd ON ((pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id)))
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM (((pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                     JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                  WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa_ri ON ((pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220226_145727_migrate_DHC33_laporan_permintaan_makan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220226_145727_migrate_DHC33_laporan_permintaan_makan cannot be reverted.\n";

        return false;
    }
    */
}
