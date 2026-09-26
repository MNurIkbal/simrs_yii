CREATE TABLE IF NOT EXISTS public.satusehat_cvx_m (
  "satusehat_cvx_id" serial4 NOT NULL,
  "cvx_group_code" varchar(50) COLLATE "pg_catalog"."default",
  "cvx_group_display" varchar(50) COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "satusehat_cvx_m_pkey" PRIMARY KEY ("satusehat_cvx_id")
);