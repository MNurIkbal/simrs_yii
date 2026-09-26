<?php

use yii\db\Migration;

/**
 * Class m220324_170721_migrate_skema_fisio_programterapidetailpaket_t
 */
class m220324_170721_migrate_skema_fisio_programterapidetailpaket_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapidetailpaket_t" (
          "programterapidetailpaket_id" serial8,
          "programterapidetail_id" int4,
          "daftartindakan_id" int4,
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
          "catatan" text COLLATE "pg_catalog"."default",
          CONSTRAINT "programterapidetailpaket_t_pkey" PRIMARY KEY ("programterapidetailpaket_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."programterapidetailpaket_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_170721_migrate_skema_fisio_programterapidetailpaket_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_170721_migrate_skema_fisio_programterapidetailpaket_t cannot be reverted.\n";

        return false;
    }
    */
}
