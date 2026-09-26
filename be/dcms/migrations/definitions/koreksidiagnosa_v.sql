-- public.koreksidiagnosa_v source

CREATE OR REPLACE VIEW public.koreksidiagnosa_v
AS SELECT koreksidiagnosa_t.koreksidiagnosa_id,
    koreksidiagnosa_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    koreksidiagnosa_t.pasienadmisi_id,
    koreksidiagnosa_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    koreksidiagnosa_t.dokterdpjp_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    koreksidiagnosa_t.tgl_koreksidiagnosa,
    koreksidiagnosa_t.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
    koreksidiagnosa_t.diagnosaasal_id,
    koreksidiagnosa_t.diag_asal_masuk,
    koreksidiagnosa_t.diag_asal_utama,
    koreksidiagnosa_t.diag_asal_penyerta,
    koreksidiagnosa_t.diag_asal_terapi,
    koreksidiagnosa_t.is_inacbg,
    koreksidiagnosa_t.is_icdprimer,
    koreksidiagnosa_t.is_deleted,
    koreksidiagnosa_t.is_active,
    kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa,
    tabularlist_m.tabularlist_chapter,
    tabularlist_m.tabularlist_versi
   FROM koreksidiagnosa_t
     JOIN ( SELECT pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.pendaftaran_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienadmisi_t_1.pasienadmisi_id
           FROM pasienadmisi_t pasienadmisi_t_1) pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.nama_pasien
           FROM pasien_m pasien_m_1) pasien_m ON koreksidiagnosa_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) pegawai_m ON koreksidiagnosa_t.dokterdpjp_id = pegawai_m.pegawai_id
     JOIN ( SELECT kelompokdiagnosa_m_1.kelompokdiagnosa_id,
            kelompokdiagnosa_m_1.kelompokdiagnosa_nama,
            kelompokdiagnosa_m_1.kelompokdiagnosa_namalainnya
           FROM kelompokdiagnosa_m kelompokdiagnosa_m_1) kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
     JOIN ( SELECT diagnosa_m_1.diagnosa_id,
            diagnosa_m_1.diagnosa_kode,
            diagnosa_m_1.diagnosa_namalainnya,
            diagnosa_m_1.klasifikasidiagnosa_id
           FROM diagnosa_m diagnosa_m_1) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     LEFT JOIN ( SELECT klasifikasidiagnosa_m_1.dtd_id,
            klasifikasidiagnosa_m_1.klasifikasidiagnosa_id
           FROM klasifikasidiagnosa_m klasifikasidiagnosa_m_1) klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
     LEFT JOIN ( SELECT dtd_m_1.dtd_id,
            dtd_m_1.tabularlist_id
           FROM dtd_m dtd_m_1) dtd_m ON klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id
     LEFT JOIN ( SELECT tabularlist_m_1.tabularlist_id,
            tabularlist_m_1.tabularlist_chapter,
            tabularlist_m_1.tabularlist_versi
           FROM tabularlist_m tabularlist_m_1) tabularlist_m ON dtd_m.tabularlist_id = tabularlist_m.tabularlist_id
  WHERE koreksidiagnosa_t.is_deleted = false;