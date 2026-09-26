-- public.int_ruangan_v source

CREATE OR REPLACE VIEW public.int_ruangan_v
AS SELECT ruangan_m.ruangan_id AS sync_id_api,
    '-'::text AS parent_id,
    ruangan_m.ruangan_nama AS name,
    true AS active,
    6 AS sync_type,
    ruangan_m.ruangan_id,
        CASE ruangan_m.additional_data
            WHEN 'EMA'::text THEN NULL::text
            ELSE ruangan_m.additional_data
        END AS additional_data,
    ruangan_m.is_store,
    ruangan_m.is_mainstore,
    ruangan_m.is_substore,
    ruangan_m.is_cartstore,
        CASE
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS TRUE THEN true
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS FALSE THEN false
            WHEN ruangan_m.is_active IS FALSE AND ruangan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN ruangan_m.additional_data <> 'EMA'::text AND (ruangan_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN ruangan_m.additional_data <> 'EMA'::text AND (ruangan_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'MENUNGGU PROSES'::text
        END AS status_proses,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.kode_ruangan_bpjs,
        CASE
            WHEN ruangan_m.instalasi_id = 1 THEN true
            ELSE false
        END AS is_poliklinik
   FROM ruangan_m
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id;