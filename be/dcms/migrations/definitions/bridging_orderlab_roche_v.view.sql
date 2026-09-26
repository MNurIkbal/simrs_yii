-- public.bridging_orderlab_roche_v source

CREATE OR REPLACE VIEW public.bridging_orderlab_roche_v
AS SELECT 'order'::text AS tipe,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasien_m.no_rekam_medik AS patient_id,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.tanggal_lahir AS date_of_birth,
        CASE pasien_m.jeniskelamin::integer
            WHEN 15 THEN 'M'::text
            ELSE 'F'::text
        END AS gender,
    pasien_m.alamat_pasien AS address,
    pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
    kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
    pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
    ruangan_m.ruangan_nama AS location_name,
    pendaftaran_t.no_pendaftaran AS case_no,
    pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
    pegawai_m.nomorindukpegawai AS ref_doctor_id,
    pegawai_m.nama_pegawai AS ref_doctor_name,
    ''::text AS order_reason,
        CASE
            CASE
                WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
                   FROM permintaankepenunjang_t countcyto
                  WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
                ELSE false
            END
            WHEN true THEN 'S'::text
            ELSE 'R'::text
        END AS priority,
    diagnosa.diagnosa_utama AS clinical_info,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS FALSE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) d) AS tests,
    ( SELECT array_to_json(array_agg(row_to_json(f.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS TRUE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) f) AS remove_tests,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE carabayar_m.groupcarabayar_id
                WHEN 417 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasienmasukpenunjang_t.additional_data,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasien_m.pasien_id,
    pasien_m.nama_ibu AS mother_maiden_name,
    pegawai_m.pegawai_id AS ref_doctor_primary_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN kelaspelayanan_m kelas ON pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pegawai_m ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
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
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
  WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL AND pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT 'Rujukan'::text AS tipe,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasien_m.no_rekam_medik AS patient_id,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.tanggal_lahir AS date_of_birth,
        CASE pasien_m.jeniskelamin::integer
            WHEN 15 THEN 'M'::text
            ELSE 'F'::text
        END AS gender,
    pasien_m.alamat_pasien AS address,
    pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
    kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
    pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
    ruangan_m.ruangan_nama AS location_name,
    pendaftaran_t.no_pendaftaran AS case_no,
    pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
    pegawai_m.nomorindukpegawai AS ref_doctor_id,
    pegawai_m.nama_pegawai AS ref_doctor_name,
    ''::text AS order_reason,
        CASE
            CASE
                WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
                   FROM permintaankepenunjang_t countcyto
                  WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
                ELSE false
            END
            WHEN true THEN 'S'::text
            ELSE 'R'::text
        END AS priority,
    diagnosa.diagnosa_utama AS clinical_info,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS FALSE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) d) AS tests,
    ( SELECT array_to_json(array_agg(row_to_json(f.*))) AS array_to_json
           FROM ( SELECT pemeriksaanlab_m.pemeriksaanlab_id::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS TRUE
                  GROUP BY pemeriksaanlab_m.pemeriksaanlab_id, daftartindakan_m.daftartindakan_nama) f) AS remove_tests,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE carabayar_m.groupcarabayar_id
                WHEN 417 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasienmasukpenunjang_t.additional_data,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasien_m.pasien_id,
    pasien_m.nama_ibu AS mother_maiden_name,
    pegawai_m.pegawai_id AS ref_doctor_primary_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN lookup_m jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
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
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS tipe,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasien_m.no_rekam_medik AS patient_id,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.tanggal_lahir AS date_of_birth,
        CASE pasien_m.jeniskelamin::integer
            WHEN 15 THEN 'M'::text
            ELSE 'F'::text
        END AS gender,
    pasien_m.alamat_pasien AS address,
    pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
    kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
    pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
    ruangan_m.ruangan_nama AS location_name,
    pendaftaran_t.no_pendaftaran AS case_no,
    pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
    pegawai_m.nomorindukpegawai AS ref_doctor_id,
    pegawai_m.nama_pegawai AS ref_doctor_name,
    ''::text AS order_reason,
        CASE
            CASE
                WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
                   FROM permintaankepenunjang_t countcyto
                  WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
                ELSE false
            END
            WHEN true THEN 'S'::text
            ELSE 'R'::text
        END AS priority,
    diagnosa.diagnosa_utama AS clinical_info,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS FALSE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) d) AS tests,
    ( SELECT array_to_json(array_agg(row_to_json(f.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS TRUE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) f) AS remove_tests,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE carabayar_m.groupcarabayar_id
                WHEN 417 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasienmasukpenunjang_t.additional_data,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasien_m.pasien_id,
    pasien_m.nama_ibu AS mother_maiden_name,
    pegawai_m.pegawai_id AS ref_doctor_primary_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN lookup_m jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
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
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;