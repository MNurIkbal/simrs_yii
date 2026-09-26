-- public.sy_infopasienbpjsdiagnosa_v source

CREATE OR REPLACE VIEW public.sy_infopasienbpjsdiagnosa_v
AS SELECT sy_kunjungan.kunjungan_id,
    koreksi_diagnosa.kelompokdiagnosa_id,
    kelompok_diagnosa.kelompokdiagnosa_nama,
    koreksi_diagnosa.diagnosa_id,
    diagnosa.diagnosa_kode,
    diagnosa.diagnosa_nama,
    koreksi_diagnosa.diagnosaasal_id,
    koreksi_diagnosa.diag_asal_masuk,
    koreksi_diagnosa.diag_asal_utama,
    koreksi_diagnosa.diag_asal_terapi,
    koreksi_diagnosa.diag_asal_penyerta,
    koreksi_diagnosa.is_inacbg,
    koreksi_diagnosa.is_icdprimer,
    koreksi_diagnosa.is_deleted,
    koreksi_diagnosa.is_inagrouper,
    koreksi_diagnosa.sy_koreksidiagnosa_id,
    koreksi_diagnosa.is_idrg,
    koreksi_diagnosa.multiplicity,
    diagnosa.validcode,
    diagnosa.accpdx,
    diagnosa.asterik
   FROM sy_kunjungan
     JOIN ( SELECT a.kunjungan_id,
            a.kelompokdiagnosa_id,
            a.sy_koreksidiagnosa_id,
            a.diagnosa_id,
            a.diagnosaasal_id,
            a.diag_asal_masuk,
            a.diag_asal_utama,
            a.diag_asal_terapi,
            a.diag_asal_penyerta,
            a.is_inacbg,
            a.is_icdprimer,
            a.is_deleted,
            a.is_inagrouper,
            a.is_idrg,
            a.multiplicity
           FROM sy_koreksidiagnosa a
          WHERE a.is_deleted IS FALSE) koreksi_diagnosa ON sy_kunjungan.kunjungan_id = koreksi_diagnosa.kunjungan_id
     JOIN ( SELECT a.kelompokdiagnosa_id,
            a.kelompokdiagnosa_nama
           FROM kelompokdiagnosa_m a) kelompok_diagnosa ON kelompok_diagnosa.kelompokdiagnosa_id = koreksi_diagnosa.kelompokdiagnosa_id
     JOIN ( SELECT a.diagnosa_id,
            a.diagnosa_nama,
            a.diagnosa_kode,
            a.validcode,
            a.accpdx,
            a.asterik
           FROM diagnosa_m a) diagnosa ON diagnosa.diagnosa_id = koreksi_diagnosa.diagnosa_id
  WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false
  ORDER BY koreksi_diagnosa.sy_koreksidiagnosa_id;