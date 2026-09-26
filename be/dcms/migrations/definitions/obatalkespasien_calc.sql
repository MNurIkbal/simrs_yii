CREATE TABLE IF NOT EXISTS public.obatalkespasien_calc (
    obatalkespasien_id serial4 NOT NULL,
    qty_konversi float8 NULL,
    det_konversi float8 NULL,
    obatalkes_id int4 NULL,
    penjualanresep_id int4 NULL,
    is_deleted bool NULL
);