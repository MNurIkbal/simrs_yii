CREATE OR REPLACE VIEW public.satusehat_loinc_tindakan_v
AS
 SELECT a.loinc_tindakan_id,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    a.loinc_id,
    satusehat_loinc_m.loinc_code,
    satusehat_loinc_m.loinc_display,
    satusehat_loinc_m.loinc_tipe,
    a.componen
   FROM satusehat_loinc_tindakan_mp a
     LEFT JOIN satusehat_loinc_m ON a.loinc_id = satusehat_loinc_m.loinc_id
     LEFT JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = a.daftartindakan_id
  WHERE a.is_deleted = false;