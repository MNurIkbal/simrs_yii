<?php

use yii\db\Migration;

/**
 * Class m210226_095809_migrate_20210226_resepturracikan_t
 */
class m210226_095809_migrate_20210226_resepturracikan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."resepturracikan_t" (
  "resepturracikan_id" serial8,
  "reseptur_id" int4 NOT NULL,
  "rke" varchar(50) COLLATE "pg_catalog"."default",
  "no_racikan" varchar(100) COLLATE "pg_catalog"."default",
  "racikan" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "resepturracikan_t_pkey" PRIMARY KEY ("resepturracikan_id")
)
;
');
        $this->execute('ALTER TABLE "public"."resepturracikan_t" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_095809_migrate_20210226_resepturracikan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_095809_migrate_20210226_resepturracikan_t cannot be reverted.\n";

        return false;
    }
    */
}
