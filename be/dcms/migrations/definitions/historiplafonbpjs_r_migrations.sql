CREATE TABLE IF NOT EXISTS  "public"."historiplafonbpjs_r" (
  "historiplafonbpjs_id" serial4 NOT NULL PRIMARY KEY,
  "plafonbpjs_id" int4 NOT NULL,
  "action" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "old_data" jsonb,
  "new_data" jsonb,
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