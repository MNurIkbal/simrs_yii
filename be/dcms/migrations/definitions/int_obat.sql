-- public.int_obat source

CREATE OR REPLACE VIEW public.int_obat
AS SELECT concat('OBT', obatalkes_m.obatalkes_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS categ_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom_po_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom2_id,
    obatalkes_m.satuankecil_id AS uom_id,
    obatalkes_m.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS TRUE THEN true
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS FALSE THEN false
            WHEN obatalkes_m.is_active IS FALSE AND obatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_m.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN obatalkes_m.satuanbesar_id IS NULL THEN 1
            ELSE obatalkes_m.kemasan_besar
        END AS conversion_rate,
    6 AS sync_type,
    'OBAT'::text AS jenis,
    obatalkes_m.obatalkes_id AS id,
    obatalkes_m.additional_data,
        CASE
            WHEN (obatalkes_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (obatalkes_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status,
    obatalkes_m.obatalkes_id,
    false AS sync_is_package,
    false AS sync_is_service,
    jm.servicecategory_id,
    jm.servicegroup_id,
    obatalkes_m.groupinacbg_id,
    false AS is_inventaris
   FROM obatalkes_m
     LEFT JOIN jenisobatalkes_m jm ON jm.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id;