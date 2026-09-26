<?php

use yii\db\Migration;

/**
 * Class m220511_055234_migrate_programterapi_t
 */
class m220511_055234_migrate_programterapi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE if not exists "public"."programterapi_t" (
                          "programterapi_id" serial8,
                          "pendaftaran_id" int4,
                          "pasienmasukpenunjang_id" int4,
                          "pasien_id" int4,
                          "tgl_permintaan" timestamp(6),
                          "diagnosa" text COLLATE "pg_catalog"."default",
                          "frekuensi" float4,
                          "catatan" text COLLATE "pg_catalog"."default",
                          "dokterperujuk_id" int4,
                          "additional_data" text COLLATE "pg_catalog"."default",
                          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                          "created_by" int4,
                          "modified_count" int4,
                          "last_modified_date" timestamp(6),
                          "last_modified_by" int4,
                          "is_deleted" bool NOT NULL DEFAULT false,
                          "is_active" bool NOT NULL DEFAULT true,
                          "deleted_date" timestamp(6),
                          "deleted_by" int4,
                          "status_fisio" varchar(50) COLLATE "pg_catalog"."default",
                          "tipe_instalasi" varchar(8) COLLATE "pg_catalog"."default",
                          "is_paket" bool DEFAULT false,
                          "status_program_fisio" varchar(50) COLLATE "pg_catalog"."default",
                          "pasienkirimkeunitlain_id" int4,
                          CONSTRAINT "programterapi_t_pkey" PRIMARY KEY ("programterapi_id")
                        )
                        ;');

   $this->execute('ALTER TABLE "public"."programterapi_t" 
                        OWNER TO "postgres";');

   $this->execute('COMMENT ON COLUMN "public"."programterapi_t"."status_fisio" IS \'Lihat di lookup_m type status_fisio\';');
   $this->execute('COMMENT ON COLUMN "public"."programterapi_t"."status_fisio" IS \'Lihat di lookup_m type status_fisio\';');
   $this->execute('COMMENT ON COLUMN "public"."programterapi_t"."status_fisio" IS \'Lihat di lookup_m type status_fisio\';');
   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_055234_migrate_programterapi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_055234_migrate_programterapi_t cannot be reverted.\n";

        return false;
    }
    */
}
