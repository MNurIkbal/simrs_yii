CREATE VIEW "public"."laporankunjunganri_v" AS  SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_panggilan AS nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.kabupaten_id,
        CASE
            WHEN (EXISTS ( SELECT kabupaten_m.kabupaten_nama
               FROM kabupaten_m
              WHERE kabupaten_m.kabupaten_id = pasien_m.kabupaten_id
             LIMIT 1)) THEN ( SELECT kabupaten_m.kabupaten_nama
               FROM kabupaten_m
              WHERE kabupaten_m.kabupaten_id = pasien_m.kabupaten_id)
            ELSE NULL::character varying
        END AS kabupaten_nama,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.nama_pegawai,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pendaftaran_t.status_periksa::integer) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.pasienpulang_id,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_namalain,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.agama::integer) AS agama,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.jenisidentitas::integer) AS jenisidentitas,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.namadepan::integer) AS namadepan,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.golongandarah::integer) AS golongandarah,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.statusperkawinan::integer) AS statusperkawinan,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pendaftaran_t.status_pasien::integer) AS status_pasien,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pendaftaran_t.kunjungan::integer) AS kunjungan,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pegawai_m.gelardepan::integer) AS gelardepan,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pegawai_m.gelarbelakang::integer) AS gelarbelakang,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasien_m.rhesus::integer) AS rhesus,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pendaftaran_t.status_masuk::integer) AS status_masuk,
    pasien_m.jeniskelamin,
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pasienadmisi_t.status_ranap) AS status_ranap_nama,
    golonganumur_m.golonganumur_nama,
    carakeluar_m.carakeluar_nama,
    pasienadmisi_t.kamartempattidur_id,
    bpjs_t.nosep,
    bpjs_t.bpjs_id,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.is_stoptitipan,
    pindah_kamar.pindahkamar_id,
        CASE
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
        CASE
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
            WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
            WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
    resume.tgl_keluar,
    resume.diagnosa
   FROM pendaftaran_t
     JOIN ( SELECT pasienadmisi_t_1.pendaftaran_id,
            pasienadmisi_t_1.caramasuk_id,
            pasienadmisi_t_1.kamarruangan_id,
            pasienadmisi_t_1.carabayar_id,
            pasienadmisi_t_1.kelaspelayanan_id,
            pasienadmisi_t_1.penjamin_id,
            pasienadmisi_t_1.ruangan_id,
            pasienadmisi_t_1.pasienadmisi_id,
            pasienadmisi_t_1.tgl_admisi,
            pasienadmisi_t_1.tgl_pulang,
            pasienadmisi_t_1.status_keluar,
            pasienadmisi_t_1.rawat_gabung,
            pasienadmisi_t_1.pegawai_id,
            pasienadmisi_t_1.status_ranap,
            pasienadmisi_t_1.kamartempattidur_id,
            pasienadmisi_t_1.is_pasientitipan,
            pasienadmisi_t_1.kelas_ditagihkan_id,
            pasienadmisi_t_1.is_stoptitipan,
            pasienadmisi_t_1.pasienpulang_id,
            pasienadmisi_t_1.pasienbatalperiksa_id
           FROM pasienadmisi_t pasienadmisi_t_1) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_identitas_pasien,
            pasien_m_1.nama_pasien,
            pasien_m_1.nama_panggilan,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.alamat_pasien,
            pasien_m_1.rt,
            pasien_m_1.rw,
            pasien_m_1.photopasien,
            pasien_m_1.alamatemail,
            pasien_m_1.statusrekammedis,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.tgl_rekam_medik,
            pasien_m_1.pekerjaan_id,
            pasien_m_1.jeniskelamin,
            pasien_m_1.kabupaten_id,
            pasien_m_1.anakke,
            pasien_m_1.jumlah_bersaudara,
            pasien_m_1.no_telepon_pasien,
            pasien_m_1.no_mobile_pasien,
            pasien_m_1.warga_negara,
            pasien_m_1.nama_ibu,
            pasien_m_1.nama_ayah,
            pasien_m_1.is_deleted,
            pasien_m_1.agama,
            pasien_m_1.jenisidentitas,
            pasien_m_1.namadepan,
            pasien_m_1.golongandarah,
            pasien_m_1.statusperkawinan,
            pasien_m_1.rhesus,
            pasien_m_1.suku_id,
            pasien_m_1.pendidikan_id
           FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT pekerjaan_m_1.pekerjaan_id,
            pekerjaan_m_1.pekerjaan_nama
           FROM pekerjaan_m pekerjaan_m_1) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai,
            pegawai_m_1.kelompokpegawai_id,
            pegawai_m_1.gelardepan,
            pegawai_m_1.gelarbelakang
           FROM pegawai_m pegawai_m_1) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m_1.asuransipasien_id,
            asuransipasien_m_1.nokartuasuransi,
            asuransipasien_m_1.namapemilikasuransi,
            asuransipasien_m_1.nomorpokokperusahaan,
            asuransipasien_m_1.status_konfirmasi,
            asuransipasien_m_1.tgl_konfirmasi,
            asuransipasien_m_1.nopeserta,
            asuransipasien_m_1.tglcetakkartuasuransi,
            asuransipasien_m_1.kodefeskestk1,
            asuransipasien_m_1.nama_feskestk1,
            asuransipasien_m_1.masaberlakukartu,
            asuransipasien_m_1.nokartukeluarga,
            asuransipasien_m_1.nopassport,
            asuransipasien_m_1.is_active
           FROM asuransipasien_m asuransipasien_m_1) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT carabayar_m_1.carabayar_id,
            carabayar_m_1.carabayar_nama
           FROM carabayar_m carabayar_m_1) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT penjamin_m_1.penjamin_id,
            penjamin_m_1.penjamin_nama
           FROM penjamin_m penjamin_m_1) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT caramasuk_m_1.caramasuk_id,
            caramasuk_m_1.caramasuk_nama
           FROM caramasuk_m caramasuk_m_1) caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN ( SELECT rujukan_t_1.rujukan_id,
            rujukan_t_1.no_rujukan,
            rujukan_t_1.asalrujukan_id,
            rujukan_t_1.nama_perujuk,
            rujukan_t_1.tanggal_rujukan,
            rujukan_t_1.kodediagnosa_rujukan
           FROM rujukan_t rujukan_t_1) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT penanggungjawab_m_1.penanggungjawab_id,
            penanggungjawab_m_1.pengantar,
            penanggungjawab_m_1.hubungankeluarga,
            penanggungjawab_m_1.penanggungjawab_nama
           FROM penanggungjawab_m penanggungjawab_m_1) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.instalasi_id,
            ruangan_m_1.ruangan_nama
           FROM ruangan_m ruangan_m_1) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT instalasi_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama
           FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT jeniskasuspenyakit_m_1.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m_1.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m jeniskasuspenyakit_m_1) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT kelaspelayanan_m_1.kelaspelayanan_id,
            kelaspelayanan_m_1.kelaspelayanan_nama
           FROM kelaspelayanan_m kelaspelayanan_m_1) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT kamarruangan_m_1.kamarruangan_id,
            kamarruangan_m_1.kamarruangan_nokamar
           FROM kamarruangan_m kamarruangan_m_1) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT kamartempattidur_m_1.kamartempattidur_id,
            kamartempattidur_m_1.no_tempattidur
           FROM kamartempattidur_m kamartempattidur_m_1) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT pasienpulang_t_1.pasienpulang_id,
            pasienpulang_t_1.kondisikeluar_id,
            pasienpulang_t_1.carakeluar_id
           FROM pasienpulang_t pasienpulang_t_1) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT kondisikeluar_m_1.kondisikeluar_id,
            kondisikeluar_m_1.kondisikeluar_nama
           FROM kondisikeluar_m kondisikeluar_m_1) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN ( SELECT carakeluar_m_1.carakeluar_id,
            carakeluar_m_1.carakeluar_namalain,
            carakeluar_m_1.carakeluar_nama
           FROM carakeluar_m carakeluar_m_1) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN ( SELECT pasienbatalperiksa_t_1.pasienbatalperiksa_id,
            pasienbatalperiksa_t_1.alasan_batal
           FROM pasienbatalperiksa_t pasienbatalperiksa_t_1) pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN ( SELECT golonganumur_m_1.golonganumur_id,
            golonganumur_m_1.golonganumur_nama
           FROM golonganumur_m golonganumur_m_1) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN ( SELECT bpjs_t_1.nosep,
            bpjs_t_1.bpjs_id,
            bpjs_t_1.is_deleted
           FROM bpjs_t bpjs_t_1) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
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
             LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
          WHERE pindahkamar_t.is_deleted = false AND pindahkamar_t.is_pasientitipan = true) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
     LEFT JOIN ( SELECT kelaspelayanan_m_1.kelaspelayanan_id,
            kelaspelayanan_m_1.kelaspelayanan_nama
           FROM kelaspelayanan_m kelaspelayanan_m_1) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
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
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.tgl_keluar,
            a.diag_utama ->> 'text'::text AS diagnosa
           FROM resumemedisri_t a
             JOIN ( SELECT max(b.resumemedisri_id) AS resumemedisri_id,
                    b.pendaftaran_id
                   FROM resumemedisri_t b
                  GROUP BY b.pendaftaran_id) resume_max ON a.resumemedisri_id = resume_max.resumemedisri_id) resume ON pendaftaran_t.pendaftaran_id = resume.pendaftaran_id
     LEFT JOIN ( SELECT suku_m_1.suku_id,
            suku_m_1.suku_nama
           FROM suku_m suku_m_1) suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN ( SELECT pendidikan_m_1.pendidikan_id,
            pendidikan_m_1.pendidikan_nama
           FROM pendidikan_m pendidikan_m_1) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.status_periksa::text <> '628'::text AND pendaftaran_t.status_periksa::text <> '453'::text;

