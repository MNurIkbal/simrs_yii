-- public.bridging_orderlab_roche_v source

CREATE OR REPLACE VIEW public.bridging_orderlab_roche_v
AS SELECT 'order'::text AS tipe,
    header_data.pasienmasukpenunjang_id,
    header_data.pasienkirimkeunitlain_id,
    pasien_m.no_rekam_medik AS patient_id,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.tanggal_lahir AS date_of_birth,
        CASE pasien_m.jeniskelamin::integer
            WHEN 15 THEN 'M'::text
            ELSE 'F'::text
        END AS gender,
    pasien_m.alamat_pasien AS address,
    header_data.patient_class,
    header_data.patient_class_name,
    header_data.location_id,
    header_data.location_name,
    header_data.case_no,
    header_data.order_no,
    header_data.no_masukpenunjang,
    header_data.order_time,
    header_data.ref_doctor_id,
    header_data.ref_doctor_name,
    ''::text AS order_reason,
        CASE
            WHEN permintaankepenunjang_t.cyto_tindakan > 0 THEN 'S'::text
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
                  WHERE header_data.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS FALSE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) d) AS tests,
    ( SELECT array_to_json(array_agg(row_to_json(f.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_kode::text AS id,
                    daftartindakan_m.daftartindakan_nama AS name
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted IS FALSE
                  WHERE header_data.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted IS TRUE
                  GROUP BY daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama) f) AS remove_tests,
    header_data.is_bayar,
    header_data.additional_data,
    header_data.pendaftaran_id,
    header_data.pasien_id,
    pasien_m.nama_ibu AS mother_maiden_name,
    header_data.ref_doctor_primary_id,
    header_data.instalasi_id
   FROM ( SELECT 'order'::text AS tipe,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
            kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
            pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
            ruangan_m.ruangan_nama AS location_name,
            ruangan_m.instalasi_id,
            pendaftaran_t.no_pendaftaran AS case_no,
            pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
            pegawai_m.nomorindukpegawai AS ref_doctor_id,
            pegawai_m.nama_pegawai AS ref_doctor_name,
            ''::text AS order_reason,
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
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS ref_doctor_primary_id
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN pegawai_m ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
          WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL AND pasienkirimkeunitlain_t.instalasi_id = 4
        UNION ALL
         SELECT 'Rujukan'::text AS tipe,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
            kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
            pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
            ruangan_m.ruangan_nama AS location_name,
            ruangan_m.instalasi_id,
            pendaftaran_t.no_pendaftaran AS case_no,
            pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
            pegawai_m.nomorindukpegawai AS ref_doctor_id,
            pegawai_m.nama_pegawai AS ref_doctor_name,
            ''::text AS order_reason,
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
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS ref_doctor_primary_id
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
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
          WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
        UNION ALL
         SELECT 'APS'::text AS tipe,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienmasukpenunjang_t.kelaspelayanan_id::text AS patient_class,
            kelaspelayanan_m.kelaspelayanan_nama AS patient_class_name,
            pasienmasukpenunjang_t.ruanganasal_id::text AS location_id,
            ruangan_m.ruangan_nama AS location_name,
            ruangan_m.instalasi_id,
            pendaftaran_t.no_pendaftaran AS case_no,
            pasienmasukpenunjang_t.no_masukpenunjang AS order_no,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.tglmasukpenunjang AS order_time,
            pegawai_m.nomorindukpegawai AS ref_doctor_id,
            pegawai_m.nama_pegawai AS ref_doctor_name,
            ''::text AS order_reason,
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
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS ref_doctor_primary_id
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
          WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL) header_data
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin,
            a.alamat_pasien,
            a.nama_ibu
           FROM pasien_m a) pasien_m ON header_data.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            count(a.permintaankepenunjang_id) AS cyto_tindakan
           FROM permintaankepenunjang_t a
          WHERE a.is_cyto = true
          GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON header_data.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            b.a_diag_utama AS diagnosa_utama
           FROM cppt_t b
             JOIN ( SELECT b_1.pendaftaran_id,
                    max(b_1.cppt_id) AS cppt_id
                   FROM cppt_t b_1
                  WHERE b_1.is_deleted = false AND b_1.a_diag_utama IS NOT NULL
                  GROUP BY b_1.pendaftaran_id) cppt_max ON b.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_max.cppt_id = b.cppt_id
        UNION ALL
         SELECT b.pendaftaran_id,
            b.a_diag_utama AS diagnosa_utama
           FROM soaprj_t b
             JOIN ( SELECT b_1.pendaftaran_id,
                    max(b_1.soaprj_id) AS soaprj_id
                   FROM soaprj_t b_1
                  WHERE b_1.is_deleted = false AND b_1.a_diag_utama IS NOT NULL AND (b_1.a_diag_utama ->> 'text'::text) <> '-'::text
                  GROUP BY b_1.pendaftaran_id) soaprj_max ON b.pendaftaran_id = soaprj_max.pendaftaran_id AND soaprj_max.soaprj_id = b.soaprj_id
  ORDER BY 1) diagnosa ON header_data.pendaftaran_id = diagnosa.pendaftaran_id;