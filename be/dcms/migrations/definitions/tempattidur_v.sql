-- public.tempattidur_v source

CREATE OR REPLACE VIEW public.tempattidur_v
AS SELECT kamarruangan_m.kamarruangan_id,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    kettempattidur_m.kettempattidur_nama,
    kamartempattidur_m.status_isi,
    kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.is_active,
    kamartempattidur_m.is_rekapkinerjaprofesi,
    kamartempattidur_m.is_terisi
   FROM kamarruangan_m
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = false
     LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id AND kettempattidur_m.is_deleted = false
  WHERE kamarruangan_m.is_deleted = false;