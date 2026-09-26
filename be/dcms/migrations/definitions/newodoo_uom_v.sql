-- public.newodoo_uom_v source

CREATE OR REPLACE VIEW public.newodoo_uom_v
AS SELECT satuanunit_m.satuanunit_id AS sync_id_api,
    satuanunit_m.satuanunit_nama AS name,
    satuanunit_m.satuanunit_namalain AS code,
    true AS active,
    1 AS factor,
    'reference'::text AS uom_type,
    1 AS category_id,
        CASE
            WHEN satuanunit_m.is_active IS TRUE AND satuanunit_m.is_deleted IS TRUE THEN true
            WHEN satuanunit_m.is_active IS TRUE AND satuanunit_m.is_deleted IS FALSE THEN false
            WHEN satuanunit_m.is_active IS FALSE AND satuanunit_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM satuanunit_m;