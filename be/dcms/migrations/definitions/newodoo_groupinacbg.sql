-- public.newodoo_groupinacbg source

CREATE OR REPLACE VIEW public.newodoo_groupinacbg
AS SELECT gm.groupinacbg_id AS sync_id_api,
    gm.groupinacbg_nama AS name,
    gm.groupinacbg_kode AS code,
    gm.is_obat,
    gm.inacbgs_field,
    gm.catatan,
    gm.is_deleted
   FROM groupinacbg_m gm;