CREATE OR REPLACE VIEW public.koreksidiagnosa_new_v
AS SELECT sy_koreksidiagnosa.sy_koreksidiagnosa_id,
    sy_koreksidiagnosa.kunjungan_id,
    sy_kunjungan.no_pendaftaran,
    sy_kunjungan.pasien_id,
    sy_kunjungan.no_rekammedik,
    sy_kunjungan.nama_pasien,
    sy_kunjungan.dokter_kode,
    sy_kunjungan.dokter_nama AS dokter_dpjp,
    sy_koreksidiagnosa.tgl_koreksidiagnosa,
    sy_koreksidiagnosa.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    sy_koreksidiagnosa.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
    sy_koreksidiagnosa.diagnosaasal_id,
    sy_koreksidiagnosa.diag_asal_masuk,
    sy_koreksidiagnosa.diag_asal_utama,
    sy_koreksidiagnosa.diag_asal_penyerta,
    sy_koreksidiagnosa.diag_asal_terapi,
    sy_koreksidiagnosa.is_inacbg,
    sy_koreksidiagnosa.is_icdprimer,
    sy_koreksidiagnosa.is_deleted,
    sy_koreksidiagnosa.is_active,
    kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa,
    sy_koreksidiagnosa.is_inagrouper
   FROM sy_koreksidiagnosa
     JOIN sy_kunjungan ON sy_koreksidiagnosa.kunjungan_id = sy_kunjungan.kunjungan_id
     JOIN kelompokdiagnosa_m ON sy_koreksidiagnosa.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
     JOIN diagnosa_m ON sy_koreksidiagnosa.diagnosa_id = diagnosa_m.diagnosa_id
  WHERE sy_koreksidiagnosa.is_deleted = false
  ORDER BY sy_koreksidiagnosa.sy_koreksidiagnosa_id;