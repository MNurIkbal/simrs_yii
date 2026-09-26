CREATE TABLE IF NOT EXISTS "public"."histori_plafon_bpjs_pasien_r" (
  "histori_plafon_bpjs_pasien_id" serial4 NOT NULL PRIMARY KEY,
  "pendaftaran_id" int4,
  "pasienadmisi_id" int4,
  "plafon_lama" float8,
  "plafon_baru" float8,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" date,
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" date,
  "deleted_by" int4
);