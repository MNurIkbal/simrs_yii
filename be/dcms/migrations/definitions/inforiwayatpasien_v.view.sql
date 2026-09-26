-- public.inforiwayatpasien_v source

CREATE OR REPLACE VIEW public.inforiwayatpasien_v
AS SELECT riwayat.tes,
    riwayat.pendaftaran_id,
    riwayat.pasienadmisi_id,
    riwayat.pasien_id,
    riwayat.pasienpulang_id,
    riwayat.no_rekam_medik,
    riwayat.no_pendaftaran,
    riwayat.tgl_pendaftaran,
    riwayat.ruangan_pend_id,
    riwayat.ruangan_pend,
    riwayat.ruangan_adm_id,
    riwayat.ruangan_adm,
    riwayat.dok_rjrd_id,
    riwayat.dok_rjrd,
    riwayat.dok_ri_id,
    riwayat.dok_ri,
    riwayat.r_anamesa,
    riwayat.r_pemeriksaanfisik,
    riwayat.r_diagnosa,
    riwayat.r_konsulpoli,
    riwayat.r_tindakan,
    riwayat.r_bmhp,
    riwayat.r_reseptur,
        CASE
            WHEN riwayat.r_anamesa IS NULL AND riwayat.r_pemeriksaanfisik IS NULL AND riwayat.r_diagnosa IS NULL AND riwayat.r_tindakan IS NULL AND riwayat.r_bmhp IS NULL AND riwayat.r_reseptur IS NULL AND riwayat.p_laboratorium IS NULL AND riwayat.p_radiologi IS NULL THEN 0
            ELSE 1
        END AS r_resumemedis_rj_rd,
    riwayat.hasil_laboratorium,
    riwayat.p_laboratorium,
    riwayat.p_radiologi,
    riwayat.p_operasi,
    riwayat.r_asesmenawal,
    riwayat.r_rekonsiliasiobat,
    riwayat.r_asesmenmedis,
    riwayat.r_dischargeplan,
    riwayat.r_cppt,
    riwayat.r_instruktitindakan,
    riwayat.r_instruktitindakanbmhp,
    riwayat.r_pemberianobat,
    riwayat.r_permintaankonsul,
    riwayat.r_pindahkamar,
    riwayat.r_resumemedis_ri,
    riwayat.r_visitedokter,
    riwayat.r_kesimpulan_rd,
    riwayat.r_asesmendokter,
    riwayat.r_asesmenperawat_rd,
    riwayat.r_asuhangizi,
    riwayat.r_soaprj,
    riwayat.r_hasilusg,
    riwayat.tglpasienpulang,
    riwayat.carakeluar_id,
    riwayat.carakeluar_nama,
        CASE
            WHEN riwayat.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_operasi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rj,
        CASE
            WHEN riwayat.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN riwayat.p_operasi = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_asesmenawal = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_rekonsiliasiobat = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_asesmenmedis = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_dischargeplan = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_cppt = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_instruktitindakan = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_instruktitindakanbmhp = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_pemberianobat = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_pindahkamar = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_permintaankonsul = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_visitedokter = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_kesimpulan_rd = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_asesmendokter = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_asesmenperawat_rd = 1 THEN 'SELESAI'::text
            WHEN riwayat.r_asuhangizi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rd_ri,
    riwayat.kondisikeluar_id,
    riwayat.kondisikeluar_nama,
    riwayat.instalasi_pend_id,
    riwayat.is_dokumen,
    riwayat.is_askep,
    riwayat.is_meninggal,
    riwayat.pemeriksaanfisik_id,
    riwayat.pendaftaran_rj_id,
    riwayat.r_asesmengizinrs,
    riwayat.r_pagt,
    riwayat.r_triase,
    riwayat.is_partograf,
    riwayat.is_nursingnotes,
    riwayat.r_asesmenmedis_spesialis,
    riwayat.is_resume_fisio,
    riwayat.is_rehabilitasimedis,
    riwayat.is_monitoringttv,
    riwayat.is_sbar
   FROM ( SELECT 'RJ/RD'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.ruangan_id AS ruangan_pend_id,
            pend_ruangan.ruangan_nama AS ruangan_pend,
            NULL::integer AS ruangan_adm_id,
            NULL::character varying AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            dok_rjrd.nama_pegawai AS dok_rjrd,
            NULL::integer AS dok_ri_id,
            NULL::character varying AS dok_ri,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM anamnesa_t
                      WHERE anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_anamesa,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pemeriksaanfisik_t
                      WHERE pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pemeriksaanfisik,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pasienmorbiditas_t
                      WHERE pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_diagnosa,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM konsulpoli_t
                      WHERE konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_konsulpoli,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                         LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                               FROM daftartindakan_m
                              WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
                      WHERE tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND obatalkespasien_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_bmhp,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM reseptur_t
                      WHERE reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND reseptur_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_reseptur,
            NULL::text AS r_resumemedis_rj_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilpemeriksaanlab_t
                      WHERE hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM hasilpemeriksaanlab_wynacom_t
                             JOIN pasienmasukpenunjang_t ON hasilpemeriksaanlab_wynacom_t.his_reg_no::text = pasienmasukpenunjang_t.no_masukpenunjang::text
                          WHERE pasienmasukpenunjang_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS hasil_laboratorium,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pasienkirimkeunitlain_t
                      WHERE pasienkirimkeunitlain_t.is_deleted = false AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL AND pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM pasienmasukpenunjang_t
                          WHERE pasienmasukpenunjang_t.is_deleted = false AND pasienmasukpenunjang_t.status_periksa::text <> '476'::text AND pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)) THEN 1
                        ELSE 0
                    END
                END AS p_laboratorium,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilpemeriksaanrad_t
                      WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE AND hasilpemeriksaanrad_t.pasienadmisi_id IS NULL AND hasilpemeriksaanrad_t.tgl_verifikasi IS NOT NULL AND hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS p_radiologi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM inpostoperasi_t
                         JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                      WHERE pasienmasukpenunjang_t.status_periksa::text <> '476'::text AND pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS p_operasi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenawal_t
                      WHERE asesmenawal_t.is_deleted IS FALSE AND asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenawal,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM rekonsiliasiobat_t
                      WHERE rekonsiliasiobat_t.is_deleted IS FALSE AND rekonsiliasiobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_rekonsiliasiobat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenmedisrd_t
                      WHERE asesmenmedisrd_t.is_deleted IS FALSE AND asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenmedis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM rencanapulang_t
                      WHERE rencanapulang_t.is_deleted IS FALSE AND rencanapulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_dischargeplan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM cppt_t
                      WHERE cppt_t.is_deleted IS FALSE AND cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_cppt,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM instruksitindakan_t
                      WHERE instruksitindakan_t.is_deleted IS FALSE AND instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_instruktitindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM instruksitindakanbmhp_t
                      WHERE instruksitindakanbmhp_t.is_deleted IS FALSE AND instruksitindakanbmhp_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_instruktitindakanbmhp,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pemberianobat_t
                      WHERE pemberianobat_t.is_deleted IS FALSE AND pemberianobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pemberianobat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM permintaankonsul_t
                      WHERE permintaankonsul_t.is_deleted IS FALSE AND permintaankonsul_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_permintaankonsul,
            0 AS r_pindahkamar,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM resumemedisri_t
                      WHERE resumemedisri_t.is_deleted IS FALSE AND resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_resumemedis_ri,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                         JOIN ( SELECT a.tindakanvisite_id,
                                a.is_visitedokter
                               FROM cppt_t a) cppt_t_1 ON tindakanpelayanan_t.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
                         JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                                daftartindakan_m_1.kelompoktindakan_id
                               FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE AND tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_visitedokter,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM kesimpulanrd_t
                      WHERE kesimpulanrd_t.is_deleted IS FALSE AND kesimpulanrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_kesimpulan_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenmedisrd_t
                      WHERE asesmenmedisrd_t.is_deleted IS FALSE AND asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmendokter,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenperawatrd_t
                      WHERE asesmenperawatrd_t.is_deleted IS FALSE AND asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenperawat_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asuhangizi_t
                      WHERE asuhangizi_t.is_deleted IS FALSE AND asuhangizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asuhangizi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM programterapirajal_r programterapirajal_r_1
                         JOIN ( SELECT a.programterapi_id,
                                a.pendaftaran_id
                               FROM programterapi_t a) programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id
                      WHERE programterapirajal_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id AND programterapirajal_r_1.is_deleted IS FALSE
                     LIMIT 1)) THEN
                    CASE
                        WHEN pendaftaran_t.ruangan_id = (( SELECT lookuptransaksi_m.kode_id
                           FROM lookuptransaksi_m
                          WHERE lookuptransaksi_m.kode_transaksi::text = 'ruang_fisio'::text)) AND (( SELECT programterapi_t.pendaftaran_id AS pendaftaran_rj_id
                           FROM programterapirajal_r programterapirajal_r_1
                             JOIN ( SELECT a.programterapi_id,
                                    a.pendaftaran_id
                                   FROM programterapi_t a) programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id
                          WHERE programterapirajal_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id AND programterapirajal_r_1.is_deleted IS FALSE
                         LIMIT 1)) IS NOT NULL THEN 1
                        ELSE NULL::integer
                    END
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM soaprj_t
                          WHERE soaprj_t.is_deleted IS FALSE AND soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS r_soaprj,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilusg_t
                         JOIN ( SELECT a.ruangan_id,
                                a.instalasi_id
                               FROM ruangan_m a) rm_usg ON rm_usg.ruangan_id = hasilusg_t.ruangan_id
                      WHERE hasilusg_t.is_deleted IS FALSE AND hasilusg_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND rm_usg.instalasi_id <> 3
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_hasilusg,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            NULL::text AS status_rj,
            NULL::text AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pendaftaran_t.instalasi_id AS instalasi_pend_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM dokumenupload_t
                      WHERE dokumenupload_t.is_deleted IS FALSE AND dokumenupload_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_dokumen,
                CASE pendaftaran_t.instalasi_id
                    WHEN 1 THEN
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM anamnesa_t
                          WHERE anamnesa_t.is_deleted IS FALSE AND anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM asesmenperawatrd_t
                          WHERE asesmenperawatrd_t.is_deleted IS FALSE AND asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS is_askep,
                CASE carakeluar_m.carakeluar_id
                    WHEN 4 THEN 1
                    ELSE 0
                END AS is_meninggal,
                CASE
                    WHEN NOT (EXISTS ( SELECT 1
                       FROM pemeriksaanfisik_t
                      WHERE pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pemeriksaanfisik_t.is_deleted IS FALSE
                     LIMIT 1)) THEN NULL::integer
                    ELSE ( SELECT max(pemeriksaanfisik_t.pemeriksaanfisik_id) AS max
                       FROM pemeriksaanfisik_t
                      WHERE pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pemeriksaanfisik_t.is_deleted IS FALSE
                     LIMIT 1)
                END AS pemeriksaanfisik_id,
                CASE
                    WHEN NOT (EXISTS ( SELECT 1
                       FROM programterapirajal_r programterapirajal_r_1
                         JOIN ( SELECT a.programterapi_id,
                                a.pendaftaran_id
                               FROM programterapi_t a) programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id
                      WHERE programterapirajal_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id AND programterapirajal_r_1.is_deleted IS FALSE
                     LIMIT 1)) THEN NULL::integer
                    ELSE ( SELECT programterapi_t.pendaftaran_id AS pendaftaran_rj_id
                       FROM programterapirajal_r programterapirajal_r_1
                         JOIN ( SELECT a.programterapi_id,
                                a.pendaftaran_id
                               FROM programterapi_t a) programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id
                      WHERE programterapirajal_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id AND programterapirajal_r_1.is_deleted IS FALSE
                     LIMIT 1)
                END AS pendaftaran_rj_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenawalgizinrs_t
                      WHERE asesmenawalgizinrs_t.is_deleted IS FALSE AND asesmenawalgizinrs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmengizinrs,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pagt_t
                      WHERE pagt_t.is_deleted IS FALSE AND pagt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pagt,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM triase_t
                      WHERE triase_t.is_deleted IS FALSE AND triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_triase,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM kelahiranbayi_t
                      WHERE kelahiranbayi_t.is_deleted IS FALSE AND kelahiranbayi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_partograf,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM catatankeperawatan_t
                      WHERE catatankeperawatan_t.is_deleted IS FALSE AND catatankeperawatan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_nursingnotes,
            0 AS r_asesmenmedis_spesialis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM resumemedis_t
                       WHERE pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id AND (resumemedis_t.additional_data::json ->> 'program_terapi_ids'::text) IS NOT NULL
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_resume_fisio,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM rehabilitasiterapi_t
                       WHERE rehabilitasiterapi_t.is_deleted IS FALSE AND rehabilitasiterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_rehabilitasimedis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM vitalsign_t
                       WHERE vitalsign_t.is_deleted IS FALSE AND vitalsign_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_monitoringttv,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM sbar_t
                       WHERE sbar_t.is_deleted IS FALSE AND sbar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_sbar
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) pend_ruangan ON pendaftaran_t.ruangan_id = pend_ruangan.ruangan_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_rjrd ON pendaftaran_t.pegawai_id = dok_rjrd.pegawai_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.carakeluar_id,
                    a.kondisikeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
          WHERE pendaftaran_t.instalasi_id <> 3
        UNION ALL
         SELECT 'RI'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pasienadmisi_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.ruangan_id
                    ELSE pendaftaran_t.ruangan_id
                END AS ruangan_pend_id,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.ruangan_nama
                    ELSE ruangan_m.ruangan_nama
                END AS ruangan_pend,
            pasienadmisi_t.ruangan_id AS ruangan_adm_id,
            adm_ruangan.ruangan_nama AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            pegawai_m.nama_pegawai AS dok_rjrd,
            pasienadmisi_t.pegawai_id AS dok_ri_id,
            dok_ri.nama_pegawai AS dok_ri,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM anamnesa_t
                      WHERE anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_anamesa,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pemeriksaanfisik_t
                      WHERE pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pemeriksaanfisik,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pasienmorbiditas_t
                      WHERE pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_diagnosa,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM konsulpoli_t
                      WHERE konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_konsulpoli,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                         LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                               FROM daftartindakan_m
                              WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
                      WHERE tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND obatalkespasien_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_bmhp,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM reseptur_t
                      WHERE reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND reseptur_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_reseptur,
            NULL::text AS r_resumemedis_rj_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilpemeriksaanlab_t
                      WHERE hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND hasilpemeriksaanlab_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM hasilpemeriksaanlab_wynacom_t
                             JOIN pasienmasukpenunjang_t ON hasilpemeriksaanlab_wynacom_t.his_reg_no::text = pasienmasukpenunjang_t.no_masukpenunjang::text
                          WHERE pasienmasukpenunjang_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.pasienadmisi_id = pasienmasukpenunjang_t.pasienadmisi_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS hasil_laboratorium,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pasienkirimkeunitlain_t
                      WHERE pasienkirimkeunitlain_t.is_deleted = false AND pasienkirimkeunitlain_t.pasienadmisi_id IS NOT NULL AND pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienkirimkeunitlain_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id AND pasienkirimkeunitlain_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS p_laboratorium,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilpemeriksaanrad_t
                      WHERE hasilpemeriksaanrad_t.is_deleted = false AND hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND hasilpemeriksaanrad_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id AND hasilpemeriksaanrad_t.is_deleted IS FALSE
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS p_radiologi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM inpostoperasi_t
                         JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                      WHERE pasienmasukpenunjang_t.status_periksa::text <> '476'::text AND pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pasienmasukpenunjang_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS p_operasi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenawal_t
                      WHERE asesmenawal_t.is_deleted IS FALSE AND asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenawal,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM rekonsiliasiobat_t
                      WHERE rekonsiliasiobat_t.is_deleted IS FALSE AND rekonsiliasiobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_rekonsiliasiobat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenmedis_t
                      WHERE asesmenmedis_t.is_deleted IS FALSE AND asesmenmedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND asesmenmedis_t.r_penyakitdahulu IS NOT NULL
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenmedis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM rencanapulang_t
                      WHERE rencanapulang_t.is_deleted IS FALSE AND rencanapulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_dischargeplan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM cppt_t
                      WHERE cppt_t.is_deleted IS FALSE AND cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND cppt_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_cppt,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM instruksitindakan_t
                      WHERE instruksitindakan_t.is_deleted IS FALSE AND instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_instruktitindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM instruksitindakanbmhp_t
                      WHERE instruksitindakanbmhp_t.is_deleted IS FALSE AND instruksitindakanbmhp_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_instruktitindakanbmhp,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pemberianobat_t
                      WHERE pemberianobat_t.is_deleted IS FALSE AND pemberianobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pemberianobat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM permintaankonsul_t
                      WHERE permintaankonsul_t.is_deleted IS FALSE AND permintaankonsul_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_permintaankonsul,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pindahkamar_t
                      WHERE pindahkamar_t.is_deleted IS FALSE AND pindahkamar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pindahkamar,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM resumemedisri_t
                      WHERE resumemedisri_t.is_deleted IS FALSE AND resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_resumemedis_ri,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                         JOIN ( SELECT a.tindakanvisite_id,
                                a.is_visitedokter
                               FROM cppt_t a) cppt_t_1 ON tindakanpelayanan_t.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
                         JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                                daftartindakan_m_1.kelompoktindakan_id
                               FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE AND tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_visitedokter,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM kesimpulanrd_t
                      WHERE kesimpulanrd_t.is_deleted IS FALSE AND kesimpulanrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_kesimpulan_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenmedisrd_t
                      WHERE asesmenmedisrd_t.is_deleted IS FALSE AND asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmendokter,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenperawatrd_t
                      WHERE asesmenperawatrd_t.is_deleted IS FALSE AND asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenperawat_rd,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asuhangizi_t
                      WHERE asuhangizi_t.is_deleted IS FALSE AND asuhangizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asuhangizi,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM soaprj_t
                      WHERE soaprj_t.is_deleted IS FALSE AND soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_soaprj,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM hasilusg_t
                         JOIN ( SELECT a.ruangan_id,
                                a.instalasi_id
                               FROM ruangan_m a) rm_usg ON rm_usg.ruangan_id = hasilusg_t.ruangan_id
                      WHERE hasilusg_t.is_deleted IS FALSE AND hasilusg_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND rm_usg.instalasi_id = 3
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_hasilusg,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            NULL::text AS status_rj,
            NULL::text AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.instalasi_id
                    ELSE pendaftaran_t.instalasi_id
                END AS instalasi_pend_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM dokumenupload_t
                      WHERE dokumenupload_t.is_deleted IS FALSE AND dokumenupload_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_dokumen,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenawal_t
                      WHERE asesmenawal_t.is_deleted IS FALSE AND asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_askep,
                CASE carakeluar_m.carakeluar_id
                    WHEN 4 THEN 1
                    ELSE 0
                END AS is_meninggal,
            NULL::integer AS pemeriksaanfisik_id,
            NULL::integer AS pendaftaran_rj_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM asesmenawalgizinrs_t
                      WHERE asesmenawalgizinrs_t.is_deleted IS FALSE AND asesmenawalgizinrs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmengizinrs,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pagt_t
                      WHERE pagt_t.is_deleted IS FALSE AND pagt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_pagt,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM triase_t
                      WHERE triase_t.is_deleted IS FALSE AND triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_triase,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM kelahiranbayi_t
                      WHERE kelahiranbayi_t.is_deleted IS FALSE AND kelahiranbayi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_partograf,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM catatankeperawatan_t
                      WHERE catatankeperawatan_t.is_deleted IS FALSE AND catatankeperawatan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_nursingnotes,
                CASE
                     WHEN (EXISTS ( SELECT 1
                        FROM asesmenmedis_t
                       WHERE asesmenmedis_t.is_deleted IS FALSE AND asesmenmedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND asesmenmedis_t.pemeriksaan_spesialis IS NOT NULL AND asesmenmedis_t.pasienadmisi_id IS NOT NULL
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS r_asesmenmedis_spesialis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM resumemedis_t
                       WHERE pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id AND (resumemedis_t.additional_data::json ->> 'program_terapi_ids'::text) IS NOT NULL
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_resume_fisio,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM rehabilitasiterapi_t
                       WHERE rehabilitasiterapi_t.is_deleted IS FALSE AND rehabilitasiterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE 0
                END AS is_rehabilitasimedis,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM vitalsign_t
                       WHERE vitalsign_t.is_deleted IS FALSE AND vitalsign_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_monitoringttv,
                CASE
                    WHEN (EXISTS ( SELECT 1
                        FROM sbar_t
                       WHERE sbar_t.is_deleted IS FALSE AND sbar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN true
                    ELSE false
                END AS is_sbar
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pendaftaran_id,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.tgl_admisi,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) adm_ruangan ON pasienadmisi_t.ruangan_id = adm_ruangan.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.carakeluar_id,
                    a.kondisikeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id) riwayat;