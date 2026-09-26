CREATE TABLE IF NOT EXISTS public.bentuksediaan_m (
  "bentuksediaan_id" serial4 NOT NULL,
  "bentuksediaan_kode" varchar(100) COLLATE "pg_catalog"."default",
  "bentuksediaan_nama" varchar(100) COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "bentuksediaan_m_pkey" PRIMARY KEY ("bentuksediaan_id")
);
