<?php

use yii\db\Migration;

/**
 * Class m230227_104744_hotfix_GBD184_infopasienri_v
 */
class m230227_104744_hotfix_GBD184_infopasienri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienri_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienri_v
        AS SELECT pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
            COALESCE(permintaankonsul_t.dokter_id, pasienadmisi_t.pegawai_id) AS dokter_admisi_id,
            pasienadmisi_t.carabayar_id,
            pasienadmisi_t.penjamin_id,
            bpjs_t.klsrawat,
            pasienadmisi_t.kelaspelayanan_id,
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.tgl_admisi,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.bpjs_id,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.namadepan,
            look_namadepan.lookup_name AS nama_depan,
            pasien_m.nama_pasien,
            pasien_m.alamat_pasien,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            pasienadmisi_t.tgl_pulang,
            rencanapulang_t.rencana_pulang,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pasienadmisi_t.status_ranap,
            look_statusranap.lookup_name AS stat_ranap,
            kamarruangan_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pendaftaran_t.golonganumur_id,
            pasienadmisi_t.tgl_pindahkamar,
            asesmenmedis_t.r_alergiobat,
            asesmenmedis_t.is_hamil,
            asesmenmedis_t.sumber_info,
            asesmenmedis_t.sumber_hubungan,
            asesmenmedis_t.luas_permukaantubuh,
            asesmenmedis_t.tinggi_badan,
            asesmenmedis_t.berat_badan,
            asesmenmedis_t.r_penyakitkeluarga,
            asesmenmedis_t.r_imunisasi,
            asesmenmedis_t.diagnosa_id,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            pasienadmisi_t.kamarruangan_id,
            pasienadmisi_t.kamartempattidur_id,
            pasien_m.photopasien,
            pendaftaran_t.caramasuk_id,
            caramasuk_m.caramasuk_nama,
            asesmenmedis_t.discharge_plan,
            asesmenawal_t.obatan_rumah,
            asesmenawal_t.obat_darirumah,
                CASE
                    WHEN (( SELECT count(*) AS count
                       FROM cppt_t x
                      WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
                    ELSE false
                END AS instruksi_pulang,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienadmisi_t.pasienpulang_id,
            pasien_m.jeniskelamin,
            pekerjaan_m.pekerjaan_nama,
            pendidikan_m.pendidikan_nama,
            asesmenmedis_t.r_peskk,
            asesmenmedis_t.is_merokok,
            asesmenmedis_t.jml_rokok,
            COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
            COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
            carabayar_m.groupcarabayar_id AS group_carabayar,
                CASE
                    WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
                    ELSE 'SUDAH DIMONITOR'::text
                END AS status_monitor,
            bpjs_t.nosep,
            pasienadmisi_t.is_aps,
            kelaspelayanan_m.urutankelas,
            kelaspelayanan_m.bpjs_kelas,
            pendaftaran_t.keterangan_pendaftaran,
            pasienadmisi_t.asuransipasien_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
                CASE
                    WHEN implementasi.sisa = 0 THEN true
                    WHEN implementasi.sisa <> 0 THEN false
                    ELSE false
                END AS status_implementasi,
            pasienadmisi_t.is_pasientitipan,
                CASE
                    WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
                    ELSE pindah_kamar.kelas_ditagihkan_id
                END AS kelas_ditagihkan_id,
                CASE
                    WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
                    ELSE pindah_kamar.kelas_ditagihkan
                END AS kelas_ditagihkan_nama,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            pasienadmisi_t.is_stoptitipan,
            pindah_kamar.pindahkamar_id,
                CASE
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
                    WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
                    WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
                    ELSE false
                END AS is_stoppasientitipan,
            stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
                    ELSE false
                END AS is_bayi,
            pendaftaran_t.status_bayar,
            carabayar_m.carabayar_warna,
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.penanggungbiaya_id,
            pasienadmisi_t.limit_tagihan,
            pasien_m.catatanpenting_pasien,
            asesmenawal_t.r_alergi AS alergi,
            skrininggizi_t.skrininggizi_id,
            skrininggizi_t.skor,
            pasienadmisi_t.hakkelas_id,
            pasienadmisi_t.kelaspermintaan_id,
            pasienadmisi_t.dokterpengirim_id,
            pasienadmisi_t.dokterkonsul_id,
            pasienadmisi_t.prosedurmasuk_id,
            pasienadmisi_t.diagnosa_awal,
                CASE
                    WHEN btrim(COALESCE(pasien_m.no_telepon_pasien, ''::character varying)::text) = ''::text THEN pasien_m.no_mobile_pasien
                    ELSE COALESCE(pasien_m.no_telepon_pasien, ''::character varying)
                END AS no_telepon_pasien,
            permintaankonsul_t.permintaankonsul_id AS konsulpoli_id,
            pasienadmisi_t.pegawai_id AS admisi_dokter_id,
            admisi_dokter.nama_pegawai AS admisi_dokter,
            permintaankonsul_t.dokter_id AS konsul_dokter_id,
            konsul_dokter.nama_pegawai AS konsul_dokter,
            permintaankonsul_t.jenis_konsul,
            permintaankonsul_t.status_konsul,
            pendaftaran_t.prev_pendaftaran_id
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.tgl_admisi,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.ruangan_id,
                    a.tgl_pulang,
                    a.status_ranap,
                    a.tgl_pindahkamar,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.pasienpulang_id,
                    a.is_aps,
                    a.asuransipasien_id,
                    a.is_pasientitipan,
                    a.kamar_titipan_id,
                    a.ruangan_titipan_id,
                    a.is_stoptitipan,
                    a.limit_tagihan,
                    a.hakkelas_id,
                    a.kelaspermintaan_id,
                    a.dokterpengirim_id,
                    a.dokterkonsul_id,
                    a.prosedurmasuk_id,
                    a.diagnosa_awal,
                    a.kelas_ditagihkan_id,
                    a.is_active,
                    a.is_deleted
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) admisi_dokter ON pasienadmisi_t.pegawai_id = admisi_dokter.pegawai_id
             LEFT JOIN ( SELECT a.permintaankonsul_id,
                    a.dokter_id,
                    a.jenis_konsul,
                    a.status_konsul,
                    a.pasienadmisi_id
                   FROM permintaankonsul_t a) permintaankonsul_t ON pasienadmisi_t.pasienadmisi_id = permintaankonsul_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) konsul_dokter ON permintaankonsul_t.dokter_id = konsul_dokter.pegawai_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.namadepan,
                    a.nama_pasien,
                    a.alamat_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.photopasien,
                    a.catatanpenting_pasien,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.pekerjaan_id,
                    a.pendidikan_id
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_admisi ON COALESCE(permintaankonsul_t.dokter_id, pasienadmisi_t.pegawai_id) = dokter_admisi.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id,
                    a.carabayar_warna,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.nosep,
                    a.klsrawat
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama,
                    a.urutankelas,
                    a.bpjs_kelas
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.jeniskasuspenyakit_id
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.r_alergiobat,
                    a.is_hamil,
                    a.sumber_info,
                    a.sumber_hubungan,
                    a.luas_permukaantubuh,
                    a.tinggi_badan,
                    a.berat_badan,
                    a.r_penyakitkeluarga,
                    a.r_imunisasi,
                    a.diagnosa_id,
                    a.discharge_plan,
                    a.r_peskk,
                    a.is_merokok,
                    a.jml_rokok,
                    a.is_deleted
                   FROM asesmenmedis_t a) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.obatan_rumah,
                    a.obat_darirumah,
                    a.r_alergi
                   FROM asesmenawal_t a) asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.rencana_pulang,
                    a.is_deleted
                   FROM rencanapulang_t a) rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    x.pasienadmisi_id,
                    sum(x.sub_total) AS sub_total
                   FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                            pendaftaran_t_1.pasienadmisi_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.tarif_tindakan,
                                    a.is_deleted
                                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                        UNION ALL
                         SELECT pendaftaran_t_1.pendaftaran_id,
                            pendaftaran_t_1.pasienadmisi_id,
                            sum(obatalkespasien_t.hargajual_oa) AS sub_total
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.hargajual_oa,
                                    a.is_deleted
                                   FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
                  GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id
             LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
                    monitorsetdiagnosa_t.pendaftaran_id,
                    monitorsetdiagnosa_t.pasienadmisi_id,
                    monitorsetdiagnosa_t.diag_utama_id,
                    diagnosa_m.diagnosa_kode,
                    diagnosa_m.diagnosa_nama,
                    monitorsetdiagnosa_t.diag_penyerta,
                    monitorsetdiagnosa_t.diag_tindakan,
                    monitorsetdiagnosa_t.total,
                    monitorsetdiagnosa_t.is_dokter
                   FROM monitorsetdiagnosa_t
                     JOIN ( SELECT a.diagnosa_id,
                            a.diagnosa_kode,
                            a.diagnosa_nama
                           FROM diagnosa_m a) diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
                  WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
             LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
                    count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi) AS sisa
                   FROM cppt_t
                     LEFT JOIN ( SELECT a.instruksi_id,
                            a.cppt_id,
                            a.is_deleted
                           FROM instruksi_t a) instruksi_t ON cppt_t.cppt_id = instruksi_t.cppt_id AND instruksi_t.is_deleted = false
                     LEFT JOIN ( SELECT a.instruksi_id,
                            a.status_implementasi,
                            a.is_deleted
                           FROM instruksitindakan_t a) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id AND instruksitindakan_t.is_deleted = false AND instruksitindakan_t.status_implementasi::text <> '455'::text
                     LEFT JOIN ( SELECT a.instruksi_id,
                            a.status_implementasi,
                            a.is_deleted
                           FROM instruksitindakanbmhp_t a) instruksitindakanbmhp_t ON instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id AND instruksitindakanbmhp_t.is_deleted = false AND instruksitindakanbmhp_t.status_implementasi::text <> '455'::text
                  WHERE cppt_t.is_deleted = false
                  GROUP BY cppt_t.pendaftaran_id) implementasi ON pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.kelas_ditagihkan_id,
                    kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
                  WHERE pindahkamar_t.is_deleted = false AND pindahkamar_t.is_pasientitipan = true) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.is_pasientitipan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                  WHERE pindahkamar_t.is_deleted = false) stop_titipan ON pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    kelahiranbayi_t_1.pendaftaranbaru_id
                   FROM kelahiranbayi_t kelahiranbayi_t_1
                  GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.skrininggizi_id,
                    a.pendaftaran_id,
                    a.skor,
                    a.is_active
                   FROM skrininggizi_t a) skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
          WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230227_104744_hotfix_GBD184_infopasienri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230227_104744_hotfix_GBD184_infopasienri_v cannot be reverted.\n";

        return false;
    }
    */
}
