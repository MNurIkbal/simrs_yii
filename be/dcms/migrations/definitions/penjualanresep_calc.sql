CREATE TABLE IF NOT EXISTS public.penjualanresep_calc (
    penjualanresep_id serial4 NOT NULL,
    ruangan_id int4 NOT NULL,
    noresep varchar NOT NULL,
    reseptur_id int4 NULL,
    status_reseptur int4 NULL,
    is_deleted bool NULL
);