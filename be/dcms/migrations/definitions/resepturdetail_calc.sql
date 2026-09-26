CREATE TABLE IF NOT EXISTS public.resepturdetail_calc (
    resepturdetail_id serial4 NOT NULL,
    obatalkes_id int4 NOT NULL,
    qty_konversi float8 NULL,
    det_konversi float8 NULL,
    reseptur_id int4 NULL,
    is_deleted bool NULL
);