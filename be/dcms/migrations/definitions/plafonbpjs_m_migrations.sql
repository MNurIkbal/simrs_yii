CREATE TABLE IF NOT EXISTS  "public"."plafonbpjs_m" (
  "plafonbpjs_id" serial4 NOT NULL PRIMARY KEY,
  "instalasi_id" int4 NOT NULL,
  "kelaspelayanan_id" int4 NOT NULL,
	"plafon" float8 NOT NULL,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4
);