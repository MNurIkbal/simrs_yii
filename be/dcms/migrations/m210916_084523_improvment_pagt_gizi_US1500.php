<?php

use yii\db\Migration;

/**
 * Class m210916_084523_improvment_pagt_gizi_US1500
 */
class m210916_084523_improvment_pagt_gizi_US1500 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pagt_t ADD IF NOT EXISTS is_verifikasi BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE pagt_t ADD IF NOT EXISTS pegawai_verifikasi_id  int4;
        ');

        $this->execute('
            ALTER TABLE pagt_t ADD IF NOT EXISTS tgl_verifikasi TIMESTAMP(6);
        ');

        $this->execute('
            DROP VIEW IF EXISTS cpptgizi_v;
        ');

        $this->execute('
            CREATE VIEW "public"."cpptgizi_v" AS  SELECT pagt_t.pagt_id,
                pegawai_m.nama_pegawai AS pemberi_asuhan,
                pagt_t.pendaftaran_id, 
                pagt_t.tgl_kajian,
                pagt_t.bb,
                pagt_t.tb,
                pagt_t.imt_dewasa,
                pagt_t.imt_anak,
                pagt_t.lila,
                pagt_t.bb_anak,
                pagt_t.ulna,
                pagt_t.status_gizi,
                pagt_t.trigliserida,
                pagt_t.hdl,
                pagt_t.ldl,
                pagt_t.kolesterol,
                pagt_t.ureum,
                pagt_t.kreatinin,
                pagt_t.kalium,
                pagt_t.natrium,
                pagt_t.kalsium,
                pagt_t.phospor,
                pagt_t.sgot,
                pagt_t.sgpt,
                pagt_t.bilirubin,
                pagt_t.gd_sewaktu,
                pagt_t.gd_puasa,
                pagt_t.hba1c,
                pagt_t.dua_jam_pp,
                pagt_t.hb,
                pagt_t.albumin,
                pagt_t.ht,
                pagt_t.pemeriksaan_fisik,
                pagt_t.tekanan_darah,
                pagt_t.gangguan_pencernaan,
                pagt_t.makan_pagi_pokok,
                pagt_t.makan_pagi_hewani,
                pagt_t.makan_pagi_nabati,
                pagt_t.makan_pagi_sayur,
                pagt_t.makan_pagi_buah,
                pagt_t.makan_pagi_energi,
                pagt_t.makan_pagi_protein,
                pagt_t.makan_pagi_lemak,
                pagt_t.makan_pagi_kh,
                pagt_t.selingan_pagi_pokok,
                pagt_t.selingan_pagi_hewani,
                pagt_t.selingan_pagi_nabati,
                pagt_t.selingan_pagi_sayur,
                pagt_t.selingan_pagi_buah,
                pagt_t.selingan_pagi_energi,
                pagt_t.selingan_pagi_protein,
                pagt_t.selingan_pagi_lemak,
                pagt_t.selingan_pagi_kh,
                pagt_t.makan_siang_pokok,
                pagt_t.makan_siang_hewani,
                pagt_t.makan_siang_nabati,
                pagt_t.makan_siang_sayur,
                pagt_t.makan_siang_buah,
                pagt_t.makan_siang_energi,
                pagt_t.makan_siang_protein,
                pagt_t.makan_siang_lemak,
                pagt_t.makan_siang_kh,
                pagt_t.selingan_sore_pokok,
                pagt_t.selingan_sore_hewani,
                pagt_t.selingan_sore_nabati,
                pagt_t.selingan_sore_sayur,
                pagt_t.selingan_sore_buah,
                pagt_t.selingan_sore_energi,
                pagt_t.selingan_sore_protein,
                pagt_t.selingan_sore_lemak,
                pagt_t.selingan_sore_kh,
                pagt_t.makan_malam_pokok,
                pagt_t.makan_malam_hewani,
                pagt_t.makan_malam_nabati,
                pagt_t.makan_malam_sayur,
                pagt_t.makan_malam_buah,
                pagt_t.makan_malam_energi,
                pagt_t.makan_malam_protein,
                pagt_t.makan_malam_lemak,
                pagt_t.makan_malam_kh,
                pagt_t.selingan_malam_pokok,
                pagt_t.selingan_malam_hewani,
                pagt_t.selingan_malam_nabati,
                pagt_t.selingan_malam_sayur,
                pagt_t.selingan_malam_buah,
                pagt_t.selingan_malam_energi,
                pagt_t.selingan_malam_protein,
                pagt_t.selingan_malam_lemak,
                pagt_t.selingan_malam_kh,
                pagt_t.diagnosa_gizi,
                pagt_t.cara_intervensi,
                pagt_t.diet_diberikan,
                pagt_t.energi,
                pagt_t.lemak,
                pagt_t.protein,
                pagt_t.kh,
                pagt_t.tujuan_diet,
                pagt_t.bentuk_makanan,
                pagt_t.bentuk_makanan_saji AS sonde_voeding_saji,
                pagt_t.bentuk_makanan_hari AS sonde_voeding_hari,
                pagt_t.cara_pemberian,
                pagt_t.bagi_makan_pagi_nasi,
                pagt_t.bagi_makan_pagi_hewani,
                pagt_t.bagi_makan_pagi_nabati,
                pagt_t.bagi_makan_pagi_sayur,
                pagt_t.bagi_makan_pagi_buah,
                pagt_t.bagi_selingan_pagi_nasi,
                pagt_t.bagi_selingan_pagi_hewani,
                pagt_t.bagi_selingan_pagi_nabati,
                pagt_t.bagi_selingan_pagi_sayur,
                pagt_t.bagi_selingan_pagi_buah,
                pagt_t.bagi_makan_siang_nasi,
                pagt_t.bagi_makan_siang_hewani,
                pagt_t.bagi_makan_siang_nabati,
                pagt_t.bagi_makan_siang_sayur,
                pagt_t.bagi_makan_siang_buah,
                pagt_t.bagi_selingan_sore_nasi,
                pagt_t.bagi_selingan_sore_hewani,
                pagt_t.bagi_selingan_sore_nabati,
                pagt_t.bagi_selingan_sore_sayur,
                pagt_t.bagi_selingan_sore_buah,
                pagt_t.bagi_makan_malam_nasi,
                pagt_t.bagi_makan_malam_hewani,
                pagt_t.bagi_makan_malam_nabati,
                pagt_t.bagi_makan_malam_sayur,
                pagt_t.bagi_makan_malam_buah,
                pagt_t.bagi_selingan_malam_nasi,
                pagt_t.bagi_selingan_malam_hewani,
                pagt_t.bagi_selingan_malam_nabati,
                pagt_t.bagi_selingan_malam_sayur,
                pagt_t.bagi_selingan_malam_buah,
                pagtmonev_t.pagtmonev_id,
                pagtmonev_t.tgl_monev,
                pagtmonev_t.berat_badan,
                pagtmonev_t.tekanan_darah AS tekanan_darah_monev,
                pagtmonev_t.nilai_lab_abnormal,
                pagtmonev_t.tgl_monev AS tgl_asupan_makanan,
                pagtmonev_t.oral_energi,
                pagtmonev_t.oral_protein,
                pagtmonev_t.oral_lemak,
                pagtmonev_t.oral_kh,
                pagtmonev_t.enteral_energi,
                pagtmonev_t.enteral_protein,
                pagtmonev_t.enteral_lemak,
                pagtmonev_t.enteral_kh,
                pagtmonev_t.parenteral_energi,
                pagtmonev_t.parenteral_protein,
                pagtmonev_t.parenteral_lemak,
                pagtmonev_t.parenteral_kh,
                pagtmonev_t.total_asupan_energi,
                pagtmonev_t.total_asupan_protein,
                pagtmonev_t.total_asupan_lemak,
                pagtmonev_t.total_asupan_kh,
                pagtmonev_t.evaluasi_usulan,
                \'\'::character varying AS instruksi_ppa,
                pagt_t.is_verifikasi,
                pagt_t.tgl_verifikasi,
                pagt_t.pegawai_verifikasi_id,
                pegawai_verifikasi.nama_pegawai AS pegawai_verifikasi_nama,
                pasienadmisi.dokteradmisi_id,
                pasienadmisi.dokteradmisi_nama,
                pegawai_m.pegawai_id AS pemberi_instruksi_id,
                kelompokpegawai_m.kelompokpegawai_nama
               FROM (((((((pagt_t
                 LEFT JOIN ( SELECT pagtmonev_t_1.pagt_id,
                        max(pagtmonev_t_1.pagtmonev_id) AS pagtmonev_id
                       FROM pagtmonev_t pagtmonev_t_1
                      GROUP BY pagtmonev_t_1.pagt_id) pagtmonev_last ON ((pagt_t.pagt_id = pagtmonev_last.pagt_id)))
                 LEFT JOIN pagtmonev_t ON ((pagtmonev_last.pagtmonev_id = pagtmonev_t.pagtmonev_id)))
                 LEFT JOIN loginpemakai_k ON ((pagt_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m pegawai_verifikasi ON ((pagt_t.pegawai_verifikasi_id = pegawai_verifikasi.pegawai_id)))
                 LEFT JOIN ( SELECT pasienadmisi_t.pegawai_id AS dokteradmisi_id,
                        dokteradmisi.nama_pegawai AS dokteradmisi_nama,
                        pasienadmisi_t.pendaftaran_id
                       FROM (pasienadmisi_t
                         LEFT JOIN pegawai_m dokteradmisi ON ((pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id)))) pasienadmisi ON ((pagt_t.pendaftaran_id = pasienadmisi.pendaftaran_id)))
                 LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)));
        ');

        $this->execute('
            DROP VIEW IF EXISTS infopasienri_v;
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienri_v" AS  SELECT pasienadmisi_t.pasienadmisi_id, 
                pendaftaran_t.pendaftaran_id,
                pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
                pasienadmisi_t.pegawai_id AS dokter_admisi_id,
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
                fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
                pasien_m.nama_pasien,
                pasien_m.alamat_pasien,
                pasien_m.jeniskelamin AS jeniskelamin_id,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
                fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
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
                          WHERE ((x.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (x.is_instruksi_pulang = true))) > 0) THEN true
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
                COALESCE(tagihan.sub_total, (0)::double precision) AS tagihan_rs,
                COALESCE(monitorsetdiagnosa.total, (0)::double precision) AS tarif_inacbg,
                carabayar_m.groupcarabayar_id AS group_carabayar,
                    CASE
                        WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN \'BELUM DIMONITOR\'::text
                        ELSE \'SUDAH DIMONITOR\'::text
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
                        WHEN (implementasi.sisa = 0) THEN true
                        WHEN (implementasi.sisa <> 0) THEN false
                        ELSE false
                    END AS status_implementasi,
                pasienadmisi_t.is_pasientitipan,
                    CASE
                        WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
                        ELSE pindah_kamar.kelas_ditagihkan_id
                    END AS kelas_ditagihkan_id,
                    CASE
                        WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
                        ELSE pindah_kamar.kelas_ditagihkan
                    END AS kelas_ditagihkan_nama,
                pasienadmisi_t.kamar_titipan_id,
                kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
                pasienadmisi_t.ruangan_titipan_id,
                ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
                pasienadmisi_t.is_stoptitipan,
                pindah_kamar.pindahkamar_id,
                    CASE
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
                        ELSE false
                    END AS is_stoppasientitipan,
                stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
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
                skrininggizi_t.skor
               FROM ((((((((((((((((((((((((((((pendaftaran_t
                 JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                 LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
                 JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
                 JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (asesmenmedis_t.is_deleted = false))))
                 LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 LEFT JOIN asesmenawal_t ON ((pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id)))
                 LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
                 LEFT JOIN ( SELECT x.pendaftaran_id,
                        x.pasienadmisi_id,
                        sum(x.sub_total) AS sub_total
                       FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                                pendaftaran_t_1.pasienadmisi_id,
                                sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                               FROM (pendaftaran_t pendaftaran_t_1
                                 JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                              GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                pendaftaran_t_1.pasienadmisi_id,
                                sum(obatalkespasien_t.hargajual_oa) AS sub_total
                               FROM (pendaftaran_t pendaftaran_t_1
                                 JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                              GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
                      GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON (((pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id))))
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
                       FROM (monitorsetdiagnosa_t
                         JOIN diagnosa_m ON ((monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id)))
                      WHERE (monitorsetdiagnosa_t.is_deleted = false)) monitorsetdiagnosa ON ((pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id)))
                 LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
                        (count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi)) AS sisa
                       FROM (((cppt_t
                         LEFT JOIN instruksi_t ON (((cppt_t.cppt_id = instruksi_t.cppt_id) AND (instruksi_t.is_deleted = false))))
                         LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> \'455\'::text))))
                         LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> \'455\'::text))))
                      WHERE (cppt_t.is_deleted = false)
                      GROUP BY cppt_t.pendaftaran_id) implementasi ON ((pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id)))
                 LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
                 LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
                 LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
                 LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                        pindahkamar_t.pasienadmisi_id,
                        pindahkamar_t.kelas_ditagihkan_id,
                        kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                        pindahkamar_t.is_stoptitipan
                       FROM ((pindahkamar_t
                         JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                                pk.pasienadmisi_id
                               FROM pindahkamar_t pk
                              GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
                         LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))
                      WHERE ((pindahkamar_t.is_deleted = false) AND (pindahkamar_t.is_pasientitipan = true))) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)))
                 LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                        pindahkamar_t.pasienadmisi_id,
                        pindahkamar_t.is_pasientitipan,
                        pindahkamar_t.is_stoptitipan
                       FROM (pindahkamar_t
                         JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                                pk.pasienadmisi_id
                               FROM pindahkamar_t pk
                              GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
                      WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN skrininggizi_t ON (((pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id) AND (skrininggizi_t.is_active = true))))
              WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210916_084523_improvment_pagt_gizi_US1500 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210916_084523_improvment_pagt_gizi_US1500 cannot be reverted.\n";

        return false;
    }
    */
}
