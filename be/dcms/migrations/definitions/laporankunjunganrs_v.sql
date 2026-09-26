-- public.laporankunjunganrs_v source

CREATE OR REPLACE VIEW public.laporankunjunganrs_v
      AS SELECT 'a'::text AS ket,
          pasien_m.pasien_id,
          pasien_m.no_identitas_pasien,
          pasien_m.nama_pasien,
          pasien_m.nama_panggilan AS nama_bin,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
          pasien_m.tempat_lahir,
          pasien_m.tanggal_lahir,
          pasien_m.alamat_pasien,
          pasien_m.rt,
          pasien_m.rw,
          pasien_m.photopasien,
          pasien_m.alamatemail,
          pasien_m.statusrekammedis,
          pasien_m.statusperkawinan,
          pasien_m.no_rekam_medik,
          pasien_m.tgl_rekam_medik,
          pendaftaran_t.pendaftaran_id,
          pendaftaran_t.no_pendaftaran,
          pendaftaran_t.tgl_pendaftaran,
          pendaftaran_t.no_urutantri,
          pendaftaran_t.transportasi,
          pendaftaran_t.keadaan_masuk,
          pendaftaran_t.alih_status,
          pendaftaran_t.by_phone,
          pendaftaran_t.kunjungan_rumah,
          pendaftaran_t.status_masuk,
          pendaftaran_t.umur,
          asuransipasien_m.nokartuasuransi AS no_asuransi,
          asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
          asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
          pendaftaran_t.shift_id,
          ruangan_m.ruangan_id,
          ruangan_m.ruangan_nama,
          instalasi_m.instalasi_id,
          instalasi_m.instalasi_nama,
          jeniskasuspenyakit_m.jeniskasuspenyakit_id,
          jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
          kelaspelayanan_m.kelaspelayanan_id,
          kelaspelayanan_m.kelaspelayanan_nama,
          pendaftaran_t.rujukan_id,
          pendaftaran_t.pasienpulang_id,
          fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
          pasien_m.kabupaten_id,
          fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
          pasien_m.kelurahan_id,
          fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
          pasien_m.kecamatan_id,
          carabayar_m.carabayar_id,
          carabayar_m.carabayar_nama,
          penjamin_m.penjamin_id,
          penjamin_m.penjamin_nama,
          asuransipasien_m.nopeserta,
          asuransipasien_m.tglcetakkartuasuransi,
          asuransipasien_m.kodefeskestk1,
          asuransipasien_m.nama_feskestk1,
          asuransipasien_m.masaberlakukartu,
          asuransipasien_m.nokartukeluarga,
          asuransipasien_m.nopassport,
          asuransipasien_m.status_konfirmasi,
          asuransipasien_m.tgl_konfirmasi,
          asuransipasien_m.is_active,
          pasien_m.is_deleted,
          fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
          fgetnamalookup(pasien_m.agama::integer) AS agama,
          fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
          fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
          fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
          fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
          fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
          fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
          fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
          gelarbelakang.gelarbelakang_nama,
          fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
          bpjs_t.nosep,
          NULL::integer AS kelas_ditagihkan_id,
          NULL::character varying AS kelas_ditagihkan_nama,
          NULL::boolean AS is_pasientitipan,
          NULL::boolean AS is_stoptitipan,
          NULL::integer AS pindahkamar_id,
          NULL::boolean AS is_stoppasientitipan,
          NULL::boolean AS is_pasientitipan_pk,
              CASE
                  WHEN pendaftaran_t.status_periksa::text <> '433'::text THEN 'Rawat Jalan'::text
                  ELSE 'Rawat Inap'::text
              END AS jenis_pendaftaran
         FROM pendaftaran_t
           JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
           LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
           LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
           LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
           LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
           LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
           LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
           LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
           LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
           LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
        WHERE pendaftaran_t.status_periksa::text <> '628'::text AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.status_periksa::text <> '453'::text AND instalasi_m.instalasi_id <> 3
      UNION ALL
       SELECT 'admisi'::text AS ket,
          pasien_m.pasien_id,
          pasien_m.no_identitas_pasien,
          pasien_m.nama_pasien,
          pasien_m.nama_panggilan AS nama_bin,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
          pasien_m.tempat_lahir,
          pasien_m.tanggal_lahir,
          pasien_m.alamat_pasien,
          pasien_m.rt,
          pasien_m.rw,
          pasien_m.photopasien,
          pasien_m.alamatemail,
          pasien_m.statusrekammedis,
          pasien_m.statusperkawinan,
          pasien_m.no_rekam_medik,
          pasien_m.tgl_rekam_medik,
          pendaftaran_t.pendaftaran_id,
          pendaftaran_t.no_pendaftaran,
          pendaftaran_t.tgl_pendaftaran,
          pendaftaran_t.no_urutantri,
          pendaftaran_t.transportasi,
          pendaftaran_t.keadaan_masuk,
          pendaftaran_t.alih_status,
          pendaftaran_t.by_phone,
          pendaftaran_t.kunjungan_rumah,
          pendaftaran_t.status_masuk,
          pendaftaran_t.umur,
          asuransipasien_m.nokartuasuransi AS no_asuransi,
          asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
          asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
          pendaftaran_t.shift_id,
          pasienadmisi_t.ruangan_id,
          ruangan_m.ruangan_nama,
          ruangan_m.instalasi_id,
          instalasi_m.instalasi_nama,
          jeniskasuspenyakit_m.jeniskasuspenyakit_id,
          jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
          kelaspelayanan_m.kelaspelayanan_id,
          kelaspelayanan_m.kelaspelayanan_nama,
          pendaftaran_t.rujukan_id,
          pendaftaran_t.pasienpulang_id,
          fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
          pasien_m.kabupaten_id,
          fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
          pasien_m.kelurahan_id,
          fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
          pasien_m.kecamatan_id,
          pasienadmisi_t.carabayar_id,
          carabayar_m.carabayar_nama,
          penjamin_m.penjamin_id,
          penjamin_m.penjamin_nama,
          asuransipasien_m.nopeserta,
          asuransipasien_m.tglcetakkartuasuransi,
          asuransipasien_m.kodefeskestk1,
          asuransipasien_m.nama_feskestk1,
          asuransipasien_m.masaberlakukartu,
          asuransipasien_m.nokartukeluarga,
          asuransipasien_m.nopassport,
          asuransipasien_m.status_konfirmasi,
          asuransipasien_m.tgl_konfirmasi,
          asuransipasien_m.is_active,
          pasien_m.is_deleted,
          fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
          fgetnamalookup(pasien_m.agama::integer) AS agama,
          fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
          fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
          fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
          fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
          fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
          fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
          fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
          gelarbelakang.gelarbelakang_nama,
          fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
          bpjs_t.nosep,
              CASE
                  WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
                  ELSE pindah_kamar.kelas_ditagihkan_id
              END AS kelas_ditagihkan_id,
              CASE
                  WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
                  ELSE pindah_kamar.kelas_ditagihkan
              END AS kelas_ditagihkan_nama,
          pasienadmisi_t.is_pasientitipan,
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
                  WHEN pendaftaran_t.status_periksa::text <> '433'::text THEN 'Rawat Jalan'::text
                  ELSE 'Rawat Inap'::text
              END AS jenis_pendaftaran
         FROM pendaftaran_t
           JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
           JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
           LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
           JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
           JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
           JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
           LEFT JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
           LEFT JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
           LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
           LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
           LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
           LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
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
        WHERE pendaftaran_t.status_periksa::text <> '628'::text AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.status_periksa::text <> '453'::text AND instalasi_m.instalasi_id <> 3
      UNION ALL
       SELECT 'admisi rd'::text AS ket,
          pasien_m.pasien_id,
          pasien_m.no_identitas_pasien,
          pasien_m.nama_pasien,
          pasien_m.nama_panggilan AS nama_bin,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
          pasien_m.tempat_lahir,
          pasien_m.tanggal_lahir,
          pasien_m.alamat_pasien,
          pasien_m.rt,
          pasien_m.rw,
          pasien_m.photopasien,
          pasien_m.alamatemail,
          pasien_m.statusrekammedis,
          pasien_m.statusperkawinan,
          pasien_m.no_rekam_medik,
          pasien_m.tgl_rekam_medik,
          pendaftaran_t.pendaftaran_id,
          pendaftaran_t.no_pendaftaran,
          pasienadmisi_t.tgl_pendaftaran,
          pendaftaran_t.no_urutantri,
          pendaftaran_t.transportasi,
          pendaftaran_t.keadaan_masuk,
          pendaftaran_t.alih_status,
          pendaftaran_t.by_phone,
          pendaftaran_t.kunjungan_rumah,
          pendaftaran_t.status_masuk,
          pendaftaran_t.umur,
          asuransipasien_m.nokartuasuransi AS no_asuransi,
          asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
          asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
          pendaftaran_t.shift_id,
          pasienadmisi_t.ruangan_id,
          ruangan_m.ruangan_nama,
          ruangan_m.instalasi_id,
          instalasi_m.instalasi_nama,
          jeniskasuspenyakit_m.jeniskasuspenyakit_id,
          jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
          kelaspelayanan_m.kelaspelayanan_id,
          kelaspelayanan_m.kelaspelayanan_nama,
          pendaftaran_t.rujukan_id,
          pendaftaran_t.pasienpulang_id,
          fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
          pasien_m.kabupaten_id,
          fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
          pasien_m.kelurahan_id,
          fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
          pasien_m.kecamatan_id,
          pasienadmisi_t.carabayar_id,
          carabayar_m.carabayar_nama,
          penjamin_m.penjamin_id,
          penjamin_m.penjamin_nama,
          asuransipasien_m.nopeserta,
          asuransipasien_m.tglcetakkartuasuransi,
          asuransipasien_m.kodefeskestk1,
          asuransipasien_m.nama_feskestk1,
          asuransipasien_m.masaberlakukartu,
          asuransipasien_m.nokartukeluarga,
          asuransipasien_m.nopassport,
          asuransipasien_m.status_konfirmasi,
          asuransipasien_m.tgl_konfirmasi,
          asuransipasien_m.is_active,
          pasien_m.is_deleted,
          fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
          fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
          fgetnamalookup(pasien_m.agama::integer) AS agama,
          fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
          fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
          fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
          fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
          fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
          fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
          fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
          gelarbelakang.gelarbelakang_nama,
          fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
          bpjs_t.nosep,
              CASE
                  WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
                  ELSE pindah_kamar.kelas_ditagihkan_id
              END AS kelas_ditagihkan_id,
              CASE
                  WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
                  ELSE pindah_kamar.kelas_ditagihkan
              END AS kelas_ditagihkan_nama,
          pasienadmisi_t.is_pasientitipan,
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
                  WHEN pendaftaran_t.status_periksa::text <> '433'::text THEN 'Rawat Jalan'::text
                  ELSE 'Rawat Inap'::text
              END AS jenis_pendaftaran
         FROM pendaftaran_t
           JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
           JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
           LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
           JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
           JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
           JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
           LEFT JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
           LEFT JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
           LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
           LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
           LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
           LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
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
        WHERE pendaftaran_t.status_periksa::text <> '628'::text AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.status_periksa::text <> '453'::text AND instalasi_m.instalasi_id = 3;
