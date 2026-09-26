BEGIN;

ALTER TABLE public.soapfisioterapi_t
  ADD COLUMN IF NOT EXISTS diagnosa_fungsi json,
  ADD COLUMN IF NOT EXISTS prosedur_kerja json,
  ADD COLUMN IF NOT EXISTS goal text;

COMMIT;