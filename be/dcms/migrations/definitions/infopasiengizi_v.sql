-- public.infopasiengizi_v source

CREATE OR REPLACE VIEW public.infopasiengizi_v
AS SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    bpjs_t.klsrawatnaik,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    look_jeniskelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
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
    pendaftaran_t.jeniskasuspenyakit_id,
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
    cppt_t.diagnosa_id,
    cppt_t.a_diag_utama AS diagnosa_nama,
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
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE look_statusasesmen.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenawal_t.asmen_riwayat,
    asesmenmedis_t.r_peskk,
    makan_diet.jenisdiet_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter_pendaftaran ON COALESCE(pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id) = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT DISTINCT ON (bpjs_t_1.pendaftaran_id) bpjs_t_1.pendaftaran_id,
            bpjs_t_1.klsrawat,
            bpjs_t_1.klsrawatnaik
           FROM bpjs_t bpjs_t_1
          WHERE bpjs_t_1.is_deleted IS FALSE AND bpjs_t_1.jnspelayanan = 1
          ORDER BY bpjs_t_1.pendaftaran_id, bpjs_t_1.bpjs_id DESC) bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            a.r_alergiobat,
            a.is_hamil,
            a.sumber_info,
            a.sumber_hubungan,
            a.luas_permukaantubuh,
            a.tinggi_badan,
            a.berat_badan,
            a.r_penyakitkeluarga,
            a.r_imunisasi,
            a.discharge_plan,
            a.is_merokok,
            a.jml_rokok,
            a.r_peskk
           FROM asesmenmedis_t a
          WHERE a.is_deleted = false
          ORDER BY a.pendaftaran_id, a.tgl_asesmenmedis DESC) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT permintaanmakan_t.pasienadmisi_id,
            max(jenisdiet_m.jenisdiet_nama::text) AS jenisdiet_nama
           FROM permintaanmakandetail_t
             LEFT JOIN permintaanmakan_t ON permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail_t.permintaanmakan_id
             LEFT JOIN jenisdiet_m ON jenisdiet_m.jenisdiet_id = permintaanmakandetail_t.jenisdiet_id
          GROUP BY permintaanmakan_t.pasienadmisi_id) makan_diet ON makan_diet.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            a.a_diag_utama ->> 'text'::text AS diagnosa,
            a.a_diag_utama,
            a.a_diag_utama ->> 'id'::text AS diagnosa_id
           FROM cppt_t a
          WHERE a.is_deleted = false AND a.pasienadmisi_id IS NOT NULL AND (a.subject <> '-'::text OR a.object <> '-'::text OR (a.a_diag_utama ->> 'text'::text) <> '-'::text OR a.planning <> '-'::text)
          ORDER BY a.pasienadmisi_id, a.tgl_cppt DESC) cppt_t ON pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a
          WHERE a.lookup_type::text = 'jenis_kelamin'::text) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a
          WHERE a.lookup_type::text = 'status_ranap'::text) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
     LEFT JOIN ( SELECT a.lookupkeperawatan_id,
            a.lookup_name
           FROM lookupkeperawatan_m a) look_statusasesmen ON skrininggizi_t.status_asesmen = look_statusasesmen.lookupkeperawatan_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;