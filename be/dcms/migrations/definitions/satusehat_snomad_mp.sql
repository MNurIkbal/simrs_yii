CREATE TABLE IF NOT EXISTS "public"."satusehat_snomad_mp" (
  "satusehat_snomad_mp_id" serial4 NOT NULL,
  "snomad_id" int4,
  "samplelab_id" int4,
  "created_date" timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "snomed_bodysite_id" int4,
  "snomed_collectionmethod_id" int4,
  CONSTRAINT "satusehat_snomad_mp_pkey" PRIMARY KEY ("satusehat_snomad_mp_id")
);