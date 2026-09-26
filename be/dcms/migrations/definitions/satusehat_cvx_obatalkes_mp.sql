CREATE TABLE IF NOT EXISTS public.satusehat_cvx_obatalkes_mp (
  "satusehat_cvx_obatalkes_mp_id" serial4 NOT NULL,
  "satusehat_cvx_id" int4 NOT NULL,
  "obatalkes_id" int4 NOT NULL,
  "cvx_reasoncode" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "cvx_name_code" varchar(20) COLLATE "pg_catalog"."default" NOT NULL,
  "cvx_name_display" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "satusehat_cvx_obatalkes_mp_pkey" PRIMARY KEY ("satusehat_cvx_obatalkes_mp_id")
);