-- public.diagnosa_v source

CREATE OR REPLACE VIEW public.diagnosa_v
AS SELECT diagnosa_m.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_nama AS diagnosa_namalainnya,
    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
    klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
    dtd_m.dtd_nama,
    tabularlist_m.tabularlist_chapter,
    tabularlist_m.tabularlist_versi,
    diagnosa_m.is_active,
    diagnosa_m.is_deleted,
    diagnosa_m.validcode,
    diagnosa_m.ina_grouper,
    diagnosa_m.type,
    diagnosa_m.accpdx,
    diagnosa_m.asterik,
    diagnosa_m.validcode_idrg
   FROM diagnosa_m
     LEFT JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
     LEFT JOIN dtd_m ON klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id
     LEFT JOIN tabularlist_m ON dtd_m.tabularlist_id = tabularlist_m.tabularlist_id
  WHERE diagnosa_m.is_active = true AND diagnosa_m.is_deleted = false AND diagnosa_m.klasifikasidiagnosa_id IS NOT NULL
UNION ALL
 SELECT diagnosakep_m.diagnosakep_id AS diagnosa_id,
    diagnosakep_m.diagnosakep_kode AS diagnosa_kode,
    diagnosakep_m.diagnosakep_nama AS diagnosa_namalainnya,
    diagnosakep_m.diagnosakep_nama AS diagnosa_nama,
    NULL::character varying AS klasifikasidiagnosa_nama,
    NULL::character varying AS dtd_nama,
    NULL::character varying AS tabularlist_chapter,
    'ICD_KEP'::character varying AS tabularlist_versi,
    diagnosakep_m.is_active,
    diagnosakep_m.is_deleted,
    0 AS validcode,
    NULL::character varying AS ina_grouper,
    NULL::character varying AS type,
    NULL::character varying AS accpdx,
    NULL::integer AS asterik,
    0 AS validcode_idrg
   FROM diagnosakep_m
  WHERE diagnosakep_m.is_active = true AND diagnosakep_m.is_deleted = false;