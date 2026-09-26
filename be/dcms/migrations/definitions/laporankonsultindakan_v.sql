CREATE OR REPLACE VIEW public.laporankonsultindakan_v
AS SELECT laporankonsul_v.tgl_pendaftaran AS "Tanggal Pendfaftaran",
    laporankonsul_v.no_pendaftaran AS "No Pendaftaran",
    laporankonsul_v.no_rekam_medik AS "No Rekam Medik",
    laporankonsul_v.nama_pasien AS "Nama Pasien",
    laporankonsul_v.nama_dokter AS "Dokter Pengirim (Dokter DPJP)",
    laporankonsul_v.ruangan_asal AS "Ruangan Asal",
    laporankonsul_v.dok_mengkonsul AS "Dokter Rujukan",
    laporankonsul_v.ruangan_tujuan AS "Ruangan Tujuan",
    tindakanpelayanan_t.tgl_tindakan AS "Tanggal Tindakan",
    daftartindakan_m.daftartindakan_nama AS "Nama Tindakan",
    concat(carabayar_m.carabayar_nama, ' / ', penjamin_m.penjamin_nama) AS "Cara Bayar / Penjamin"
   FROM ( SELECT konsulpoli_t.konsulpoli_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            konsulpoli_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            konsulpoli_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan_tujuan,
            konsulpoli_t.pegawai_id,
            pegawai_m.nama_pegawai AS nama_dokter,
            konsulpoli_t.status_periksa,
            fgetnamalookup(konsulpoli_t.status_periksa::integer) AS status,
            konsulpoli_t.catatan_dokter_konsul,
            pendaftaran_t.ruangan_id AS ruanganasal_id,
            ruangan_asal.ruangan_nama AS ruangan_asal,
            konsulpoli_t.tgl_konsulpoli,
            konsulpoli_t.tgl_selesaikonsul,
            konsulpoli_t.jawaban_konsul,
            pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
            dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
            konsulpoli_t.status_konsul AS status_konsul_id,
            fgetnamalookup(konsulpoli_t.status_konsul::integer) AS status_konsul,
            konsulpoli_t.status_approve AS status_approve_id,
            fgetnamalookup(konsulpoli_t.status_approve::integer) AS status_approve,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id
           FROM konsulpoli_t
             JOIN pendaftaran_t ON konsulpoli_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pegawai_m dok_mengkonsul ON pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id
             JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
          WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false AND konsulpoli_t.status_approve = 565
        UNION ALL
         SELECT konsulpoli_t.konsulpoli_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            konsulpoli_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            konsulpoli_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan_tujuan,
            konsulpoli_t.pegawai_id,
            pegawai_m.nama_pegawai AS nama_dokter,
            konsulpoli_t.status_periksa,
            fgetnamalookup(konsulpoli_t.status_periksa::integer) AS status,
            konsulpoli_t.catatan_dokter_konsul,
            pendaftaran_t.ruangan_id AS ruanganasal_id,
            ruangan_asal.ruangan_nama AS ruangan_asal,
            konsulpoli_t.tgl_konsulpoli,
            konsulpoli_t.tgl_selesaikonsul,
            konsulpoli_t.jawaban_konsul,
            pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
            dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
            konsulpoli_t.status_konsul AS status_konsul_id,
            fgetnamalookup(konsulpoli_t.status_konsul::integer) AS status_konsul,
            konsulpoli_t.status_approve AS status_approve_id,
            fgetnamalookup(konsulpoli_t.status_approve::integer) AS status_approve,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id
           FROM konsulpoli_t
             JOIN pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pegawai_m dok_mengkonsul ON pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id
             JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
          WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false
        UNION ALL
         SELECT permintaankonsul_t.permintaankonsul_id AS konsulpoli_id,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan_tujuan,
            pasienadmisi_t.pegawai_id,
            dok_dpjp.nama_pegawai AS nama_dokter,
            pasienadmisi_t.status_ranap::character varying AS status_periksa,
            status_periksa.lookup_name AS status,
            permintaankonsul_t.ket_konsul AS catatan_dokter_konsul,
            pasienadmisi_t.ruangan_id AS ruanganasal_id,
            ruangan_m.ruangan_nama AS ruangan_asal,
            permintaankonsul_t.waktu_permintaan AS tgl_konsulpoli,
            permintaankonsul_t.waktu_persetujuan AS tgl_selesaikonsul,
            permintaankonsul_t.jawaban_konsul,
            permintaankonsul_t.dokter_id AS dok_mengkonsul_id,
            dok_konsul.nama_pegawai AS dok_mengkonsul,
            permintaankonsul_t.status_konsul AS status_konsul_id,
            fgetnamalookup(permintaankonsul_t.status_konsul) AS status_konsul,
            permintaankonsul_t.status_konsul AS status_approve_id,
            fgetnamalookup(permintaankonsul_t.status_konsul) AS status_approve,
            ruangan_m.instalasi_id,
            pasienadmisi_t.penjamin_id
           FROM permintaankonsul_t
             JOIN pendaftaran_t ON permintaankonsul_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
             JOIN pegawai_m dok_konsul ON permintaankonsul_t.dokter_id = dok_konsul.pegawai_id
             JOIN carabayar_m carabayar_m_1 ON pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m kls_rawat ON pasienadmisi_t.kelaspelayanan_id = kls_rawat.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kls_hak ON bpjs_t.klsrawat = kls_hak.kelaspelayanan_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN pegawai_m dok_dpjp_asal ON permintaankonsul_t.dokterdpjpasal_id = dok_dpjp_asal.pegawai_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_periksa ON pasienadmisi_t.status_ranap = status_periksa.lookup_id
          WHERE permintaankonsul_t.is_active = true AND permintaankonsul_t.is_deleted = false) laporankonsul_v
     LEFT JOIN tindakanpelayanan_t ON laporankonsul_v.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND laporankonsul_v.dok_mengkonsul_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN penjamin_m ON laporankonsul_v.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
  WHERE laporankonsul_v.instalasi_id = 3 AND (laporankonsul_v.status_konsul_id = ANY (ARRAY[437, 9999, 671, 670]))
  ORDER BY laporankonsul_v.tgl_konsulpoli;