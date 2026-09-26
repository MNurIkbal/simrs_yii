-- public.infopermintaankonsul_v source

CREATE OR REPLACE VIEW public.infopermintaankonsul_v
AS SELECT permintaankonsul_t.waktu_permintaan,
    permintaankonsul_t.waktu_persetujuan,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin::integer AS jenis_kelamin_id,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.pegawai_id AS dok_dpjp_id,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.kelaspelayanan_id AS kls_rawat_id,
    kls_rawat.kelaspelayanan_nama AS kls_rawat,
    bpjs_t.klsrawat AS kls_hak_id,
    kls_hak.kelaspelayanan_nama AS kls_hak,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    permintaankonsul_t.jenis_konsul,
    permintaankonsul_t.dokter_id,
    dok_konsul.nama_pegawai AS dok_konsul,
    permintaankonsul_t.status_konsul,
    fgetnamalookup(permintaankonsul_t.status_konsul) AS status_konsul_nama,
    permintaankonsul_t.permintaankonsul_id,
    permintaankonsul_t.ket_konsul,
    fgetnamalookup(permintaankonsul_t.jenis_konsul::integer) AS jenis_konsul_nama,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.pendaftaran_id,
    permintaankonsul_t.jawaban_konsul,
    pendaftaran_t.pasien_id,
    permintaankonsul_t.pasienadmisi_id,
    permintaankonsul_t.created_by,
    permintaankonsul_t.created_by AS creator,
    pasienadmisi_t.pasienpulang_id,
    permintaankonsul_t.dokterdpjpasal_id,
    dok_dpjp_asal.nama_pegawai AS dokterdpjpasal_nama
   FROM permintaankonsul_t
     JOIN pendaftaran_t ON permintaankonsul_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_dpjp ON permintaankonsul_t.dokterdpjpasal_id = dok_dpjp.pegawai_id
     JOIN pegawai_m dok_konsul ON permintaankonsul_t.dokter_id = dok_konsul.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m kls_rawat ON pasienadmisi_t.kelaspelayanan_id = kls_rawat.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_hak ON bpjs_t.klsrawat = kls_hak.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m dok_dpjp_asal ON permintaankonsul_t.dokterdpjpasal_id = dok_dpjp_asal.pegawai_id
  WHERE permintaankonsul_t.is_active = true AND permintaankonsul_t.is_deleted = false;
