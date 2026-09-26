CREATE TABLE IF NOT EXISTS "public"."satusehat_loinc_m" (
  "loinc_id" serial4 NOT NULL,
  "loinc_code" varchar(30) COLLATE "pg_catalog"."default",
  "loinc_display" varchar(255) COLLATE "pg_catalog"."default",
  "loinc_tipe" varchar(10) COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "satusehat_loinc_m_pkey" PRIMARY KEY ("loinc_id")
);
