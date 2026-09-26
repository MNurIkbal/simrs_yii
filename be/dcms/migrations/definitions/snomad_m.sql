CREATE TABLE IF NOT EXISTS "public"."snomad_m" (
  "snomad_id" serial4 NOT NULL,
  "snomad_kode" varchar(20) COLLATE "pg_catalog"."default",
  "snomad_nama" varchar(100) COLLATE "pg_catalog"."default",
  "snomad_namalainnya" varchar(100) COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "snomed_category" varchar(255) COLLATE "pg_catalog"."default",
  CONSTRAINT "snomad_m_pkey" PRIMARY KEY ("snomad_id")
);