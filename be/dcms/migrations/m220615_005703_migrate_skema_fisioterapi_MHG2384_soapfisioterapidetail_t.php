<?php

use yii\db\Migration;

/**
 * Class m220615_005703_migrate_skema_fisioterapi_MHG2384_soapfisioterapidetail_t
 */
class m220615_005703_migrate_skema_fisioterapi_MHG2384_soapfisioterapidetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."soapfisioterapidetail_t" (
            "soapfisioterapidetail_id" serial8,
            "soapfisioterapi_id" int4,
            "programterapi_id" int4,
            "pendaftaran_id" int4,
            "pasien_id" int4,
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
            "pasienmasukpenunjang_id" int4,
            CONSTRAINT "soapfisioterapidetail_t_pkey" PRIMARY KEY ("soapfisioterapidetail_id")
            )
            ;
            ');

        $this->execute('ALTER TABLE "public"."soapfisioterapidetail_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220615_005703_migrate_skema_fisioterapi_MHG2384_soapfisioterapidetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_005703_migrate_skema_fisioterapi_MHG2384_soapfisioterapidetail_t cannot be reverted.\n";

        return false;
    }
    */
}
