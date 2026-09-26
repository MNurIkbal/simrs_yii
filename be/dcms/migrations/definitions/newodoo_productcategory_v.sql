-- public.newodoo_productcategory_v source

CREATE OR REPLACE VIEW public.newodoo_productcategory_v
AS SELECT concat('BRG', kelompokbarang_m.kelompokbarang_id) AS sync_id_api,
    NULL::character varying AS parent_id,
    kelompokbarang_m.kelompokbarang_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    true AS active,
        CASE
            WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS TRUE THEN true
            WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS FALSE THEN false
            WHEN kelompokbarang_m.is_active IS FALSE AND kelompokbarang_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    kelompokbarang_m.kelompokbarang_id AS origin_id,
    'kelompokbarang'::text AS jenis
   FROM kelompokbarang_m
UNION ALL
 SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    NULL::character varying AS parent_id,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    true AS active,
        CASE
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS TRUE THEN true
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS FALSE THEN false
            WHEN jenisobatalkes_m.is_active IS FALSE AND jenisobatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    jenisobatalkes_m.jenisobatalkes_id AS origin_id,
    'jenisobatalkes'::text AS jenis
   FROM jenisobatalkes_m
UNION ALL
 SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
    NULL::character varying AS parent_id,
    kelompoktindakan_m.kelompoktindakan_nama AS name,
    true AS sync_is_service,
    'service'::text AS type,
    true AS active,
        CASE
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS TRUE THEN true
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS FALSE THEN false
            WHEN kelompoktindakan_m.is_active IS FALSE AND kelompoktindakan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    kelompoktindakan_m.kelompoktindakan_id AS origin_id,
    'kelompoktindakan'::text AS jenis
   FROM kelompoktindakan_m
UNION ALL
 SELECT concat('CATEG', servicecategory_m.servicecategory_id) AS sync_id_api,
    NULL::character varying AS parent_id,
    servicecategory_m.servicecategory_nama AS name,
    true AS sync_is_service,
    'service'::text AS type,
    true AS active,
        CASE
            WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS TRUE THEN true
            WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS FALSE THEN false
            WHEN servicecategory_m.is_active IS FALSE AND servicecategory_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    servicecategory_m.servicecategory_id AS origin_id,
    'servicecategory'::text AS jenis
   FROM servicecategory_m
UNION ALL
 SELECT concat('GROUP', servicegroup_m.servicegroup_id) AS sync_id_api,
    NULL::character varying AS parent_id,
    servicegroup_m.servicegroup_nama AS name,
    true AS sync_is_service,
    'service'::text AS type,
    true AS active,
        CASE
            WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS TRUE THEN true
            WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS FALSE THEN false
            WHEN servicegroup_m.is_active IS FALSE AND servicegroup_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    servicegroup_m.servicegroup_id AS origin_id,
    'servicegroup'::text AS jenis
   FROM servicegroup_m;