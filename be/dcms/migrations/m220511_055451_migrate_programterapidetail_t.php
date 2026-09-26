<?php

use yii\db\Migration;

/**
 * Class m220511_055451_migrate_programterapidetail_t
 */
class m220511_055451_migrate_programterapidetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('CREATE TABLE if not exists "public"."programterapidetail_t" (
          "programterapidetail_id" serial8,
          "programterapi_id" int4,
          "daftartindakan_id" int4,
          "frekuensi" float4,
          "catatan" text COLLATE "pg_catalog"."default",
          "dokterperujuk_id" int4,
          "terapis_id" int4,
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
          "tipepaket_id" int4,
          "tariftindakan_id" int4,
          "pemeriksaanfisio_id" int4,
          "is_paketfisio" bool DEFAULT false,
          CONSTRAINT "programterapidetail_t_pkey" PRIMARY KEY ("programterapidetail_id")
        )
        ;');

           $this->execute('ALTER TABLE "public"."programterapidetail_t" 
          OWNER TO "postgres";');
           
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_055451_migrate_programterapidetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_055451_migrate_programterapidetail_t cannot be reverted.\n";

        return false;
    }
    */
}
