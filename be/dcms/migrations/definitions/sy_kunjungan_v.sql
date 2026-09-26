-- public.sy_kunjungan_v source

CREATE OR REPLACE VIEW public.sy_kunjungan_v
AS SELECT 'RJ/RD'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
    pendaftaran_t.tgl_pendaftaran::time without time zone AS jam_pendaftaran,
    pasienpulang_t.tglpasienpulang::date AS tgl_pulang,
    pasienpulang_t.tglpasienpulang::time without time zone AS jam_pulang,
    instalasi_m.instalasi_singkatan,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_kode,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS kamartempattidur_kode,
    NULL::character varying AS no_tempattidur,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pasien_id,
    concat(lkp_namadepan.lookup_name, pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    kelaspelayanan_m.kelaspelayanan_kode,
    kelaspelayanan_m.kelaspelayanan_nama,
    carabayar_m.carabayar_kode,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_kode,
    penjamin_m.penjamin_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.nama_pegawai,
    carakeluar_m.carakeluar_kode,
    carakeluar_m.carakeluar_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    1 AS lama_rawat,
    bpjs_t.nosep,
    bpjs_t.nokartuasuransi,
    bpjs_t.klsrawat,
    bpjs_t.additional_data,
    bpjs_t.info_response,
    pembayaran_t.no_pembayaran
   FROM pendaftaran_t
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama,
            a.instalasi_singkatan
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin,
            a.namadepan
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_kode,
            a.carabayar_nama
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.pegawai_id,
            a.nomorindukpegawai,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang,
            a.carakeluar_id
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_kode,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.nosep,
            a.nokartuasuransi,
            a.klsrawat,
            a.additional_data,
            a.info_response
           FROM bpjs_t a
          WHERE a.is_deleted = false) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_namadepan ON pasien_m.namadepan::integer = lkp_namadepan.lookup_id
  WHERE pendaftaran_t.pasienadmisi_id IS NULL AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
           FROM gabungpelayanandetail_t
          WHERE gabungpelayanandetail_t.is_deleted = false))
UNION ALL
 SELECT 'RI'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasienadmisi_id,
    instalasi_m.instalasi_id,
    pasienadmisi_t.tgl_admisi::date AS tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi::time without time zone AS jam_pendaftaran,
    pasienpulang_t.tglpasienpulang::date AS tgl_pulang,
    pasienpulang_t.tglpasienpulang::time without time zone AS jam_pulang,
    instalasi_m.instalasi_singkatan,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_kode,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.kamartempattidur_kode,
    kamartempattidur_m.no_tempattidur,
    pasien_m.no_rekam_medik,
    pasienadmisi_t.pasien_id,
    concat(lkp_namadepan.lookup_name, pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    kelaspelayanan_m.kelaspelayanan_kode,
    kelaspelayanan_m.kelaspelayanan_nama,
    carabayar_m.carabayar_kode,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_kode,
    penjamin_m.penjamin_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.nama_pegawai,
    carakeluar_m.carakeluar_kode,
    carakeluar_m.carakeluar_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date AS lama_rawat,
    bpjs_t.nosep,
    bpjs_t.nokartuasuransi,
    bpjs_t.klsrawat,
    bpjs_t.additional_data,
    bpjs_t.info_response,
    pembayaran_t.no_pembayaran
   FROM pasienadmisi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.umur,
            a.jeniskasuspenyakit_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama,
            a.instalasi_singkatan
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin,
            a.namadepan
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_kode,
            a.carabayar_nama
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.nomorindukpegawai
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_kode,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ( SELECT a.kamartempattidur_id,
            a.kamartempattidur_kode,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pasienadmisi_t.pasienadmisi_id = pembayaran_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.carakeluar_id,
            a.tglpasienpulang
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_kode,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.nosep,
            a.nokartuasuransi,
            a.klsrawat,
            a.additional_data,
            a.info_response
           FROM bpjs_t a
          WHERE a.is_deleted = false) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_namadepan ON pasien_m.namadepan::integer = lkp_namadepan.lookup_id
  WHERE pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.pasienbatalperiksa_id IS NULL;