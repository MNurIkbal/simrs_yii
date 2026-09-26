-- public.int_barang source

CREATE OR REPLACE VIEW public.int_barang
AS SELECT concat('BRG', barang_m.barang_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    barang_m.barang_nama AS name,
    concat('BRG', barang_m.kelompokbarang_id) AS categ_id,
    COALESCE(barang_m.satuankecil_id, barang_m.satuan1_id) AS uom_po_id,
    COALESCE(barang_m.satuankecil_id, barang_m.satuan1_id) AS uom2_id,
    barang_m.satuankecil_id AS uom_id,
    barang_m.barang_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS TRUE THEN true
            WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS FALSE THEN false
            WHEN barang_m.is_active IS FALSE AND barang_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN barang_m.satuan1_id IS NULL THEN 1
            ELSE barang_m.isi_satuan1
        END AS conversion_rate,
    6 AS sync_type,
    'BARANG'::text AS jenis,
    barang_m.barang_id AS id,
    barang_m.additional_data,
        CASE
            WHEN (barang_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (barang_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status,
    barang_m.barang_id,
    false AS sync_is_package,
    false AS sync_is_service,
    NULL::integer AS servicecategory_id,
    NULL::integer AS servicegroup_id,
    NULL::integer AS groupinacbg_id,
        CASE
            WHEN barang_m.golonganbarang_id = 562 THEN true
            ELSE false
        END AS is_inventaris
   FROM barang_m;